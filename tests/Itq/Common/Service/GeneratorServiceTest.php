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

use Itq\Common\Service;
use Itq\Common\Tests\Service\Base\AbstractServiceTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group services
 * @group services/generator
 */
class GeneratorServiceTest extends AbstractServiceTestCase
{
    /**
     * @return Service\GeneratorService
     */
    public function s()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::s();
    }
    /**
     * @group unit
     */
    public function testRegister()
    {
        $callback = function () {
        };

        $this->s()->register('test', $callback);

        $this->assertEquals(['type' => 'callable', 'callable' => $callback, 'options' => []], $this->s()->get('test'));
    }
}
