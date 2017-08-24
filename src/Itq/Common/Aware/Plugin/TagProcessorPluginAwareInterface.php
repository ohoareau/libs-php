<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Aware\Plugin;

use Itq\Common\Plugin;

/**
 * Aware interface trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
interface TagProcessorPluginAwareInterface
{
    /**
     * @param Plugin\TagProcessorInterface $tagProcessor
     */
    public function addTagProcessor(Plugin\TagProcessorInterface $tagProcessor);

    /**
     * @return Plugin\TagProcessorInterface[]
     */
    public function getTagProcessors();
}
