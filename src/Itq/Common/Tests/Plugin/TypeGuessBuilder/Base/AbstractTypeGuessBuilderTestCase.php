<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Tests\Plugin\TypeGuessBuilder\Base;

use Symfony\Component\Form\Guess\TypeGuess;
use Itq\Common\Plugin\TypeGuessBuilderInterface;
use Itq\Common\Tests\Plugin\Base\AbstractPluginTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractTypeGuessBuilderTestCase extends AbstractPluginTestCase
{
    /**
     * @return TypeGuessBuilderInterface
     */
    public function b()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return $this->p();
    }
    /**
     * @param mixed $expected
     * @param array $definition
     * @param array $options
     * @param array $mockedMethods
     *
     * @group unit
     *
     * @dataProvider getBuildData
     */
    public function testBuild($expected, array $definition = [], array $options = [], array $mockedMethods = [])
    {
        foreach ($mockedMethods as $mockName => $methodCalls) {
            $mock = $this->mocked($mockName);
            foreach ($methodCalls as $methodCall) {
                $method = $mock->expects($this->once())->method($methodCall[0]);
                call_user_func_array([$method, 'with'], $methodCall[1]);
                $method->willReturn($methodCall[2]);
            }
        }

        $this->assertEquals($expected, $this->b()->build($definition, $options));
    }
    /**
     * @return array
     */
    abstract public function getBuildData();
    /**
     * @param string $type
     * @param array  $options
     * @param int    $confidence
     *
     * @return TypeGuess
     */
    protected function tg($type, array $options, $confidence)
    {
        return new TypeGuess($type, $options, $confidence);
    }
}
