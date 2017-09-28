<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Bundle\ItqBundle\DependencyInjection;

use Itq\Common\Tests\DependencyInjection\Base\AbstractExtensionTestCase;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group extensions
 * @group extensions/itq
 */
class ItqExtensionTest extends AbstractExtensionTestCase
{
    /**
     * @group unit
     */
    public function testLoadForEmptyConfigThrowException()
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('The child node "tenant" at path "itq" must be configured.');
        $this->expectExceptionCode(0);
        $this->assertNotNull($this->load([]));
    }
    /**
     * @group unit
     */
    public function testLoad()
    {
        $c = $this->load(
            [
                [
                    'tenant' => 'test',
                    'short_link' => ['dns' => null, 'secret' => null],
                    'apps' => ['front' => ['name' => 'a', 'url' => 'b']],
                    'partner_types' => ['a' => ['interface' => 'TheInterface']],
                    'analyzed_dirs' => ['the_dir'],
                    'recipients' => [
                        'admins' => [
                            'a@b.com' => ['name' => 'A B'],
                            'e@f.com' => ['name' => 'E F', 'envs' => ['x', 'y'], 'types' => ['z']],
                        ],
                    ],
                    'events' => [
                        'user_created' => [
                            'actions' => [
                              ['action' => 'inc_kpi', 'value' => 'users'],
                              ['action' => 'mail_user'],
                              ['action' => 'mail_admin'],
                              ['action' => 'fire'],
                            ],
                        ],
                    ],
                ],
            ]
        );

        $this->assertTrue($c->hasParameter('app_events'));
        $this->assertTrue($c->hasParameter('app_recipients'));
        $this->assertTrue($c->hasParameter('app_analyzed_dirs'));
        $this->assertEquals(
            [
                realpath(__DIR__.'/../../../../../src/Itq/Bundle/ItqBundle/DependencyInjection').'/../../../Common/Model',
                realpath(__DIR__.'/../../../../../src/Itq/Bundle/ItqBundle/DependencyInjection').'/../../../Common/Plugin',
                'the_dir',
            ],
            $c->getParameter('app_analyzed_dirs')
        );
        $this->assertEquals(
            [
                'user_created' => [
                    'actions' => [
                        ['action' => 'inc_kpi', 'params' => ['value' => 'users']],
                        ['action' => 'mail_user', 'params' => []],
                        ['action' => 'mail_admin', 'params' => []],
                        ['action' => 'fire', 'params' => []],
                    ],
                    'throwExceptions' => true,
                ],
            ],
            $c->getParameter('app_events')
        );
        $this->assertEquals(
            [
                'admins' => [
                    'a@b.com' => ['name' => 'A B', 'envs' => ['*'], 'types' => ['*']],
                    'e@f.com' => ['name' => 'E F', 'envs' => ['x', 'y'], 'types' => ['z']],
                ],
            ],
            $c->getParameter('app_recipients')
        );

        $methodCalls = $c->getDefinition('app.partner')->getMethodCalls();

        $this->assertCount(1, $methodCalls);
        $this->assertEquals(['registerType', ['a', ['interface' => 'TheInterface']]], $methodCalls[0]);

        $map = [
            'form.type_guesser'                           => 1,
            'form.type'                                   => 4,
            'app.modelcleaner'                            => 4,
            'app.modeldynamicpropertybuilder'             => 5,
            'app.modelfieldlistfilter'                    => 1,
            'app.modelpropertyauthorizationchecker'       => 1,
            'app.modelpropertylinearizer'                 => 3,
            'app.modelpropertymutator'                    => 8,
            'app.modelrefresher'                          => 14,
            'app.modelrestricter'                         => 1,
            'app.modelupdateenricher'                     => 1,
            'app.action'                                  => 5,
            'app.codeGenerator'                           => 14,
            'app.connection_bag'                          => 1,
            'app.criteriumtype'                           => 69,
            'data_collector'                              => 1,
            'app.datafilter'                              => 1,
            'app.dataprovider'                            => 3,
            'app.document_builder'                        => 1,
            'app.exceptiondescriptor'                     => 9,
            'app.formatter'                               => 5,
            'app.generator'                               => 2,
            'app.httpprotocolhandler'                     => 1,
            'app.migrator'                                => 3,
            'app.modeldescriptor'                         => 1,
            'app.requestcodec'                            => 5,
            'app.rule_type'                               => 1,
            'app.tracker'                                 => 1,
            'app.typeguessbuilder'                        => 14,
            'app.unique_code_generator_algorithm'         => 1,
            'app.workflowexecutor'                        => 1,
            'app.cruds_aware'                             => 1,
            'itq.aware.criteriumtype'                     => 1,
            'itq.aware.dataprovider'                      => 1,
            'itq.aware.instanceprovider'                  => 1,
            'itq.aware.modelcleaner'                      => 1,
            'itq.aware.modeldynamicpropertybuilder'       => 1,
            'itq.aware.modelfieldlistfilter'              => 1,
            'itq.aware.modelpropertyauthorizationchecker' => 1,
            'itq.aware.modelpropertylinearizer'           => 1,
            'itq.aware.modelpropertymutator'              => 1,
            'itq.aware.modelrefresher'                    => 1,
            'itq.aware.modelrestricter'                   => 1,
            'itq.aware.modelupdateenricher'               => 1,
            'twig.extension'                              => 2,
            'validator.constraint_validator'              => 1,
            'preprocessor.before_step'                    => 1,
            'preprocessor.aware.conditionals'             => 1,
            'preprocessor.contextdumper'                  => 2,
            'preprocessor.step'                           => 9,
            'preprocessor.annotation'                     => 31,
            'preprocessor.conditionalbefore'              => 1,
            'preprocessor.storage'                        => 5,
            'preprocessor.tag'                            => 53,
        ];

        foreach ($map as $tag => $count) {
            $actual = count($c->findTaggedServiceIds($tag));
            $this->assertEquals(
                $count,
                $actual,
                sprintf("%d service(s) tagged with '%s' instead of %d", $actual, $tag, $count)
            );
        }
    }
}
