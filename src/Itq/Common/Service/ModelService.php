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
use Itq\Common\Service;
use Itq\Common\RepositoryInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class ModelService
{
    use Traits\ServiceTrait;
    use Traits\AuthorizationCheckerAwareTrait;
    use Traits\ServiceAware\CrudServiceAwareTrait;
    use Traits\ServiceAware\TenantServiceAwareTrait;
    use Traits\ServiceAware\StorageServiceAwareTrait;
    use Traits\ServiceAware\MetaDataServiceAwareTrait;
    use Traits\ServiceAware\WorkflowServiceAwareTrait;
    use Traits\ServiceAware\GeneratorServiceAwareTrait;
    use Traits\ServiceAware\ExpressionServiceAwareTrait;
    /**
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param Service\TenantService         $tenantService
     * @param Service\MetaDataService       $metaDataService
     * @param Service\CrudService           $crudService
     * @param Service\StorageService        $storageService
     * @param Service\WorkflowService       $workflowService
     * @param Service\GeneratorService      $generatorService
     * @param Service\ExpressionService     $expressionService
     */
    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker,
        Service\TenantService $tenantService,
        Service\MetaDataService $metaDataService,
        Service\CrudService $crudService,
        Service\StorageService $storageService,
        Service\WorkflowService $workflowService,
        Service\GeneratorService $generatorService,
        Service\ExpressionService $expressionService
    ) {
        $this->setAuthorizationChecker($authorizationChecker);
        $this->setTenantService($tenantService);
        $this->setMetaDataService($metaDataService);
        $this->setCrudService($crudService);
        $this->setStorageService($storageService);
        $this->setWorkflowService($workflowService);
        $this->setGeneratorService($generatorService);
        $this->setExpressionService($expressionService);
    }
    /**
     * @param array  $data
     * @param string $class
     * @param array  $options
     *
     * @return array
     */
    public function enrichUpdates($data, $class, array $options = [])
    {
        unset($options);

        $enrichments = $this->getMetaDataService()->getModelUpdateEnrichments($class);

        foreach ($data as $k => $v) {
            if (!isset($enrichments[$k])) {
                continue;
            }
            unset($data[$k]);
            list($k, $v) = $this->computePropertyEnrichments($enrichments[$k], $k, $v);
            $data[$k] = $v;
        }

        return $data;
    }
    /**
     * @param mixed $doc
     * @param array $options
     *
     * @return mixed
     */
    public function refresh($doc, $options = [])
    {
        $this->restrict($doc, $options);

        $doc = $this->loadDefaultValues($doc, $options);
        $doc = $this->convertScalarProperties($doc, $options);
        $doc = $this->checkReferences($doc, $options);
        $doc = $this->fetchEmbeddedReferences($doc, $options);
        $doc = $this->checkEmbeddedLists($doc, $options);
        $doc = $this->checkEmbeddeds($doc, $options);
        $doc = $this->checkBasicLists($doc, $options);
        $doc = $this->checkTagLists($doc, $options);
        $doc = $this->checkHashLists($doc, $options);
        $doc = $this->triggerRefreshes($doc, $options);
        $doc = $this->buildGenerateds($doc, $options);
        $doc = $this->computeFingerPrints($doc, $options);
        $doc = $this->saveStorages($doc, $options);
        $doc = $this->updateWitnesses($doc, $options);

        foreach ($doc as $k => $v) {
            if (!is_object($v) || !$this->getMetaDataService()->isModel($v)) {
                continue;
            }
            $this->refresh($v, $options);
        }

        return $doc;
    }
    /**
     * @param mixed $doc
     * @param array $options
     * @throws \Exception
     */
    public function restrict($doc, array $options = [])
    {
        $operation = isset($options['operation']) ? $options['operation'] : null;

        unset($options);

        $restricts = $this->getMetaDataService()->getModelRestricts($doc);

        if (!count($restricts)) {
            return ;
        }

        if (!isset($doc->id)) {
            return ;
        }
        $retrievedDoc = $this->getCrudByModelClass($doc)->get($doc->id, ['stats']);

        $selectedRestricts = [];

        if (isset($restricts[$operation])) {
            $selectedRestricts += $restricts[$operation];
        }
        if (isset($doc->status) && isset($restricts['status.'.$doc->status])) {
            $selectedRestricts += $restricts['status.'.$doc->status];
        }

        foreach ($selectedRestricts as $restrict) {
            $negate = false;
            $condition = 'false';
            if (isset($restrict['if'])) {
                $condition = $restrict['if'];
            } elseif ($restrict['ifNot']) {
                $condition = $restrict['ifNot'];
                $negate = true;
            }
            $stats = (isset($retrievedDoc->stats) && is_array($retrievedDoc->stats)) ? $retrievedDoc->stats : [];
            $matches = null;
            if (0 < preg_match_all('/\$([a-z0-9_]+)/i', $condition, $matches)) {
                foreach ($matches[1] as $i => $match) {
                    if (!isset($stats[$match])) {
                        $stats[$match] = null;
                    }
                    $condition = str_replace($matches[0][$i], 'stats.'.$match, $condition);
                }
            }
            $vars = ['doc' => $doc, 'stats' => (object) $stats];
            if ($negate !== $this->getExpressionService()->evaluate('$'.$condition, $vars)) {
                throw $this->createDeniedException(isset($restrict['message']) ? $restrict['message'] : sprintf('%s is restricted', $operation));
            }
        }
    }
    /**
     * @param mixed $doc
     * @param array $options
     *
     * @return mixed
     */
    public function clean($doc, $options = [])
    {
        if (isset($options['operation']) && ('create' === $options['operation'] || 'update' === $options['operation'])) {
            $doc = $this->populateStorages($doc, $options);
        }

        if (isset($options['operation']) && 'update' === $options['operation']) {
            $this->refreshEmbeddedReferenceLinks($doc, $options);
        }

        $this->refreshCached($doc, $options);

        $this->triggerTrackers($doc, $options);

        return $doc;
    }
    /**
     * @param string $model
     * @param array  $fields
     *
     * @return array
     */
    public function prepareFields($model, $fields)
    {
        $cleanedFields = [];

        if (!is_array($fields)) {
            return $cleanedFields;
        }

        foreach ($fields as $k => $v) {
            if (is_numeric($k)) {
                if (!is_string($v)) {
                    $v = (string) $v;
                }
                $cleanedFields[$v] = true;
                $a = $v;
            } else {
                if (!is_bool($v)) {
                    $v = (bool) $v;
                }
                $cleanedFields[$k] = $v;
                $a = $k;
            }
            // @todo : if $model = 'legalRepresentative.signatureUrl', $requirements is empty
            $requirements = $this->getMetaDataService()->getModelPropertyRequirements($model, $a);

            if (isset($requirements['fields']) && is_array($requirements['fields'])) {
                foreach ($requirements['fields'] as $_k) {
                    $cleanedFields[$_k] = true;
                }
            }
        }

        return $cleanedFields;
    }
    /**
     * @param mixed $doc
     * @param array $options
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function convertObjectToArray($doc, $options = [])
    {
        $options += ['removeNulls' => true];

        if (!is_object($doc)) {
            throw $this->createMalformedException('Not a valid object');
        }

        $removeNulls = true === $options['removeNulls'];

        $meta = $this->getMetaDataService()->getModel($doc);
        $data = get_object_vars($doc);

        $globalObjectCast = false;
        if (get_class($doc) === 'stdClass') {
            $globalObjectCast = true;
        }
        $extraData = [];
        foreach ($data as $k => $v) {
            if ($removeNulls && null === $v) {
                unset($data[$k]);
                continue;
            }
            if (is_string($v) && false !== strpos($v, '*cleared*')) {
                $v = null;
                $doc->$k = $v;
            }
            if (isset($meta['types'][$k]['type'])) {
                switch (true) {
                    case 'DateTime' === substr($meta['types'][$k]['type'], 0, 8):
                        $data = $this->convertDataDateTimeFieldToMongoDateWithTimeZone($data, $k);
                        continue 2;
                }
            }
            if (isset($meta['geopoints'][$k])) {
                foreach ($meta['geopoints'][$k] as $kkk => $vvv) {
                    $extraData[$meta['geopoints'][$k][$kkk]['name']] = ['type' => 'geopoint'];
                }
            }
            if (is_array($v) && count($v) && !is_numeric(key($v))) {
                $v = (object) $v;
            }
            if (is_object($v)) {
                $objectCast = false;
                if ('stdClass' === get_class($v)) {
                    $objectCast = true;
                }
                $v = $this->convertObjectToArray($v, $options);
                if (true === $objectCast) {
                    $v = (object) $v;
                }
            }
            $data[$k] = $v;
        }

        if (count($extraData)) {
            foreach ($extraData as $kk => $vv) {
                switch ($vv['type']) {
                    case 'geopoint':
                        $geopointConfig = $meta['geopointVirtuals'][$kk];
                        $_lat  = (isset($geopointConfig['latitude']) && isset($data[$geopointConfig['latitude']])) ? $data[$geopointConfig['latitude']] : null;
                        $_long = (isset($geopointConfig['longitude']) && isset($data[$geopointConfig['longitude']])) ? $data[$geopointConfig['longitude']] : null;
                        $data[$kk] = [
                            'type' => 'Point',
                            'coordinates' => [$_long, $_lat],
                        ];
                        break;
                    default:
                        throw $this->createMalformedException("Unsupported extra data type ".$vv['type']);
                }
                unset($extraData[$kk]);
            }
        }

        if (true === $globalObjectCast) {
            $data = (object) $data;
        }

        return $data;
    }
    /**
     * @param mixed  $doc
     * @param string $property
     * @param array  $options
     *
     * @return mixed
     */
    public function buildDynamicUrl($doc, $property, array $options = [])
    {
        return $this->computeDynamicUrl($doc, $this->getMetaDataService()->getModelPropertyDynamicUrl($doc, $property), $options);
    }
    /**
     * @param mixed $doc
     * @param array $data
     * @param array $options
     *
     * @return mixed
     */
    public function populateObject($doc, $data = [], $options = [])
    {
        if (isset($data['_id']) && !isset($data['id'])) {
            $data['id'] = (string) $data['_id'];
            unset($data['_id']);
        } else {
            if (isset($data['id'])) {
                $data['id'] = (string) $data['id'];
            }
        }

        $doc = $this->mutateArrayToObject($data, $doc, $options);

        return $doc;
    }
    /**
     * @param mixed  $doc
     * @param mixed  $data
     * @param string $propertyName
     * @param array  $options
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function populateObjectProperty($doc, $data, $propertyName, $options = [])
    {
        if (!property_exists($doc, $propertyName)) {
            throw $this->createRequiredException("Property '%s' does not exist on %s", $propertyName, get_class($doc));
        }

        $doc->$propertyName = $data;

        $this->populateStorages($doc, $options);

        return $doc->$propertyName;
    }
    /**
     * @param mixed $doc
     * @param array $options
     *
     * @return mixed
     */
    protected function triggerTrackers($doc, array $options = [])
    {
        if (!isset($options['operation'])) {
            return $doc;
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

        return $doc;
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
     * @param mixed $doc
     * @param array $options
     *
     * @return mixed
     */
    protected function fetchEmbeddedReferences($doc, $options = [])
    {
        if (!is_object($doc)) {
            return $doc;
        }

        foreach ($this->getMetaDataService()->getModelEmbeddedReferences($doc) as $property => $embeddedReference) {
            if (!isset($doc->$property)) {
                continue;
            }
            if (!$this->isPopulableModelProperty($doc, $property, $options)) {
                continue;
            }
            if ('*cleared*' === $doc->$property) {
                $doc->$property = '*cleared*';
            } elseif (isset($embeddedReference['key'])) {
                $doc->$property = $this->convertReferenceToObject($embeddedReference['key'], $doc->$property, $this->getMetaDataService()->getModelClassForId($embeddedReference['localType']), $embeddedReference['type']);
            } else {
                $doc->$property = $this->convertIdToObject($doc->$property, $this->getMetaDataService()->getModelClassForId($embeddedReference['localType']), $embeddedReference['type']);
            }
        }

        return $doc;
    }
    /**
     * @param mixed $doc
     * @param array $options
     *
     * @return mixed
     */
    protected function checkReferences($doc, $options = [])
    {
        if (!is_object($doc)) {
            return $doc;
        }

        foreach ($this->getMetaDataService()->getModelReferences($doc) as $property => $reference) {
            if (null === $doc->$property) {
                continue;
            }
            if (!$this->isPopulableModelProperty($doc, $property, $options)) {
                continue;
            }
            if ('*cleared*' === $doc->$property) {
                continue;
            }
            if (isset($reference['key'])) {
                $this->checkReference($reference['key'], $doc->$property, $reference['type']);
            } else {
                $this->checkReference('id', $doc->$property, $reference['type']);
            }
        }

        return $doc;
    }
    /**
     * @param mixed $doc
     * @param array $options
     *
     * @return mixed
     */
    protected function checkEmbeddeds($doc, $options = [])
    {
        if (!is_object($doc)) {
            return $doc;
        }

        foreach ($this->getMetaDataService()->getModelEmbeddeds($doc) as $property => $embedded) {
            if (!$this->isPopulableModelProperty($doc, $property, $options)) {
                continue;
            }
            $type = $this->getMetaDataService()->getModelPropertyType($doc, $property);
            $doc->$property = $this->convertMixedToObject($doc->$property, isset($embedded['class']) ? $embedded['class'] : ($type ? $type['type'] : null), $embedded['type']);
        }

        return $doc;
    }
    /**
     * @param mixed $doc
     * @param array $options
     *
     * @return mixed
     */
    protected function checkBasicLists($doc, $options = [])
    {
        if (!is_object($doc)) {
            return $doc;
        }

        foreach ($this->getMetaDataService()->getModelBasicLists($doc) as $property => $list) {
            if (!$this->isPopulableModelProperty($doc, $property, $options)) {
                continue;
            }
            if (is_object($doc->$property)) {
                $doc->$property = (array) $doc->$property;
            }
            if (!is_array($doc->$property)) {
                $doc->$property = [];
            }
            $doc->$property = array_values($doc->$property);
        }

        return $doc;
    }
    /**
     * @param mixed $doc
     * @param array $options
     *
     * @return mixed
     */
    protected function checkTagLists($doc, $options = [])
    {
        if (!is_object($doc)) {
            return $doc;
        }

        foreach ($this->getMetaDataService()->getModelTagLists($doc) as $property => $list) {
            if (!$this->isPopulableModelProperty($doc, $property, $options)) {
                continue;
            }
            if (is_object($doc->$property)) {
                $doc->$property = (array) $doc->$property;
            }
            if (!is_array($doc->$property)) {
                $doc->$property = [];
            }
            $doc->$property = array_values($doc->$property);
        }

        return $doc;
    }
    /**
     * @param mixed $doc
     * @param array $options
     *
     * @return mixed
     */
    protected function checkHashLists($doc, $options = [])
    {
        if (!is_object($doc)) {
            return $doc;
        }

        foreach ($this->getMetaDataService()->getModelHashLists($doc) as $property => $list) {
            $list += ['type' => 'mixed'];
            if (!$this->isPopulableModelProperty($doc, $property, $options)) {
                continue;
            }
            if (is_object($doc->$property)) {
                $doc->$property = (array) $doc->$property;
            }
            if (!is_array($doc->$property)) {
                $doc->$property = [];
            }
            foreach ($doc->$property as $kk => $vv) {
                $theProperty = &$doc->$property;
                switch ($list['type']) {
                    case 'bool':
                        $theProperty[$kk] = $this->convertMixedToBool($vv);
                        break;
                    case 'string':
                        $theProperty[$kk] = $this->convertMixedToString($vv);
                        break;
                    case 'integer':
                        $theProperty[$kk] = $this->convertMixedToInteger($vv);
                        break;
                    case 'float':
                        $theProperty[$kk] = $this->convertMixedToFloat($vv);
                        break;
                    default:
                    case 'mixed':
                        break;
                }
            }
            $doc->$property = (object) $doc->$property;
        }

        return $doc;
    }
    /**
     * @param mixed $v
     *
     * @return bool
     */
    protected function convertMixedToBool($v)
    {
        return !(false === $v || 'false' === $v || '0' === $v || '' === $v || 0 === $v || null === $v);
    }
    /**
     * @param mixed $v
     *
     * @return string
     */
    protected function convertMixedToString($v)
    {
        return (string) $v;
    }
    /**
     * @param mixed $v
     *
     * @return int
     */
    protected function convertMixedToInteger($v)
    {
        return (int) $v;
    }
    /**
     * @param mixed $v
     *
     * @return float
     */
    protected function convertMixedToFloat($v)
    {
        return (float) $v;
    }
    /**
     * @param mixed  $doc
     * @param string $property
     * @param array  $options
     *
     * @return bool
     */
    protected function isPopulableModelProperty($doc, $property, array $options = [])
    {
        return property_exists($doc, $property) && (!isset($options['populateNulls']) || (false === $options['populateNulls'] && null !== $doc->$property));
    }
    /**
     * @param mixed $doc
     * @param array $options
     *
     * @return mixed
     *
     * @throws \Exception
     */
    protected function checkEmbeddedLists($doc, $options = [])
    {
        if (!is_object($doc)) {
            return $doc;
        }

        foreach ($this->getMetaDataService()->getModelEmbeddedLists($doc) as $property => $embeddedList) {
            if (!property_exists($doc, $property)) {
                continue;
            }
            if (!isset($doc->$property) || [] === $doc->$property) {
                if (isset($options['operation']) && 'create' === $options['operation']) {
                    $doc->$property = (object) [];
                }
                continue;
            } else {
                if (isset($options['operation'])) {
                    if ('create' === $options['operation']) {
                        throw $this->createDeniedException("Not allowed to set '%s' (embedded) on new document", $property);
                    } elseif ('update' === $options['operation']) {
                        if (!isset($embeddedList['updatable']) || true !== $embeddedList['updatable']) {
                            throw $this->createDeniedException("Not allowed to change '%s' (embedded)", $property);
                        }
                        $bulkData = $doc->$property;
                        $doc->$property = null;
                        if (!is_array($bulkData)) {
                            throw $this->createDeniedException("Not allowed to change '%s' without providing a list (embedded)", $property);
                        }
                        $subService = $this->getCrudService()->get($embeddedList['type']);
                        $subService->replaceAll($options['id'], $bulkData);
                    } else {
                        throw $this->createDeniedException("Not allowed to change '%s' (embedded) when operation is %s", $property, isset($options['operation']) ? $options['operation'] : '?');
                    }
                }
            }
        }

        return $doc;
    }
    /**
     * @param string $id
     * @param string $class
     * @param string $type
     *
     * @return Object
     */
    protected function convertIdToObject($id, $class, $type)
    {
        $model = $this->createModelInstance(['model' => $class]);
        $fields = array_keys(get_object_vars($model));

        return $this->getCrudService()->get($type)->get($id, $fields, ['model' => $model]);
    }
    /**
     * @param string $referenceKey
     * @param string $referenceValue
     * @param string $class
     * @param string $type
     *
     * @return Object
     */
    protected function convertReferenceToObject($referenceKey, $referenceValue, $class, $type)
    {
        $model = $this->createModelInstance(['model' => $class]);
        $fields = array_keys(get_object_vars($model));

        return $this->getCrudService()->get($type)->getBy($referenceKey, $referenceValue, $fields, ['model' => $model]);
    }
    /**
     * @param string $referenceKey
     * @param string $referenceValue
     * @param string $type
     *
     * @return $this
     *
     * @throws \Exception
     */
    protected function checkReference($referenceKey, $referenceValue, $type)
    {
        $this->getCrudService()->get($type)->checkExistBy($referenceKey, $referenceValue);

        return $this;
    }
    /**
     * @param string $data
     * @param string $class
     * @param string $type
     *
     * @return mixed
     *
     * @throws \Exception
     */
    protected function convertMixedToObject($data, $class, $type)
    {
        unset($type);

        $model = $this->createModelInstance(['model' => $class]);
        $fields = array_keys(get_object_vars($model));

        if (is_object($data)) {
            if (get_class($data) !== $class && !is_subclass_of($data, $class)) {
                $data = (array) $data;
            } else {
                return $data;
            }
        }
        if (null === $data) {
            return null;
        }

        if (!is_array($data)) {
            throw $this->createMalformedException("Array expected to be able to convert to %s", $class);
        }

        if (!count($data)) {
            return null;
        }

        foreach ($fields as $field) {
            if (!isset($data[$field])) {
                continue;
            }
            $model->$field = $data[$field];
        }

        return $model;
    }
    /**
     * @param mixed $doc
     * @param array $options
     *
     * @return mixed
     *
     * @throws \Exception
     */
    protected function buildGenerateds($doc, $options = [])
    {
        if (!is_object($doc)) {
            return $doc;
        }

        $generateds = $this->getMetaDataService()->getModelGenerateds($doc);

        foreach ($generateds as $k => $v) {
            $generate = false;
            if (isset($v['trigger'])) {
                if (isset($doc->{$v['trigger']})) {
                    $generate = true;
                }
            } else {
                if ($this->isPopulableModelProperty($doc, $k, $options)) {
                    $generate = true;
                }
            }
            if (true === $generate) {
                $value = $this->generateValue($v, $doc);

                if (isset($v['encode'])) {
                    switch ($v['encode']) {
                        case 'base64':
                            $value = base64_encode($value);
                            break;
                        default:
                            throw $this->createUnexpectedException("Unsupported encode option '%s'", $v['encode']);
                    }
                }

                $doc->$k = $value;
            }
        }

        return $doc;
    }
    /**
     * @param mixed $doc
     * @param array $options
     *
     * @return mixed
     */
    protected function loadDefaultValues($doc, $options = [])
    {
        if (!is_object($doc)) {
            return $doc;
        }

        if (!isset($options['operation']) || 'create' !== $options['operation']) {
            return $doc;
        }

        $defaults = $this->getMetaDataService()->getModelDefaults($doc);

        foreach ($defaults as $k => $v) {
            if (isset($doc->$k)) {
                continue;
            }
            if (!$this->isPopulableModelProperty($doc, $k, $options)) {
                continue;
            }
            $doc->$k = $this->generateDefault($doc, $v);
        }

        return $doc;
    }
    /**
     * @param mixed $doc
     * @param mixed $v
     *
     * @return array|mixed
     */
    protected function generateDefault($doc, $v)
    {
        if (!is_array($v)) {
            return $v;
        }

        $v += ['value' => null, 'options' => []];

        if (!isset($v['generator'])) {
            if (is_string($v['value']) && '{{' === substr($v['value'], 0, 2)) {
                $matches = null;
                if (0 < preg_match_all('/\{\{([^\}]+)\}\}/', $v['value'], $matches)) {
                    foreach ($matches[0] as $i => $match) {
                        if ('.' === substr($matches[1][$i], 0, 1)) {
                            $v['value'] = isset($doc->{substr($matches[1][$i], 1)}) ? $doc->{substr($matches[1][$i], 1)} : null;
                        } elseif ('now' === $matches[1][$i]) {
                            $v['value'] = new \DateTime();
                        } elseif ('tenant' === $matches[1][$i]) {
                            $v['value'] = $this->getTenantService()->getCurrent();
                        }
                    }
                }
            }

            return $v['value'];
        }

        return $this->getGeneratorService()->generate($v['generator'], (array) $doc, $v['options']);
    }
    /**
     * @param mixed $doc
     * @param array $definition
     * @param array $options
     *
     * @return mixed
     */
    protected function computeDynamicUrl($doc, $definition, $options = [])
    {
        $_vars = [];

        if (!isset($definition['vars']) || !is_array($definition['vars'])) {
            $definition['vars'] = [];
        }

        foreach ($definition['vars'] as $kk => $vv) {
            if ('@' === substr($vv, 0, 1)) {
                $vv = substr($vv, 1);
                $cdoc = $doc;
                if (strpos($vv, '.')) {
                    $ps = explode('.', $vv);
                    $vv = $ps[count($ps) - 1];
                    unset($ps[count($ps) -1]);
                    foreach ($ps as $vvv) {
                        if (isset($cdoc->$vvv)) {
                            $cdoc = $cdoc->$vvv;
                        }
                    }
                }
                $v = isset($cdoc->$vv) ? $cdoc->$vv : null;
            } else {
                $v = $vv;
            }

            if (null === $v) {
                return null;
            }

            $_vars[$kk] = $v;
        }

        $type = $definition['type'];

        foreach ($_vars as $k => $v) {
            if (false !== strpos($type, '{'.$k.'}')) {
                $type = str_replace('{'.$k.'}', (string) $v, $type);
            }
        }

        $_vars['dynamicPattern'] = $type;

        unset($options);

        return $this->generateValue(['type' => 'dynamicurl'], $_vars);
    }
    /**
     * @param mixed $doc
     * @param array $options
     *
     * @return mixed
     */
    protected function saveStorages($doc, $options = [])
    {
        if (!is_object($doc)) {
            return $doc;
        }

        $vars = ((array) $doc);

        foreach ($options as $k => $v) {
            if (!isset($vars[$k])) {
                $vars[$k] = $v;
            }
        }

        $storages = $this->getMetaDataService()->getModelStorages($doc);

        foreach ($storages as $k => $definition) {
            if (!isset($doc->$k)) {
                continue;
            }
            if (!$this->isPopulableModelProperty($doc, $k, $options)) {
                continue;
            }
            $doc->$k = $this->saveStorageValue($doc->$k, $definition, $vars);
        }

        return $doc;
    }
    /**
     * @param mixed $doc
     * @param array $options
     *
     * @return mixed
     */
    protected function populateStorages($doc, $options = [])
    {
        if (!is_object($doc)) {
            return $doc;
        }

        unset($options);

        $storages = $this->getMetaDataService()->getModelStorages($doc);

        foreach ($storages as $k => $definition) {
            if (isset($doc->$k)) {
                if (is_array($doc->$k)) {
                    // this is a legacy value which was not externalized to a storage
                    // but we need to simulate that is was picked up
                    // from the stroage
                    $doc->$k = json_encode($doc->$k);
                } else {
                    $doc->$k = $this->readStorageValue($doc->$k);
                }
            }
            unset($definition);
        }

        return $doc;
    }
    /**
     * @param mixed $doc
     * @param array $options
     *
     * @return mixed
     *
     * @throws \Exception
     */
    protected function triggerRefreshes($doc, $options = [])
    {
        if (!is_object($doc)) {
            return $doc;
        }

        if (!isset($options['operation'])) {
            return $doc;
        }

        foreach ($this->getMetaDataService()->getModelRefreshablePropertiesByOperation($doc, $options['operation']) as $property) {
            $type = $this->getMetaDataService()->getModelPropertyType($doc, $property);
            switch ($type['type']) {
                case "DateTime<'c'>":
                    $doc->$property = new \DateTime();
                    break;
                default:
                    throw $this->createUnexpectedException("Unable to refresh model property '%s': unsupported type '%s'", $property, $type);
            }
        }

        return $doc;
    }
    /**
     * @param mixed $doc
     * @param array $options
     *
     * @return mixed
     */
    protected function convertScalarProperties($doc, $options = [])
    {
        if (!is_object($doc)) {
            return $doc;
        }

        $types = $this->getMetaDataService()->getModelTypes($doc);

        foreach ($types as $property => $type) {
            if (!$this->isPopulableModelProperty($doc, $property, ['populateNulls' => false] + $options)) {
                continue;
            }
            switch ($type['type']) {
                case "DateTime<'c'>":
                    if ('' === $doc->$property) {
                        $doc->$property = null;
                    }
                    break;
            }
        }

        return $doc;
    }
    /**
     * @param array $definition
     * @param mixed $data
     *
     * @return string
     *
     * @throws \Exception
     */
    protected function generateValue($definition, $data)
    {
        return $this->getGeneratorService()->generate($definition['type'], is_object($data) ? (array) $data : $data);
    }
    /**
     * @param mixed $value
     * @param array $definition
     * @param mixed $vars
     *
     * @return string
     *
     * @throws \Exception
     */
    protected function saveStorageValue($value, $definition, $vars)
    {
        $key = $definition['key'];
        $origKey = $key;

        if (0 < preg_match_all('/\{([^\}]+)\}/', $key, $matches)) {
            foreach ($matches[1] as $i => $match) {
                if (!array_key_exists($match, $vars)) {
                    throw $this->createRequiredException("Missing data '%s' in document for computing the storage key '%s'", $match, $origKey);
                }
                $key = str_replace($matches[0][$i], isset($vars[$match]) ? $vars[$match] : null, $key);
            }
        }

        if ('*cleared*' === $value) {
            /**
             * be careful, if you want to remove the storage, we need to first pick up the real location
             * from the doc, DO NOT use $key
             */
            return '*cleared*';
        } else {
            $this->getStorageService()->save($key, $value);
        }

        return $key;
    }
    /**
     * @param mixed $key
     *
     * @return mixed
     *
     * @throws \Exception
     */
    protected function readStorageValue($key)
    {
        return $this->getStorageService()->read($key);
    }
    /**
     * @param array $options
     *
     * @return object
     */
    protected function createModelInstance(array $options)
    {
        $class = $options['model'];

        return new $class();
    }
    /**
     * @param array $data
     * @param mixed $doc
     * @param array $options
     *
     * @return Object
     */
    protected function mutateArrayToObject($data, $doc, array $options = [])
    {
        $ctx     = (object) ['models' => []];
        $modelId = $this->getMetaDataService()->getModelIdForClass($doc);

        foreach ($data as $k => $v) {
            $this->mutatePropertyIfNecessary($modelId, $doc, $k, $v, $ctx, $data, $options);
        }

        if (isset($options['requestedFields']) && is_array($options['requestedFields'])) {
            foreach (array_keys($options['requestedFields']) as $requestedField) {
                $this->mutateRequestedFieldIfNecessary(
                    $modelId,
                    $doc,
                    is_int($requestedField) ? $options['requestedFields'][$requestedField] : $requestedField,
                    $ctx,
                    $options
                );
            }
        }

        return $this->populateStorages($doc);
    }
    /**
     * @param string $modelId
     * @param mixed  $doc
     * @param string $k
     * @param mixed  $v
     * @param object $ctx
     * @param array  $data
     * @param array  $options
     *
     * @return void
     */
    protected function mutatePropertyIfNecessary($modelId, $doc, $k, $v, $ctx, &$data, &$options)
    {
        if (!isset($ctx->models[$modelId])) {
            $ctx->models[$modelId] = $this->getMetaDataService()->fetchModelDefinition($doc);
        }

        $m = &$ctx->models[$modelId];

        if (!property_exists($doc, $k)) {
            return;
        }
        if (!$this->isPropertyOperationAllowed($doc, $k, isset($options['operation']) ? $options['operation'] : null, $options)) {
            $doc->$k = null;

            return;
        }
        if (isset($m['embeddedReferences'][$k])) {
            if (null !== $v) {
                $v = $this->mutateArrayToObject($v, $this->createModelInstance(['model' => $this->getMetaDataService()->getModelClassForId($m['embeddedReferences'][$k]['localType'])]), $options);
            }
        } elseif (isset($m['embeddeds'][$k])) {
            $v = $this->mutateArrayToObject($v, $this->createModelInstance(['model' => $this->getMetaDataService()->getModelClassForId($m['embeddeds'][$k]['type'])]), $options);
        } elseif (isset($m['basicLists'][$k])) {
            $v = (array) $v;
        } elseif (isset($m['tagLists'][$k])) {
            $v = (array) $v;
        } elseif (isset($m['hashLists'][$k])) {
            $v = (array) $v;
        }
        if (isset($m['embeddedLists'][$k])) {
            $tt = isset($m['embeddedLists'][$k]['class']) ? $m['embeddedLists'][$k]['class'] : (isset($m['types'][$k]) ? $m['types'][$k]['type'] : null);
            if (null !== $tt) {
                $tt = preg_replace('/^array<([^>]+)>$/', '\\1', $tt);
            }
            if (!is_array($v)) {
                $v = [];
            }
            $subDocs = [];
            foreach ($v as $kk => $vv) {
                $subDocs[$kk] = $this->mutateArrayToObject($vv, $this->createModelInstance(['model' => $tt]), $options);
            }
            $v = $subDocs;
        }
        if (isset($m['cachedLists'][$k])) {
            $tt = isset($m['cachedLists'][$k]['class']) ? $m['cachedLists'][$k]['class'] : (isset($m['types'][$k]) ? $m['types'][$k]['type'] : null);
            if (null !== $tt) {
                $tt = preg_replace('/^array<([^>]+)>$/', '\\1', $tt);
            }
            if (!is_array($v)) {
                $v = [];
            }
            $subDocs = [];
            foreach ($v as $kk => $vv) {
                $subDocs[$kk] = $this->mutateArrayToObject($vv, $this->createModelInstance(['model' => $tt]), $options);
            }
            $v = $subDocs;
        }
        if (isset($m['types'][$k])) {
            switch (true) {
                case 'DateTime' === substr($m['types'][$k]['type'], 0, 8):
                    $data = $this->revertDocumentMongoDateWithTimeZoneFieldToDateTime($data, $k);
                    $v = $data[$k];
            }
            $doc->$k = $v;
        } else {
            $doc->$k = $v;
        }
    }
    /**
     * @param string $modelId
     * @param mixed  $doc
     * @param string $requestedField
     * @param object $ctx
     * @param array  $options
     *
     * @return void
     */
    protected function mutateRequestedFieldIfNecessary($modelId, $doc, $requestedField, $ctx, &$options)
    {
        if (false !== ($pos = strpos($requestedField, '.'))) {
            $property = substr($requestedField, 0, $pos);
            if (property_exists($doc, $property)) {
                $subDoc = $doc->$property;
                if (is_object($subDoc)) {
                    if ($this->getMetaDataService()->isModel($subDoc)) {
                        $this->mutateRequestedFieldIfNecessary(
                            $this->getMetaDataService()->getModelIdForClass($subDoc),
                            $subDoc,
                            substr($requestedField, $pos + 1),
                            $ctx,
                            $options
                        );

                        return;
                    }
                }
            }

            return;
        }

        if (!isset($ctx->models[$modelId])) {
            $ctx->models[$modelId] = $this->getMetaDataService()->fetchModelDefinition($doc);
        }

        $m = &$ctx->models[$modelId];

        if (!property_exists($doc, $requestedField)) {
            return;
        }
        if (isset($doc->$requestedField)) {
            return;
        }
        if (isset($m['virtualEmbeddedReferenceLists'][$requestedField])) {
            $virtualEmbeddedReferenceList = $m['virtualEmbeddedReferenceLists'][$requestedField];
            if (isset($virtualEmbeddedReferenceList['criteria'])) {
                if (!is_array($virtualEmbeddedReferenceList['criteria'])) {
                    $virtualEmbeddedReferenceList['criteria'] = [];
                }
                $criteria = [];
                foreach ($virtualEmbeddedReferenceList['criteria'] as $kkk => $vvv) {
                    if ('@' === substr($vvv, 0, 1)) {
                        $vvv = $doc->{substr($vvv, 1)};
                    }
                    $criteria[$kkk] = $vvv;
                }
            } else {
                $criteria = [$virtualEmbeddedReferenceList['key'] => $doc->{$virtualEmbeddedReferenceList['localKey']}];
            }
            $sorts = [];
            if (isset($virtualEmbeddedReferenceList['sorts']) && is_array($virtualEmbeddedReferenceList['sorts'])) {
                $sorts = $virtualEmbeddedReferenceList['sorts'];
            }
            $limit = null;
            if (isset($virtualEmbeddedReferenceList['limit']) && is_numeric($virtualEmbeddedReferenceList['limit']) && 0 < $virtualEmbeddedReferenceList['limit']) {
                $limit = (int) $virtualEmbeddedReferenceList['limit'];
            }
            $offset = null;
            if (isset($virtualEmbeddedReferenceList['offset']) && is_numeric($virtualEmbeddedReferenceList['offset']) && 0 < $virtualEmbeddedReferenceList['offset']) {
                $offset = (int) $virtualEmbeddedReferenceList['offset'];
            }
            $doc->$requestedField = $this->getCrudService()->get($virtualEmbeddedReferenceList['type'])
                ->find(
                    $criteria,
                    $virtualEmbeddedReferenceList['fields'],
                    $limit,
                    $offset,
                    $sorts,
                    ['model' => $this->getMetaDataService()->getModelClassForId($virtualEmbeddedReferenceList['itemType'])]
                )
            ;
        } elseif (isset($m['virtualEmbeddedReferences'][$requestedField])) {
            $virtualEmbeddedReference = $m['virtualEmbeddedReferences'][$requestedField];
            if (isset($virtualEmbeddedReference['criteria'])) {
                if (!is_array($virtualEmbeddedReference['criteria'])) {
                    $virtualEmbeddedReference['criteria'] = [];
                }
                $criteria = [];
                foreach ($virtualEmbeddedReference['criteria'] as $kkk => $vvv) {
                    if ('@' === substr($vvv, 0, 1)) {
                        $vvv = $doc->{substr($vvv, 1)};
                    }
                    $criteria[$kkk] = $vvv;
                }
            } else {
                $criteria = [$virtualEmbeddedReference['key'] => $doc->{$virtualEmbeddedReference['localKey']}];
            }
            $doc->$requestedField = $this->getCrudService()->get($virtualEmbeddedReference['type'])
                ->findOne(
                    $criteria,
                    $virtualEmbeddedReference['fields'],
                    0,
                    [],
                    ['model' => $this->getMetaDataService()->getModelClassForId($virtualEmbeddedReference['itemType'])]
                )
            ;
        } elseif (isset($m['virtuals'][$requestedField])) {
            $doc->$requestedField = $this->computeVirtual($doc, $requestedField, $m['virtuals'][$requestedField], $options);
        } elseif (isset($m['storageUrls'][$requestedField])) {
            $doc->$requestedField = $this->computeStorageUrl($doc, $m['storageUrls'][$requestedField], ['requestedField' => $requestedField] + $options);
        } elseif (isset($m['dynamicUrls'][$requestedField])) {
            $doc->$requestedField = $this->computeDynamicUrl($doc, $m['dynamicUrls'][$requestedField], ['requestedField' => $requestedField] + $options);
        }
    }
    /**
     * @param mixed  $doc
     * @param string $property
     * @param string $operation
     * @param array  $options
     *
     * @return bool
     */
    protected function isPropertyOperationAllowed($doc, $property, $operation, array $options = [])
    {
        $decision = null;

        foreach ($this->getMetaDataService()->getModelPropertySecures($doc, $property) as $secure) {
            if (true === $this->isSecureAllowedForOperation($doc, $secure, $operation, ['property' => $property] + $options)) {
                $decision = true;
                break;
            }
        }

        if (null === $decision) {
            $decision = true;
        }

        return $decision;
    }
    /**
     * @param mixed  $doc
     * @param array  $secure
     * @param string $operation
     * @param array  $options
     *
     * @return bool
     */
    protected function isSecureAllowedForOperation(
        /** @noinspection PhpUnusedParameterInspection */ $doc,
        array $secure,
        $operation,
        array $options = []
    ) {
        $operation = strtolower($operation);

        if (!isset($secure['operations'][$operation])) {
            if (!isset($secure['operations']['all'])) {
                return true;
            }
        }

        if (isset($secure['roles'])) {
            if (!$this->isUserHavingOneOfTheseRoles($secure['roles'])) {
                return false;
            }
        }

        return true;
    }
    /**
     * @param array $roles
     *
     * @return bool
     */
    protected function isUserHavingOneOfTheseRoles(array $roles)
    {
        if (!count($roles)) {
            return true;
        }

        return $this->getAuthorizationChecker()->isGranted(array_keys($roles));
    }
    /**
     * @param mixed  $doc
     * @param string $property
     * @param array  $definition
     * @param array  $options
     *
     * @return mixed
     *
     * @throws \Exception
     */
    protected function computeVirtual($doc, $property, $definition, array $options = [])
    {
        $service = $this->getCrudByModelClass($doc);

        if (!isset($definition['params'])) {
            $definition['params'] = [$doc->id];
            if (method_exists($service, 'getExpectedTypeCount')) {
                switch ($service->getExpectedTypeCount()) {
                    case 2:
                        $definition['params'] = array_merge(
                            [isset($options['parentId']) ? $options['parentId'] : null],
                            $definition['params']
                        );
                        break;
                }
            }
        }

        if (!isset($definition['method']) || $this->isEmptyString($definition['method'])) {
            $definition['method'] = 'get'.ucfirst($property);
        }

        $method = $definition['method'];
        $params = $definition['params'];

        foreach ($params as $k => $v) {
            $matches = null;
            if (':options' === $v) {
                $params[$k] = $options;
            } elseif (0 < preg_match('/^@(.+)$/', $v, $matches)) {
                $params[$k] = isset($doc->{$matches[1]}) ? $doc->{$matches[1]} : null;
            }
        }

        if (!method_exists($service, $method)) {
            throw $this->createRequiredException("Missing method '%s' in service '%s'", $method, $this->getMetaDataService()->getModelIdForClass($doc));
        }

        $options['doc']        = $doc;
        $options['property']   = $property;
        $options['definition'] = $definition;

        $params[] = $options;

        return call_user_func_array([$service, $method], array_values($params));
    }
    /**
     * @param array  $data
     * @param string $fieldName
     *
     * @return array
     *
     * @throws \Exception
     */
    protected function convertDataDateTimeFieldToMongoDateWithTimeZone($data, $fieldName)
    {
        if (!isset($data[$fieldName])) {
            throw $this->createRequiredException("Missing date time field '%s'", $fieldName);
        }

        if (null !== $data[$fieldName] && !$data[$fieldName] instanceof \DateTime) {
            throw $this->createRequiredException("Field '%s' must be a valid DateTime", $fieldName);
        }

        /** @var \DateTime $date */
        $date = $data[$fieldName];

        $data[$fieldName] = new \MongoDate($date->getTimestamp());
        $data[sprintf('%s_tz', $fieldName)] = $date->getTimezone()->getName();

        return $data;
    }
    /**
     * @param array  $enrichments
     * @param string $k
     * @param mixed  $v
     *
     * @return array
     */
    protected function computePropertyEnrichments($enrichments, $k, $v)
    {
        foreach ($enrichments as $enrichment) {
            switch ($enrichment['type']) {
                case 'toggleItems':
                    $k .= ':toggle';
                    if (!is_array($v)) {
                        $v = explode(',', $v);
                    }
                    break;
            }
        }

        return [$k, $v];
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
    /**
     * @param mixed $doc
     * @param array $options
     */
    protected function refreshCached($doc, array $options = [])
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
                            $realDoc = $this->getCrudService()->get($this->getMetaDataService()->getModel($doc)['id'])->get($doc->id, $requiredFields);
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
     * @param array $definition
     * @param array $options
     *
     * @return mixed
     */
    protected function computeStorageUrl($doc, $definition, $options = [])
    {
        $_vars = [];

        if (!isset($definition['vars']) || !is_array($definition['vars'])) {
            $definition['vars'] = [];
        }

        $definition['vars'] += array_intersect_key($options, ['docId' => true, 'docToken' => true, 'parentId' => true, 'parentParentId' => true, 'parentToken' => true, 'parentParentToken' => true]);

        foreach ($definition['vars'] as $kk => $vv) {
            if ('@' === substr($vv, 0, 1)) {
                $vv = substr($vv, 1);
                $v = isset($doc->$vv) ? $doc->$vv : null;
            } else {
                $v = $vv;
            }

            if (null === $v) {
                return null;
            }

            $_vars[$kk] = $v;
        }

        unset($options);

        return $this->generateValue(['type' => 'storageurl'], $_vars);
    }
    /**
     * @param mixed $doc
     * @param array $options
     *
     * @return mixed
     */
    protected function updateWitnesses($doc, array $options = [])
    {
        unset($options);

        $witnesses = $this->getMetaDataService()->getModelWitnesses($doc);

        foreach ($doc as $k => $v) {
            $value = true;
            if (null === $v) {
                continue;
            }
            if (!isset($witnesses[$k])) {
                continue;
            }
            if ('*cleared*' === $v) {
                $value = false;
            }
            foreach ($witnesses[$k] as $witness) {
                if (!isset($doc->{$witness['property']})) {
                    $doc->{$witness['property']} = $value;
                }
            }
        }

        return $doc;
    }
    /**
     * @param array  $doc
     * @param string $fieldName
     *
     * @return array
     *
     * @throws \Exception
     */
    protected function revertDocumentMongoDateWithTimeZoneFieldToDateTime($doc, $fieldName)
    {
        if (!isset($doc[$fieldName])) {
            $doc[$fieldName] = null;

            return $doc;
        }

        if (!isset($doc[sprintf('%s_tz', $fieldName)])) {
            $doc[sprintf('%s_tz', $fieldName)] = date_default_timezone_get();
        }

        if (!$doc[$fieldName] instanceof \MongoDate) {
            if (!is_string($doc[$fieldName])) {
                throw $this->createMalformedException("Field '%s' must be a valid MongoDate", $fieldName);
            }
            $doc[$fieldName] = new \DateTime($doc[$fieldName]);
        } else {
            /** @var \MongoDate $mongoDate */
            $mongoDate = $doc[$fieldName];

            $dateObject = new \DateTime(sprintf('@%d', $mongoDate->sec));
            $dateObject->setTimezone(new \DateTimeZone($doc[sprintf('%s_tz', $fieldName)]));
            $doc[$fieldName] = $dateObject;
        }

        unset($doc[sprintf('%s_tz', $fieldName)]);

        return $doc;
    }
    /**
     * @param mixed $doc
     * @param array $options
     *
     * @return mixed
     */
    protected function computeFingerPrints($doc, $options = [])
    {
        if (!is_object($doc)) {
            return $doc;
        }

        $fingerPrints = $this->getMetaDataService()->getModelFingerPrints($doc);

        foreach ($fingerPrints as $k => $v) {
            $values = [];

            $found = false;
            $clear = false;

            foreach ($v['of'] as $p) {
                if (!isset($doc->$p)) {
                    $values[$p] = null;
                    continue;
                } else {
                    if ('*cleared*' === $doc->$p) {
                        $clear = true;
                    } else {
                        $values[$p] = $doc->$p;
                        $found = true;
                    }
                }
            }

            unset($v['of']);

            if (true === $found) {
                $doc->$k = $this->generateValue(['type' => 'fingerprint'], count($values) > 1 ? $values : array_shift($values));
            } elseif (true === $clear) {
                $doc->$k = '*cleared*';
            }
        }

        unset($options);

        return $doc;
    }
    /**
     * @param $doc
     * @param array $options
     *
     * @return $this
     *
     * @throws \Exception
     */
    protected function refreshEmbeddedReferenceLinks($doc, array $options = [])
    {
        $embeddedReferenceLinks = $this->getMetaDataService()->getModelEmbeddedReferenceLinks($doc, $options);

        if (!count($embeddedReferenceLinks)) {
            return $this;
        }

        $fields = [];

        foreach ($embeddedReferenceLinks as $linkName => $link) {
            $fields += $link['fields'];
        }

        $service = $this->getCrudByModelClass($doc);

        switch ($service->getExpectedTypeCount()) {
            case 1:
                $fullDoc = $service->get($doc->id, $fields);
                break;
            case 2:
                $fullDoc = $service->get($options['parentId'], $doc->id, $fields);
                break;
            default:
                throw $this->createUnexpectedException(
                    "Unsupported number of expected type '%d' for embedded reference links",
                    $service->getExpectedTypeCount()
                );
        }

        foreach ($embeddedReferenceLinks as $linkName => $link) {
            $joinDocClass = $link['joinClass'];
            $joinDoc = new $joinDocClass();
            foreach (array_keys(get_object_vars($joinDoc)) as $field) {
                $joinDoc->$field = isset($fullDoc->$field) ? $fullDoc->$field : null;
            }
            $joinDocArray = $this->convertObjectToArray($joinDoc, $options);
            $owningSideService = $this->getCrudService()->get($link['owningSideType']);
            switch ($owningSideService->getExpectedTypeCount()) {
                case 1:
                    $selectCriteria = [$link['owningSideField'].'.id' => $fullDoc->id];
                    $set = [$link['owningSideField'] => $joinDocArray];
                    break;
                default:
                    return $this;
            }
            $owningSideService->getRepository()->alter($selectCriteria, ['$set' => $set], ['multiple' => true]);
        }

        return $this;
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
