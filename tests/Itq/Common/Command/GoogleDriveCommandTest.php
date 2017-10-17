<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common\Command;

use Closure;
use Itq\Common\Command\GoogleDriveCommand;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Output\BufferedOutput;
use Itq\Common\Tests\Command\Base\AbstractCommandTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group commands
 * @group commands/google-drive
 */
class GoogleDriveCommandTest extends AbstractCommandTestCase
{
    /**
     * @return GoogleDriveCommand
     */
    public function c()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::c();
    }

    /**
     * @param array  $config
     * @param mixed  $closureResult
     * @param string $closureUrl
     * @param string $closureFile
     * @param string $expectedText
     *
     * @group unit
     *
     * @dataProvider getRunData
     */
    public function testRun(array $config, $closureResult, $closureUrl, $closureFile, $expectedText = null)
    {
        $helperSet = new HelperSet();
        $helperSet->set($this->mocked('questionHelper', QuestionHelper::class), 'question');

        $this->c()->setConfig($config);
        $this->c()->setGoogleService($this->mockedGoogleService());
        $this->c()->setHelperSet($helperSet);

        $that = $this;

        $this->mocked('questionHelper')->expects($this->once())->method('ask')->willReturn($closureResult);
        $this->mockedGoogleService()->expects($this->once())->method('authorize')->willReturnCallback(
            function ($cfg, Closure $closure) use ($that, $config, $closureResult, $closureUrl, $closureFile) {
                $that->assertEquals($config, $cfg);
                $that->assertEquals($closureResult, $closure($closureUrl, $closureFile));
            }
        );
        list ($result, $input, $output) = $this->runCommand();

        unset($result, $input);

        if (null !== $expectedText) {
            /** @var BufferedOutput $output */
            $text = $output->fetch();

            $this->assertEquals(rtrim(join(PHP_EOL, $expectedText)), rtrim($text));
        }
    }
    /**
     * @return array
     */
    public function getRunData()
    {
        return [
            'default' => [
                [],
                'thecode',
                'theurl',
                'thefile',
                [
                    'Open the following link in your browser:',
                    '  theurl',
                    'Credentials will be saved to thefile',
                ],
            ],
        ];
    }
}
