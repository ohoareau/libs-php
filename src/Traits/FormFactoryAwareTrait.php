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

use Symfony\Component\Form\FormFactoryInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait FormFactoryAwareTrait
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
     * @param FormFactoryInterface $formFactory
     *
     * @return $this
     */
    public function setFormFactory(FormFactoryInterface $formFactory)
    {
        return $this->setService('formFactory', $formFactory);
    }
    /**
     * @return FormFactoryInterface
     */
    public function getFormFactory()
    {
        return $this->getService('formFactory');
    }
}
