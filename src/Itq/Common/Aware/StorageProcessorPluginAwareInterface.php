<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Aware;

use Itq\Common\Plugin;

/**
 * Aware interface trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
interface StorageProcessorPluginAwareInterface
{
    /**
     * @param Plugin\StorageProcessorInterface $processor
     */
    public function addStorageProcessor(Plugin\StorageProcessorInterface $processor);
    /**
     * @return Plugin\StorageProcessorInterface[]
     */
    public function getStorageProcessors();
    /**
     * @param string $type
     *
     * @return Plugin\StorageProcessorInterface
     */
    public function getStorageProcessor($type);
}
