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

use Itq\Common\Service\CodeGeneratorService;

/**
 * CodeGeneratorServiceAware trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait CodeGeneratorServiceAwareTrait
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
     * @return CodeGeneratorService
     */
    public function getCodeGeneratorService()
    {
        return $this->getService('codeGeneratorService');
    }
    /**
     * @param CodeGeneratorService $service
     *
     * @return $this
     */
    public function setCodeGeneratorService(CodeGeneratorService $service)
    {
        return $this->setService('codeGeneratorService', $service);
    }
}
