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
        $bundles = $this->registerCommonBundles();

        if ($this->isDebugEnvironment()) {
            $bundles = array_merge($bundles, $this->registerDebugBundles());
        }

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
            if (isset($envs[$k]) && $envs[$k]) {
                $suffix .= '-'.$v;
            }
        }

        unset($envs);

        return sprintf('%s/cache/%s/%s', $this->rootDir, $this->getEnvironment(), $suffix);
    }
    /**
     * @param LoaderInterface $loader
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(sprintf('%s/config/config_%s.yml', $this->getRootDir(), $this->getEnvironment()));

        $envs = $this->getEnvParameters();

        $loader->load(
            function(Container $container) use($envs) {
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
}
