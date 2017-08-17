<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Traits\Document;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait PurgeServiceTrait
{
    /**
     * Purge all the documents matching the specified criteria.
     *
     * @param array $criteria
     * @param array $options
     *
     * @return mixed
     */
    public function purge($criteria = [], $options = [])
    {
        $this->savePurge($criteria, $options);
        $this->event('purged');

        return $this;
    }
}
