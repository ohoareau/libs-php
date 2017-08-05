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

use Itq\Common\Service\GeneratorService;

/**
 * GeneratorServiceAware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait GeneratorServiceAwareTrait
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
     * @return GeneratorService
     */
    public function getGeneratorService()
    {
        return $this->getService('generator');
    }
    /**
     * @param GeneratorService $service
     *
     * @return $this
     */
    public function setGeneratorService(GeneratorService $service)
    {
        return $this->setService('generator', $service);
    }
}
