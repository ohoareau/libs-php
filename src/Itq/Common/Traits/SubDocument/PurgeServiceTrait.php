<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Traits\SubDocument;

/**
 * Purge service trait.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait PurgeServiceTrait
{
    /**
     * Purge all the documents matching the specified criteria.
     *
     * @param string $parentId
     * @param array  $criteria
     * @param array  $options
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function purge($parentId, $criteria = [], $options = [])
    {
        if ([] !== $criteria) {
            throw $this->createUnexpectedException('Purging sub documents with criteria not supported');
        }

        unset($criteria);

        $this->savePurge($parentId, [], $options);
        $this->event($parentId, 'purged');

        return $this;
    }
}
