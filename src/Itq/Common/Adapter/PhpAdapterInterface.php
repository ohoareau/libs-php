<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Adapter;

/**
 * Php Adapter Interface.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
interface PhpAdapterInterface
{
    /**
     * @param string $name
     *
     * @return mixed
     */
    public function getDefinedConstant($name);
    /**
     * @param string $name
     *
     * @return bool
     */
    public function isDefinedConstant($name);
    /**
     * @return string
     */
    public function getOs();
    /**
     * @return string
     */
    public function getVersion();
    /**
     * @return int
     */
    public function getVersionId();
}
