<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Adapter\Php;

use Itq\Common\Traits;

/**
 * Decorated Native Php Adapter.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class DecoratedNativePhpAdapter extends NativePhpAdapter
{
    use Traits\ServiceTrait;
    /**
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        foreach (['constants'] as $key) {
            if (!isset($data[$key]) || !is_array($data[$key])) {
                $data[$key] = [];
            }
            $this->setParameter($key, $data[$key]);
        }
    }
    /**
     * @param string $name
     *
     * @return mixed
     */
    public function getDefinedConstant($name)
    {
        if ($this->hasDecoratedConstant($name)) {
            return $this->getDecoratedConstant($name);
        }

        return parent::getDefinedConstant($name);
    }
    /**
     * @param string $name
     *
     * @return bool
     */
    public function isDefinedConstant($name)
    {
        if ($this->hasDecoratedConstant($name)) {
            return true;
        }

        return parent::isDefinedConstant($name);
    }
    /**
     * @param string $name
     *
     * @return bool
     */
    protected function hasDecoratedConstant($name)
    {
        return $this->hasArrayParameterKey('constants', $name);
    }
    /**
     * @param string $name
     *
     * @return mixed
     */
    protected function getDecoratedConstant($name)
    {
        return $this->getArrayParameterKey('constants', $name);
    }
}
