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

use Symfony\Component\Finder\Finder;
use Itq\Dev\Extension\Core\Command\Base\AbstractCommand;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class GenerateMissingTestFileCommand extends AbstractCommand
{
    /**
     * @param array $args
     * @param array $options
     *
     * @return void
     */
    public function execute(array $args = [], array $options = [])
    {
        $this->generate(
            [
                'annotations' => [
                    'dir'      => 'Itq/Common/Annotation',
                    'suffix'   => 'Annotation',
                    'template' => 'template.php',
                ],
                'services' => [
                    'dir' => 'Itq/Common/Service',
                    'suffix' => 'Service',
                    'template' => 'template.php',
                    'ignores' => ['/Interface\.php$/'],
                ],
                'plugins/actions' => [
                    'dir' => 'Itq/Common/Plugin/Action',
                    'suffix' => 'Action',
                    'template' => 'template.php',
                ],
                'plugins/context-dumpers' => [
                    'dir' => 'Itq/Common/Plugin/ContextDumper',
                    'suffix' => 'ContextDumper',
                    'template' => 'template.php',
                ],
                'plugins/exception-descriptors' => [
                    'dir' => 'Itq/Common/Plugin/ExceptionDescriptor',
                    'suffix' => 'ExceptionDescriptor',
                    'template' => 'template.php',
                ],
                'plugins/criterium-types/mongo' => [
                    'dir' => 'Itq/Common/Plugin/CriteriumType/Mongo',
                    'suffix' => 'MongoCriteriumType',
                    'template' => 'template.php',
                ],
            ]
        );
    }
    /**
     * @param array $map
     */
    protected function generate(array $map)
    {
        $srcDir  = 'src';
        $testDir = 'tests';

        foreach ($map as $definition) {
            $definition += ['params' => [], 'ignores' => []];
            $f = new Finder();
            $f->in(sprintf('%s/%s', $srcDir, $definition['dir']))->depth(0);
            if (isset($definition['ignores']) && is_array($definition['ignores']) && count($definition['ignores'])) {
                foreach ($definition['ignores'] as $ignore) {
                    $f->notName($ignore);
                }
            }
            foreach ($f->files() as $file) {
                /** @var \Symfony\Component\Finder\SplFileInfo $file */
                $name = preg_replace('/\.php$/', '', $file->getFilename());
                $shortName = preg_replace(sprintf('/%s$/', $definition['suffix']), '', $name);
                $sluggedShortName = strtolower($shortName);
                $testFile = sprintf('%s/%s/%sTest.php', $testDir, $definition['dir'], $name);
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
        $content = file_get_contents(sprintf('tests/templates/%s', $template));
        $matches = null;

        if (0 < preg_match_all('/\{\{\s*([^\}]+)\s*\}\}/', $content, $matches)) {
            foreach ($matches[1] as $i => $match) {
                $content = str_replace($matches[0][$i], isset($params[$match]) ? $params[$match] : null, $content);
            }
        }

        return $content;
    }
}
