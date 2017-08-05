<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <cto@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Traits\ServiceAware;

use Itq\Common\Service\CustomizerService;

/**
 * CustomizerServiceAware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait CustomizerServiceAwareTrait
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
     * @param CustomizerService $service
     *
     * @return $this
     */
    public function setCustomizerService(CustomizerService $service)
    {
        return $this->setService('customizer', $service);
    }
    /**
     * @return CustomizerService
     */
    public function getCustomizerService()
    {
        return $this->getService('customizer');
    }
}
