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

use Closure;
use Exception;
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
        $this->sendByType(null, $params, $context);
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
        $this->sendByType('user', $params, $context);
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
        $options  = $vars->has('options') ? $vars->get('options') : [];

        $vars->setDefault('titleDomain', ($vars->has('_tenant') ? ($vars->get('_tenant').'_') : '').($type ? ($type.'_') : '').'pushnotif');

        $setting = $this->getCustomizerService()->customize('pushNotification', $template, $vars);

        $this->parseOptionalInlineTemplateSetting($setting);
        $this->ensureIsArray($options);

        $locale = $setting->get('_locale', $this->getDefaultLocale());
        $title  = $setting->has('custom_title')
            ? $setting->get('custom_title')
            : $this->getTranslator()->trans($setting->get('title'), [], $setting->get('titleDomain'), $locale);


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
     * @param string  $type
     * @param Bag     $params
     * @param Bag     $context
     * @param Closure $prepareDataCallback
     * @param array   $defaultData
     * @param bool    $silentIfNoRecipients
     *
     * @throws Exception
     */
    protected function sendBulkByType($type, Bag $params, Bag $context, Closure $prepareDataCallback = null, array $defaultData = [], $silentIfNoRecipients = false)
    {
        unset($prepareDataCallback, $silentIfNoRecipients);

        return parent::sendBulkByType(
            $type,
            $params,
            $context,
            function (Bag $cleanedParams, array &$all, $recipient) {
                $newParams = new Bag($cleanedParams);
                if (isset($all['recipientsData'][$recipient]) && is_array(isset($all['recipientsData'][$recipient]))) {
                    $newParams->set($all['recipientsData'][$recipient]);
                }

                return $newParams;
            },
            $defaultData + ['recipientsData' => []],
            true
        );
    }
}
