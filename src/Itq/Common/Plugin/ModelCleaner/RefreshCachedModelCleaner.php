<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\ModelCleaner;

use Exception;
use Itq\Common\Traits;
use Itq\Common\Service;
use Itq\Common\ModelInterface;
use Itq\Common\RepositoryInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class RefreshCachedModelCleaner extends Base\AbstractMetaDataAwareModelCleaner
{
    use Traits\ServiceAware\CrudServiceAwareTrait;
    /**
     * @param Service\MetaDataService $metaDataService
     * @param Service\CrudService     $crudService
     */
    public function __construct(
        Service\MetaDataService $metaDataService,
        Service\CrudService     $crudService
    ) {
        parent::__construct($metaDataService);
        $this->setCrudService($crudService);
    }
    /**
     * @param ModelInterface $doc
     * @param array          $options
     *
     * @return void
     *
     * @throws Exception
     */
    public function clean($doc, array $options = [])
    {
        $triggers = $this->getMetaDataService()->getModelTriggers($doc, $options);

        $options += ['operation' => null];

        foreach ($triggers as $triggerName => $trigger) {
            $isLevel0DocType = false === strpos($trigger['targetDocType'], '.');
            $docTypeIdProperty = $isLevel0DocType ? '_id' : 'id';
            $targetId = $this->replaceProperty($doc, $trigger['targetId']);
            $updateData = $this->replaceProperty($doc, $trigger['targetData']);
            $requiredFields = [];
            foreach ($trigger['targetData'] as $kkk => $vvv) {
                if ('@' === $vvv{0}) {
                    $requiredFields[substr($vvv, 1)] = true;
                }
            }
            $requiredFields = array_values($requiredFields);
            $createData = $updateData;
            $updateCriteria = [];
            $createCriteria = [];
            $deleteCriteria = [];
            $skip = true;
            $list = isset($trigger['joinFieldType']) && 'list' === $trigger['joinFieldType'];
            $processNew = false;
            $processUpdated = false;
            $processDeleted = false;
            switch ($options['operation']) {
                case 'create':
                    if ($list) {
                        if (count((array) $doc->{$trigger['joinField']})) {
                            $createCriteria['$or'] = [];
                            foreach ((array) $doc->{$trigger['joinField']} as $kk => $vv) {
                                $createCriteria['$or'][] = [$docTypeIdProperty => $vv->id];
                            }
                            $processNew = true;
                            $skip = false;
                        }
                    } else {
                        $joinFieldTokens = explode('.', $trigger['joinField']);
                        $joinField = array_pop($joinFieldTokens);
                        $docFieldParent = $doc;
                        foreach ($joinFieldTokens as $joinFieldToken) {
                            $docFieldParent = $docFieldParent->$joinFieldToken;
                        }
                        $criteriaField = isset($trigger['criteriaField']) ? $trigger['criteriaField'] : ('id' === $joinField ? $docTypeIdProperty : $joinField);
                        if (null === $docFieldParent->$joinField) {
                            $skip = true;
                            $processNew = false;
                        } else {
                            $createCriteria[$criteriaField] = $docFieldParent->$joinField;
                            $processNew = true;
                            $skip = false;
                        }
                    }
                    break;
                case 'update':
                    $updateData = array_filter(
                        $updateData,
                        function ($v) {

                            return null !== $v;
                        }
                    );
                    if ($list) {
                        $expectedIds = [];
                        $existingIds = [];
                        if (count((array) $doc->{$trigger['joinField']})) {
                            foreach ((array) $doc->{$trigger['joinField']} as $kk => $vv) {
                                $expectedIds[$vv->id] = true;
                            }
                        }

                        $criteria = [
                            $trigger['targetDocProperty'].'.'.$targetId => '*notempty*',
                        ];
                        $docs = $this->getCrudService()->get($trigger['targetDocType'])->find($criteria, ['id']);

                        if (count($docs)) {
                            foreach ($docs as $_doc) {
                                $existingIds[$_doc->id] = true;
                            }
                        }

                        $newIds = array_diff_key($expectedIds, $existingIds);
                        $deletedIds = array_diff_key($existingIds, $expectedIds);
                        $updatedIds = array_intersect_key($existingIds, $expectedIds);

                        if (count($newIds)) {
                            $createCriteria['$or'] = [];
                            foreach (array_keys($newIds) as $newId) {
                                $createCriteria['$or'][] = [$docTypeIdProperty => $newId];
                            }
                            $realDoc = $this->getCrudService()->get($this->getMetaDataService()->getModel($doc)['id'])->get($doc->getId(), $requiredFields);
                            $rootRequiredFields = [];
                            foreach ($requiredFields as $requiredField) {
                                if (false !== strpos($requiredField, '.')) {
                                    $rootRequiredFields[explode('.', $requiredField, 2)[0]] = true;
                                } else {
                                    $rootRequiredFields[$requiredField] = true;
                                }
                            }
                            $rootRequiredFields = array_values($rootRequiredFields);
                            sort($rootRequiredFields);
                            foreach ($rootRequiredFields as $requiredField) {
                                if (isset($realDoc->$requiredField) && !isset($doc->$requiredField)) {
                                    $doc->$requiredField = $realDoc->$requiredField;
                                }
                            }
                            $skip = false;
                            $processNew = true;
                        }
                        if (count($deletedIds)) {
                            $deleteCriteria['$or'] = [];
                            foreach (array_keys($deletedIds) as $deletedId) {
                                $deleteCriteria['$or'][] = [$docTypeIdProperty => $deletedId];
                            }
                            $skip = false;
                            $processDeleted = true;
                        }
                        if (count($updatedIds)) {
                            $updateCriteria['$or'] = [];
                            foreach (array_keys($updatedIds) as $updatedId) {
                                $updateCriteria['$or'][] = [$docTypeIdProperty => $updatedId];
                            }
                            $skip = false;
                            $processUpdated = true;
                        }
                    } else {
                        $criteria = [
                            $trigger['targetDocProperty'].'.'.$targetId => '*notempty*',
                        ];
                        $docs = $this->getCrudService()->get($trigger['targetDocType'])->find($criteria, ['id']);

                        if (count($docs)) {
                            $updateCriteria['$or'] = [];
                            foreach ($docs as $_doc) {
                                $updateCriteria['$or'][] = [$docTypeIdProperty => $_doc->id];
                            }
                            $processUpdated = true;
                            $skip = false;
                        }
                    }
                    break;
                case 'delete':
                    $criteria = [
                        $trigger['targetDocProperty'].'.'.$targetId => '*notempty*',
                    ];
                    $docs = $this->getCrudService()->get($trigger['targetDocType'])->find($criteria, ['id']);

                    if (count($docs)) {
                        $deleteCriteria['$or'] = [];
                        foreach ($docs as $_doc) {
                            $deleteCriteria['$or'][] = [$docTypeIdProperty => $_doc->id];
                        }
                        $skip = false;
                        $processDeleted = true;
                    }
                    break;
            }
            if (!$skip) {
                /** @var RepositoryInterface $repo */
                $repo = $this->getCrudService()->get($trigger['targetDocType'])->getRepository();
                if ($processDeleted) {
                    $repo->unsetProperty($deleteCriteria, $trigger['targetDocProperty'].'.'.$targetId, ['multiple' => true]);
                }
                if ($processNew) {
                    $repo->alter($createCriteria, ['$set' => [$trigger['targetDocProperty'].'.'.$targetId => $createData]], ['multiple' => true]);
                }
                if ($processUpdated) {
                    $updates = [];
                    foreach ($updateData as $k => $v) {
                        $updates[$trigger['targetDocProperty'].'.'.$targetId.'.'.$k] = $v;
                    }
                    if (count($updates)) {
                        $repo->alter($updateCriteria, ['$set' => $updates], ['multiple' => true]);
                    }
                }
            }
        }
    }
    /**
     * @param mixed $doc
     * @param mixed $value
     *
     * @return mixed
     */
    protected function replaceProperty($doc, $value)
    {
        if (is_array($value)) {
            foreach ($value as $k => $v) {
                $value[$k] = $this->replaceProperty($doc, $v);
            }

            return $value;
        }

        if ('@' !== $value{0}) {
            return $value;
        }

        $key = substr($value, 1);

        $parentKeys = explode('.', $key);
        $childKey = array_pop($parentKeys);

        foreach ($parentKeys as $parentKey) {
            if (is_object($doc)) {
                if (!property_exists($doc, $parentKey)) {
                    return null;
                }
                $doc = $doc->$parentKey;
            } elseif (is_array($doc)) {
                if (!isset($doc[$parentKey])) {
                    return null;
                }
                $doc = $doc[$parentKey];
            }
        }

        if (is_object($doc)) {
            if (!property_exists($doc, $childKey)) {
                return null;
            }

            return $doc->$childKey;
        } elseif (is_array($doc)) {
            if (!isset($doc[$childKey])) {
                return null;
            }

            return $doc[$childKey];
        }

        return null;
    }
}
