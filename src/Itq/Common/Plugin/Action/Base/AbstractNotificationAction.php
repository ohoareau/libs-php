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
    public function getEnvironment()
    {
        return $this->getParameter('environment');
    }
    /**
     * @param string $environment
     *
     * @return $this
     */
    public function setEnvironment($environment)
    {
        return $this->setParameter('environment', $environment);
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
     * @param array $defaultSenders
     *
     * @return $this
     */
    public function setDefaultSenders(array $defaultSenders)
    {
        return $this->setParameter('defaultSenders', $defaultSenders);
    }
    /**
     * @return array
     *
     * @throws \Exception
     */
    public function getDefaultSenders()
    {
        return $this->getParameter('defaultSenders');
    }
    /**
     * @param array $defaultRecipients
     *
     * @return $this
     */
    public function setDefaultRecipients(array $defaultRecipients)
    {
        return $this->setParameter('defaultRecipients', $defaultRecipients);
    }
    /**
     * @return array
     *
     * @throws \Exception
     */
    public function getDefaultRecipients()
    {
        return $this->getParameter('defaultRecipients');
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
            ($vars->has('_tenant') ? ('tenants/'.$vars->get('_tenant').'/') : '').($vars->has('_locale') ? ('locales/'.$vars->get('_locale').'/') : '').$name,
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
            'AppBundle::expression.txt.twig',
            ['_expression' => $expression] + $vars->all()
        );
    }
    /**
     * @param string $locale
     *
     * @return $this
     */
    protected function setDefaultLocale($locale)
    {
        return $this->setParameter('defaultLocale', $locale);
    }
    /**
     * @return string
     */
    protected function getDefaultLocale()
    {
        return $this->getParameter('defaultLocale');
    }
}
