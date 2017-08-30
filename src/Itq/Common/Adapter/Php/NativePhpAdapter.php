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

/**
 * Native Php Adapter.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class NativePhpAdapter extends Base\AbstractPhpAdapter
{
    /**
     * @param string $name
     *
     * @return mixed
     */
    public function getDefinedConstant($name)
    {
        return constant($name);
    }
    /**
     * @param string $name
     *
     * @return bool
     */
    public function isDefinedConstant($name)
    {
        return true === defined($name);
    }
    /**
     * @return string
     */
    public function getOs()
    {
        return PHP_OS;
    }
    /**
     * @return string
     */
    public function getVersion()
    {
        return PHP_VERSION;
    }
    /**
     * @return int
     */
    public function getVersionId()
    {
        return PHP_VERSION_ID;
    }
}
