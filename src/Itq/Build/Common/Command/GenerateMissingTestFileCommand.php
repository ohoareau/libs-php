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
use Itq\Common\Tests\Base\AbstractTestCase;
use Itq\TestGen\Service\ModelService;
use Itq\TestGen\Service\ParserService;
use Itq\TestGen\Service\TestService;
use Twig_Environment;
use Itq\Common\Traits;
use Itq\Common\Service\YamlService;
use Symfony\Component\Finder\Finder;
use Itq\Common\Service\SystemService;
use Itq\Common\Service\FilesystemService;
use Symfony\Component\Finder\SplFileInfo;
use Itq\Common\Adapter\System\NativeSystemAdapter;
use Itq\Dev\Extension\Core\Command\Base\AbstractCommand;
use Itq\Common\Adapter\Filesystem\NativeFilesystemAdapter;
use Twig\Loader\FilesystemLoader;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class GenerateMissingTestFileCommand extends AbstractCommand
{
    use Traits\BaseTrait;
    use Traits\TemplatingAwareTrait;
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
        $this->setTwig(new Twig_Environment(new FilesystemLoader([__DIR__.'/../Resources/templates/tests'])));
        $this->setTestService(new TestService(new ModelService(new ParserService())));
    }
    /**
     * @param TestService $testService
     *
     * @return $this
     */
    public function setTestService(TestService $testService)
    {
        return $this->setService('testService', $testService);
    }
    /**
     * @return TestService
     */
    public function getTestService()
    {
        return $this->getService('testService');
    }
    /**
     * @param Twig_Environment $twig
     *
     * @return $this
     */
    public function setTwig(\Twig_Environment $twig)
    {
        return $this->setService('twig', $twig);
    }
    /**
     * @return Twig_Environment
     */
    public function getTwig()
    {
        return $this->getService('twig');
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
     * @param array $config
     *
     * @throws Exception
     */
    protected function generate(array $config)
    {
        $common     = (isset($config['config']) && is_array($config['config'])) ? $config['config'] : [];
        $common    += ['source' => 'src', 'target' => 'tests'];
        $dynamicMap = (isset($config['types']) && is_array($config['types'])) ? $config['types'] : [];
        $staticMap  = (isset($config['map']) && is_array($config['map'])) ? $config['map'] : [];

        foreach (array_keys($dynamicMap) as $k) {
            $dynamicMap[$k] += ['mode' => 'dynamic'];
        }

        foreach (array_keys($staticMap) as $k) {
            $staticMap[$k] += ['mode' => 'default', 'template' => 'template.php.tmpl'];
        }

        $map = $dynamicMap + $staticMap;

        foreach ($map as $definition) {
            $definition += $common + ['params' => [], 'ignores' => [], 'only' => []];

            foreach ($this->find($definition + ['rootDir' => $definition['source']]) as $file) {
                /** @var SplFileInfo $file */
                $name                      = preg_replace('/\.php$/', '', $file->getFilename());
                $testFile         = sprintf('%s/%s/%sTest.php', $definition['target'], $definition['dir'], $name);
                if (!$this->getFilesystemService()->isReadableFile($testFile)) {
                    $parentDir = dirname($testFile);
                    $this->getFilesystemService()->ensureDirectory($parentDir);
                    $this->getFilesystemService()->writeFile(
                        $testFile,
                        $this->buildTestFile(['name' => $name] + $definition)
                    );
                    echo $testFile.PHP_EOL;
                }
            }
        }
    }
    /**
     * @param array $definition
     *
     * @return string
     */
    protected function buildTestFile(array $definition)
    {
        $name                      = $definition['name'];
        $shortName                 = $name;
        $shortName                 = isset($definition['prefix']) ? preg_replace(sprintf('/^%s/', $definition['prefix']), '', $shortName) : $shortName;
        $shortName                 = isset($definition['suffix']) ? preg_replace(sprintf('/%s$/', $definition['suffix']), '', $shortName) : $shortName;
        $sluggedSnakeCaseShortName = $this->convertCamelCaseStringToSnakeCaseString($shortName);
        $sluggedShortName          = str_replace('_', '-', $sluggedSnakeCaseShortName);

        $params = [
            'name'                      => $name,
            'shortName'                 => $shortName,
            'sluggedShortName'          => $sluggedShortName,
            'sluggedSnakeCaseShortName' => $sluggedSnakeCaseShortName,
            'className'                 => $name,
            'fullClassName'             => str_replace('/', '\\', $definition['dir']).'\\'.$name,
        ] + $definition['params']
        ;

        $definition += ['mode' => 'default'];

        switch ($definition['mode']) {
            case 'dynamic':
                $content = $this->buildClassFile($this->buildTestClass($params, $definition), $definition);
                break;
            default:
                $content = $this->render(sprintf('%s/%s', $definition['dir'], $definition['template']), $params);
                break;
        }

        return $content;
    }
    /**
     * @param object $class
     * @param array  $options
     *
     * @return string
     */
    protected function buildClassFile($class, array $options = [])
    {
        return $this->render('class.php.twig', ((array) $class) + ['options' => $options], ['engine' => 'twig']);
    }
    /**
     * @param array $params
     * @param array $options
     *
     * @return object
     */
    protected function buildTestClass(array $params, array $options = [])
    {
        $testClass = [];

        $testClass['name'] = sprintf('%sTest', $params['name']);
        $testClass['className'] = sprintf('%sTest', $params['className']);
        $testClass['fullClassName'] = sprintf('Tests\\%sTest', $params['fullClassName']);
        $testClass['namespace'] = str_replace('/', '\\', dirname(str_replace('\\', '/', $testClass['fullClassName'])));
        $testClass['classUnderTest'] = $params['fullClassName'];
        $testClass['classUnderTestNamespace'] = str_replace('/', '\\', dirname(str_replace('\\', '/', $params['fullClassName'])));
        $testClass['fullParentClass'] = $this->detectParentTestClass($testClass, $options);
        $testClass['parentClass'] = basename(str_replace('\\', '/', $testClass['fullParentClass']));

        print_r($this->getTestService()->describeClassTests($testClass['classUnderTest']));
        $class = $params + [
            'namespace' => $testClass['namespace'],
            'uses' => [
                $params['fullClassName'] => true,
                $testClass['fullParentClass'] => true,
            ],
            'groups' => isset($options['groups']) ? $options['groups'] : $this->buildGroupsFromClass($testClass['classUnderTestNamespace'], $params['sluggedShortName'], $options),
            'class' => $testClass['name'],
            'extends' => $testClass['parentClass'],
            'methods' => [
                'o' => ['scope' => 'public', 'return' => $params['className'], 'body' => "        /** @noinspection PhpIncompatibleReturnTypeInspection */\n\n        return parent::o();"],
                'constructor' => ['scope' => 'public', 'return' => 'array', 'body' => '        return [];'],
            ],
        ] + $options;

        return (object) $class;
    }
    /**
     * @param array $testClass
     * @param array $options
     *
     * @return mixed|string
     */
    protected function detectParentTestClass(array $testClass, array $options = [])
    {
        unset($testClass);

        return (isset($options['parent']) ? $options['parent'] : AbstractTestCase::class);
    }
    /**
     * @param string $namespace
     * @param string $name
     * @param array  $options
     *
     * @return array
     */
    protected function buildGroupsFromClass($namespace, $name, array $options = [])
    {
        $t = [];

        foreach (explode('/', str_replace('\\', '/', $namespace)) as $token) {
            $t[] = str_replace('_', '-', $this->convertCamelCaseStringToSnakeCaseString($token));
        }

        $t = join('/', $t);

        if (isset($options['groupRemovePrefix'])) {
            if (0 < preg_match(sprintf(',^%s/,', $options['groupRemovePrefix']), $t)) {
                $t = substr($t, strlen($options['groupRemovePrefix']) + 1);
            }
        }

        $t .= '/'.$name;

        $groups = [];
        $lastGroup = null;
        $tokens = explode('/', $t);
        $n = count($tokens);
        foreach ($tokens as $i => $token) {
            $lastGroup = $lastGroup.($lastGroup ? '/' : '').$token.((($i + 1) === $n) ? '' : 's');
            $groups[] = $lastGroup;
        }

        return $groups;
    }
    /**
     * @param string|array $a
     * @param array        $params
     *
     * @return array|string
     */
    protected function replaceParams($a, $params)
    {
        if (is_array($a)) {
            foreach ($a as $k => $v) {
                $a[$k] = $this->replaceParams($v, $params);
            }
        } else {
            while (0 < preg_match_all('/\{\{([^\}]+)\}\}/', $a, $matches)) {
                foreach ($matches[0] as $i => $match) {
                    $a = str_replace($match, isset($params[$matches[1][$i]]) ? $params[$matches[1][$i]] : null, $a);
                }
            }
        }

        return $a;
    }
    /**
     * @param array $definition
     *
     * @return Finder
     */
    protected function find(array $definition)
    {
        $definition += ['depth' => 0];

        $f = new Finder();

        $f->in(sprintf('%s/%s', $definition['rootDir'], $definition['dir']));
        $f->depth($definition['depth']);

        $this->ensureArrayKeyIsArray($definition, 'ignores');
        $this->ensureArrayKeyIsArray($definition, 'only');

        foreach ($definition['ignores'] as $item) {
            $f->notName($item);
        }
        foreach ($definition['only'] as $item) {
            $f->name($item);
        }

        return $f->files();
    }
    /**
     * @param string $template
     * @param array  $params
     * @param array  $options
     *
     * @return string
     *
     * @throws Exception
     */
    protected function render($template, array $params, array $options = [])
    {
        $options += ['engine' => 'default'];

        switch ($options['engine']) {
            case 'twig':
                $content = $this->getTwig()->render($template, $params);
                break;
            default:
            case 'default':
                $templateDir = $this->getConfig()->get('gentests_template_dir');

                if (null === $templateDir) {
                    throw $this->createRequiredException('Gentests template dir required');
                }

                $this->getFilesystemService()->checkReadableDirectory($templateDir);

                $content = $this->getFilesystemService()->readFile(sprintf('%s/%s', $templateDir, $template));
                $matches = null;

                if (0 < preg_match_all('/\{\{\s*([^\}]+)\s*\}\}/', $content, $matches)) {
                    foreach ($matches[1] as $i => $match) {
                        $content = str_replace($matches[0][$i], isset($params[$match]) ? $params[$match] : null, $content);
                    }
                }
                break;
        }

        return $content;
    }
}
