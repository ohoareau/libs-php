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

use Itq\Common\Controller\Base\AbstractController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Security controller.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class SecurityController extends AbstractController
{
    /**
     * @return Response
     */
    public function loginAction()
    {
        $authenticationUtils = $this->get('security.authentication_utils');
        $error               = $authenticationUtils->getLastAuthenticationError();
        $lastUsername        = $authenticationUtils->getLastUsername();

        return $this->render(
            'security/login.html.twig',
            [
                'last_username' => $lastUsername,
                'error'         => $error,
            ]
        );
    }
    /**
     * @return Response
     */
    public function resetPasswordAction()
    {
        return $this->render('security/reset-password.html.twig');
    }
    /**
     *
     */
    public function loginCheckAction()
    {
    }
    /**
     *
     */
    public function logoutAction()
    {
    }
}
