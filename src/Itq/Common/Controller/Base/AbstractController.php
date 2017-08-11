<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Controller\Base;

use Itq\Common\Traits;
use Itq\Common\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractController implements ContainerAwareInterface
{
    use Traits\SymfonyControllerTrait;
    /**
     * Gets a container service by its id.
     *
     * @param string $id The service id
     *
     * @return object The service
     */
    abstract public function get($id);
    /**
     * @return Service\ExceptionService
     */
    protected function getExceptionService()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return $this->get('app.exception');
    }
    /**
     * @return Service\RequestService
     */
    protected function getRequestService()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return $this->get('app.request');
    }
    /**
     * @return Service\ResponseService
     */
    protected function getResponseService()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return $this->get('app.response');
    }
    /**
     * @return Service\DataFilterService
     */
    protected function getDataFilterService()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return $this->get('app.datafilter');
    }
    /**
     * @return RequestStack
     */
    protected function getRequestStack()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return $this->get('request_stack');
    }
}
