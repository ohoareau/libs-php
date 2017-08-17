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
class GoogleDriveCommand extends AbstractCommand
{
    use Traits\ServiceAware\GoogleServiceAwareTrait;
    /**
     * @param array $config
     */
    public function setConfig(array $config)
    {
        $this->setParameter('config', $config);
    }
    /**
     * @return mixed
     *
     * @throws \Exception
     */
    public function getConfig()
    {
        return $this->getParameter('config');
    }
    /**
     * @return \Google_Client $googleClient
     */
    public function getGoogleClient()
    {
        return $this->getGoogleService()->getGoogleClient();
    }
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
        $config = $this->getConfig();
        $credentialsPath = $config['tokenFilePath'];
        $dir = dirname($credentialsPath);
        if (!file_exists($dir)) {
            mkdir($dir, 0775, true);
        }
        if (file_exists($credentialsPath)) {
            $accessToken = json_decode(file_get_contents($credentialsPath), true);
        } else {
            // Request authorization from the user.
            $authUrl = $this->getGoogleClient()->createAuthUrl();
            $output->writeln(sprintf("Open the following link in your browser:\n%s", $authUrl), OutputInterface::OUTPUT_RAW);
            $output->writeln('Enter verification code: ');
            $authCode = trim(fgets(STDIN));
            // Exchange authorization code for an access token.
            $accessToken = $this->getGoogleClient()->fetchAccessTokenWithAuthCode($authCode);
            // Store the credentials to disk.
            file_put_contents($credentialsPath, json_encode($accessToken));
            $output->writeln(sprintf("Credentials saved to %s\n", $credentialsPath));
        }
        $this->getGoogleClient()->setAccessToken($accessToken);
        $this->getGoogleService()->refreshTokenIfNeeded($this->getGoogleClient());
    }
}
