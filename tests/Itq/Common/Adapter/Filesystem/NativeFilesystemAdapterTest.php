<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common\Adapter\Filesystem;

use Itq\Common\Tests\Base\AbstractTestCase;
use Itq\Common\Adapter\Filesystem\NativeFilesystemAdapter;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group adapters
 * @group adapters/filesystem
 * @group adapters/filesystem/native
 */
class NativeFilesystemAdapterTest extends AbstractTestCase
{
    /**
     * @return NativeFilesystemAdapter
     */
    public function a()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::o();
    }
    /**
     * @group integ
     */
    public function testIsFile()
    {
        $this->assertTrue($this->a()->isFile(__FILE__));
        $this->assertFalse($this->a()->isFile(__FILE__.'.unknown'));
    }
    /**
     * @group integ
     */
    public function testIsDir()
    {
        $this->assertTrue($this->a()->isDir(__DIR__));
        $this->assertFalse($this->a()->isDir(__DIR__.'.unknown'));
    }
    /**
     * @group integ
     */
    public function testFileContents()
    {
        $path = tempnam(sys_get_temp_dir(), 'common-test-');
        $value = rand(1, 10000)*rand(1, 1000)+rand(0, 100)+rand(1, 10)*rand(1, 2);
        $this->assertEquals(strlen($value), $this->a()->filePutContents($path, $value));
        $this->assertEquals($value, $this->a()->fileGetContents($path));
        $this->assertTrue($this->a()->isFile($path));
        $this->a()->unlink($path);
        $this->assertFalse($this->a()->isFile($path));
    }
    /**
     * @group integ
     */
    public function testDir()
    {
        $path = tempnam(sys_get_temp_dir(), 'common-test-2-');
        $this->assertFalse($this->a()->isDir($path.'-dir'));
        $this->assertTrue($this->a()->mkdir($path.'-dir'));
        $this->assertTrue($this->a()->isDir($path.'-dir'));
        $this->assertTrue($this->a()->rmdir($path.'-dir'));
        $this->a()->unlink($path);
    }
    /**
     * @group integ
     */
    public function testTempnam()
    {
        $path = $this->a()->tempnam(sys_get_temp_dir(), 'common-test-3-');
        $this->assertTrue(is_file($path));
        $this->assertEquals(0, strpos(realpath($path), realpath(sys_get_temp_dir().DIRECTORY_SEPARATOR.'common-test-3-')));
        unlink($path);
        $this->assertFalse(is_file($path));
    }
}
