<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\PollableSourceType;

use Exception;
use Itq\Common\Plugin\PollableSource\MemoryPollableSource;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class MemoryPollableSourceType extends Base\AbstractPollableSourceType
{
    /**
     * @param array $definition
     * @param array $options
     *
     * @return MemoryPollableSource
     *
     * @throws Exception
     */
    public function create(array $definition = [], array $options = [])
    {
        $definition += ['in' => [], 'out' => [], 'waitForReply' => false, 'virtualTimeout' => null];

        return new MemoryPollableSource($definition['in'], $definition['out'], $definition['waitForReply'], $definition['virtualTimeout']);
    }
}
