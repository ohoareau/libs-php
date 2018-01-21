<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\DocGenerator\Base;

use Exception;
use ReflectionClass;
use Itq\Common\Traits;
use Itq\Common\Service;
use Psr\Log\LoggerInterface;
use Symfony\Component\Finder\Finder;
use Itq\Common\DocDescriptorInterface;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author itiQiti Dev Team <cto@itiqiti.com>
 */
abstract class AbstractDirectoryDocGenerator extends AbstractDocGenerator
{
    use Traits\LoggerAwareTrait;
    use Traits\FilesystemAwareTrait;
    use Traits\ServiceAware\TemplateServiceAwareTrait;
    use Traits\ParameterAware\ConfigParameterAwareTrait;
    /**
     * @param Filesystem              $filesystem
     * @param LoggerInterface         $logger
     * @param Service\TemplateService $templateService
     * @param array                   $config
     */
    public function __construct(
        Filesystem $filesystem,
        LoggerInterface $logger,
        Service\TemplateService $templateService,
        array $config = []
    ) {
        $this->setLogger($logger);
        $this->setConfig($config);
        $this->setFilesystem($filesystem);
        $this->setTemplateService($templateService);
    }
    /**
     * @param DocDescriptorInterface $descriptor
     * @param array                  $options
     *
     * @return $this
     */
    public function describe(DocDescriptorInterface $descriptor, array $options = [])
    {
        return $this;
    }
    /**
     * @param DocDescriptorInterface $docDescriptor
     * @param array                  $options
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function generate(DocDescriptorInterface $docDescriptor, array $options = [])
    {
        $ctx = (object) [
            'root'              => dirname(dirname(dirname((new ReflectionClass($this))->getFileName()))),
            'exceptions'        => [],
            'options'           => $options,
            'types'             => [],
            'generatedPrepared' => false,
        ];

        $this->prepareGeneration($docDescriptor, $ctx);
        $this->processGeneration($docDescriptor, $ctx);
        $this->finishGeneration($docDescriptor, $ctx);

        return null;
    }
    /**
     * @param DocDescriptorInterface $docDescriptor
     * @param object                 $ctx
     *
     * @return void
     *
     * @throws Exception
     */
    protected function prepareGeneration(DocDescriptorInterface $docDescriptor, $ctx)
    {
        $path = $docDescriptor->getTargetPath();

        $this->log(sprintf("create '%s' directory", $path), 'info');
        $this->getFilesystem()->mkdir($path);

        if (null === $this->getConfig()) {
            throw $this->createRequiredException(
                "No DOC configuration for target '%s'",
                $docDescriptor->getTargetName()
            );
        }

        $ctx->generationPrepared = true;
    }
    /**
     * @param DocDescriptorInterface $docDescriptor
     * @param object                 $ctx
     *
     * @return void
     */
    protected function processGeneration(DocDescriptorInterface $docDescriptor, $ctx)
    {
        $this->generateStatics($docDescriptor, $ctx, $ctx->root.sprintf('/Resources/views/docs/%s/root', $docDescriptor->getTargetName()), sprintf('@docs/%s/root/', $docDescriptor->getTargetName()));

        if ($this->getConfigValue('customTemplateDir')) {
            $this->generateStatics($docDescriptor, $ctx, $this->getConfigValue('customTemplateDir').'/root', null);
        }

        if ($this->getConfigValue('custom_template_dir')) {
            $this->generateStatics($docDescriptor, $ctx, $this->getConfigValue('custom_template_dir').'/root', null);
        }

        $this->generateDynamics($docDescriptor, $ctx);

        if (is_array($ctx->exceptions) && count($ctx->exceptions)) {
            throw array_shift($ctx->exceptions);
        }
    }
    /**
     * @param DocDescriptorInterface $docDescriptor
     * @param object                 $ctx
     *
     * @return void
     */
    protected function finishGeneration(DocDescriptorInterface $docDescriptor, $ctx)
    {
    }
    /**
     * @param DocDescriptorInterface $docDescriptor
     * @param object                 $ctx
     * @param string                 $sourceDir
     * @param string                 $twigPrefix
     *
     * @return $this
     */
    protected function generateStatics(DocDescriptorInterface $docDescriptor, $ctx, $sourceDir, $twigPrefix)
    {
        if (!is_dir($sourceDir)) {
            return $this;
        }

        $f = new Finder();
        $f->ignoreDotFiles(false);

        $exceptions = [];

        foreach ($f->in($sourceDir) as $file) {
            /** @var SplFileInfo $file */
            $realPath = $docDescriptor->getTargetPath().'/'.$file->getRelativePathname();
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
                } catch (Exception $e) {
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
     * @param DocDescriptorInterface $docDescriptor
     * @param object                 $ctx
     *
     * @return void
     */
    protected function generateDynamics(DocDescriptorInterface $docDescriptor, $ctx)
    {
    }
}
