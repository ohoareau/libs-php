<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Build\Common\Command;

use Exception;
use Itq\Common\Traits;
use Itq\Common\Service\YamlService;
use Symfony\Component\Finder\Finder;
use Itq\Common\Service\SystemService;
use Itq\Common\Service\FilesystemService;
use Symfony\Component\Finder\SplFileInfo;
use Itq\Common\Adapter\System\NativeSystemAdapter;
use Itq\Dev\Extension\Core\Command\Base\AbstractCommand;
use Itq\Common\Adapter\Filesystem\NativeFilesystemAdapter;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class GenerateMissingTestFileCommand extends AbstractCommand
{
    use Traits\BaseTrait;
    use Traits\Helper\String\Camel2SnakeCaseTrait;
    use Traits\ServiceAware\YamlServiceAwareTrait;
    use Traits\ServiceAware\SystemServiceAwareTrait;
    use Traits\ServiceAware\FilesystemServiceAwareTrait;
    /**
     *
     */
    public function __construct()
    {
        parent::__construct();

        $this->setYamlService(new YamlService());
        $this->setSystemService(new SystemService(new NativeSystemAdapter()));
        $this->setFilesystemService(new FilesystemService($this->getSystemService(), new NativeFilesystemAdapter()));
    }
    /**
     * @param array $args
     * @param array $options
     *
     * @return void
     */
    public function execute(array $args = [], array $options = [])
    {
        $path = $this->getConfig()->get('gentests_config_file');

        if (!$this->getFilesystemService()->isReadableFile($path)) {
            return;
        }

        $this->generate($this->getYamlService()->unserialize($this->getFilesystemService()->readFile($path)));
    }
    /**
     * @param array $map
     *
     * @throws Exception
     */
    protected function generate(array $map)
    {
        $srcDir  = 'src';
        $testDir = 'tests';

        foreach ($map as $definition) {
            $definition += ['template' => 'template.php.tmpl', 'params' => [], 'ignores' => [], 'only' => []];
            $f = new Finder();
            $f->in(sprintf('%s/%s', $srcDir, $definition['dir']))->depth(0);
            if (isset($definition['ignores']) && is_array($definition['ignores']) && count($definition['ignores'])) {
                foreach ($definition['ignores'] as $ignore) {
                    $f->notName($ignore);
                }
            }
            if (isset($definition['only']) && is_array($definition['only']) && count($definition['only'])) {
                foreach ($definition['only'] as $only) {
                    $f->name($only);
                }
            }
            foreach ($f->files() as $file) {
                /** @var SplFileInfo $file */
                $name                      = preg_replace('/\.php$/', '', $file->getFilename());
                $shortName                 = $name;
                $shortName                 = isset($definition['prefix']) ? preg_replace(sprintf('/^%s/', $definition['prefix']), '', $shortName) : $shortName;
                $shortName                 = isset($definition['suffix']) ? preg_replace(sprintf('/%s$/', $definition['suffix']), '', $shortName) : $shortName;
                $sluggedSnakeCaseShortName = $this->convertCamelCaseStringToSnakeCaseString($shortName);
                $sluggedShortName          = str_replace('_', '-', $sluggedSnakeCaseShortName);
                $testFile         = sprintf('%s/%s/%sTest.php', $testDir, $definition['dir'], $name);
                if (!$this->getFilesystemService()->isReadableFile($testFile)) {
                    $parentDir = dirname($testFile);
                    $this->getFilesystemService()->ensureDirectory($parentDir);
                    $this->getFilesystemService()->writeFile(
                        $testFile,
                        $this->render(
                            sprintf('%s/%s', $definition['dir'], $definition['template']),
                            [
                                'name'                      => $name,
                                'shortName'                 => $shortName,
                                'sluggedShortName'          => $sluggedShortName,
                                'sluggedSnakeCaseShortName' => $sluggedSnakeCaseShortName,
                                'className'                 => $name,
                                'fullClassName'             => str_replace('/', '\\', $definition['dir']).'\\'.$name,
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
     *
     * @throws Exception
     */
    protected function render($template, array $params)
    {
        $templateDir = $this->getConfig()->get('gentests_template_dir');

        if (null === $templateDir) {
            throw $this->createRequiredException('Gentests template dir required');
        }

        $this->getFilesystemService()->checkReadableDirectory($templateDir);

        $content = $this->getFilesystemService()->readFile(sprintf('%s/%s', $template));
        $matches = null;

        if (0 < preg_match_all('/\{\{\s*([^\}]+)\s*\}\}/', $content, $matches)) {
            foreach ($matches[1] as $i => $match) {
                $content = str_replace($matches[0][$i], isset($params[$match]) ? $params[$match] : null, $content);
            }
        }

        return $content;
    }
}
