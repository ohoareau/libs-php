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

use Itq\Common\Service\CallableService;
use Itq\Common\Tests\Service\Base\AbstractServiceTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group services
 * @group services/callable
 */
class CallableServiceTest extends AbstractServiceTestCase
{
    /**
     * @return CallableService
     */
    public function s()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::s();
    }
    /**
     * @group unit
     *
     * @return void
     */
    public function testRegisterByType()
    {
        $callable1 = function () {
            return 1;
        };
        $callable2 = function () {
            return 2;
        };

        $this->s()->registerByType('type1', 'callable1', $callable1);
        $this->s()->registerByType('type1', 'callable2', $callable2);

        $this->assertEquals(
            ['type' => 'callable', 'callable' => $callable1, 'options' => []],
            $this->s()->getByType('type1', 'callable1')
        );

        $this->assertEquals(
            ['type' => 'callable', 'callable' => $callable2, 'options' => []],
            $this->s()->getByType('type1', 'callable2')
        );
    }
}
