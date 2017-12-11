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

use Closure;
use Exception;
use Itq\Common\Traits;
use Itq\Common\RepositoryInterface;

/**
 * Model Stat Service.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class ModelStatsService
{
    use Traits\ServiceTrait;
    use Traits\ServiceAware\CrudServiceAwareTrait;
    use Traits\ServiceAware\MetaDataServiceAwareTrait;
    use Traits\ServiceAware\ExpressionServiceAwareTrait;
    /**
     * @param MetaDataService   $metaDataService
     * @param CrudService       $crudService
     * @param ExpressionService $expressionService
     */
    public function __construct(MetaDataService $metaDataService, CrudService $crudService, ExpressionService $expressionService)
    {
        $this->setMetaDataService($metaDataService);
        $this->setCrudService($crudService);
        $this->setExpressionService($expressionService);
    }
    /**
     * @param array $definition
     * @param mixed $data
     * @param array $options
     *
     * @return void
     * @throws Exception
     */
    public function track(array $definition, $data, array $options = [])
    {
        foreach ($definition as $targetType => $defs) {
            /** @var RepositoryInterface $targetRepo */
            $targetRepo = $this->getCrudService()->get($targetType)->getRepository();
            $ctx        = (object) [
                'fetched' => false, 'incs' => [], 'sets' => [], 'criteria' => [],
                'computedIncs' => [], 'computedSets' => [], 'alterOptions' => [],
                'fields' => [], 'otherSideFields' => [],
            ];

            foreach ($defs as $def) {
                $this->executeTargetRepoTracker($targetRepo, $data, $def, $ctx, $options);
            }

            if (!$ctx->fetched && 0 < count($ctx->fields)) {
                $this->populate($data, $data->id, $ctx->fields, [], $options);
            }

            foreach ($ctx->criteria as $index => $criteria) {
                $updates = [];
                if (count($ctx->incs[$index])) {
                    $updates['$inc'] = $ctx->incs[$index];
                }
                if (count($ctx->sets[$index])) {
                    $updates['$set'] = $ctx->sets[$index];
                }
                if (count($updates)) {
                    $targetRepo->alter($criteria, $updates, $ctx->alterOptions[$index]);
                }
                unset($updates);
            }

            $this->applyComputedAlters($targetRepo, $ctx);
        }
    }
    /**
     * @param RepositoryInterface $targetRepo
     * @param object              $doc
     * @param array               $def
     * @param object              $ctx
     * @param array               $options
     */
    protected function executeTargetRepoTracker(RepositoryInterface $targetRepo, $doc, array $def, $ctx, array $options = [])
    {
        list ($value, $index, $d, $def) = $this->computeTargetRepoTrackerValue($targetRepo, $doc, $def, $ctx, $options);

        if (null === $index) {
            return;
        }

        $this->buildAlters($def, $value, $index, $d, $ctx);
    }
    /**
     * @param array  $def
     * @param mixed  $value
     * @param mixed  $index
     * @param object $d
     * @param object $ctx
     */
    protected function buildAlters(array $def, $value, $index, $d, $ctx)
    {
        $sets         = [];
        $incs         = [];
        $computedSets = [];
        $computedIncs = [];
        $criteria     = ['_id' => $d];

        if (isset($def['replace']) && true === $def['replace']) {
            if ($value instanceof Closure) {
                $computedSets = ['key' => 'stats.'.$def['key'], 'callable' => $value];
            } else {
                $sets = ['stats.'.$def['key'] => $value];
            }
        } elseif (null !== $value) {
            if ($value instanceof \Closure) {
                $computedIncs = ['key' => 'stats.'.$def['key'], 'callable' => $value];
            } else {
                $incs = ['stats.'.$def['key'] => $value];
            }
        }

        if (!isset($ctx->criteria[$index])) {
            $ctx->criteria[$index]     = [];
            $ctx->incs[$index]         = [];
            $ctx->sets[$index]         = [];
            $ctx->computedIncs[$index] = [];
            $ctx->computedSets[$index] = [];
            $ctx->alterOptions[$index] = [];
        }
        $ctx->criteria[$index] += $criteria;
        $ctx->incs[$index]     += $incs;
        $ctx->sets[$index]     += $sets;
        if (count($computedIncs)) {
            $ctx->computedIncs[$index][] = $computedIncs;
        }
        if (count($computedSets)) {
            $ctx->computedSets[$index][] = $computedSets;
        }
        $ctx->alterOptions[$index] += ['multiple' => true];
    }
    /**
     * @param RepositoryInterface $targetRepo
     * @param object              $doc
     * @param array               $def
     * @param object              $ctx
     * @param array               $options
     *
     * @return array
     */
    protected function computeTargetRepoTrackerValue(RepositoryInterface $targetRepo, $doc, array $def, $ctx, array $options = [])
    {
        $fetchFields = ['id' => true];
        $value       = 1;
        $mode        = 'default';

        if (isset($def['increment'])) {
            $value = $def['increment'];
        } elseif (isset($def['decrement'])) {
            $value = -$def['decrement'];
        } elseif (isset($def['formula'])) {
            $formula               = $this->describeFormula($def['formula'], $doc, $targetRepo);
            $fetchFields          += $formula['docFields'];
            $ctx->otherSideFields += $formula['otherDocFields'];
            $value                 = $formula['callable'];
            $def['replace']        = true;
        }

        switch (is_string($value) ? substr($value, 0, 1) : null) {
            case '@':
                $mode                                 = 'fieldValue';
                $fetchFields[substr($value, 1)] = true;
                break;
            case '$':
                $mode                                          = 'statValue';
                $fetchFields['stats.'.substr($value, 1)] = true;
                break;
        }

        if (!isset($def['match'])) {
            return [null, null, null, $def];
        }

        if ('_parent' === $def['match']) {
            if (!isset($options['parentId'])) {
                return [null, null, null, $def];
            }
            $index = '_parent';
            $d     = $options['parentId'];
        } else {
            $index       = $def['match'];
            $kk          = explode('.', $def['match']);
            $kkk         = array_pop($kk);
            $d           = $doc;
            $theOriginId = $d->id;
            $ffield      = null;
            if (count($kk)) {
                foreach ($kk as $mm) {
                    if (null === $ffield) {
                        $ffield = $mm;
                        if (!isset($d->$mm)) {
                            $ctx->fields += $fetchFields + [$mm => true];
                            $d = $this->populate($doc, $d->id, $ctx->fields, [], $options);
                            $ctx->fetched = true;
                        }
                    }
                    $d = $d->$mm;
                }
            } elseif (!isset($d->$kkk)) {
                $ctx->fields += $fetchFields + [$kkk => true];
                $d = $this->populate($doc, $theOriginId, $fetchFields + [$kkk => true], [], $options);
                $ctx->fetched = true;
            }
            if (!is_object($d)) {
                return [null, null, null, $def];
            }
            $d = is_object($d->$kkk) ? $d->$kkk->id : $d->$kkk;
            if (!isset($d)) {
                return [null, null, null, $def];
            }
        }

        switch ($mode) {
            case 'fieldValue':
                $vars     = ['doc' => $doc];
                $keyValue = substr($value, 1);
                $this->populate($doc, $doc->id, [$keyValue => true], ['cache' => false, 'force' => true], $options);
                $value = $this->getExpressionService()->evaluate('$'.'doc.'.substr($value, 1), $vars);
                unset($vars);
                break;
            case 'statValue':
                $vars  = ['stats' => (object) (isset($doc->stats) ? $doc->stats : [])];
                $value = $this->getExpressionService()->evaluate('$'.'stats.'.substr($value, 1), $vars);
                unset($vars);
                break;
        }

        switch ((isset($def['type']) && !($value instanceof Closure)) ? $def['type'] : null) {
            case 'double':
                $value = (float) $value;
                break;
            case 'integer':
                $value = (int) $value;
                break;
        }

        return [$value, $index, $d, $def] ;
    }
    /**
     * @param RepositoryInterface $targetRepo
     * @param object              $ctx
     */
    protected function applyComputedAlters(RepositoryInterface $targetRepo, $ctx)
    {
        foreach ($ctx->criteria as $index => $criteria) {
            $ctx->incs[$index] = [];
            $ctx->sets[$index] = [];
            $updates           = [];
            if (isset($ctx->computedIncs[$index]) && count($ctx->computedIncs[$index])) {
                foreach ($ctx->computedIncs[$index] as $kkk => $cc) {
                    $ctx->incs[$index] += [$cc['key'] => $cc['callable']($criteria, $ctx->otherSideFields)];
                    unset($ctx->computedIncs[$index][$kkk]);
                }
            }
            unset($ctx->computedIncs[$index]);
            if (isset($ctx->computedSets[$index]) && count($ctx->computedSets[$index])) {
                foreach ($ctx->computedSets[$index] as $kkk => $cc) {
                    $ctx->sets[$index] += [$cc['key'] => $cc['callable']($criteria, $ctx->otherSideFields)];
                    unset($ctx->computedSets[$index][$kkk]);
                }
            }
            unset($ctx->computedSets[$index]);
            if (count($ctx->incs[$index])) {
                $updates['$inc'] = $ctx->incs[$index];
            }
            if (count($ctx->sets[$index])) {
                $updates['$set'] = $ctx->sets[$index];
            }
            if (count($updates)) {
                $targetRepo->alter($criteria, $updates, $ctx->alterOptions[$index]);
            }
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
        $fields          = [];
        $otherSideFields = [];
        $stats           = [];

        if (0 < preg_match_all('/(\$|\@|\:)([a-z0-9_\.]+)/i', $dsl, $matches)) {
            foreach ($matches[2] as $i => $match) {
                switch ($matches[1][$i]) {
                    case '$':
                        $stats[$match] = true;
                        $dsl           = str_replace($matches[0][$i], 'stats.'.$match, $dsl);
                        break;
                    case '@':
                        $fields[$match] = true;
                        $dsl           = str_replace($matches[0][$i], 'doc.'.$match, $dsl);
                        break;
                    case ':':
                        $otherSideFields[$match] = true;
                        $dsl                     = str_replace($matches[0][$i], 'otherDoc.'.$match, $dsl);
                        break;
                }
            }
        }

        $expressionService = $this->getExpressionService();
        $callable          = function ($criteria, $otherDocFields) use ($dsl, $doc, $stats, $targetRepo, $expressionService) {
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

            $vars   = ['doc' => $doc, 'otherDoc' => $otherDoc, 'stats' => (object) ($otherDoc->stats)];
            $result = $expressionService->evaluate('$'.$dsl, $vars);

            return $result;
        };

        foreach (array_keys($stats) as $stat) {
            $fields['stats.'.$stat] = true;
        }

        return ['docFields' => $fields, 'otherDocFields' => $otherSideFields, 'callable' => $callable];
    }
    /**
     * @param object $doc
     * @param string $id
     * @param array  $fields
     * @param array  $options
     * @param array  $globalOptions
     *
     * @return object
     *
     * @throws Exception
     */
    protected function populate($doc, $id, array $fields, array $options = [], array $globalOptions = [])
    {
        $options += ['cached' => true, 'force' => false];
        $service  = $this->getCrudService()->get($this->getMetaDataService()->getModel($doc)['id']);

        switch ($service->getExpectedTypeCount()) {
            case 1:
                $d = $service->get($id, $fields, $options);
                break;
            case 2:
                $d = $service->get($globalOptions['parentId'], $id, $fields, $options);
                break;
            default:
                throw $this->createFailedException(
                    "Unsupported type count for service '%d'",
                    $service->getExpectedTypeCount()
                );
        }

        foreach (array_keys($fields) as $field) {
            if (false !== strpos($field, '.')) {
                list($field) = explode('.', $field, 2);
            }
            if (true === $options['force']) {
                if (isset($d->$field)) {
                    $doc->$field = $d->$field;
                }
                continue;
            }
            if (!isset($doc->$field)) {
                $doc->$field = $d->$field;
            }
        }

        return $doc;
    }
}
