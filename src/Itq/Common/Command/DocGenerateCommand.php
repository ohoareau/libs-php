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
class DocGenerateCommand extends AbstractCommand
{
    use Traits\ServiceAware\DocServiceAwareTrait;
    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('doc:generate')
            ->setDescription('Generate DOC')
            ->addArgument('path', InputArgument::OPTIONAL, 'output directory', 'doc')
            ->addArgument('type', InputArgument::OPTIONAL, 'doc type', 'default')
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
        $this->getDocService()->generate($input->getArgument('type'), $input->getArgument('path'));
    }
}
