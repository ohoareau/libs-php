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
 * @group services/attachment
 */
class AttachmentServiceTest extends AbstractServiceTestCase
{
    /**
     * @return Service\AttachmentService
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
        return [$this->mockedGeneratorService()];
    }
    /**
     * @group unit
     * @group attachment
     */
    public function testBuild()
    {
        $this->mockedGeneratorService()->expects($this->once())->method('generate')->will($this->returnValue('result'));

        $this->assertEquals(
            ['name' => 'test.pdf', 'type' => 'application/pdf', 'content' => base64_encode('result')],
            $this->s()->build(['name' => 'test.pdf', 'generator' => 'test'])
        );
    }
}
