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

use Itq\Common\Service\TypeGuessService;
use Symfony\Component\Form\Guess\TypeGuess;
use Itq\Common\Plugin\TypeGuessBuilderInterface;
use PHPUnit_Framework_MockObject_MockObject;
use Itq\Common\Tests\Service\Base\AbstractServiceTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group services
 * @group services/type-guess
 */
class TypeGuessServiceTest extends AbstractServiceTestCase
{
    /**
     * @return TypeGuessService
     */
    public function s()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::s();
    }
    /**
     * @group integ
     */
    public function testCreate()
    {
        $expected = new TypeGuess('type', [], TypeGuess::VERY_HIGH_CONFIDENCE);
        $this->mocked('typeGuesser', TypeGuessBuilderInterface::class)
            ->expects($this->once())->method('build')->will($this->returnValue($expected));

        $this->s()->add('type',  $this->mocked('typeGuesser'));

        $this->assertSame($expected, $this->s()->create('type', ['definition' => '']));
    }
    /**
     * @group integ
     */
    public function testCreateWithUnknownType()
    {
        $expected = new TypeGuess('type', [], TypeGuess::VERY_HIGH_CONFIDENCE);
        $this
            ->mocked('typeGuesser', TypeGuessBuilderInterface::class)
            ->expects($this->once())->method('build')
            ->will($this->returnValue($expected));

        $this->s()->add('unknown', $this->mocked('typeGuesser'));

        $this->assertSame($expected, $this->s()->create('type', ['definition' => '']));
    }
}
