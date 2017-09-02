<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Adapter\Filesystem;

/**
 * Native Filesystem Adapter.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class NativeFilesystemAdapter extends Base\AbstractFilesystemAdapter
{
    /**
     * @param string      $dir
     * @param null|string $prefix
     *
     * @return string
     */
    public function tempnam($dir, $prefix = null)
    {
        return tempnam($dir, $prefix);
    }
    /**
     * @param string $path
     *
     * @return bool
     */
    public function unlink($path)
    {
        return unlink($path);
    }
    /**
     * @param string        $path
     * @param string        $content
     * @param int|null      $flag
     * @param resource|null $context
     *
     * @return int
     */
    public function filePutContents($path, $content, $flag = null, $context = null)
    {
        return file_put_contents($path, $content, $flag, $context);
    }
    /**
     * @param string        $path
     * @param int|bool|null $flags
     * @param resource|null $context
     *
     * @return string
     */
    public function fileGetContents($path, $flags = null, $context = null)
    {
        return file_get_contents($path, $flags, $context);
    }
    /**
     * @param string $path
     * @param int    $mode
     * @param bool   $recursive
     *
     * @return bool
     */
    public function mkdir($path, $mode = 0777, $recursive = false)
    {
        return mkdir($path, $mode, $recursive);
    }
    /**
     * @param string $path
     *
     * @return bool
     */
    public function rmdir($path)
    {
        return rmdir($path);
    }
    /**
     * @param string $path
     *
     * @return bool
     */
    public function isFile($path)
    {
        return is_file($path);
    }
    /**
     * @param string $path
     *
     * @return bool
     */
    public function isDir($path)
    {
        return is_dir($path);
    }
}
