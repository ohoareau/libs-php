<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common\Form\DataTransformer;

use Itq\Common\Form\Type\BooleanType;
use Itq\Common\Tests\Base\AbstractTestCase;
use Itq\Common\Form\DataTransformer\BooleanTypeToBooleanTransformer;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group forms
 * @group forms/data-transformers
 * @group forms/data-transformers/boolean-type-to-boolean
 */
class BooleanTypeToBooleanTransformerTest extends AbstractTestCase
{
    /**
     * @return BooleanTypeToBooleanTransformer
     */
    public function t()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::o();
    }
    /**
     * @dataProvider getTransformData
     *
     * @param mixed $value
     * @param mixed $expected
     *
     * @group boolean
     * @group unit
     */
    public function testTransform($value, $expected)
    {
        $this->assertEquals($expected, $this->t()->transform($value));
    }
    /**
     * @return array
     */
    public function getTransformData()
    {
        return [
            [true, BooleanType::VALUE_TRUE],
            [false, BooleanType::VALUE_FALSE],
            ['no', BooleanType::VALUE_FALSE],
            ['1', BooleanType::VALUE_TRUE],
            ['0', BooleanType::VALUE_FALSE],
            [1, BooleanType::VALUE_TRUE],
            [0, BooleanType::VALUE_FALSE],
            [null, BooleanType::VALUE_NULL],
        ];
    }
    /**
     * @dataProvider getReverseTransformData
     *
     * @param mixed $value
     * @param mixed $expected
     *
     * @group boolean
     * @group unit
     */
    public function testReverseTransform($value, $expected)
    {
        $this->assertEquals($expected, $this->t()->reverseTransform($value));
    }
    /**
     * @return array
     */
    public function getReverseTransformData()
    {
        return [
            [BooleanType::VALUE_TRUE, true],
            [1, true],
            ['1', true],
            [true, true],
            ['yes', false],
            [BooleanType::VALUE_FALSE, false],
            [0, false],
            ['0', false],
            [false, false],
            ['no', false],
        ];
    }
}
