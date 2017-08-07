<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Traits;

use Itq\Common\PreprocessableClassFinder;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait PreprocessableClassFinderAwareTrait
{
    /**
     * @param string $key
     * @param mixed  $service
     *
     * @return $this
     */
    protected abstract function setService($key, $service);
    /**
     * @param string $key
     *
     * @return mixed
     */
    protected abstract function getService($key);
    /**
     * @param PreprocessableClassFinder $preprocessableClassFinder
     *
     * @return $this
     */
    public function setPreprocessableClassFinder(PreprocessableClassFinder $preprocessableClassFinder)
    {
        return $this->setService('preprocessableClassFinder', $preprocessableClassFinder);
    }
    /**
     * @return PreprocessableClassFinder
     */
    public function getPreprocessableClassFinder()
    {
        return $this->getService('preprocessableClassFinder');
    }
}
