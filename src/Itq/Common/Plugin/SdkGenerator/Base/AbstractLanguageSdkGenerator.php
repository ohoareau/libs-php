<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\SdkGenerator\Base;

use Itq\Common\Traits;
use Itq\Common\Service;
use Psr\Log\LoggerInterface;
use Symfony\Component\Finder\Finder;
use Itq\Common\SdkDescriptorInterface;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author itiQiti Dev Team <cto@itiqiti.com>
 */
abstract class AbstractLanguageSdkGenerator extends AbstractSdkGenerator
{
    use Traits\LoggerAwareTrait;
    use Traits\FilesystemAwareTrait;
    use Traits\ServiceAware\MetaDataServiceAwareTrait;
    use Traits\ServiceAware\TemplateServiceAwareTrait;
    use Traits\ServiceAware\CodeGeneratorServiceAwareTrait;
    /**
     * @param Filesystem                   $filesystem
     * @param LoggerInterface              $logger
     * @param Service\TemplateService      $templateService
     * @param Service\MetaDataService      $metaDataService
     * @param Service\CodeGeneratorService $codeGeneratorService
     * @param array                        $config
     */
    public function __construct(
        Filesystem $filesystem,
        LoggerInterface $logger,
        Service\TemplateService $templateService,
        Service\MetaDataService $metaDataService,
        Service\CodeGeneratorService $codeGeneratorService,
        array $config = []
    ) {
        $this->setLogger($logger);
        $this->setConfig($config);
        $this->setFilesystem($filesystem);
        $this->setTemplateService($templateService);
        $this->setMetaDataService($metaDataService);
        $this->setCodeGeneratorService($codeGeneratorService);
    }
    /**
     * @param array $config
     *
     * @return $this
     */
    public function setConfig($config)
    {
        return $this->setParameter('config', $config);
    }
    /**
     * @return array|null
     *
     * @throws \Exception
     */
    public function getConfig()
    {
        return $this->getParameter('config');
    }
    /**
     * @param SdkDescriptorInterface $descriptor
     * @param array                  $options
     *
     * @return $this
     */
    public function describe(SdkDescriptorInterface $descriptor, array $options = [])
    {
        return $this;
    }
    /**
     * @param SdkDescriptorInterface $sdkDescriptor
     * @param array                  $options
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function generate(SdkDescriptorInterface $sdkDescriptor, array $options = [])
    {
        $ctx = (object) [
            'root'              => dirname(dirname(dirname((new \ReflectionClass($this))->getFileName()))),
            'exceptions'        => [],
            'options'           => $options,
            'types'             => [],
            'generatedPrepared' => false,
        ];

        $this->prepareGeneration($sdkDescriptor, $ctx);
        $this->processGeneration($sdkDescriptor, $ctx);
        $this->finishGeneration($sdkDescriptor, $ctx);

        return null;
    }
    /**
     * @param SdkDescriptorInterface $sdkDescriptor
     * @param object                 $ctx
     *
     * @return void
     *
     * @throws \Exception
     */
    protected function prepareGeneration(SdkDescriptorInterface $sdkDescriptor, $ctx)
    {
        unset($options);

        $path = $sdkDescriptor->getTargetPath();

        $this->log(sprintf("create '%s' directory", $path), 'info');
        $this->getFilesystem()->mkdir($path);

        if (null === $this->getConfig()) {
            throw $this->createRequiredException(
                "No SDK configuration for target '%s'",
                $sdkDescriptor->getTargetName()
            );
        }

        $ctx->generationPrepared = true;
    }
    /**
     * @param SdkDescriptorInterface $sdkDescriptor
     * @param object                 $ctx
     *
     * @return void
     */
    protected function processGeneration(SdkDescriptorInterface $sdkDescriptor, $ctx)
    {
        $this->generateStatics($sdkDescriptor, $ctx, $ctx->root.sprintf('/Resources/views/sdks/%s/root', $sdkDescriptor->getTargetName()), sprintf('@sdks/%s/root/', $sdkDescriptor->getTargetName()));

        if ($this->getConfigValue('customTemplateDir')) {
            $this->generateStatics($sdkDescriptor, $ctx, $this->getConfigValue('customTemplateDir').'/root', null);
        }

        if ($this->getConfigValue('custom_template_dir')) {
            $this->generateStatics($sdkDescriptor, $ctx, $this->getConfigValue('custom_template_dir').'/root', null);
        }

        $this->generateDynamics($sdkDescriptor, $ctx);
        $this->generateConfigs($sdkDescriptor, $ctx);

        if (is_array($ctx->exceptions) && count($ctx->exceptions)) {
            throw array_shift($ctx->exceptions);
        }
    }
    /**
     * @param SdkDescriptorInterface $sdkDescriptor
     * @param object                 $ctx
     *
     * @return void
     */
    protected function finishGeneration(SdkDescriptorInterface $sdkDescriptor, $ctx)
    {
    }
    /**
     * @param SdkDescriptorInterface $sdkDescriptor
     * @param object                 $ctx
     * @param string                 $sourceDir
     * @param string                 $twigPrefix
     *
     * @return $this
     */
    protected function generateStatics(SdkDescriptorInterface $sdkDescriptor, $ctx, $sourceDir, $twigPrefix)
    {
        if (!is_dir($sourceDir)) {
            return $this;
        }

        $f = new Finder();
        $f->ignoreDotFiles(false);

        $exceptions = [];

        foreach ($f->in($sourceDir) as $file) {
            /** @var SplFileInfo $file */
            $realPath = $sdkDescriptor->getTargetPath().'/'.$file->getRelativePathname();
            if (false !== strpos($realPath, '{')) {
                $realPath = $this->getTemplateService()->render('ItqBundle::expression.txt.twig', ['_expression' => $realPath]);
            }
            if ($file->isDir()) {
                $this->getFilesystem()->mkdir($realPath);
            } else {
                try {
                    if ('raw' === strtolower($file->getExtension())) {
                        $this->getFilesystem()->dumpFile(preg_replace('/\.raw/', '', $realPath), $file->getContents());
                    } else {
                        $content = 'twig' === strtolower($file->getExtension()) && null !== $twigPrefix ? $this->getTemplateService()->render($twigPrefix.$file->getRelativePathname(), $ctx->options) : $file->getContents();
                        $this->getFilesystem()->dumpFile(preg_replace('/\.twig$/', '', $realPath), $content);
                    }
                } catch (\Exception $e) {
                    $exceptions[] = $e;
                }
            }
            if ('bin/' === substr(str_replace(DIRECTORY_SEPARATOR, '/', $file->getRelativePathname()), 0, 4)) {
                $this->getFilesystem()->chmod($realPath, 0755);
            }
        }

        unset($exceptions);

        return $this;
    }
    /**
     * @param SdkDescriptorInterface $sdkDescriptor
     * @param object                 $ctx
     *
     * @return void
     */
    protected function generateDynamics(SdkDescriptorInterface $sdkDescriptor, $ctx)
    {
        $this->generateDynamicServices($sdkDescriptor, $ctx);
    }
    /**
     * @param SdkDescriptorInterface $sdkDescriptor
     * @param object                 $ctx
     *
     * @return void
     */
    protected function generateDynamicServices(SdkDescriptorInterface $sdkDescriptor, $ctx)
    {
        foreach ($this->getMetaDataService()->getSdkServices($sdkDescriptor->getTargetName()) as $serviceName => $service) {
            $this->generateService($sdkDescriptor, $ctx, $serviceName, $service);
            $this->generateServiceTest($sdkDescriptor, $ctx, $serviceName, $service);
        }
    }
    /**
     * @param SdkDescriptorInterface $sdkDescriptor
     * @param object                 $ctx
     * @param string                 $serviceName
     * @param array                  $service
     *
     * @return void
     *
     * @throws \Exception
     */
    protected function generateService(SdkDescriptorInterface $sdkDescriptor, $ctx, $serviceName, array $service)
    {
    }
    /**
     * @param SdkDescriptorInterface $sdkDescriptor
     * @param object                 $ctx
     * @param string                 $serviceName
     * @param array                  $service
     *
     * @return void
     *
     * @throws \Exception
     */
    protected function generateServiceTest(SdkDescriptorInterface $sdkDescriptor, $ctx, $serviceName, array $service)
    {
    }
    /**
     * @param SdkDescriptorInterface $sdkDescriptor
     * @param object                 $ctx
     */
    protected function generateConfigs(SdkDescriptorInterface $sdkDescriptor, $ctx)
    {
    }
    /**
     * @param string $key
     * @param mixed  $defaultValue
     *
     * @return mixed
     */
    protected function getConfigValue($key, $defaultValue = null)
    {
        $config = $this->getConfig();

        if (!isset($config[$key])) {
            return $defaultValue;
        }

        return $config[$key];
    }
}
