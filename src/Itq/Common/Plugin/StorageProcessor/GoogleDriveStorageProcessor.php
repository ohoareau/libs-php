<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\StorageProcessor;

use Itq\Common\Plugin\Storage;
use Itq\Common\Plugin\StorageProcessor\Base\AbstractStorageProcessor;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class GoogleDriveStorageProcessor extends AbstractStorageProcessor
{
    /**
     * @return string|array
     */
    public function getType()
    {
        return 'googledrive';
    }
    /**
     * @param array $params
     *
     * @return Definition
     */
    public function build(array $params)
    {
        return new Definition(Storage\GoogleDriveStorage::class, [new Reference('app.google'), new Reference('app.job'), $params['root']]);
    }
}
