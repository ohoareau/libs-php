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
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class FileStorage extends Base\AbstractStorage
{
    use Traits\FilesystemAwareTrait;
    /**
     * @param string     $root
     * @param Filesystem $filesystem
     * @param array      $options
     */
    public function __construct($root, Filesystem $filesystem, array $options = [])
    {
        foreach ($options as $key => $value) {
            $this->setParameter($key, $value);
        }

        $this->setRoot($root);
        $this->setFilesystem($filesystem);
    }
    /**
     * @param string $root
     *
     * @return $this
     */
    public function setRoot($root)
    {
        return $this->setParameter('root', $root);
    }
    /**
     * @return string
     *
     * @throws \Exception
     */
    public function getRoot()
    {
        return $this->getParameter('root');
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
        $realPath       = $this->locate($key);
        $parentRealPath = dirname($realPath);

        $umask    = $this->getParameterIfExists('umask');
        $oldUmask = null;

        if (null !== $umask) {
            $oldUmask = umask($umask);
        }

        try {
            $this->getFilesystem()->mkdir($parentRealPath);
            $this->getFilesystem()->dumpFile($realPath, $value);
        } catch (\Exception $e) {
            if (null !== $oldUmask) {
                umask($oldUmask);
            }
            throw $e;
        }

        if (null !== $oldUmask) {
            umask($oldUmask);
        }

        return $this;
    }
    /**
     * @param string $key
     * @param array  $options
     *
     * @return $this
     */
    public function clear($key, $options = [])
    {
        unset($options);

        $this->getFilesystem()->remove($this->locate($key));

        return $this;
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
        $realPath = $this->locate($key);

        if (!$this->getFilesystem()->exists($realPath)) {
            throw $this->createNotFoundException("Unknown file '%s'", $realPath);
        }

        return (new SplFileInfo($realPath, $this->getRoot(), $key))->getContents();
    }
    /**
     * @param string $key
     * @param array  $options
     *
     * @return bool
     */
    public function has($key, $options = [])
    {
        return $this->getFilesystem()->exists($this->locate($key));
    }
    /**
     * @param string $relativePath
     *
     * @return string
     */
    protected function locate($relativePath)
    {
        if ('/' !== substr($relativePath, 0, 1)) {
            $relativePath = '/'.$relativePath;
        }

        return $this->getRoot().$relativePath;
    }
}
