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

use RuntimeException;
use Tests\Itq\Common\Service\Stub\Generator;
use Itq\Common\Exception\UnsupportedTokenGeneratorTypeException;
use Itq\Common\Service\TokenProviderService;
use Itq\Common\Tests\Service\Base\AbstractServiceTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group services
 * @group services/token-provider
 */
class TokenProviderServiceTest extends AbstractServiceTestCase
{
    /**
     * @return TokenProviderService
     */
    public function s()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::s();
    }
    /**
     * @group unit
     */
    public function testGenerate()
    {
        $this->s()->addGenerator(new Generator(), 'test', 'fakeGeneratorMethod');
        $this->assertEquals('fake', $this->s()->generate('test'));
    }
    /**
     * @group unit
     */
    public function testGenerateUnsupportedTokenGeneratorException()
    {

        $this->expectExceptionThrown(new UnsupportedTokenGeneratorTypeException('test'));
        $this->assertEquals('fake', $this->s()->generate('test'));
    }
    /**
     * @group unit
     */
    public function testGenerateNotFoundException()
    {
        $this->s()->addGenerator(new Generator(), 'test', 'NotExistingGeneratorMethod');
        $this->expectExceptionThrown(new RuntimeException("Unable to generate token from generator 'Tests\Itq\Common\Service\Stub\Generator' (method: NotExistingGeneratorMethod)", 404));
        $this->assertEquals('fake', $this->s()->generate('test'));
    }
}
