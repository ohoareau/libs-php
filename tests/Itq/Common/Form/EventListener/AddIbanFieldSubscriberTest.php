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

use Symfony\Component\Form\FormEvents;
use Itq\Common\Form\EventListener\AddIbanFieldSubscriber;
use Itq\Common\Tests\Form\EventListener\Base\AbstractEventListenerFormTestCase;

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
}
