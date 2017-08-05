<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <cto@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Service;

use Itq\Common\Traits;
use Itq\Common\Service;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Filesystem Service.
 *
 * @author Olivier Hoareau <olivier@itiqiti.com>
 */
class FilesystemService
{
    use Traits\ServiceTrait;
    use Traits\ServiceAware\SystemServiceAwareTrait;
    /**
     * @param Service\SystemService $systemService
     */
    public function __construct(Service\SystemService $systemService)
    {
        $this->setSystemService($systemService);
    }
    /**
     * @param string $content
     * @param string $suffix
     *
     * @return string
     *
     * @throws \Exception
     */
    public function createTempFile($content, $suffix = null)
    {
        if (null !== $suffix) {
            if (false !== strpos($suffix, '~')) {
                throw $this->createMalformedException('Temp file suffix are not allowed to have "~" chars');
            }
        }

        $internalPath = tempnam($this->getSystemService()->getTempDirectory(), 'cl-fs-'.md5(__DIR__).'-');
        $realPath     = $internalPath.'~'.$suffix;

        $this->writeFile($realPath, $content);

        return $realPath;
    }
    /**
     * @param string|array $path
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function cleanTempFile($path)
    {
        if (is_array($path)) {
            foreach($path as $p) {
                $this->cleanTempFile($p);
            }

            return $this;
        }

        if (false === strpos($path, '~')) {
            throw $this->createMalformedException('The temp file name is not supported');
        }

        list($internalPath) = explode('~', $path);

        $this
            ->deleteFile($path)
            ->deleteFile($internalPath)
        ;

        return $this;
    }
    /**
     * @param string $path
     *
     * @return $this
     */
    public function deleteFile($path)
    {
        if ($this->isReadableFile($path)) {
            unlink($path);
        }

        return $this;
    }
    /**
     * @param string $path
     * @param string $content
     *
     * @return $this
     */
    public function writeFile($path, $content)
    {
        file_put_contents($path, $content);

        return $this;
    }
    /**
     * @param string $path
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function ensureDirectory($path)
    {
        if ($this->isReadableDirectory($path)) {
            return $this;
        }

        if (!mkdir($path, 0777, true)) {
            throw $this->createFailedException("Unable to create directory '%s'", $path);
        }

        return $this;
    }
    /**
     * @param string $path
     *
     * @return string
     */
    public function readFile($path)
    {
        $this->checkReadableFile($path);

        return file_get_contents($path);
    }
    /**
     * @param string $path
     * @param mixed  $defaultValue
     *
     * @return mixed
     */
    public function readFileIfExist($path, $defaultValue = null)
    {
        if (!$this->isReadableFile($path)) {
            return $defaultValue;
        }

        return $this->readFile($path);
    }
    /**
     * @param string $path
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function checkReadableFile($path)
    {
        if ($this->isReadableFile($path)) {
            return $this;
        }

        throw $this->createRequiredException(
            "File '%s' is not readable (permissions problem or file missing)",
            $path
        );
    }
    /**
     * @param string $path
     *
     * @return $this
     *
     * @throws \Exception
     */
    public function checkReadableDirectory($path)
    {
        if ($this->isReadableDirectory($path)) {
            return $this;
        }

        throw $this->createRequiredException(
            "Directory '%s' is not readable (permissions problem or directory missing)",
            $path
        );
    }
    /**
     * @param string $path
     *
     * @return bool
     */
    public function isReadableFile($path)
    {
        return true === is_file($path);
    }
    /**
     * @param string $path
     *
     * @return bool
     */
    public function isReadableDirectory($path)
    {
        return true === is_dir($path);
    }
    /**
     * @param string $path
     * @return string
     */
    public function readAndDeleteFile($path)
    {
        $content = $this->readFile($path);

        $this->deleteFile($path);

        return $content;
    }
    /**
     * @param string $path
     * @param string $extension
     *
     * @return Finder|SplFileInfo[]
     */
    public function findFilesByExtension($path, $extension)
    {
        return (new Finder())->files()->in($path.'/*')->name('*.'.$extension);
    }
}
