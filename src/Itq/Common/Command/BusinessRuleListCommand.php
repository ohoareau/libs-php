<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Command;

use Itq\Common\Traits;
use Itq\Common\Command\Base\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class BusinessRuleListCommand extends AbstractCommand
{
    use Traits\ServiceAware\BusinessRuleServiceAwareTrait;
    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('business-rule:list')
            ->setDescription('List business rules')
        ;
    }
    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        foreach ($this->getBusinessRuleService()->getFlattenBusinessRuleDefinitions() as $definition) {
            $output->writeln(
                sprintf(
                    " <info>%s</info> on %s %s <comment>%s</comment>%s",
                    $definition['id'],
                    str_replace('_', ' ', $definition['operation']),
                    str_replace(['.', '_'], ' ', $definition['model']),
                    $definition['name'],
                    sprintf(
                        '%s%s',
                        count($definition['tenants']) > 0 ? sprintf(' (only for tenant: <info>%s</info>)', strtoupper(join(', ', $definition['tenants']))) : '',
                        count($definition['notTenants']) > 0 ? sprintf(' (not for tenant: <info>%s</info>)', strtoupper(join(', ', $definition['notTenants']))) : ''
                    )
                )
            );
        }
    }
}
