<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin;

use Symfony\Component\DependencyInjection\Definition;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
interface StorageProcessorInterface
{
    /**
     * @return string|array
     */
    public function getType();
    /**
     * @param array $params
     *
     * @return Definition
     */
    public function build(array $params);
}
