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
 * Document Service.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class DocumentService extends Base\AbstractDocumentService implements DocumentServiceInterface
{
    use Traits\Document\GetServiceTrait;
    use Traits\Document\FindServiceTrait;
    use Traits\Document\TagsServiceTrait;
    use Traits\Document\StatsServiceTrait;
    use Traits\Document\PurgeServiceTrait;
    use Traits\Document\CreateServiceTrait;
    use Traits\Document\UpdateServiceTrait;
    use Traits\Document\DeleteServiceTrait;
    use Traits\Document\ImportServiceTrait;
    use Traits\Document\ReplaceServiceTrait;
    use Traits\Document\CreateOrUpdateServiceTrait;
    use Traits\Document\CreateOrDeleteServiceTrait;
    /**
     * @param array $array
     * @param array $options
     *
     * @return mixed|void
     */
    protected function saveCreate(array $array, array $options = [])
    {
        $this->getRepository()->create($array, $options);
    }
    /**
     * @param array $arrays
     * @param array $options
     *
     * @return array
     */
    protected function saveCreateBulk(array $arrays, array $options = [])
    {
        return $this->getRepository()->createBulk($arrays, $options);
    }
    /**
     * @param string $id
     * @param array  $array
     * @param array  $options
     *
     * @return mixed|void
     */
    protected function saveUpdate($id, array $array, array $options)
    {
        $this->getRepository()->update($id, $array, $options);
    }
    /**
     * @param array $arrays
     * @param array $options
     *
     * @return array
     */
    protected function saveUpdateBulk(array $arrays, array $options)
    {
        return $this->getRepository()->updateBulk($arrays, $options);
    }
    /**
     * @param string|array $id
     * @param string       $property
     * @param mixed        $value
     * @param array        $options
     */
    protected function saveIncrementProperty($id, $property, $value, array $options)
    {
        $this->getRepository()->incrementProperty($id, $property, $value, $options);
    }
    /**
     * @param string|array $id
     * @param array        $properties
     * @param array        $options
     */
    protected function saveIncrementProperties($id, $properties, array $options)
    {
        $this->getRepository()->incrementProperties($id, $properties, $options);
    }
    /**
     * @param string|array $id
     * @param string       $property
     * @param mixed        $value
     * @param array        $options
     */
    protected function saveDecrementProperty($id, $property, $value, array $options)
    {
        $this->getRepository()->decrementProperty($id, $property, $value, $options);
    }
    /**
     * @param string|array $id
     * @param array        $properties
     * @param array        $options
     */
    protected function saveDecrementProperties($id, array $properties, array $options)
    {
        $this->getRepository()->decrementProperties($id, $properties, $options);
    }
    /**
     * @param array $criteria
     * @param array $options
     *
     * @return mixed|void
     */
    protected function savePurge(array $criteria = [], array $options = [])
    {
        $this->getRepository()->deleteFound($criteria, $options);
    }
    /**
     * @param array $criteria
     * @param array $options
     */
    protected function saveDeleteFound(array $criteria, array $options)
    {
        $this->getRepository()->deleteFound($criteria, $options);
    }
    /**
     * @param string $id
     * @param array  $options
     *
     * @return mixed|void
     */
    protected function saveDelete($id, array $options)
    {
        $this->getRepository()->delete($id, $options);
    }
    /**
     * @param array $ids
     * @param array $options
     *
     * @return array
     */
    protected function saveDeleteBulk($ids, array $options)
    {
        return $this->getRepository()->deleteBulk($ids, $options);
    }
    /**
     * @param string $id
     * @param string $property
     * @param bool   $value
     *
     * @return $this
     */
    protected function markProperty($id, $property, $value = true)
    {
        return $this->setProperty($id, $property, (bool) $value);
    }
    /**
     * @param string $id
     * @param string $property
     * @param mixed  $value
     *
     * @return $this
     */
    protected function setProperty($id, $property, $value)
    {
        $this->getRepository()->setProperty($id, $property, $value);

        return $this;
    }
}
