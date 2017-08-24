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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class BatchCommand extends AbstractCommand
{
    use Traits\ServiceAware\BatchServiceAwareTrait;
    use Traits\ParameterAware\EnabledParameterAwareTrait;
    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('batch')
            ->setDescription('Run named batch')
            ->addArgument('name', InputArgument::REQUIRED, 'batch name')
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
        if ($this->isEnabled()) {
            $this->getBatchService()->execute($input->getArgument('name'));
        }
    }
}
