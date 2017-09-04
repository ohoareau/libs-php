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

/**
 * Pollable Source Type Interface.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
interface PollableSourceTypeInterface
{
    /**
     * @param array $definition
     * @param array $options
     *
     * @return PollableSourceInterface
     */
    public function create(array $definition = [], array $options = []);
}
