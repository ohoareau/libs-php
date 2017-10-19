<?php

/*
 * This file is part of the tests-ws package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Itq\Common\Tests\Traits\Base;

use Itq\Common\Tests\Base\AbstractTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractTraitTestCase extends AbstractTestCase
{
    /**
     * @return object|\PHPUnit_Framework_MockObject_MockObject
     */
    public function t()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return $this->o();
    }
    /**
     *
     */
    public function setUp()
    {
        $this->setObject(
            $this->getMockForTrait($this->getObjectClass())
        );
    }
}
