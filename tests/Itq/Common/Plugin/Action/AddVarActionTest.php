<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common\Plugin\Action;

use Itq\Common\Bag;
use Itq\Common\Plugin\Action\AddVarAction;
use Symfony\Component\EventDispatcher\GenericEvent;
use Itq\Common\Tests\Plugin\Action\Base\AbstractActionTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group plugins
 * @group plugins/actions
 * @group plugins/actions/addvar
 */
class AddVarActionTest extends AbstractActionTestCase
{
    /**
     * @return AddVarAction
     */
    public function a()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::p();
    }
    /**
     * @return array
     */
    public function constructor()
    {
        return [
            $this->mockedContainer(),
        ];
    }
    /**
     * @group unit
     * @group attachment
     */
    public function testExecute()
    {
        $c = new AddVarActionTestExecuteTestClass();

        $this->mockedContainer()->expects($this->once())->method('get')->with('s1')->will($this->returnValue($c));
        $this->a()->execute(
            new Bag(['method' => 'm1', 'service' => 's1', 'name' => 'v1']),
            new Bag(['event' => new GenericEvent(), 'eventName' => 'e1'])
        );

        $this->assertTrue($c->called);
    }
}
