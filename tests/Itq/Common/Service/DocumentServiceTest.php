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

use Itq\Common\Service\DocumentService;
use Itq\Common\Tests\Service\Base\AbstractServiceTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group services
 * @group services/document
 */
class DocumentServiceTest extends AbstractServiceTestCase
{
    /**
     * @return DocumentService
     */
    public function s()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::s();
    }
    /**
     * @group unit
     * @group document
     */
    public function testGetTypes()
    {
        $this->s()->setTypes(['a']);
        $this->assertEquals(['a'], $this->s()->getTypes());
    }
    /**
     * @group unit
     * @group document
     */
    public function testFullType()
    {
        $this->s()->setTypes(['a']);
        $this->assertEquals('a', $this->s()->getFullType());
    }
    /**
     * @group unit
     * @group document
     */
    public function testGetRepoKey()
    {
        $this->s()->setTypes(['x']);

        $this->assertEquals('', $this->s()->getRepoKey());
        $this->assertEquals('a', $this->s()->getRepoKey(['a']));
    }
}
