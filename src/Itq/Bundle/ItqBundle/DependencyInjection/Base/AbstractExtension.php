<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Bundle\ItqBundle\DependencyInjection\Base;

use ReflectionClass;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractExtension extends Extension
{
    /**
     * @param array            $configs
     * @param ContainerBuilder $container
     *
     * @return void
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $defaultConfigs = $this->getDefaultConfigs();

        if (null !== $defaultConfigs && is_array($defaultConfigs) && count($defaultConfigs)) {
            $configs = array_merge($configs, $defaultConfigs);
        }

        unset($defaultConfigs);

        $config = $this->processConfiguration($this->createConfiguration(), $configs);

        $this->preApply($config, $container);
        $this->apply($config, $container);
        $this->postApply($config, $container);
        $this->finishApply($config, $container);
    }
    /**
     * @return array
     */
    protected function getDefaultConfigs()
    {
        $dir = realpath(sprintf('%s/Resources/config', dirname(dirname((new \ReflectionClass($this))->getFileName()))));

        if (!$dir) {
            return [];
        }

        $files   = $this->getDefaultConfigFiles();
        $configs = [];

        foreach ($files as $file) {
            $path = sprintf('%s/s%', $dir, $file);

            if (!is_file($path)) {
                continue;
            }

            $configs[] = Yaml::parse(file_get_contents($path));
        }

        return $configs;
    }
    /**
     * @return string[]
     */
    protected function getDefaultConfigFiles()
    {
        return ['config.yml'];
    }
    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    protected function preApply(/** @noinspection PhpUnusedParameterInspection */ array $config, ContainerBuilder $container)
    {
    }
    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    protected function apply(/** @noinspection PhpUnusedParameterInspection */ array $config, ContainerBuilder $container)
    {
    }
    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    protected function postApply(/** @noinspection PhpUnusedParameterInspection */ array $config, ContainerBuilder $container)
    {
        $dir = realpath(dirname((new ReflectionClass($this))->getFileName()).'/../Resources/config');

        if ($dir && is_dir($dir)) {
            foreach ($this->getLoadableFiles() as $file) {
                if (is_file($dir.'/'.$file)) {
                    $loader = new Loader\YamlFileLoader($container, new FileLocator($dir));
                    $loader->load($file);
                }
            }
            if (is_file($dir.'/error-mapping.yml')) {
                $this->registerErrorMappingFile($dir.'/error-mapping.yml', $container);
            }
        }
    }
    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    protected function finishApply(/** @noinspection PhpUnusedParameterInspection */ array $config, ContainerBuilder $container)
    {
    }
    /**
     * @param string           $path
     * @param ContainerBuilder $container
     *
     * @return $this
     */
    protected function registerErrorMappingFile($path, ContainerBuilder $container)
    {
        $files = $container->hasParameter('app_error_mappings') ? $container->getParameter('app_error_mappings') : [];

        if (!is_array($files)) {
            $files = [];
        }

        $files[$path] = Yaml::parse(file_get_contents($path));

        $container->setParameter('app_error_mappings', $files);

        return $this;
    }
    /**
     * @return ConfigurationInterface
     */
    protected function createConfiguration()
    {
        $class = $this->getConfigurationClass();

        return new $class();
    }
    /**
     * @return string
     */
    protected function getConfigurationClass()
    {
        return str_replace('/', '\\', dirname(str_replace('\\', '/', get_class($this)))).'\\Configuration';
    }
    /**
     * @return string[]
     */
    protected function getLoadableFiles()
    {
        return ['preprocessor.yml', 'services.yml'];
    }
}
