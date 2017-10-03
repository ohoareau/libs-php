<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common\Form\EventListener;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Itq\Common\Form\EventListener\AddIbanFieldSubscriber;
use Itq\Common\Tests\Form\EventListener\Base\AbstractEventListenerFormTestCase;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints\Iban;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group forms
 * @group forms/event-listeners
 * @group forms/event-listeners/add-iban-field
 */
class AddIbanFieldSubscriberTest extends AbstractEventListenerFormTestCase
{
    /**
     * @return AddIbanFieldSubscriber
     */
    public function l()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::l();
    }
    /**
     * @return array
     */
    public function constructor()
    {
        return [[]];
    }
    /**
     * @group unit
     */
    public function testGetSubscribedEvents()
    {
        $this->assertEquals([FormEvents::PRE_SUBMIT => 'preSubmit'], $this->l()->getSubscribedEvents());
    }
    /**
     * @param array $addWith
     * @param array $authorizedIbans
     *
     * @group unit
     *
     * @dataProvider getPreSubmitData
     */
    public function testPreSubmit(array $addWith, $authorizedIbans, $bankAccountIban)
    {
        /** @var AddIbanFieldSubscriber $l */
        $l         = $this->instantiate([$authorizedIbans]);
        $form      = $this->mocked('form', FormInterface::class);
        $formEvent = $this->mocked('formEvent', FormEvent::class);

        $formEvent->expects($this->once())->method('getForm')->willReturn($form);
        $formEvent->expects($this->once())->method('getData')->willReturn(['iban' => $bankAccountIban]);

        call_user_func_array([$form->expects($this->once())->method('add'), 'with'], $addWith);

        $l->preSubmit($this->mocked('formEvent'));
    }
    /**
     * @return array
     */
    public function getPreSubmitData()
    {
        return [
            '0 - authorized ibans' => [
                ['iban'],
                'FR89370400440532013000',
                'FR89370400440532013000',
            ],
            '1 - not authorized ibans' => [
                ['iban', 'text', ['constraints' => [new Iban(['groups'  => ['create', 'update']])]]],
                'FR89370400440532013000',
                'FR89370400440532013001',
            ],
            '2 - none' => [
                ['iban', 'text', ['constraints' => [new Iban(['groups'  => ['create', 'update']])]]],
                null,
                'FR89370400440532013001',
            ]
        ];
    }
}
