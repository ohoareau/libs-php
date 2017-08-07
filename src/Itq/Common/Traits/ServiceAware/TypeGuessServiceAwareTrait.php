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

use Itq\Common\Service\TypeGuessService;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait TypeGuessServiceAwareTrait
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
     * @return TypeGuessService
     */
    public function getTypeGuessService()
    {
        return $this->getService('typeGuess');
    }
    /**
     * @param TypeGuessService $service
     *
     * @return $this
     */
    public function setTypeGuessService(TypeGuessService $service)
    {
        return $this->setService('typeGuess', $service);
    }
}
