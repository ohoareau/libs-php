<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Tests\DependencyInjection\Base;

use Itq\Common\Tests\Base\AbstractTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractExtensionTestCase extends AbstractTestCase
{
    /**
     * @return ExtensionInterface
     */
    public function e()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return $this->o();
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
