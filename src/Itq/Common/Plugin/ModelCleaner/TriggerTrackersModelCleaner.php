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
class TriggerTrackersModelCleaner extends Base\AbstractMetaDataAwareModelCleaner
{
    use Traits\ServiceAware\CrudServiceAwareTrait;
    use Traits\ServiceAware\ExpressionServiceAwareTrait;
    /**
     * @param Service\MetaDataService   $metaDataService
     * @param Service\CrudService       $crudService
     * @param Service\ExpressionService $expressionService
     */
    public function __construct(
        Service\MetaDataService   $metaDataService,
        Service\CrudService       $crudService,
        Service\ExpressionService $expressionService
    ) {
        parent::__construct($metaDataService);
        $this->setCrudService($crudService);
        $this->setExpressionService($expressionService);
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
        if (!isset($options['operation'])) {
            return;
        }

        switch ($options['operation']) {
            case 'create':
                $this->triggerOperationTrackers($this->getMetaDataService()->getModelIdForClass($doc).'.created', $doc, $options);
                break;
            case 'delete':
                $this->triggerOperationTrackers($this->getMetaDataService()->getModelIdForClass($doc).'.deleted', $doc, $options);
                break;
            case 'update':
                $this->triggerOperationTrackers($this->getMetaDataService()->getModelIdForClass($doc).'.updated', $doc, $options);
                if (isset($doc->status)) {
                    $this->triggerOperationTrackers($this->getMetaDataService()->getModelIdForClass($doc).'.'.$doc->status, $doc, $options);
                }
                break;
        }
    }
    /**
     * @param string $trackedOperation
     * @param mixed  $doc
     * @param array  $options
     */
    protected function triggerOperationTrackers($trackedOperation, $doc, array $options = [])
    {
        foreach ($this->getMetaDataService()->getOperationTrackers($trackedOperation) as $trackerType => $tracker) {
            $this->executeTracker($trackerType, $tracker, $doc, $options);
        }
    }
    /**
     * @param string $type
     * @param array  $config
     * @param mixed  $docOrig
     * @param array  $options
     */
    protected function executeTracker($type, $config, $docOrig, array $options = [])
    {
        $doc = clone $docOrig;

        switch ($type) {
            case 'stat':
                foreach ($config as $targetType => $defs) {
                    /** @var RepositoryInterface $targetRepo */
                    $criteriaBag = [];
                    $incsBag  = [];
                    $setsBag  = [];
                    $computedIncsBag  = [];
                    $computedSetsBag  = [];
                    $alterOptionsBag = [];
                    $targetRepo = $this->getCrudService()->get($targetType)->getRepository();
                    $otherSideFetchFields = [];
                    $fetched = false;
                    $realFetchedFields = [];
                    foreach ($defs as $def) {
                        $fetchFields = ['id' => true];
                        if (isset($def['increment'])) {
                            $value = $def['increment'];
                        } elseif (isset($def['decrement'])) {
                            $value = -$def['decrement'];
                        } elseif (isset($def['formula'])) {
                            $formulaDescription = $this->describeFormula($def['formula'], $doc, $targetRepo);
                            $fetchFields += $formulaDescription['docFields'];
                            $otherSideFetchFields += $formulaDescription['otherDocFields'];
                            $value = $formulaDescription['callable'];
                            $def['replace'] = true;
                        } else {
                            $value = 1;
                        }
                        if (is_string($value)) {
                            if ('@' === substr($value, 0, 1)) {
                                $fetchFields[substr($value, 1)] = true;
                            } elseif ('$' === substr($value, 0, 1)) {
                                $fetchFields['stats.'.substr($value, 1)] = true;
                            }
                        }

                        if (isset($def['match'])) {
                            if ('_parent' === $def['match']) {
                                $index = '_parent';
                                if (!isset($options['parentId'])) {
                                    continue;
                                }
                                $criteria = ['_id' => $options['parentId']];
                            } else {
                                $index = $def['match'];
                                $kk = explode('.', $def['match']);
                                $kkk = array_pop($kk);
                                $d = $doc;
                                $theOriginId = $d->id;
                                $ffield = null;
                                if (count($kk)) {
                                    foreach ($kk as $mm) {
                                        if (null === $ffield) {
                                            $ffield = $mm;
                                            if (!isset($d->$mm)) {
                                                $realFetchedFields += $fetchFields + [$mm => true];
                                                $d2 = $this->getDocument($doc, $d->id, $realFetchedFields, ['cached' => true], $options);
                                                foreach (array_keys($realFetchedFields) as $realFetchedField) {
                                                    if (false !== strpos($realFetchedField, '.')) {
                                                        list($realFetchedField) = explode('.', $realFetchedField, 2);
                                                    }
                                                    if (!isset($doc->$realFetchedField)) {
                                                        $doc->$realFetchedField = $d2->$realFetchedField;
                                                    }
                                                }
                                                $d = $doc;
                                                $fetched = true;
                                            }
                                        }
                                        $d = $d->$mm;
                                    }
                                } else {
                                    if (!isset($d->$kkk)) {
                                        $realFetchedFields += $fetchFields + [$kkk => true];
                                        $d2 = $this->getDocument($doc, $theOriginId, $fetchFields + [$kkk => true], ['cached' => true], $options);
                                        foreach (array_keys($realFetchedFields) as $realFetchedField) {
                                            if (false !== strpos($realFetchedField, '.')) {
                                                list($realFetchedField) = explode('.', $realFetchedField, 2);
                                            }
                                            if (!isset($doc->$realFetchedField)) {
                                                $doc->$realFetchedField = $d2->$realFetchedField;
                                            }
                                        }
                                        $d = $doc;
                                        $fetched = true;
                                    }
                                }
                                if (!is_object($d)) {
                                    // should not be reached
                                    continue;
                                }
                                if (is_object($d->$kkk)) {
                                    $d = $d->$kkk->id;
                                } else {
                                    $d = $d->$kkk;
                                }
                                if (!isset($d)) {
                                    continue;
                                }
                                $criteria = ['_id' => $d];
                            }
                        } else {
                            continue;
                        }
                        if (is_string($value)) {
                            if ('@' === substr($value, 0, 1)) {
                                $vars = ['doc' => $doc];
                                $keyValue = substr($value, 1);
                                if (!isset($doc->$keyValue)) {
                                    $d4 = $this->getDocument($doc, $doc->id, [$keyValue => true], [], $options);
                                    if (isset($d4->$keyValue)) {
                                        $doc->$keyValue = $d4->$keyValue;
                                    }
                                }
                                $value = $this->getExpressionService()->evaluate('$'.'doc.'.substr($value, 1), $vars);
                                unset($vars);
                            } elseif ('$' === substr($value, 0, 1)) {
                                $vars = ['stats' => (object) (isset($doc->stats) ? $doc->stats : [])];
                                $value = $this->getExpressionService()->evaluate('$'.'stats.'.substr($value, 1), $vars);
                                unset($vars);
                            }
                        }

                        if (isset($def['type']) && !($value instanceof \Closure)) {
                            switch ($def['type']) {
                                case 'double':
                                    $value = (double) $value;
                                    break;
                                case 'integer':
                                    $value = (int) $value;
                                    break;
                            }
                        }

                        $sets = [];
                        $incs = [];
                        $computedSets = [];
                        $computedIncs = [];

                        if (isset($def['replace']) && true === $def['replace']) {
                            if ($value instanceof \Closure) {
                                $computedSets = ['key' => 'stats.'.$def['key'], 'callable' => $value];
                            } else {
                                $sets = ['stats.'.$def['key'] => $value];
                            }
                        } else {
                            if (null !== $value) {
                                if ($value instanceof \Closure) {
                                    $computedIncs = ['key' => 'stats.'.$def['key'], 'callable' => $value];
                                } else {
                                    $incs = ['stats.'.$def['key'] => $value];
                                }
                            }
                        }

                        if (!isset($criteriaBag[$index])) {
                            $criteriaBag[$index] = [];
                            $incsBag[$index] = [];
                            $setsBag[$index] = [];
                            $computedIncsBag[$index] = [];
                            $computedSetsBag[$index] = [];
                            $alterOptionsBag[$index] = [];
                        }
                        $criteriaBag[$index] += $criteria;
                        $incsBag[$index] += $incs;
                        $setsBag[$index] += $sets;
                        if (count($computedIncs)) {
                            $computedIncsBag[$index][] = $computedIncs;
                        }
                        if (count($computedSets)) {
                            $computedSetsBag[$index][] = $computedSets;
                        }
                        $alterOptionsBag[$index] += ['multiple' => true];
                    }
                    if (!$fetched) {
                        if (count($realFetchedFields)) {
                            $d3 = $this->getDocument($doc, $doc->id, $realFetchedFields, ['cached' => true], $options);
                            foreach (array_keys($realFetchedFields) as $realFetchedField) {
                                if (!isset($doc->$realFetchedField)) {
                                    $doc->$realFetchedField = $d3->$realFetchedField;
                                }
                            }
                        }
                    }
                    foreach ($criteriaBag as $index => $criteria) {
                        $updates = [];
                        if (count($incsBag[$index])) {
                            $updates['$inc'] = $incsBag[$index];
                        }
                        if (count($setsBag[$index])) {
                            $updates['$set'] = $setsBag[$index];
                        }
                        if (count($updates)) {
                            $targetRepo->alter($criteria, $updates, $alterOptionsBag[$index]);
                        }
                    }
                    foreach ($criteriaBag as $index => $criteria) {
                        $incsBag[$index] = [];
                        $setsBag[$index] = [];
                        $updates = [];
                        if (isset($computedIncsBag[$index]) && count($computedIncsBag[$index])) {
                            foreach ($computedIncsBag[$index] as $kkk => $cc) {
                                $incsBag[$index] += [$cc['key'] => $cc['callable']($criteria, $otherSideFetchFields)];
                                unset($computedIncsBag[$index][$kkk]);
                            }
                        }
                        unset($computedIncsBag[$index]);
                        if (isset($computedSetsBag[$index]) && count($computedSetsBag[$index])) {
                            foreach ($computedSetsBag[$index] as $kkk => $cc) {
                                $setsBag[$index] += [$cc['key'] => $cc['callable']($criteria, $otherSideFetchFields)];
                                unset($computedSetsBag[$index][$kkk]);
                            }
                        }
                        unset($computedSetsBag[$index]);
                        if (count($incsBag[$index])) {
                            $updates['$inc'] = $incsBag[$index];
                        }
                        if (count($setsBag[$index])) {
                            $updates['$set'] = $setsBag[$index];
                        }
                        if (count($updates)) {
                            $targetRepo->alter($criteria, $updates, $alterOptionsBag[$index]);
                        }
                    }
                }
                break;
        }
    }
    /**
     * @param string              $dsl
     * @param Object              $doc
     * @param RepositoryInterface $targetRepo
     *
     * @return array
     */
    protected function describeFormula($dsl, $doc, $targetRepo)
    {
        $fields = [];
        $otherSideFields = [];
        $stats = [];

        if (0 < preg_match_all('/\$([a-z0-9_\.]+)/i', $dsl, $matches)) {
            foreach ($matches[1] as $i => $match) {
                $stats[$match] = true;
                $dsl = str_replace($matches[0][$i], 'stats.'.$match, $dsl);
            }
        }
        if (0 < preg_match_all('/\@([a-z0-9_\.]+)/i', $dsl, $matches)) {
            foreach ($matches[1] as $i => $match) {
                $fields[$match] = true;
                $dsl = str_replace($matches[0][$i], 'doc.'.$match, $dsl);
            }
        }
        if (0 < preg_match_all('/\:([a-z0-9_\.]+)/i', $dsl, $matches)) {
            foreach ($matches[1] as $i => $match) {
                $otherSideFields[$match] = true;
                $dsl = str_replace($matches[0][$i], 'otherDoc.'.$match, $dsl);
            }
        }

        $expressionService = $this->getExpressionService();

        $callable = function ($criteria, $otherDocFields) use ($dsl, $doc, $stats, $targetRepo, $expressionService) {
            $otherDocData = $targetRepo->get($criteria, $otherDocFields + ['stats' => true], ['cached' => true]);
            foreach (array_keys($otherDocFields) as $field) {
                if (!isset($otherDocData[$field])) {
                    $otherDocData[$field] = null;
                }
            }
            $otherDoc = (object) $otherDocData;

            if (!isset($otherDoc->stats)) {
                $otherDoc->stats = [];
            }
            foreach (array_keys($stats) as $key) {
                if (!isset($otherDoc->stats[$key])) {
                    $otherDoc->stats[$key] = null;
                }
            }
            $vars = ['doc' => $doc, 'otherDoc' => $otherDoc, 'stats' => (object) ($otherDoc->stats)];
            $result = $expressionService->evaluate('$'.$dsl, $vars);

            return $result;
        };

        foreach (array_keys($stats) as $stat) {
            $fields['stats.'.$stat] = true;
        }

        return ['docFields' => $fields, 'otherDocFields' => $otherSideFields, 'callable' => $callable];
    }
    /**
     * @param mixed  $doc
     * @param string $id
     * @param array  $realFetchedFields
     * @param array  $options
     * @param array  $globalOptions
     *
     * @return mixed
     *
     * @throws \Exception
     */
    protected function getDocument($doc, $id, $realFetchedFields, $options, $globalOptions)
    {
        $service = $this->getCrudByModelClass($doc);

        switch ($service->getExpectedTypeCount()) {
            case 1:
                return $service->get($id, $realFetchedFields, $options);
            case 2:
                return $service->get($globalOptions['parentId'], $id, $realFetchedFields, $options);
            default:
                throw $this->createFailedException("Unsupported type count for service '%d'", $service->getExpectedTypeCount());
        }
    }
    /**
     * @param string $class
     *
     * @return mixed
     */
    protected function getCrudByModelClass($class)
    {
        return $this->getCrudService()->get($this->getMetaDataService()->getModel($class)['id']);
    }
}
