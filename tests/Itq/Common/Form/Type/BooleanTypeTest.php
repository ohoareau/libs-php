<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common\Form\Type;

use Itq\Common\Form\Type\BooleanType;
use Itq\Common\Tests\Base\AbstractTestCase;

use Symfony\Component\Form\Forms;
use Symfony\Component\Form\FormBuilder;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group forms
 * @group forms/types
 * @group forms/types/boolean
 */
class BooleanTypeTest extends AbstractTestCase
{
    /**
     * @return BooleanType
     */
    public function t()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::o();
    }
    /**
     *
     */
    public function initializer()
    {
        $this->mock('factory', Forms::createFormFactoryBuilder()->addExtensions([])->getFormFactory());
        $this->mockedEventDispatcher();
        $this->mock('builder', new FormBuilder(null, null, $this->mockedEventDispatcher(), $this->mock('factory')));
    }
    /**
     * @param mixed $value
     * @param mixed $expected
     *
     * @group unit
     * @dataProvider getTestData
     */
    public function testFormType($value, $expected)
    {
        $form = $this->mock('factory')->create($this->getObjectClass());
        $form->submit($value);
        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($expected, $form->getData());
    }
    /**
     * @return array
     */
    public function getTestData()
    {
        return [
            ['1', true],
            [1, true],
            [true, true],
            ['0', false],
            [0, false],
            [false, false],
            ['yes', false],
            ['no', false],
            [null, null],
        ];
    }
}
