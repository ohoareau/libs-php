<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common;

use Exception;
use Itq\Common\Traits;
use Itq\Common\Exception as CommonException;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Translation\TranslatorBagInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class ErrorManager implements ErrorManagerInterface
{
    use Traits\BaseTrait;
    use Traits\TranslatorAwareTrait;
    /**
     * @param TranslatorInterface|null $translator
     * @param string                   $locale
     * @param array                    $keyCodeMapping
     */
    public function __construct(TranslatorInterface $translator = null, $locale = null, array $keyCodeMapping = [])
    {
        if (null !== $translator) {
            $this->setTranslator($translator);
        }
        if ($this->isNonEmptyString($locale)) {
            $this->setLocale($locale);
        }

        $this->setKeyCodeMapping($keyCodeMapping);
    }
    /**
     * @param array $keyCodeMapping
     *
     * @return $this
     */
    public function setKeyCodeMapping(array $keyCodeMapping)
    {
        $that = $this;

        return $this->setParameter(
            'keyCodeMapping',
            array_map(
                function ($a) use ($that) {
                    return $that->buildKeyCodeData($a);
                },
                $keyCodeMapping
            )
        );
    }
    /**
     * @param array $keyCodeMapping
     *
     * @return $this
     */
    public function addKeyCodeMapping(array $keyCodeMapping)
    {
        return $this->setKeyCodeMapping($keyCodeMapping + $this->getKeyCodeMapping());
    }
    /**
     * @return array
     */
    public function getKeyCodeMapping()
    {
        return $this->getArrayParameter('keyCodeMapping');
    }
    /**
     * @param string $key
     * @param array  $code
     *
     * @return $this
     */
    public function setKeyCode($key, $code)
    {
        return $this->setArrayParameterKey('keyCodeMapping', $key, $this->buildKeyCodeData($code));
    }
    /**
     * @param string $key
     *
     * @return null|array
     */
    public function findOneKeyCode($key)
    {
        if (!$this->hasArrayParameterKey('keyCodeMapping', $key)) {
            return null;
        }

        return $this->getArrayParameterKey('keyCodeMapping', $key);
    }
    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->getParameterIfExists('locale');
    }
    /**
     * @param string $locale
     *
     * @return $this
     */
    public function setLocale($locale)
    {
        return $this->setParameter('locale', $locale);
    }
    /**
     * @param string $key
     * @param array  $params
     * @param array  $options
     *
     * @return Exception
     */
    public function createException($key, array $params = [], array $options = [])
    {
        $options    += ['locale' => $this->getLocale()];
        $code        = null;
        $codeData    = [];
        $originalKey = $key;
        $metaData    = isset($options['metaData']) ? $options['metaData'] : [];

        if ('#' === substr($key, 0, 1) && false !== strpos($key, ':')) {
            list ($code, $key) = explode(':', substr($key, 1), 2);
        }

        $realKey  = $key;

        if (false !== strpos($key, '/')) {
            list ($key, $context) = explode('/', $key, 2);
            $metaData['context'] = $context;
        }
        if (null === $code) {
            $codeData = ($this->findOneKeyCode($key) ?: []) + $codeData + ['code' => 0];
        } else {
            $codeData['code'] = (int) $code;
        }

        $translator = $this->hasTranslator() ? $this->getTranslator() : null;
        $tries      = [$realKey];

        if ($realKey !== $key) {
            $tries[] = $key;
        }

        $replaceParams          = [];
        $paramsAlreadyProcessed = false;

        if (is_array($params) && 1 === count($params) && is_array($params[0])) {
            $ignore = false;
            foreach (array_keys($params[0]) as $k) {
                if (!preg_match('/^\%.+\%$/', $k)) {
                    $ignore = true;
                    break;
                }
            }
            if (!$ignore) {
                $replaceParams = $params[0];
                $params = [];
                foreach ($replaceParams as $k => $v) {
                    $params[substr($k, 1, $this->getStringLength($k) - 2)] = $v;
                }
                $paramsAlreadyProcessed = true;
            }
        }
        if (!$paramsAlreadyProcessed) {
            foreach ($params as $k => $v) {
                $replaceParams['%'.$k.'%'] = $v;
            }
        }

        $message     = $key;
        $selectedKey = $key;

        if ($translator) {
            if ($translator instanceof TranslatorBagInterface) {
                foreach ($tries as $try) {
                    $catalog = $translator->getCatalogue($options['locale']);
                    if ($catalog->has($try, 'errors')) {
                        $selectedKey = $try;
                        $metaData['selectedPattern'] = $catalog->get($try, 'errors');
                        $metaData['locale'] = $catalog->getLocale();
                        break;
                    }
                }
            }
            $message = $translator->trans($selectedKey, $replaceParams, 'errors', $options['locale']);
        }

        $metaData['selectedKey'] = $selectedKey;
        $metaData['originalKey'] = $originalKey;

        return new CommonException\ErrorException(
            $message,
            isset($options['exceptionCode']) ? $options['exceptionCode'] : 500,
            $realKey,
            $params,
            $codeData['code'],
            $metaData,
            isset($options['previousException']) ? $options['previousException'] : null
        );
    }
    /**
     * @param array $options
     *
     * @return array
     */
    public function getErrorCodes(array $options = [])
    {
        /** @var TranslatorBagInterface $translator */
        $errorCodes      = [];
        $translator      = $this->hasTranslator() ? $this->getTranslator() : null;
        $translatorIsBag = $translator instanceof TranslatorBagInterface;

        foreach ($this->getKeyCodeMapping() as $key => $map) {
            $code = isset($map['code']) ? $map['code'] : 0;
            $code = (int) $code;
            if (!isset($errorCodes[$code])) {
                $errorCodes[$code] = ['errors' => []];
            }
            unset($map['code']);
            $this->prepareErrorMap($key, $map, $translator, $translatorIsBag);
            $errorCodes[$code]['errors'][$key] = $map;
        }

        return $errorCodes;
    }
    /**
     * @param int   $code
     * @param array $options
     *
     * @return array
     */
    public function getErrorCode($code, array $options = [])
    {
        /** @var TranslatorBagInterface $translator */
        $translator      = $this->hasTranslator() ? $this->getTranslator() : null;
        $translatorIsBag = $translator instanceof TranslatorBagInterface;
        $errorCode       = ['errors' => []];

        foreach ($this->getKeyCodeMapping() as $key => $map) {
            $_code = isset($map['code']) ? $map['code'] : 0;
            if ($_code !== $code) {
                continue;
            }
            unset($map['code']);
            $this->prepareErrorMap($key, $map, $translator, $translatorIsBag);
            $errorCode['errors'][$key] = $map;
        }

        return $errorCode;
    }
    /**
     * @param string                       $key
     * @param array                        $map
     * @param TranslatorBagInterface|mixed $translator
     * @param bool                         $translatorIsBag
     *
     * @return $this
     */
    protected function prepareErrorMap($key, array &$map, $translator, $translatorIsBag)
    {
        $map['messages'] = [];
        if ($translatorIsBag) {
            $catalogue = $translator->getCatalogue($this->getLocale());
            $map['messages'][$catalogue->getLocale()] = $catalogue->get($key, 'errors');
        }

        return $this;
    }
    /**
     * @param array|int $data
     *
     * @return array
     */
    protected function buildKeyCodeData($data)
    {
        if (!is_array($data)) {
            $data = ['code' => $data];
        }
        if (!isset($data['code'])) {
            $data['code'] = 0;
        }

        $data['code'] = (int) $data['code'];

        return $data;
    }
}
