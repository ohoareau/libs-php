<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Tests\Service\Base;

use Itq\Common\Traits;
use Itq\Common\Tests\Base\AbstractTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractServiceTestCase extends AbstractTestCase
{
    /**
     * @return object|Traits\ServiceTrait
     */
    public function s()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return $this->o();
    }
    /**
     * @param string $type
     * @param string $pluginClass
     * @param array  $methods
     * @param string $getter
     * @param string $adder
     * @param string $optionalTypeForAdder
     * @param string $optionalSingleGetter
     * @param string $optionalGroupGetter
     */
    protected function handleTestPlugins($type, $pluginClass, array $methods, $getter, $adder, $optionalTypeForAdder = null, $optionalSingleGetter = null, $optionalGroupGetter = null)
    {
        $mock = $this->mocked($type, $pluginClass, $methods);

        $this->assertEquals([], $this->s()->$getter());
        if (null !== $optionalTypeForAdder) {
            $this->s()->$adder($optionalTypeForAdder, $mock);
            if (null !== $optionalGroupGetter) {
                $this->assertEquals([$optionalTypeForAdder => [$mock]], $this->s()->$getter());
            } else {
                $this->assertEquals([$optionalTypeForAdder => $mock], $this->s()->$getter());
            }
            if (null !== $optionalSingleGetter) {
                $this->assertEquals($mock, $this->s()->$optionalSingleGetter($optionalTypeForAdder));
            }
            if (null !== $optionalGroupGetter) {
                $this->assertEquals([$mock], $this->s()->$optionalGroupGetter($optionalTypeForAdder));
            }
        } else {
            $this->s()->$adder($mock);
            $this->assertEquals([$mock], $this->s()->$getter());
        }
    }
}
