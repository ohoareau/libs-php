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
use Symfony\Component\Console\Question\Question;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class GoogleDriveCommand extends AbstractCommand
{
    use Traits\ServiceAware\GoogleServiceAwareTrait;
    use Traits\ParameterAware\ConfigParameterAwareTrait;
    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('google-drive:config')
            ->setDescription('Generate configuration token')
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
        $that = $this;

        $this->getGoogleService()->authorize(
            $this->getConfig(),
            function ($url, $file) use ($input, $output, $that) {
                $output->writeln('Open the following link in your browser:');
                $output->writeln('  '.$url);
                $output->writeln(sprintf("Credentials will be saved to %s", $file));

                $question = new Question('Enter verification code: ');
                $question->setHidden(true);
                $question->setHiddenFallback(false);
                $helper = $that->getHelper('question');

                return trim($helper->ask($input, $output, $question));
            }
        );
    }
}
