<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\PreprocessorStep;

use Exception;
use Itq\Common\PreprocessorContext;
use Itq\Common\Plugin\DataProvider;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class DataProvidersPreprocessorStep extends Base\AbstractPreprocessorStep
{
    /**
     * @param PreprocessorContext $ctx
     * @param ContainerBuilder    $container
     *
     * @throws Exception
     */
    public function execute(PreprocessorContext $ctx, ContainerBuilder $container)
    {
        if (!$container->hasParameter('__itq_data_providers')) {
            return;
        }

        $dataProviders = $container->getParameter('__itq_data_providers');
        $container->setParameter('__itq_data_providers', null);

        if (!is_array($dataProviders)) {
            $dataProviders = [];
        }

        foreach ($dataProviders as $dataProvider) {
            $this->registerDataProvider($dataProvider, $container);
        }
    }
    /**
     * @param array|mixed      $config
     * @param ContainerBuilder $container
     *
     * @throws Exception
     */
    protected function registerDataProvider($config, ContainerBuilder $container)
    {
        if (!isset($config['path'])) {
            throw $this->createRequiredException("Missing 'path' for data provider %s", json_encode($config));
        }
        if (!isset($config['type'])) {
            throw $this->createRequiredException("Missing 'type' for data provider %s", json_encode($config));
        }
        if (!is_file($config['path'])) {
            if (isset($config['silent']) && true === $config['silent']) {
                return;
            }

            throw $this->createNotFoundException("Missing data file '%s'", $config['path']);
        }

        $data = json_decode(file_get_contents($config['path']), true);
        $key  = isset($config['key']) ? $config['key'] : null;
        $data = isset($key) ? (isset($data[$key]) ? $data[$key] : []) : $data;
        $data = is_array($data) ? $data : [];
        $d    = new Definition(DataProvider\ArrayDataProvider::class, $data);

        $d->addTag('app.dataprovider', ['type' => $config['type']]);

        $container->setDefinition(sprintf('itq.dataprovider.generated.%s', md5($key.($key ? '@' : '').$config['path'])), $d);
    }
}
