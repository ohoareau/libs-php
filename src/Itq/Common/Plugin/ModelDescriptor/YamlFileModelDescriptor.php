<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\ModelDescriptor;

use Itq\Common\Traits;
use Itq\Common\Service;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class YamlFileModelDescriptor extends Base\AbstractModelDescriptor
{
    use Traits\ServiceAware\YamlServiceAwareTrait;
    use Traits\ServiceAware\FilesystemServiceAwareTrait;
    /**
     * @param Service\FilesystemService $filesystemService
     * @param Service\YamlService       $yamlService
     * @param array                     $dirs
     */
    public function __construct(Service\FilesystemService $filesystemService, Service\YamlService $yamlService, array $dirs = [])
    {
        $this->setFilesystemService($filesystemService);
        $this->setYamlService($yamlService);
    }
    /**
     * @param string $dir
     *
     * @return $this
     */
    public function addDirectory($dir)
    {
        return $this->pushArrayParameterItem('dirs', (string) $dir);
    }
    /**
     * @return string[]
     */
    public function getDirectories()
    {
        return $this->getArrayParameter('dirs');
    }
    /**
     * @param string $id
     * @param array  $options
     *
     * @return array|null
     *
     * @throws \Exception
     */
    public function describe($id, array $options = [])
    {
        $data = null;

        foreach ($this->getDirectories() as $dir) {
            $yamlFile = sprintf('%s/%s.yml', $dir, $id);
            if (!$this->getFilesystemService()->isReadableFile($yamlFile)) {
                continue;
            }
            $data = $this->getYamlService()->unserialize($this->getFilesystemService()->readFile($yamlFile));
            break;
        }

        if (null === $data) {
            throw $this->createNotFoundException('model.description.unknown', ['id' => $id]);
        }

        return $data;
    }
}
