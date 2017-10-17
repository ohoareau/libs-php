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

use DateTime;
use Exception;
use Itq\Common\Plugin;
use Itq\Common\Adapter;
use Itq\Common\Service;
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
     * @return Service\SupervisionService
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
                'supervision',
                ['x' => 12],
                ['x' => 12],
            ],
            '1 - identify - basic' => [
                'identify',
                ['a' => 1, 'b' => ['c' => 2]],
                ['a' => 1, 'b' => ['c' => 2]],
                'supervision.identity',
                ['x' => 12],
                ['x' => 12],
            ],
        ];
    }
    /**
     * @group integ
     */
    public function testRealDataProviders()
    {
        $this->mockedSystemService()->expects($this->any())->method('getCurrentTime')->willReturn(123);
        $this->mockedSystemService()->expects($this->any())->method('getHostName')->willReturn('testhost');

        $dataProviderService = new Service\DataProviderService();
        $symfonyService      = new Service\SymfonyService(new Adapter\Symfony\NativeSymfonyAdapter());

        $dataProviderService->addDataProvider(
            'supervision',
            new Plugin\DataProvider\Supervision\PhpSupervisionDataProvider(
                new Service\PhpService(new Adapter\Php\DecoratedNativePhpAdapter(['constants' => ['APP_TIME_START' => 100]])),
                $this->mockedSystemService(),
                new Service\DateService($this->mockedSystemService())
            )
        );
        $dataProviderService->addDataProvider(
            'supervision',
            new Plugin\DataProvider\Supervision\SymfonySupervisionDataProvider(
                $symfonyService
            )
        );

        $this->s()->setDataProviderService($dataProviderService);

        $this->assertEquals(
            [
                'currentTime'   => 123,
                'hostName'      => 'testhost',
                'date'          => new DateTime('@123'),
                'php'           => [
                    'os'         => PHP_OS,
                    'version'    => PHP_VERSION,
                    'version_id' => PHP_VERSION_ID,
                ],
                'startDuration' => 23,
                'startTime'     => 100,
                'symfony'       => $symfonyService->describe(),
            ],
            $this->s()->supervise()
        );
    }
}
