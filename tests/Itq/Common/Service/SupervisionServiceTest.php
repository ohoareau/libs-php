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

use Exception;
use Itq\Common\Service\SupervisionService;
use Itq\Common\Tests\Service\Base\AbstractServiceTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group services
 * @group services/supervision
 */
class SupervisionServiceTest extends AbstractServiceTestCase
{
    /**
     * @return SupervisionService
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
        return [$this->mockedDataProviderService()];
    }

    /**
     * @param mixed  $method
     * @param mixed  $expected
     * @param mixed  $returnedByDataProviderService
     * @param string $expectedType
     * @param array  $expectedDataProviderServiceOptions
     * @param array  $options
     *
     * @group unit
     *
     * @dataProvider getMethodCallData
     */
    public function testMethodCall(
        $method,
        $expected,
        $returnedByDataProviderService,
        $expectedType,
        array $expectedDataProviderServiceOptions = [],
        array $options = []
    ) {
        $this->mockedDataProviderService()
            ->expects($this->once())->method('provide')
            ->with($expectedType, $expectedDataProviderServiceOptions)
            ->willReturnCallback(
                function () use ($returnedByDataProviderService) {
                    if ($returnedByDataProviderService instanceof Exception) {
                        throw $returnedByDataProviderService;
                    }

                    return $returnedByDataProviderService;
                }
            )
        ;
        $this->assertEquals($expected, $this->s()->$method($options));
    }
    /**
     * @return array
     */
    public function getMethodCallData()
    {
        return [
            '0 - supervise - basic' => [
                'supervise',
                ['a' => 1, 'b' => ['c' => 2]],
                ['a' => 1, 'b' => ['c' => 2]],
                'supervision.supervision',
                ['x' => 12],
                ['x' => 12]
            ],
            '1 - identify - basic' => [
                'identify',
                ['a' => 1, 'b' => ['c' => 2]],
                ['a' => 1, 'b' => ['c' => 2]],
                'supervision.identity',
                ['x' => 12],
                ['x' => 12]
            ],
        ];
    }
}
