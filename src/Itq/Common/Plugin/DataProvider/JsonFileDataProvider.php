<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\DataProvider;

use Itq\Common\Traits;
use Itq\Common\Service;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class JsonFileDataProvider extends ArrayDataProvider
{
    use Traits\ServiceAware\JsonServiceAwareTrait;
    use Traits\ServiceAware\FilesystemServiceAwareTrait;
    /**
     * @param Service\JsonService       $jsonService
     * @param Service\FilesystemService $filesystemService
     * @param string                    $path
     * @param string|null               $key
     */
    public function __construct(
        Service\JsonService $jsonService,
        Service\FilesystemService $filesystemService,
        $path,
        $key = null
    ) {
        $this->setJsonService($jsonService);
        $this->setFilesystemService($filesystemService);

        parent::__construct($this->loadData($path, $key));
    }
    /**
     * @param array $options
     *
     * @return array
     */
    protected function loadData($path, $key)
    {
        $data = [];

        if (null !== $path && $this->getFilesystemService()->isReadableFile($path)) {
            $data = $this->getJsonService()->unserialize($this->getFilesystemService()->readFile($path));
        }
        if (null !== $key) {
            $kk = explode('.', $key);
            $lastKey = array_pop($kk);
            foreach ($kk as $kkk) {
                if (!isset($data[$kkk])) {
                    $data = [];

                    break;
                }
                $data = $data[$kkk];
            }
            $data = isset($data[$lastKey]) ? $data[$lastKey] : [];
        }

        return is_array($data) ? $data : [];
    }
}
