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
class PushNotificationAction extends Base\AbstractNotificationAction
{
    /**
     * @param Bag $params
     * @param Bag $context
     *
     * @Annotation\Action("push_notif", description="send a push notification")
     */
    public function sendPushNotification(Bag $params, Bag $context)
    {
        $this->sendPushNotificationByType(null, $params, $context);
    }
    /**
     * @param Bag $params
     * @param Bag $context
     *
     * @Annotation\Action("push_notif_user", description="send a user push notification")
     */
    public function sendUserPushNotification(Bag $params, Bag $context)
    {
        $params->setDefault('sender', $this->getDefaultSenderByTypeAndNature('push_notif_user', $params->get('template')));
        $params->setDefault('_locale', $this->getCurrentLocale());
        $params->setDefault('_tenant', $this->getTenant());
        $this->sendPushNotificationByType('user', $params, $context);
    }
    /**
     * @param Bag $params
     * @param Bag $context
     *
     * @Annotation\Action("push_notif_admin", description="send an admin push notification")
     */
    public function sendAdminPushNotification(Bag $params, Bag $context)
    {
        $params->setDefault('recipients', $this->getDefaultRecipientsByTypeAndNature('push_notif_admins', $params->get('template')));
        $params->setDefault('sender', $this->getDefaultSenderByTypeAndNature('push_notif_admin', $params->get('template')));
        $params->setDefault('_tenant', $this->getTenant());
        $this->sendPushNotificationByType('admin', $params, $context);
    }
    /**
     * @param string $type
     * @param Bag    $params
     * @param Bag    $context
     *
     * @throws \Exception
     */
    protected function sendPushNotificationByType($type, Bag $params, Bag $context)
    {
        if ($params->has('bulk') && true === $params->get('bulk')) {
            $this->sendBulkPushNotificationByType($type, $params, $context);
        } else {
            $this->sendSinglePushNotificationByType($type, $params, $context);
        }
    }
    /**
     * @param string $type
     * @param Bag    $params
     * @param Bag    $context
     *
     * @throws \Exception
     */
    protected function sendBulkPushNotificationByType($type, Bag $params, Bag $context)
    {
        $all = ($params->all() + $context->all() + ['recipients' => [], 'recipientsData' => []]);
        $recipients = $all['recipients'];
        if (!count($recipients)) {
            // nothing to do, be silent to avoid complexity in the caller (if:...)

            return;
        }

        foreach ($recipients as $recipientNotif => $recipientName) {
            if (is_numeric($recipientNotif)) {
                $recipientNotif = $recipientName;
                $recipientName = $recipientNotif;
            }
            if (!is_string($recipientName)) {
                $recipientName = $recipientNotif;
            }
            $cleanedParams = $params->all();
            unset($cleanedParams['bulk'], $cleanedParams['recipients']);
            $cleanedParams['recipients'] = [$recipientNotif => $recipientName];
            $newParams = new Bag($cleanedParams);
            if (isset($all['recipientsData'][$recipientNotif]) && is_array(isset($all['recipientsData'][$recipientNotif]))) {
                $newParams->set($all['recipientsData'][$recipientNotif]);
            }
            $this->sendSinglePushNotificationByType($type, $newParams, $context);
        }
    }
    /**
     * @param string $type
     * @param Bag    $params
     * @param Bag    $context
     */
    protected function sendSinglePushNotificationByType($type, Bag $params, Bag $context)
    {
        $vars     = $this->buildVariableBag($params, $context);
        $template = ($type ? ($type.'/') : '').$vars->get('template', 'unknown');

        $vars->setDefault('titleDomain', ($vars->has('_tenant')?($vars->get('_tenant').'_'):'').($type ? ($type.'_') : '').'pushnotif');

        $setting = $this->getCustomizerService()->customize('pushNotification', $template, $vars);

        if (!$setting->has('content') && $setting->has('inline_template')) {
            $content = trim($this->renderInlineTemplate($setting->get('inline_template'), $setting));
            if (strlen($content) > 0) {
                $setting->set('content', $content);
            }
        }

        $locale = $setting->get('_locale', $this->getDefaultLocale());

        $title = $setting->has('custom_title')
            ? $setting->get('custom_title')
            : $this->getTranslator()->trans($setting->get('title'), [], $setting->get('titleDomain'), $locale);

        $options = $vars->has('options') ? $vars->get('options') : [];

        if (!is_array($options)) {
            $options = [];
        }

        if ($vars->has('consumer')) {
            $options['consumer'] = $vars->get('consumer');
        }

        $this->dispatch(
            'pushNotification',
            new Event\PushNotificationEvent(
                $this->renderInlineTemplate('{% autoescape false %}'.$title.'{% endautoescape %}', $vars),
                $setting->has('content') ? $setting->get('content') : $this->renderTemplate('push-notif/'.$template.'.txt.twig', $vars),
                $this->cleanRecipients($params->get('recipients')),
                [
                    'application' => $params->has('application') ? $params->get('application') : null,
                    'provider'    => $params->has('provider') ? $params->get('provider') : null,
                    'id'     => $params->has('providerId') ? $params->get('providerId') : null,
                    'token'  => $params->has('providerToken') ? $params->get('providerToken') : null,
                    'secret' => $params->has('providerSecret') ? $params->get('providerSecret') : null,
                    'data'   => $params->has('providerData') ? $params->get('providerData') : null,
                    'config' => $params->has('config') ? $params->get('config') : null,
                ],
                $options
            )
        );
    }
    /**
     * @param array|mixed $recipients
     *
     * @return array
     *
     * @throws \Exception
     */
    protected function cleanRecipients($recipients)
    {
        if (!is_array($recipients)) {
            if (!is_string($recipients)) {
                throw $this->createMalformedException('Recipients must be a list or a string');
            }
            $recipients = [$recipients => $recipients];
        }

        $cleanedRecipients = [];

        foreach ($recipients as $k => $v) {
            unset($recipients[$k]);
            if (is_numeric($k)) {
                if (!is_string($v)) {
                    continue;
                }
                $cleanedRecipients[$v] = $v;
            } else {
                $cleanedRecipients[$k] = $v;
            }
        }

        if (!count($cleanedRecipients)) {
            throw $this->createRequiredException('No recipients specified');
        }

        return array_keys($cleanedRecipients);
    }
}
