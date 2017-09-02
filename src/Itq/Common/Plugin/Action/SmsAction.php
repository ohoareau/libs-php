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
use Itq\Common\Event;
use /** @noinspection PhpUnusedAliasInspection */ Itq\Common\Annotation;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class SmsAction extends Base\AbstractNotificationAction
{
    /**
     * @param Bag $params
     * @param Bag $context
     *
     * @Annotation\Action("sms", description="send a sms")
     */
    public function sendSms(Bag $params, Bag $context)
    {
        $this->sendByType(null, $params, $context);
    }
    /**
     * @param Bag $params
     * @param Bag $context
     *
     * @Annotation\Action("sms_user", description="send a user sms")
     */
    public function sendUserSms(Bag $params, Bag $context)
    {
        $params->setDefault('sender', $this->getDefaultSenderByTypeAndNature('sms_user', $params->get('template')));
        $params->setDefault('_locale', $this->getCurrentLocale());
        $params->setDefault('_tenant', $this->getTenant());
        $this->sendByType('user', $params, $context);
    }
    /**
     * @param Bag $params
     * @param Bag $context
     *
     * @Annotation\Action("sms_admin", description="send an admin sms")
     */
    public function sendAdminSms(Bag $params, Bag $context)
    {
        $params->setDefault('recipients', $this->getDefaultRecipientsByTypeAndNature('sms_admins', $params->get('template')));
        $params->setDefault('sender', $this->getDefaultSenderByTypeAndNature('sms_admin', $params->get('template')));
        $params->setDefault('_tenant', $this->getTenant());
        $this->sendByType('admin', $params, $context);
    }
    /**
     * @param string $type
     * @param Bag    $params
     * @param Bag    $context
     */
    protected function sendSingleByType($type, Bag $params, Bag $context)
    {
        $vars     = $this->buildVariableBag($params, $context);
        $template = ($type ? ($type.'/') : '').$vars->get('template', 'unknown');
        $setting  = $this->getCustomizerService()->customize('sms', $template, $vars);
        $options  = $vars->has('options') ? $vars->get('options') : [];
        $this->parseOptionalInlineTemplateSetting($setting);
        $this->ensureIsArray($options);

        if ($vars->has('consumer')) {
            $options['consumer'] = $vars->get('consumer');
        }

        $this->dispatch(
            'sms',
            new Event\SmsEvent(
                $setting->has('content') ? $setting->get('content') : $this->renderTemplate('sms/'.$template.'.txt.twig', $vars),
                $this->cleanRecipients($params->get('recipients')),
                array_map(function ($attachment) use ($vars) {
                    return $this->getAttachmentService()->build($attachment, $vars->all());
                }, $params->get('attachments', [])),
                $params->get('images', []),
                $params->get('sender', null),
                $options
            )
        );
    }
}
