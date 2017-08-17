<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common\Service;

use Itq\Common\Service\ActionService;
use Itq\Common\Tests\Service\Base\AbstractServiceTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group services
 * @group services/action
 */
class ActionServiceTest extends AbstractServiceTestCase
{
    /**
     * @return ActionService
     */
    public function s()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::s();
    }
    /**
     * @return array
     */
    public function constructor()
    {
        return [$this->mockedCallableService(), $this->mockedExpressionService()];
    }
    /**
     * @group unit
     */
    public function testRegister()
    {
        $callback = function () {
        };

        $this->mockedCallableService()
            ->expects($this->once())
            ->method('registerByType')
            ->will($this->returnValue($this->mockedCallableService()))
            ->with('action', 'test', $callback)
        ;

        $this->s()->register('test', $callback);

        $this->mockedCallableService()
            ->expects($this->once())
            ->method('getByType')
            ->will($this->returnValue(['type' => 'callable', 'callable' => $callback, 'options' => []]))
            ->with('action', 'test')
        ;

        $this->assertEquals(['type' => 'callable', 'callable' => $callback, 'options' => []], $this->s()->get('test'));
    }
}
