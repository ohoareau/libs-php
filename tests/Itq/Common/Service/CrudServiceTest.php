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

use stdClass;
use Itq\Common\Service\CrudService;
use PHPUnit_Framework_MockObject_MockObject;
use Itq\Common\Tests\Service\Base\AbstractServiceTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group services
 * @group services/crud
 */
class CrudServiceTest extends AbstractServiceTestCase
{
    /**
     * @return CrudService|PHPUnit_Framework_MockObject_MockObject
     */
    public function s()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return parent::s();
    }
    /**
     * @group integ
     */
    public function testAdd()
    {
        $expected = new stdClass;

        $this->assertSame($this->s(), $this->s()->add('tutu', $expected ));
    }
    /**
     * @group unit
     */
    public function testGet()
    {
        $expected = new stdClass;
        $this->s()->add('toto', $expected );
        $this->assertSame($expected, $this->s()->get( 'toto' ));
    }
    /**
     * @group unit
     */
    public function testGetAll()
    {
        $expected = new stdClass;
        $this->s()->add('toto', $expected );
        $this->assertArrayHasKey('toto', $this->s()->getAll());
    }
    /**
     * @group unit
     */
    public function testHas()
    {
        $expected = new stdClass;
        $this->s()->add('toto', $expected );
        $this->assertSame(true, $this->s()->has('toto'));
    }
}
