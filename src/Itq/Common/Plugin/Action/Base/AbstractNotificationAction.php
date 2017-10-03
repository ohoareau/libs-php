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

use Closure;
use Exception;
use Itq\Common\Bag;
use Itq\Common\Traits;
use Itq\Common\Service;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractNotificationAction extends AbstractAction
{
    use Traits\TranslatorAwareTrait;
    use Traits\RequestStackAwareTrait;
    use Traits\ServiceAware\TenantServiceAwareTrait;
    use Traits\ServiceAware\TemplateServiceAwareTrait;
    use Traits\ServiceAware\AttachmentServiceAwareTrait;
    use Traits\ServiceAware\CustomizerServiceAwareTrait;
    use Traits\ParameterAware\EnvironmentParameterAwareTrait;
    use Traits\ParameterAware\DefaultLocaleParameterAwareTrait;
    use Traits\ParameterAware\DefaultSendersParameterAwareTrait;
    use Traits\ParameterAware\DefaultRecipientsParameterAwareTrait;
    /**
     * @param Service\TemplateService   $templateService
     * @param TranslatorInterface       $translator
     * @param Service\AttachmentService $attachmentService
     * @param Service\CustomizerService $customizerService
     * @param EventDispatcherInterface  $eventDispatcher
     * @param array                     $defaultSenders
     * @param array                     $defaultRecipients
     * @param string                    $env
     * @param RequestStack              $requestStack
     * @param Service\TenantService     $tenantService
     * @param string                    $locale
     */
    public function __construct(
        Service\TemplateService $templateService,
        TranslatorInterface $translator,
        Service\AttachmentService $attachmentService,
        Service\CustomizerService $customizerService,
        EventDispatcherInterface $eventDispatcher,
        array $defaultSenders,
        array $defaultRecipients,
        $env,
        RequestStack $requestStack,
        Service\TenantService $tenantService,
        $locale
    ) {
        $this->setTemplateService($templateService);
        $this->setTranslator($translator);
        $this->setAttachmentService($attachmentService);
        $this->setCustomizerService($customizerService);
        $this->setEventDispatcher($eventDispatcher);
        $this->setDefaultSenders($defaultSenders);
        $this->setDefaultRecipients($defaultRecipients);
        $this->setEnvironment($env);
        $this->setRequestStack($requestStack);
        $this->setTenantService($tenantService);
        $this->setDefaultLocale($locale);
    }
    /**
     * @return string
     */
    public function getCurrentLocale()
    {
        return (null === $this->getRequestStack() || null === $this->getRequestStack()->getMasterRequest()) ? $this->getDefaultLocale() : $this->getRequestStack()->getMasterRequest()->getLocale();
    }
    /**
     * @return string
     */
    public function getTenant()
    {
        return $this->getTenantService()->getCurrent();
    }
    /**
     * @param string $type
     * @param string $nature
     * @param string $tenant
     *
     * @return array
     */
    public function getDefaultSenderByTypeAndNature($type, $nature, $tenant = null)
    {
        $tenant = null === $tenant ? $this->getTenant() : $tenant;

        $senders = $this->getArrayParameterListKey('defaultSenders', $type);

        if (isset($senders[$tenant])) {
            $senders = [$tenant => $senders[$tenant]];
        }

        $senders = $this->filterByEnvAndType(
            $senders,
            $this->getEnvironment(),
            $nature
        );

        if (!count($senders)) {
            return null;
        }

        $sender = array_shift($senders);

        return [$sender['sender'] => $sender['name']];
    }
    /**
     * @param string $type
     * @param string $nature
     *
     * @return array
     */
    public function getDefaultRecipientsByTypeAndNature($type, $nature)
    {
        $recipients = $this->filterByEnvAndType(
            $this->getArrayParameterListKey('defaultRecipients', $type),
            $this->getEnvironment(),
            $nature
        );

        if (!count($recipients)) {
            return [];
        }

        $cleanedRecipients = [];

        foreach ($recipients as $email => $recipient) {
            $recipient += ['email' => $email, 'name' => $email];
            $cleanedRecipients[$recipient['email']] = $recipient['name'];
        }

        return $cleanedRecipients;
    }
    /**
     * @param array|mixed $recipients
     *
     * @return array
     *
     * @throws Exception
     */
    public function cleanRecipients($recipients)
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
    /**
     * @param array  $items
     * @param string $env
     * @param string $type
     *
     * @return array
     */
    protected function filterByEnvAndType(array $items, $env, $type)
    {
        foreach ($items as $k => $item) {
            if (isset($item['envs']) && is_array($item['envs']) && count($item['envs'])) {
                if (!in_array('*', $item['envs']) && !in_array($env, $item['envs'])) {
                    unset($items[$k]);
                    continue;
                }
            }
            if (isset($item['types']) && is_array($item['types']) && count($item['types'])) {
                if (!in_array('*', $item['types']) && !in_array($type, $item['types'])) {
                    unset($items[$k]);
                    continue;
                }
            }
        }

        return $items;
    }
    /**
     * @param Bag $params
     * @param Bag $context
     *
     * @return Bag
     */
    protected function buildVariableBag(Bag $params, Bag $context)
    {
        return new Bag(['env' => $this->getEnvironment()] + $params->all() + $context->all());
    }
    /**
     * @param string $name
     * @param Bag    $vars
     *
     * @return string
     */
    protected function renderTemplate($name, Bag $vars)
    {
        return $this->getTemplateService()->render(
            ($vars->has('_tenant') ? ('@tenants/'.$vars->get('_tenant').'/') : '').($vars->has('_locale') ? ('locales/'.$vars->get('_locale').'/') : '').$name,
            $vars->all()
        );
    }
    /**
     * @param string $expression
     * @param Bag    $vars
     *
     * @return string
     */
    protected function renderInlineTemplate($expression, Bag $vars)
    {
        return $this->getTemplateService()->render(
            'ItqBundle::expression.txt.twig',
            ['_expression' => $expression] + $vars->all()
        );
    }
    /**
     * @param string $type
     * @param Bag    $params
     * @param Bag    $context
     *
     * @return $this
     *
     * @throws Exception
     */
    protected function sendByType($type, Bag $params, Bag $context)
    {
        if ($params->has('bulk') && true === $params->get('bulk')) {
            $this->sendBulkByType($type, $params, $context);
        } else {
            $this->sendSingleByType($type, $params, $context);
        }

        return $this;
    }
    /**
     * @param Bag $setting
     *
     * @return $this
     */
    protected function parseOptionalInlineTemplateSetting(Bag $setting)
    {
        if (!$setting->has('content') && $setting->has('inline_template')) {
            $content = trim($this->renderInlineTemplate($setting->get('inline_template'), $setting));
            if ($this->isNonEmptyString($content)) {
                $setting->set('content', $content);
            }
        }

        return $this;
    }
    /**
     * @param string       $type
     * @param Bag          $params
     * @param Bag          $context
     * @param Closure|null $prepareDataCallback
     * @param array        $defaultData
     * @param bool         $silentIfNoRecipients
     *
     * @throws Exception
     */
    protected function sendBulkByType($type, Bag $params, Bag $context, Closure $prepareDataCallback = null, array $defaultData = [], $silentIfNoRecipients = false)
    {
        $all        = ($params->all() + $context->all() + ['recipients' => []] + $defaultData);
        $recipients = $all['recipients'];
        if (0 >= count($recipients)) {
            if (true === $silentIfNoRecipients) {
                return;
            }

            throw $this->createRequiredException('No recipients specified for bulk notification');
        }

        foreach ($recipients as $recipient => $name) {
            if (is_numeric($recipient)) {
                $recipient = $name;
                $name      = $recipient;
            }
            if (!is_string($name)) {
                $name = $recipient;
            }
            $cleanedParams = $params->all();
            unset($cleanedParams['bulk'], $cleanedParams['recipients']);
            $cleanedParams['recipients'] = [$recipient => $name];
            if (null !== $prepareDataCallback) {
                $newParams = $prepareDataCallback($cleanedParams, $all, $recipient);
            } else {
                $newParams = new Bag($cleanedParams);
            }
            $this->sendSingleByType($type, $newParams, $context);
        }
    }
    /**
     * @param string $type
     * @param Bag    $params
     * @param Bag    $context
     *
     * @throws Exception
     */
    abstract protected function sendSingleByType($type, Bag $params, Bag $context);
}
