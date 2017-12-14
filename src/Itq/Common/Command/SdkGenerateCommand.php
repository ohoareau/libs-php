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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Exception;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class SdkGenerateCommand extends Base\AbstractCommand
{
    use Traits\ServiceAware\SdkServiceAwareTrait;
    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('sdk:generate')
            ->setDescription('Generate SDK source code')
            ->addArgument('path', InputArgument::OPTIONAL, 'output directory', 'sdk')
            ->addArgument('target', InputArgument::OPTIONAL, 'target language', 'php')
        ;
    }
    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     *
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getSdkService()->generate($input->getArgument('target'), $input->getArgument('path'));
    }
}
