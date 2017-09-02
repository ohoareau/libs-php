<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\Action;

use Itq\Common\Bag;
use /** @noinspection PhpUnusedAliasInspection */ Itq\Common\Annotation;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class MailAction extends Base\AbstractMailAction
{
    /**
     * @param Bag $params
     * @param Bag $context
     *
     * @Annotation\Action("mail", description="send a mail")
     */
    public function sendMail(Bag $params, Bag $context)
    {
        $this->sendByType(null, $params, $context);
    }
    /**
     * @param Bag $params
     * @param Bag $context
     *
     * @Annotation\Action("mail_user", description="send a user mail")
     */
    public function sendUserMail(Bag $params, Bag $context)
    {
        $tenant = $params->has('tenant') ? $params->get('tenant') : $this->getTenant();

        $params->setDefault('sender', $this->getDefaultSenderByTypeAndNature('mail_user', $params->get('template'), $tenant));
        $params->setDefault('_locale', $this->getCurrentLocale());
        $params->setDefault('_tenant', $tenant);
        $this->sendByType('user', $params, $context);
    }
    /**
     * @param Bag $params
     * @param Bag $context
     *
     * @Annotation\Action("mail_admin", description="send an admin mail")
     */
    public function sendAdminMail(Bag $params, Bag $context)
    {
        $params->setDefault('recipients', $this->getDefaultRecipientsByTypeAndNature('mail_admins', $params->get('template')));
        $params->setDefault('sender', $this->getDefaultSenderByTypeAndNature('mail_admin', $params->get('template')));
        $params->setDefault('_locale', 'en');
        $params->setDefault('_tenant', $this->getTenant());
        $this->sendByType('admin', $params, $context);
    }
}
