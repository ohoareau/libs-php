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

use Exception;
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
     */
    protected function sendSingleByType($type, Bag $params, Bag $context)
    {
        $vars     = $this->buildVariableBag($params, $context);
        $template = ($type ? ($type.'/') : '').$vars->get('template', 'unknown');
        $options  = $vars->has('options') ? $vars->get('options') : [];

        $vars->setDefault('subjectDomain', ($vars->has('_tenant')?($vars->get('_tenant').'_'):'').($type ? ($type.'_') : '').'mail');

        $setting = $this->getCustomizerService()->customize('mail', $template, $vars);

        $this->parseOptionalInlineTemplateSetting($setting);
        $this->ensureIsArray($options);

        $locale  = $setting->get('_locale', $this->getDefaultLocale());
        $subject = $setting->has('custom_subject')
            ? $setting->get('custom_subject')
            : $this->getTranslator()->trans($setting->get('subject'), [], $setting->get('subjectDomain'), $locale);

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
}
