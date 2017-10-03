<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common\Form\TypeGuesser;

use Itq\Common\Form\TypeGuesser\ModelTypeGuesser;
use Itq\Common\Tests\Form\TypeGuesser\Base\AbstractTypeGuesserFormTestCase;
use Symfony\Component\Form\Guess\Guess;
use Symfony\Component\Form\Guess\ValueGuess;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group forms
 * @group forms/type-guessers
 * @group forms/type-guessers/model
 */
class ModelTypeGuesserTest extends AbstractTypeGuesserFormTestCase
{
    /**
     * @return ModelTypeGuesser
     */
    public function g()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::g();
    }
    /**
     * @return array
     */
    public function constructor()
    {
        return [$this->mockedMetaDataService(), $this->mockedTypeGuessService()];
    }
    /**
     * @param mixed  $expectedReturn
     * @param string $expectedType
     * @param bool   $isModel
     * @param string $class
     * @param array  $expectedOptions
     * @param string $property
     * @param array  $propertyType
     *
     * @group unit
     *
     * @dataProvider getGuessTypeData
     */
    public function testGuessType($expectedReturn, $expectedType, $isModel, $class, $expectedOptions = null, $property = null, $propertyType = null)
    {
        $this->mockedMetaDataService()->expects($this->once())->method('isModel')->willReturn($isModel)->with($class);

        if (true === $isModel) {
            $this->mockedMetaDataService()->expects($this->once())->method('getModelPropertyType')->willReturn($propertyType)->with($class, $property);

            $this->mockedTypeGuessService()->expects($this->once())->method('create')->willReturn($expectedReturn)->with($expectedType, is_array($propertyType) ? $propertyType : [], $expectedOptions);
        }

        $this->assertEquals($expectedReturn, $this->g()->guessType($class, $property));
    }
    /**
     * @return array
     */
    public function getGuessTypeData()
    {
        return [
            [null, 'unknown', false, 'TheClass', null, 'theProperty', null],
            ['theReturn', 'unknown', true, 'TheClass', ['operation' => 'create', 'class' => 'TheClass', 'property' => 'theProperty'], 'theProperty', null],
            ['theReturn', 'theTypeType', true, 'TheClass', ['operation' => 'create', 'class' => 'TheClass', 'property' => 'theProperty'], 'theProperty', ['type' => 'theTypeType']],
        ];
    }
    /**
     * @param string $method
     * @param mixed  $expectedReturn
     * @param bool   $isModel
     * @param string $class
     * @param string $property
     *
     * @group unit
     *
     * @dataProvider getGuessData
     */
    public function testGuess($method, $expectedReturn, $isModel, $class = null, $property = null)
    {
        $this->mockedMetaDataService()->expects($this->once())->method('isModel')->willReturn($isModel)->with($class);

        $result = $this->g()->$method($class, $property);

        if ($isModel) {
            $this->assertEquals($expectedReturn, $result);
        }
    }
    /**
     * @return array
     */
    public function getGuessData()
    {
        return [
            ['guessRequired', null, false],
            ['guessRequired', new ValueGuess(true, Guess::LOW_CONFIDENCE), true],
            ['guessMaxLength', null, false],
            ['guessMaxLength', new ValueGuess(null, Guess::LOW_CONFIDENCE), true],
            ['guessPattern', null, false],
            ['guessPattern', new ValueGuess(null, Guess::LOW_CONFIDENCE), true],
        ];
    }
}
