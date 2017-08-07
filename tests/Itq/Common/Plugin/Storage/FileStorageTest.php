<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common\Plugin\Storage;

use Itq\Common\Plugin\Storage\FileStorage;
use Itq\Common\Tests\Plugin\Base\AbstractPluginTestCase;

use Symfony\Component\Filesystem\Filesystem;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group plugins
 * @group plugins/storage
 * @group plugins/storage/file
 */
class FileStorageTest extends AbstractPluginTestCase
{
    /**
     * @var string
     */
    protected $tmpDir;
    /**
     * @return FileStorage
     */
    public function s()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::p();
    }
    /**
     * @return array
     */
    public function constructor()
    {
        return [
            $this->tmpDir = tempnam(sys_get_temp_dir(), 'test-'.uniqid()),
            $this->mock('fs', Filesystem::class),
        ];
    }
    /**
     * @group unit
     */
    public function testSet()
    {
        $this->mock('fs')->expects($this->once())->method('mkdir')->with($this->tmpDir.'/a');
        $this->mock('fs')->expects($this->once())->method('dumpFile')->with($this->tmpDir.'/a/b.txt', 'xyz');
        $this->s()->set('a/b.txt', 'xyz');
    }
}
