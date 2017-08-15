<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Form\EventListener;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\Iban;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class AddIbanFieldSubscriber extends Base\AbstractEventSubscriber
{
    /**
     * @var string
     */
    protected $authorizedIbans;
    /**
     * @param string $authorizedIbans
     */
    public function __construct($authorizedIbans)
    {
        $this->authorizedIbans = $authorizedIbans;
    }
    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [FormEvents::PRE_SUBMIT => 'preSubmit'];
    }
    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $bankAccount            = $event->getData();
        $form                   = $event->getForm();
        $authorizedIbansPattern = sprintf('/^%s$/', str_replace([',', '\*'], ['$|', '[a-z0-9]*'], preg_quote($this->authorizedIbans)));

        if (empty($this->authorizedIbans) || 1 !== preg_match($authorizedIbansPattern, $bankAccount['iban'])) {
            $form->add('iban', 'text', ['constraints' => [new Iban(['groups'  => ['create', 'update']])]]);
        } else {
            $form->add('iban');
        }
    }
}
