<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\Storage;

use Itq\Common\Traits;
use Itq\Common\Service;
use Itq\Common\Plugin\StorageInterface;
use Itq\Common\Plugin\Base\AbstractPlugin;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class GoogleDriveStorage extends AbstractPlugin implements StorageInterface
{
    use Traits\ServiceAware\GoogleServiceAwareTrait;
    use Traits\ServiceAware\JobCreatorServiceAwareTrait;
    /**
     * @param Service\GoogleService $googleService
     * @param string                $rootDirId
     */
    public function __construct(Service\GoogleService $googleService, $rootDirId)
    {
        $this->setGoogleService($googleService);
        $this->setRootDirectoryId($rootDirId);
    }
    /**
     * @param string $id
     *
     * @return $this
     */
    public function setRootDirectoryId($id)
    {
        return $this->setParameter('rootDirectoryId', $id);
    }
    /**
     * @return string
     */
    public function getRootDirectoryId()
    {
        return $this->getParameter('rootDirectoryId');
    }
    /**
     * @param string $key
     * @param mixed  $value
     * @param array  $options
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function set($key, $value, $options = [])
    {
        $options += ['job' => true];
        if (true === $options['job'] && $this->hasJobCreatorService()) {
            return $this->getJobCreatorService()->create(
                [
                    'name'       => $key,
                    'autostart'  => true,
                    'autodelete' => true,
                    'type'       => 'google_drive',
                    'data' => [
                        'params'   => ['uri' => $key, 'rootId' => $this->getRootDirectoryId(), ],
                        'file'     => base64_encode($value),
                        'fileName' => basename($key),
                    ],
                ]
            );
        }

        $this->getGoogleService()->writeFileByPath($this->getRootDirectoryId(), $key, $value, $options);

        return $this;
    }
    /**
     * @param string $key
     * @param array  $options
     *
     * @return mixed
     */
    public function clear($key, $options = [])
    {
        return $this->getGoogleService()->removeFileByPath($this->getRootDirectoryId(), $key, $options);
    }
    /**
     * @param string $key
     * @param array  $options
     *
     * @return string
     *
     * @throws \Exception
     */
    public function get($key, $options = [])
    {
        return file_get_contents($this->getGoogleService()->getFileByPath($this->getRootDirectoryId(), $key, $options)->webContentLink);
    }
    /**
     * @param string $key
     * @param array  $options
     *
     * @return bool
     */
    public function has($key, $options = [])
    {
        return $this->getGoogleService()->hasFileByPath($this->getRootDirectoryId(), $key, $options);
    }
}
