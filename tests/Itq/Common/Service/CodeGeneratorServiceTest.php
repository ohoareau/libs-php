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

use Itq\Common\Service\CodeGeneratorService;
use Itq\Common\Tests\Service\Base\AbstractServiceTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group services
 * @group services/code-generator
 */
class CodeGeneratorServiceTest extends AbstractServiceTestCase
{
    /**
     * @return CodeGeneratorService|\PHPUnit_Framework_MockObject_MockObject
     */
    public function s()
    {
        return parent::s();
    }
    /**
     * @group unit
     */
    public function testCreateFile()
    {
        $definitions = [
            'uses'      => ['Test\\User\\One', ['use' => 'Test\\User\\Two', 'as' => 'Three']],
            'namespace' => 'Test\\Namespace\\One',
        ];
        $this->assertEqualsResultSet($this->s()->createFile($definitions));
    }
    /**
     * @param string $name
     * @param array  $definitions
     *
     * @group unit
     *
     * @dataProvider getCreatePropertyData
     */
    public function testCreateProperty($name, $definitions)
    {
        $this->assertEqualsResultSet($this->s()->createProperty($name, $definitions));
    }
    /**
     * @return array
     */
    public function getCreatePropertyData()
    {
        return [
            '0 - default'                 => ['defaultProperty', []],
            '1 - public property'         => ['publicProperty', ['visibility' => 'public']],
            '2 - protected property'      => ['protectedProperty', ['visibility' => 'protected']],
            '3 - private property'        => ['privateProperty', ['visibility' => 'private']],
            '4 - static property'         => ['staticProperty', ['static' => true]],
            '5 - casted property'         => ['castedProperty', ['cast' => ['null', 'array']]],
            '6 - not basic type property' => ['notBasicTypeProperty', ['type' => 'notBasic']],
        ];
    }
    /**
     * @param string $name
     * @param array  $definitions
     *
     * @group unit
     *
     * @dataProvider getCreateMethodData
     */
    public function testCreateMethod($name, $definitions)
    {
        if ('3 - type method' === $this->dataDescription()) {
            $this->mockMethodOnce('executeCallableByType');
        }
        $this->assertEqualsResultSet($this->s()->createMethod($name, $definitions));
    }
    /**
     * @return array
     */
    public function getCreateMethodData()
    {
        return [
            '0 - default'          => ['defaultMethod', []],
            '1 - protected method' => ['protectedMethod', ['visibility' => 'protected']],
            '2 - private method'   => ['privateMethod', ['visibility' => 'private']],
            '3 - type method'      => [
                'typeMethod',
                [
                    'type'    => 'typeName',
                    'params'  => ['some params', 'options' => ['some options']],
                    'options' => [],
                ],
            ],
        ];
    }
    /**
     * @group unit
     */
    public function testCreateClassFile()
    {
        $this->assertEqualsResultSet($this->s()->createClassFile('Test\\Namespace\\One\\ClassName'));
    }
    /**
     * @return array
     */
    protected function getMockedMethod()
    {
        return ['executeCallableByType', 'registerCallableByType'];
    }
}
