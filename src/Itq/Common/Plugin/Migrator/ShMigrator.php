<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\Migrator;

use Exception;
use Itq\Common\Traits;
use Itq\Common\Service;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class ShMigrator extends Base\AbstractMigrator
{
    use Traits\ServiceAware\SystemServiceAwareTrait;
    use Traits\ServiceAware\FilesystemServiceAwareTrait;
    /**
     * @param Service\SystemService     $systemService
     * @param Service\FilesystemService $filesystemService
     */
    public function __construct(
        Service\SystemService $systemService,
        Service\FilesystemService $filesystemService
    ) {
        $this->setSystemService($systemService);
        $this->setFilesystemService($filesystemService);
    }
    /**
     * @param string $path
     * @param array  $options
     *
     * @return $this
     *
     * @throws Exception
     */
    public function upgrade($path, $options = [])
    {
        $this->getFilesystemService()->checkReadableFile($path);
        $this->getSystemService()->executeInForeground(sprintf('sh %s 2>&1', $path), $options);

        return $this;
    }
}
