<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Traits\ServiceAware;

use Itq\Common\Service\CriteriumService;

/**
 * CriteriumServiceAware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait CriteriumServiceAwareTrait
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
     * @return CriteriumService
     */
    public function getCriteriumService()
    {
        return $this->getService('criterium');
    }
    /**
     * @param CriteriumService $service
     *
     * @return $this
     */
    public function setCriteriumService(CriteriumService $service)
    {
        return $this->setService('criterium', $service);
    }
}
