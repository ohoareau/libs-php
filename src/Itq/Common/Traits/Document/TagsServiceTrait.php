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

use Itq\Common\RepositoryInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait TagsServiceTrait
{
    /**
     * @param string $id
     * @param array  $hasTags
     * @param array  $hasNotTags
     *
     * @return $this
     *
     * MongoDB currently (3.0.x) not support $addToSet and $pull on the same field in the same request
     */
    public function ensureTags($id, array $hasTags = [], array $hasNotTags = [])
    {
        $updates1 = [];
        $updates2 = [];

        if (count($hasTags)) {
            $updates1['$addToSet'] = ['tags' => ['$each' => array_values($hasTags)]];
        }
        if (count($hasNotTags)) {
            $updates2['$pull'] = ['tags' => ['$in' => array_values($hasNotTags)]];
        }
        if (count($updates1)) {
            $this->getRepository()->alter($id, $updates1, ['multiple' => true]);
        }
        if (count($updates2)) {
            $this->getRepository()->alter($id, $updates2, ['multiple' => true]);
        }

        return $this;
    }
    /**
     * @param string $id
     * @param array  $options
     *
     * @return array
     */
    public function getTags($id, array $options = [])
    {
        $tags = null;

        if (isset($options['doc'])) {
            $doc = $options['doc'];
            if (property_exists($doc, 'tags')) {
                $tags = $doc->tags;
            }
        }
        if (!is_array($tags)) {
            $doc = $this->getRepository()->get($id, ['tags']);
            $tags = isset($doc['tags']) ? $doc['tags'] : null;
        }
        if (!is_array($tags) || !count($tags)) {
            $tags = [];
        }

        return $tags;
    }
    /**
     * @param string $id
     * @param string $prefix
     * @param array  $options
     *
     * @return array
     */
    public function getTagCodes($id, $prefix, array $options = [])
    {
        $values = [];
        $prefixLength = strlen($prefix);
        foreach ($this->getTags($id, $options) as $tag) {
            if (substr($tag, 0, $prefixLength) !== $prefix) {
                continue;
            }
            $values[strtolower(substr($tag, $prefixLength))] = true;
        }

        return array_keys($values);
    }
    /**
     * @return RepositoryInterface
     */
    abstract public function getRepository();
}
