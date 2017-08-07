<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\Migrator\Base;

use Itq\Common\Traits;
use Itq\Common\Plugin\MigratorInterface;
use Itq\Common\Plugin\Base\AbstractPlugin;

use Psr\Log\LoggerAwareInterface;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractMigrator extends AbstractPlugin implements MigratorInterface, ContainerAwareInterface, LoggerAwareInterface
{
    use Traits\LoggerAwareTrait;
    use Traits\ContainerAwareTrait;
}
