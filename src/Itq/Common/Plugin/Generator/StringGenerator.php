<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\Generator;

use Itq\Common\Traits;
use Itq\Common\Service;
use /** @noinspection PhpUnusedAliasInspection */ Itq\Common\Annotation;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class StringGenerator extends Base\AbstractGenerator
{
    use Traits\ServiceAware\StringServiceAwareTrait;
    use Traits\ParameterAware\PlatformParameterAwareTrait;
    /**
     * @param Service\StringService $stringService
     * @param string                $platform
     * @param string                $storageUrlPattern
     * @param array                 $dynamicUrlPatterns
     */
    public function __construct(
        Service\StringService $stringService,
        $platform,
        $storageUrlPattern,
        array $dynamicUrlPatterns = []
    ) {
        $this->setStringService($stringService);
        $this->setPlatform($platform);
        $this->setStorageUrlPattern($storageUrlPattern);
        $this->setDynamicUrlPatterns($dynamicUrlPatterns);
    }
    /**
     * @param array $dynamicUrlPatterns
     *
     * @return $this
     */
    public function setDynamicUrlPatterns(array $dynamicUrlPatterns)
    {
        $this->setParameter('dynamicUrlPatterns', []);
        $this->setParameter('dynamicUrlPatternsVars', []);

        foreach ($dynamicUrlPatterns as $k => $v) {
            $this->addDynamicUrlPattern($k, $v);
        }

        return $this;
    }
    /**
     * @param string $name
     * @param string $pattern
     *
     * @return $this
     */
    public function addDynamicUrlPattern($name, $pattern)
    {
        $this->setArrayParameterKey('dynamicUrlPatterns', $name, $pattern);

        return $this->setArrayParameterKey('dynamicUrlPatternsVars', $name, $this->parsePattern($pattern));
    }
    /**
     * @param string $pattern
     *
     * @return $this
     */
    public function setStorageUrlPattern($pattern)
    {
        $this->setParameter('storageUrlPattern', $pattern);

        return $this->setParameter('storageUrlPatternVars', $this->parsePattern($pattern));
    }
    /**
     * @return string
     */
    public function getStorageUrlPattern()
    {
        return $this->getParameter('storageUrlPattern');
    }
    /**
     * @param string $name
     *
     * @return string
     */
    public function getDynamicUrlPattern($name)
    {
        return $this->getArrayParameterKey('dynamicUrlPatterns', $name);
    }
    /**
     * @return array
     */
    public function getStorageUrlPatternVars()
    {
        return $this->getParameter('storageUrlPatternVars');
    }
    /**
     * @param string $name
     *
     * @return array
     */
    public function getDynamicUrlPatternVars($name)
    {
        return $this->getArrayParameterKey('dynamicUrlPatternsVars', $name);
    }
    /**
     * @return string
     *
     * @Annotation\Generator("random_string")
     */
    public function generateString()
    {
        return rand(0, 1000).microtime(true).rand(rand(0, 100), 10000);
    }
    /**
     * @return string
     *
     * @Annotation\Generator("random_sha1")
     */
    public function generateRandomSha1String()
    {
        return $this->generateSha1String($this->generateRandomMd5String());
    }
    /**
     * @param string $string
     *
     * @return string
     *
     * @Annotation\Generator("sha1")
     */
    public function generateSha1String($string)
    {
        return sha1($string);
    }
    /**
     * @return string
     *
     * @Annotation\Generator("random_md5")
     */
    public function generateRandomMd5String()
    {
        return $this->generateMd5String($this->generateString());
    }
    /**
     * @param string $string
     *
     * @return string
     *
     * @Annotation\Generator("md5")
     */
    public function generateMd5String($string)
    {
        return md5($string);
    }
    /**
     * @param mixed $data
     *
     * @return string
     *
     * @Annotation\Generator("serialized")
     */
    public function generateSerializedString($data)
    {
        return serialize($data);
    }
    /**
     * @param mixed $data
     *
     * @return string
     *
     * @Annotation\Generator("fingerprint")
     */
    public function generateFingerPrintString($data)
    {
        if (is_string($data)) {
            return $this->generateMd5String($data);
        }

        return $this->generateMd5String($this->generateSerializedString($data));
    }
    /**
     * @param mixed $data
     *
     * @return string
     *
     * @Annotation\Generator("storageurl")
     *
     * @throws \Exception
     */
    public function generateStorageUrl($data)
    {
        $vars = [];

        if (!is_array($data)) {
            $data = [];
        }

        $requiredVars = $this->getStorageUrlPatternVars();

        foreach ($data as $k => $v) {
            $vars['{'.$k.'}'] = $v;
            $vars['{'.strtolower($k).'}'] = $v;
        }

        $missingVars = array_diff_key($requiredVars, $vars);

        if (0 < count($missingVars)) {
            throw $this->createRequiredException(
                "Missing variables to generate storage url: %s",
                join(', ', array_keys($missingVars))
            );
        }

        $pattern = $this->getStorageUrlPattern();

        if (isset($data['level'])) {
            switch ($data['level']) {
                case 1:
                    break;
                case 2:
                    $prefix  = isset($data['docToken']) ? '{docToken}x' : (isset($data['docId']) ? 'y{docId}x' : null);
                    $pattern = str_replace(['{token}', '{id}'], [$prefix.'{token}', $prefix.'{id}'], $pattern);
                    break;
                default:
                    // not yet supported
                    $pattern = null;
                    break;
            }
        }

        return str_replace(array_keys($vars), array_values($vars), $pattern);
    }
    /**
     * @param mixed $data
     *
     * @return string
     *
     * @Annotation\Generator("dynamicurl")
     *
     * @throws \Exception
     */
    public function generateDynamicUrl($data)
    {
        $vars = [];

        if (!is_array($data)) {
            $data = [];
        }

        if (!isset($data['dynamicPattern'])) {
            throw $this->createRequiredException('No dynamic pattern specified');
        }

        $dynamicPatternName = $data['dynamicPattern'];
        unset($data['dynamicPattern']);

        $requiredVars = $this->getDynamicUrlPatternVars($dynamicPatternName);

        foreach ($data as $k => $v) {
            if (is_object($v) || is_array($v)) {
                foreach ((array) $v as $kk => $vv) {
                    $vars['{'.$k.'.'.$kk.'}'] = $vv;
                    $vars['{'.strtolower($k).'.'.strtolower($kk).'}'] = $vv;
                }
            } else {
                $vars['{'.$k.'}'] = $v;
                $vars['{'.strtolower($k).'}'] = $v;
            }
        }

        $missingVars = array_diff_key($requiredVars, $vars);

        if (0 < count($missingVars)) {
            throw $this->createRequiredException(
                "Missing variables to generate dynamic url: %s",
                join(', ', array_keys($missingVars))
            );
        }

        return str_replace(
            array_keys($vars),
            array_values($vars),
            $this->getDynamicUrlPattern($dynamicPatternName)
        );
    }
    /**
     * @return string
     *
     * @Annotation\Generator("detect_platform")
     *
     * @throws \Exception
     */
    public function detectPlatform()
    {
        return $this->getPlatform();
    }
    /**
     * @Annotation\Generator("search_key")
     *
     * @param string $value
     *
     * @return string
     */
    public function generateSearchKey($value)
    {
        return $this->getStringService()->searchKey($value);
    }
    /**
     * @param string $pattern
     *
     * @return array
     */
    protected function parsePattern($pattern)
    {
        $matches      = null;
        $requiredVars = [];

        if (0 < preg_match_all('/(\{[^\}]+\})/', $pattern, $matches)) {
            foreach ($matches[1] as $i => $match) {
                $requiredVars[$match] = true;
            }
        }

        return $requiredVars;
    }
}
