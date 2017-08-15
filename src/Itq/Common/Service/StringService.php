<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Service;

use Itq\Common\Traits;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class StringService
{
    use Traits\ServiceTrait;
    use Traits\Helper\String\StringTrait;
    use Traits\Helper\String\SlugifyTrait;
    use Traits\Helper\String\RemoveStressesTrait;
    use Traits\Helper\String\Camel2SnakeCaseTrait;
    use Traits\ServiceAware\CallableServiceAwareTrait;
    /**
     * @param CallableService $callableService
     */
    public function __construct(CallableService $callableService)
    {
        $this->setCallableService($callableService);
    }
    /**
     * Register a unique code generator algorithm for the algo (replace if exist).
     *
     * @param string   $algo
     * @param callable $callable
     * @param array    $options
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function registerUniqueCodeGeneratorAlgorithm($algo, $callable, array $options = [])
    {
        $this->getCallableService()->registerByType('uniqueCodeGeneratorAlgorithm', $algo, $callable, $options);

        if (isset($options['default']) && true === $options['default']) {
            $this->getCallableService()->registerByType('uniqueCodeGeneratorAlgorithm', 'default', $callable, $options);
        }

        return $this;
    }
    /**
     * @param string $algo
     * @param string $prefix
     * @param array  $options
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function generateCode($algo, $prefix = null, array $options = [])
    {
        if (null === $algo) {
            return $this->generateCode('default', $prefix, $options);
        }

        $result = $this->getCallableService()->executeByType(
            'uniqueCodeGeneratorAlgorithm',
            $algo,
            [['algo' => $algo, 'prefix' => $prefix] + $options]
        );

        if (null !== $prefix) {
            $result = $prefix.$result;
        }

        return $result;
    }
    /**
     * @param string $string
     *
     * @return string
     */
    public function removeStresses($string)
    {
        return $this->removeStringStresses($string);
    }
    /**
     * @param string $string
     *
     * @return string
     */
    public function slugify($string)
    {
        return $this->slugifyString($string);
    }
    /**
     * @param string $string
     *
     * @return string
     */
    public function camel2snake($string)
    {
        return $this->convertCamelCaseStringToSnakeCaseString($string);
    }
    /**
     * @param string $message
     *
     * @return string
     */
    public function normalizeKeyword($message)
    {
        return strtoupper($this->slugify($message));
    }
    /**
     * @param \Closure $tester
     * @param string   $algo
     * @param string   $prefix
     *
     * @return string
     *
     * @throws \Exception
     */
    public function generateUniqueCode(\Closure $tester, $algo, $prefix = null)
    {
        $i = 0;

        do {
            if (10 < $i) {
                throw $this->createDuplicatedException('Too much iteration (%d) for generating unique code', $i);
            }
            $value = $this->generateCode($algo, $prefix);
            $i++;
        } while ($tester($value));

        return $value;
    }
}
