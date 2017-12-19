<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Bundle\ItqBundle\Base;

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Exception;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractAppKernel extends Kernel
{
    /**
     * @return BundleInterface[]
     */
    public function registerBundles()
    {
        $bundles          = $this->registerCommonBundles();
        $lastCommonBundle = array_pop($bundles);

        foreach ($this->registerItqBundles() as $bundleName => $bundleOptions) {
            if (is_numeric($bundleName)) {
                $bundleName    = $bundleOptions;
                $bundleOptions = null;
            }
            $bundles[] = $this->instantiateItqBundle(
                $bundleName,
                is_array($bundleOptions) ? $bundleOptions : []
            );
        }

        if ($this->isDebugEnvironment()) {
            $bundles = array_merge($bundles, $this->registerDebugBundles());
        }

        $bundles[] = $lastCommonBundle;

        return $bundles;
    }
    /**
     * @return string
     */
    public function getCacheDir()
    {
        $vars   = array_fill_keys($this->getCacheDirVariables(), null);
        $envs   = $this->getEnvParameters() + $vars;
        $suffix = null;

        foreach (array_keys($vars) as $k => $v) {
            if (isset($envs[$v]) && $envs[$v]) {
                $suffix .= '-'.$envs[$v];
            }
        }

        unset($envs);

        return sprintf('%s/cache/%s-%s', $this->rootDir, $this->getEnvironment(), $suffix);
    }
    /**
     * @param LoaderInterface $loader
     *
     * @throws Exception
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(sprintf('%s/config/config_%s.yml', $this->getRootDir(), $this->getEnvironment()));

        $envs = $this->getEnvParameters();

        $loader->load(
            function (Container $container) use ($envs) {
                $container->getParameterBag()->add($envs);
            }
        );
    }
    /**
     * @return BundleInterface[]
     */
    abstract protected function registerCommonBundles();
    /**
     * @return BundleInterface[]
     */
    protected function registerDebugBundles()
    {
        return [];
    }
    /**
     * @return bool
     */
    protected function isDebugEnvironment()
    {
        return in_array($this->getEnvironment(), ['dev', 'test']);
    }
    /**
     * @return array
     */
    protected function getCacheDirVariables()
    {
        return [];
    }
    /**
     * @return string[]
     */
    protected function registerItqBundles()
    {
        return [];
    }
    /**
     * @param string $name
     * @param array  $options
     *
     * @return BundleInterface
     */
    protected function instantiateItqBundle($name, array $options = [])
    {
        $className = 'common' === $name ? 'ItqBundle' : sprintf('Itq%sBundle', ucfirst($name));
        $class     = sprintf('Itq\\Bundle\\%s\\%s', $className, $className);

        unset($options);

        return new $class();
    }
}
