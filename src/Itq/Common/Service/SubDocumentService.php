<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Service;

use Itq\Common\Traits;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class SubDocumentService extends Base\AbstractSubDocumentService implements SubDocumentServiceInterface
{
    use Traits\SubDocument\GetServiceTrait;
    use Traits\SubDocument\FindServiceTrait;
    use Traits\SubDocument\PurgeServiceTrait;
    use Traits\SubDocument\CreateServiceTrait;
    use Traits\SubDocument\UpdateServiceTrait;
    use Traits\SubDocument\DeleteServiceTrait;
    use Traits\SubDocument\ReplaceServiceTrait;
    use Traits\SubDocument\CreateOrUpdateServiceTrait;
    use Traits\SubDocument\CreateOrDeleteServiceTrait;
    /**
     * @param mixed $parentId
     * @param array $array
     * @param array $options
     */
    protected function saveCreate($parentId, array $array, array $options = [])
    {
        $this->getRepository()->setHashProperty($parentId, $this->getRepoKey([$array['id']]), $array, $options);
    }
    /**
     * @param mixed $parentId
     * @param array $arrays
     * @param array $options
     */
    protected function saveCreateBulk($parentId, array $arrays, array $options = [])
    {
        $this->getRepository()->setProperties($parentId, $arrays, $options);
    }
    /**
     * @param array $arrays
     * @param array $array
     *
     * @return mixed|void
     */
    protected function pushCreateInBulk(&$arrays, $array)
    {
        $arrays[$this->mutateKeyToRepoChangesKey('', [$array['id']])] = $array;
    }
    /**
     * @param mixed $parentId
     * @param array $criteria
     * @param array $options
     */
    protected function savePurge($parentId, array $criteria = [], array $options = [])
    {
        unset($criteria);

        $this->getRepository()->resetListProperty($parentId, $this->getRepoKey(), $options);
    }
    /**
     * @param mixed $parentId
     * @param array $criteria
     * @param array $options
     */
    protected function saveDeleteFound($parentId, array $criteria, array $options)
    {
        unset($criteria);

        $this->getRepository()->resetListProperty($parentId, $this->getRepoKey(), $options);
    }
    /**
     * @param mixed $parentId
     * @param mixed $id
     * @param array $options
     */
    protected function saveDelete($parentId, $id, array $options)
    {
        $this->getRepository()->unsetProperty($parentId, $this->getRepoKey([$id]), $options);
    }
    /**
     * @param mixed $parentId
     * @param array $ids
     * @param array $options
     */
    protected function saveDeleteBulk($parentId, $ids, array $options)
    {
        $this->getRepository()->unsetProperty($parentId, array_values($ids), $options);
    }
    /**
     * @param mixed $parentId
     * @param mixed $id
     * @param array $array
     * @param array $options
     */
    protected function saveUpdate($parentId, $id, array $array, array $options)
    {
        $this->getRepository()->setProperties($parentId, $this->mutateArrayToRepoChanges($array, [$id]), $options);
    }
    /**
     * @param mixed $parentId
     * @param array $arrays
     * @param array $options
     */
    protected function saveUpdateBulk($parentId, array $arrays, array $options)
    {
        $this->getRepository()->setProperties($parentId, $arrays, $options);
    }
    /**
     * @param mixed  $parentId
     * @param mixed  $id
     * @param string $property
     * @param mixed  $value
     * @param array  $options
     */
    protected function saveIncrementProperty($parentId, $id, $property, $value, array $options)
    {
        $this->getRepository()->incrementProperty($parentId, $this->getRepoKey([$id, $property]), $value, $options);
    }
    /**
     * @param mixed  $parentId
     * @param mixed  $id
     * @param string $property
     * @param mixed  $value
     * @param array  $options
     */
    protected function saveDecrementProperty($parentId, $id, $property, $value, array $options)
    {
        $this->getRepository()->decrementProperty($parentId, $this->getRepoKey([$id, $property]), $value, $options);
    }
    /**
     * @param array $arrays
     * @param mixed $id
     */
    protected function pushDeleteInBulk(&$arrays, $id)
    {
        $arrays[$id] = $this->getRepoKey([$id]);
    }
    /**
     * @param array $arrays
     * @param array $array
     * @param mixed $id
     */
    protected function pushUpdateInBulk(&$arrays, $array, $id)
    {
        $arrays += $this->mutateArrayToRepoChanges($array, [$id]);
    }
}
