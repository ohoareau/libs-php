<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Service;

use Itq\Common\Traits;
use Itq\Common\Service;

/**
 * Attachment Service.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class AttachmentService
{
    use Traits\ServiceTrait;
    use Traits\ServiceAware\GeneratorServiceAwareTrait;
    use Traits\ServiceAware\FilesystemServiceAwareTrait;

    /**
     * @param Service\GeneratorService  $generatorService
     * @param Service\FilesystemService $filesystemService
     */
    public function __construct(Service\GeneratorService $generatorService, Service\FilesystemService $filesystemService)
    {
        $this->setGeneratorService($generatorService);
        $this->setFilesystemService($filesystemService);
    }

    /**
     * @param array $definition
     * @param array $params
     * @param array $options
     *
     * @return array
     *
     * @throws \Exception
     */
    public function build(array $definition, array $params = [], array $options = [])
    {
        if (isset($definition['path'])) {
            return $this->buildFromPath($definition, $params, $options);
        }

        if (isset($definition['generator'])) {
            return $this->buildFromGenerator($definition, $params, $options);
        }

        if (isset($definition['content'])) {
            return $this->buildFromContent($definition, $options);
        }

        if (isset($definition['base64_content'])) {
            return $this->buildFromBase64Content($definition, $options);
        }

        throw $this->createRequiredException('Missing source for attachment');
    }
    /**
     * @param array $definition
     * @param array $params
     * @param array $options
     *
     * @return array
     *
     * @throws \Exception
     */
    protected function buildFromPath(array $definition, array $params = [], array $options = [])
    {
        unset($params);
        unset($options);

//        if ($this->getFilesystemService()->isReadableFile($definition['path'])) {
//            throw $this->createNotFoundException("File '%s' not found", $definition['path']);
//        }

        return $this->package(
            $definition,
            base64_encode($this->getFilesystemService()->readFile($definition['path']))
        );
    }
    /**
     * @param array $definition
     * @param array $params
     * @param array $options
     *
     * @return array
     */
    protected function buildFromGenerator(array $definition, array $params = [], array $options = [])
    {
        return $this->package(
            $definition,
            base64_encode($this->getGeneratorService()->generate($definition['generator'], $params, $options))
        );
    }
    /**
     * @param array $definition
     * @param array $options
     *
     * @return array
     */
    protected function buildFromContent(array $definition, array $options = [])
    {
        unset($options);

        return $this->package(
            $definition,
            base64_encode($definition['content'])
        );
    }
    /**
     * @param array $definition
     * @param array $options
     *
     * @return array
     */
    protected function buildFromBase64Content(array $definition, array $options = [])
    {
        unset($options);

        return $this->package(
            $definition,
            $definition['base64_content']
        );
    }
    /**
     * @param array $definition
     * @param mixed $content
     *
     * @return array
     */
    protected function package(array $definition, $content)
    {
        return [
            'name' => $definition['name'],
            'type' => $this->getMimeTypeFromFileName($definition['name']),
            'content' => $content,
        ];
    }
    /**
     * @param string $fileName
     *
     * @return string
     */
    protected function getMimeTypeFromFileName($fileName)
    {
        list($ext) = array_slice(explode('.', $fileName), -1);

        $exts = [
            'pdf' => 'application/pdf',
            'jpg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            '.'   => 'application/octet-stream',
        ];

        if (!isset($exts[$ext])) {
            return $exts['.'];
        }

        return $exts[$ext];
    }
}
