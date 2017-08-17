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
use Symfony\Component\DependencyInjection\Definition;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class MemoryStorageProcessor extends Base\AbstractStorageProcessor
{
    /**
     * @return string|array
     */
    public function getType()
    {
        return 'memory';
    }
    /**
     * @param array $params
     *
     * @return Definition
     */
    public function build(array $params)
    {
        return new Definition(Storage\MemoryStorage::class);
    }
}
