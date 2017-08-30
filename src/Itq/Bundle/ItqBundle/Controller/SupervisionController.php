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
 * Supervision controller.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class SupervisionController extends AbstractCrudController
{
    use Traits\Controller\ServiceAware\SupervisionServiceAwareControllerTrait;
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function pingAction(Request $request)
    {
        return $this->returnJsonGetResponse($request, $this->getSupervisionService()->supervise());
    }
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function aliveAction(Request $request)
    {
        return $this->returnTextGetResponse($request, 'OK');
    }
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function whoamiAction(Request $request)
    {
        return $this->returnJsonGetResponse($request, $this->getSupervisionService()->identify());
    }
}
