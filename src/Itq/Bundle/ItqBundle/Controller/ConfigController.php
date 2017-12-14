<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Bundle\ItqBundle\Controller;

use Itq\Common\Traits;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Itq\Common\Controller\Base\AbstractCrudController;

/**
 * Config controller.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class ConfigController extends AbstractCrudController
{
    use Traits\Controller\ServiceAware\ConfigServiceAwareControllerTrait;
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function errorsAction(Request $request)
    {
        return $this->returnJsonGetResponse($request, $this->getConfigService()->getErrors());
    }
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function eventsAction(Request $request)
    {
        return $this->returnJsonGetResponse($request, $this->getConfigService()->getEvents());
    }
}
