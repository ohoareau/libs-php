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

use Itq\Common\Service\SubDocumentService;
use Itq\Common\Tests\Service\Base\AbstractServiceTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group services
 * @group services/sub-document
 */
class SubDocumentServiceTest extends AbstractServiceTestCase
{
    /**
     * @return SubDocumentService
     */
    public function s()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::s();
    }
    /**
     * @group unit
     */
    public function testGetTypes()
    {
        $this->s()->setTypes(['a', 'b']);
        $this->assertEquals(['a', 'b'], $this->s()->getTypes());
    }
    /**
     * @group unit
     * @group document
     */
    public function testFullType()
    {
        $this->s()->setTypes(['a', 'b']);
        $this->assertEquals('a.b', $this->s()->getFullType());

        $this->s()->setTypes(['a', 'b']);
        $this->assertEquals('a b', $this->s()->getFullType(' '));
    }
    /**
     * @group unit
     * @group document
     */
    public function testGetRepoKey()
    {
        $this->s()->setTypes(['x', 'y']);

        $this->assertEquals('ys.a', $this->s()->getRepoKey(['a']));
        $this->assertEquals('ys.a.b', $this->s()->getRepoKey(['a', 'b']));
        $this->assertEquals('ys', $this->s()->getRepoKey());

        $this->assertEquals('a', $this->s()->getRepoKey(['a'], ['skip' => 1]));
    }
}
