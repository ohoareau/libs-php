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

use Itq\Bundle\ItqBundle\DependencyInjection\ItqExtension;
use Itq\Common\Tests\Base\AbstractTestCase;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group extensions
 * @group extensions/itq
 */
class ItqExtensionTest extends AbstractTestCase
{
    /**
     * @return ItqExtension
     */
    public function e()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::o();
    }
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
    public function testLoadForModelsSectionSetAppropriateParameters()
    {
        $c = $this->load([['tenant' => 'test', 'short_link' => ['dns' => null, 'secret' => null], 'apps' => ['front' => ['name' => 'a', 'url' => 'b']], 'analyzed_dirs' => ['the_dir']]]);

        $this->assertTrue($c->hasParameter('app_analyzed_dirs'));
        $this->assertEquals(
            [
                realpath(__DIR__.'/../../../../../src/Itq/Bundle/ItqBundle/DependencyInjection').'/../../../Common/Model',
                realpath(__DIR__.'/../../../../../src/Itq/Bundle/ItqBundle/DependencyInjection').'/../../../Common/Plugin',
                'the_dir',
            ],
            $c->getParameter('app_analyzed_dirs')
        );
    }
    /**
     * @group unit
     */
    public function testLoadForRecipientsSectionSetAppropriateParameters()
    {
        $c = $this->load(
            [
                [
                    'tenant' => 'test',
                    'short_link' => ['dns' => null, 'secret' => null],
                    'apps' => ['front' => ['name' => 'a', 'url' => 'b']],
                    'recipients' => [
                        'admins' => [
                            'a@b.com' => ['name' => 'A B'],
                            'e@f.com' => ['name' => 'E F', 'envs' => ['x', 'y'], 'types' => ['z']],
                        ],
                    ],
                ],
            ]
        );

        $this->assertTrue($c->hasParameter('app_recipients'));
        $this->assertEquals(
            [
                'admins' => [
                    'a@b.com' => ['name' => 'A B', 'envs' => ['*'], 'types' => ['*']],
                    'e@f.com' => ['name' => 'E F', 'envs' => ['x', 'y'], 'types' => ['z']],
                ],
            ],
            $c->getParameter('app_recipients')
        );
    }
    /**
     * @group unit
     */
    public function testLoadForEventsSectionSetAppropriateEvents()
    {
        $c = $this->load(
            [
                [
                    'tenant' => 'test',
                    'short_link' => ['dns' => null, 'secret' => null],
                    'apps' => ['front' => ['name' => 'a', 'url' => 'b']],
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
    }
    /**
     * @param mixed                 $config
     * @param ContainerBuilder|null $container
     *
     * @return ContainerBuilder
     */
    protected function load($config, ContainerBuilder $container = null)
    {
        if (null === $container) {
            $container = $this->mock('container', new ContainerBuilder());
        }

        $this->e()->load($config, $container);

        return $container;
    }
}
