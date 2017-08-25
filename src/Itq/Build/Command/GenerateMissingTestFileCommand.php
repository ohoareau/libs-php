<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Build\Command;

use Itq\Common\Traits;
use Itq\Common\Service\YamlService;
use Symfony\Component\Finder\Finder;
use Itq\Common\Service\SystemService;
use Itq\Common\Service\FilesystemService;
use Symfony\Component\Finder\SplFileInfo;
use Itq\Dev\Extension\Core\Command\Base\AbstractCommand;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class GenerateMissingTestFileCommand extends AbstractCommand
{
    use Traits\BaseTrait;
    use Traits\Helper\String\Camel2SnakeCaseTrait;
    /**
     * @param array $args
     * @param array $options
     *
     * @return void
     */
    public function execute(array $args = [], array $options = [])
    {
        $yamlService = new YamlService();
        $sysService  = new SystemService();
        $fsService   = new FilesystemService($sysService);

        $this->generate($yamlService->unserialize($fsService->readFile(__DIR__.'/../Resources/config/tests.yml')));
    }
    /**
     * @param array $map
     */
    protected function generate(array $map)
    {
        $srcDir  = 'src';
        $testDir = 'tests';

        foreach ($map as $definition) {
            $definition += ['template' => 'template.php.tmpl', 'params' => [], 'ignores' => []];
            $f = new Finder();
            $f->in(sprintf('%s/%s', $srcDir, $definition['dir']))->depth(0);
            if (isset($definition['ignores']) && is_array($definition['ignores']) && count($definition['ignores'])) {
                foreach ($definition['ignores'] as $ignore) {
                    $f->notName($ignore);
                }
            }
            foreach ($f->files() as $file) {
                /** @var SplFileInfo $file */
                $name             = preg_replace('/\.php$/', '', $file->getFilename());
                $shortName        = preg_replace(sprintf('/%s$/', $definition['suffix']), '', $name);
                $sluggedShortName = str_replace('_', '-', $this->convertCamelCaseStringToSnakeCaseString($shortName));
                $testFile         = sprintf('%s/%s/%sTest.php', $testDir, $definition['dir'], $name);
                if (!is_file($testFile)) {
                    $parentDir = dirname($testFile);
                    if (!is_dir($parentDir)) {
                        mkdir($parentDir, 0777, true);
                    }
                    file_put_contents(
                        $testFile,
                        $this->render(
                            sprintf('%s/%s', $definition['dir'], $definition['template']),
                            [
                                'name'             => $name,
                                'shortName'        => $shortName,
                                'sluggedShortName' => $sluggedShortName,
                                'className'        => $name,
                                'fullClassName'    => str_replace('/', '\\', $definition['dir']).'\\'.$name,
                            ] + $definition['params']
                        )
                    );
                    echo $testFile.PHP_EOL;
                }
            }
        }
    }
    /**
     * @param string $template
     * @param array  $params
     *
     * @return string
     */
    protected function render($template, array $params)
    {
        $content = file_get_contents(__DIR__.'/../Resources/templates/tests/'.$template);
        $matches = null;

        if (0 < preg_match_all('/\{\{\s*([^\}]+)\s*\}\}/', $content, $matches)) {
            foreach ($matches[1] as $i => $match) {
                $content = str_replace($matches[0][$i], isset($params[$match]) ? $params[$match] : null, $content);
            }
        }

        return $content;
    }
}
