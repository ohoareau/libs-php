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

use Itq\Common\PreprocessorContext;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait PreprocessorContextAwareTrait
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
     * @param PreprocessorContext $preprocessorContext
     *
     * @return $this
     */
    public function setPreprocessorContext(PreprocessorContext $preprocessorContext)
    {
        return $this->setService('preprocessorContext', $preprocessorContext);
    }
    /**
     * @return PreprocessorContext
     */
    public function getPreprocessorContext()
    {
        return $this->getService('preprocessorContext');
    }
}
