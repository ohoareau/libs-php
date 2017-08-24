<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\Action\Base;

use Itq\Common\Bag;
use Itq\Common\Event;
use /** @noinspection PhpUnusedAliasInspection */ Itq\Common\Annotation;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractMailAction extends AbstractNotificationAction
{
    /**
     * @param string $type
     * @param Bag    $params
     * @param Bag    $context
     *
     * @throws \Exception
     */
    protected function sendMailByType($type, Bag $params, Bag $context)
    {
        if ($params->has('bulk') && true === $params->get('bulk')) {
            $this->sendBulkMailByType($type, $params, $context);
        } else {
            $this->sendSingleMailByType($type, $params, $context);
        }
    }
    /**
     * @param string $type
     * @param Bag    $params
     * @param Bag    $context
     *
     * @throws \Exception
     */
    protected function sendBulkMailByType($type, Bag $params, Bag $context)
    {
        $all        = ($params->all() + $context->all() + ['recipients' => []]);
        $recipients = $all['recipients'];

        if (!count($recipients)) {
            throw $this->createRequiredException('No recipients specified for bulk email');
        }

        foreach ($recipients as $recipientEmail => $recipientName) {
            if (is_numeric($recipientEmail)) {
                $recipientEmail = $recipientName;
                $recipientName  = $recipientEmail;
            }
            if (!is_string($recipientName)) {
                $recipientName = $recipientEmail;
            }
            $cleanedParams = $params->all();
            unset($cleanedParams['bulk'], $cleanedParams['recipients']);
            $cleanedParams['recipients'] = [$recipientEmail => $recipientName];
            $this->sendSingleMailByType($type, new Bag($cleanedParams), $context);
        }
    }
    /**
     * @param string $type
     * @param Bag    $params
     * @param Bag    $context
     */
    protected function sendSingleMailByType($type, Bag $params, Bag $context)
    {
        $vars     = $this->buildVariableBag($params, $context);
        $template = ($type ? ($type.'/') : '').$vars->get('template', 'unknown');

        $vars->setDefault('subjectDomain', ($vars->has('_tenant')?($vars->get('_tenant').'_'):'').($type ? ($type.'_') : '').'mail');

        $setting = $this->getCustomizerService()->customize('mail', $template, $vars);
        if (!$setting->has('content') && $setting->has('inline_template')) {
            $content = trim($this->renderInlineTemplate($setting->get('inline_template'), $setting));
            if ($this->isNonEmptyString($content)) {
                $setting->set('content', $content);
            }
        }

        $locale = $setting->get('_locale', $this->getDefaultLocale());

        $subject = $setting->has('custom_subject')
            ? $setting->get('custom_subject')
            : $this->getTranslator()->trans($setting->get('subject'), [], $setting->get('subjectDomain'), $locale);

        $options = $vars->has('options') ? $vars->get('options') : [];

        if (!is_array($options)) {
            $options = [];
        }

        if ($vars->has('consumer')) {
            $options['consumer'] = $vars->get('consumer');
        }

        $this->dispatch(
            'mail',
            new Event\MailEvent(
                $this->renderInlineTemplate('{% autoescape false %}'.$subject.'{% endautoescape %}', $vars),
                $setting->has('content') ? $this->renderInlineTemplate($setting->get('content'), $vars) : $this->renderTemplate('mail/'.$template.'.html.twig', $vars),
                $this->cleanRecipients($vars->get('recipients')),
                array_map(function ($attachment) use ($vars) {
                    return $this->getAttachmentService()->build($attachment, $vars->all());
                }, $params->get('attachments', [])),
                $vars->get('images', []),
                $setting->has('sender') ? $setting->get('sender') : $vars->get('sender', null),
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

        return $cleanedRecipients;
    }
}
