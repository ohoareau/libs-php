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
    public function __construct(
        MetaDataService $metaDataService,
        CrudService $crudService,
        ExpressionService $expressionService
    ) {
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
     */
    public function track(array $definition, $data, array $options = [])
    {
        foreach ($definition as $targetType => $defs) {
            /** @var RepositoryInterface $targetRepo */
            $targetRepo = $this->getCrudService()->get($targetType)->getRepository();
            $ctx        = (object) [
                'doc'                  => $data,
                'targetRepo'           => $targetRepo,
                'fetched'              => false,
                'incsBag'              => [],
                'setsBag'              => [],
                'criteriaBag'          => [],
                'computedIncsBag'      => [],
                'computedSetsBag'      => [],
                'alterOptionsBag'      => [],
                'realFetchedFields'    => [],
                'otherSideFetchFields' => [],
                'options'              => $options,
            ];

            foreach ($defs as $def) {
                $this->executeTargetRepoTracker($targetRepo, $ctx->doc, $def, $ctx);
            }

            $this->fetchMissingFields($ctx);
            $this->applyAlters($ctx);
            $this->applyComputedAlters($ctx);
        }
    }
    /**
     * @param RepositoryInterface $targetRepo
     * @param object              $doc
     * @param array               $def
     * @param object              $ctx
     */
    protected function executeTargetRepoTracker(RepositoryInterface $targetRepo, $doc, array $def, $ctx)
    {
        $fetchFields = ['id' => true];
        $value       = 1;

        if (isset($def['increment'])) {
            $value = $def['increment'];
        } elseif (isset($def['decrement'])) {
            $value = -$def['decrement'];
        } elseif (isset($def['formula'])) {
            $formulaDescription         = $this->describeFormula($def['formula'], $doc, $targetRepo);
            $fetchFields               += $formulaDescription['docFields'];
            $ctx->otherSideFetchFields += $formulaDescription['otherDocFields'];
            $value                      = $formulaDescription['callable'];
            $def['replace']             = true;
        }

        if (is_string($value)) {
            if ('@' === substr($value, 0, 1)) {
                $fetchFields[substr($value, 1)] = true;
            } elseif ('$' === substr($value, 0, 1)) {
                $fetchFields['stats.'.substr($value, 1)] = true;
            }
        }

        if (!isset($def['match'])) {
            return;
        }

        if ('_parent' === $def['match']) {
            $index = '_parent';
            if (!isset($ctx->options['parentId'])) {
                return;
            }
            $criteria = ['_id' => $ctx->options['parentId']];
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
                            $ctx->realFetchedFields += $fetchFields + [$mm => true];
                            $d2 = $this->getDocument($doc, $d->id, $ctx->realFetchedFields, ['cached' => true], $ctx->options);
                            foreach (array_keys($ctx->realFetchedFields) as $realFetchedField) {
                                if (false !== strpos($realFetchedField, '.')) {
                                    list($realFetchedField) = explode('.', $realFetchedField, 2);
                                }
                                if (!isset($doc->$realFetchedField)) {
                                    $doc->$realFetchedField = $d2->$realFetchedField;
                                }
                            }
                            $d            = $doc;
                            $ctx->fetched = true;
                        }
                    }
                    $d = $d->$mm;
                }
            } elseif (!isset($d->$kkk)) {
                $ctx->realFetchedFields += $fetchFields + [$kkk => true];
                $d2                      = $this->getDocument($doc, $theOriginId, $fetchFields + [$kkk => true], ['cached' => true], $ctx->options);
                foreach (array_keys($ctx->realFetchedFields) as $realFetchedField) {
                    if (false !== strpos($realFetchedField, '.')) {
                        list($realFetchedField) = explode('.', $realFetchedField, 2);
                    }
                    if (!isset($doc->$realFetchedField)) {
                        $doc->$realFetchedField = $d2->$realFetchedField;
                    }
                }
                $d            = $doc;
                $ctx->fetched = true;
            }
            if (!is_object($d)) {
                return;
            }
            $d = is_object($d->$kkk) ? $d->$kkk->id : $d->$kkk;
            if (!isset($d)) {
                return;
            }
            $criteria = ['_id' => $d];
        }

        if (is_string($value)) {
            if ('@' === substr($value, 0, 1)) {
                $vars     = ['doc' => $doc];
                $keyValue = substr($value, 1);
                if (!isset($doc->$keyValue)) {
                    $d4 = $this->getDocument($doc, $doc->id, [$keyValue => true], [], $ctx->options);
                    if (isset($d4->$keyValue)) {
                        $doc->$keyValue = $d4->$keyValue;
                    }
                }
                $value = $this->getExpressionService()->evaluate('$'.'doc.'.substr($value, 1), $vars);
                unset($vars);
            } elseif ('$' === substr($value, 0, 1)) {
                $vars  = ['stats' => (object) (isset($doc->stats) ? $doc->stats : [])];
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

        $sets         = [];
        $incs         = [];
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

        if (!isset($ctx->criteriaBag[$index])) {
            $ctx->criteriaBag[$index]     = [];
            $ctx->incsBag[$index]         = [];
            $ctx->setsBag[$index]         = [];
            $ctx->computedIncsBag[$index] = [];
            $ctx->computedSetsBag[$index] = [];
            $ctx->alterOptionsBag[$index] = [];
        }
        $ctx->criteriaBag[$index] += $criteria;
        $ctx->incsBag[$index]     += $incs;
        $ctx->setsBag[$index]     += $sets;
        if (count($computedIncs)) {
            $ctx->computedIncsBag[$index][] = $computedIncs;
        }
        if (count($computedSets)) {
            $ctx->computedSetsBag[$index][] = $computedSets;
        }
        $ctx->alterOptionsBag[$index] += ['multiple' => true];
    }
    /**
     * @param object $ctx
     */
    protected function fetchMissingFields($ctx)
    {
        if ($ctx->fetched || count($ctx->realFetchedFields)) {
            return;
        }

        $otherSideDoc = $this->getDocument($ctx->doc, $ctx->doc->id, $ctx->realFetchedFields, ['cached' => true], $ctx->options);

        foreach (array_keys($ctx->realFetchedFields) as $realFetchedField) {
            if (isset($ctx->doc->$realFetchedField)) {
                continue;
            }
            $ctx->doc->$realFetchedField = $otherSideDoc->$realFetchedField;
        }
    }
    /**
     * @param object $ctx
     */
    protected function applyAlters($ctx)
    {
        /** @var RepositoryInterface $targetRepo */
        $targetRepo = $ctx->targetRepo;

        foreach ($ctx->criteriaBag as $index => $criteria) {
            $updates = [];
            if (count($ctx->incsBag[$index])) {
                $updates['$inc'] = $ctx->incsBag[$index];
            }
            if (count($ctx->setsBag[$index])) {
                $updates['$set'] = $ctx->setsBag[$index];
            }
            if (count($updates)) {
                $targetRepo->alter($criteria, $updates, $ctx->alterOptionsBag[$index]);
            }
        }
    }
    /**
     * @param object $ctx
     */
    protected function applyComputedAlters($ctx)
    {
        /** @var RepositoryInterface $targetRepo */
        $targetRepo = $ctx->targetRepo;

        foreach ($ctx->criteriaBag as $index => $criteria) {
            $ctx->incsBag[$index] = [];
            $ctx->setsBag[$index] = [];
            $updates              = [];
            if (isset($ctx->computedIncsBag[$index]) && count($ctx->computedIncsBag[$index])) {
                foreach ($ctx->computedIncsBag[$index] as $kkk => $cc) {
                    $ctx->incsBag[$index] += [$cc['key'] => $cc['callable']($criteria, $ctx->otherSideFetchFields)];
                    unset($ctx->computedIncsBag[$index][$kkk]);
                }
            }
            unset($ctx->computedIncsBag[$index]);
            if (isset($ctx->computedSetsBag[$index]) && count($ctx->computedSetsBag[$index])) {
                foreach ($ctx->computedSetsBag[$index] as $kkk => $cc) {
                    $ctx->setsBag[$index] += [$cc['key'] => $cc['callable']($criteria, $ctx->otherSideFetchFields)];
                    unset($ctx->computedSetsBag[$index][$kkk]);
                }
            }
            unset($ctx->computedSetsBag[$index]);
            if (count($ctx->incsBag[$index])) {
                $updates['$inc'] = $ctx->incsBag[$index];
            }
            if (count($ctx->setsBag[$index])) {
                $updates['$set'] = $ctx->setsBag[$index];
            }
            if (count($updates)) {
                $targetRepo->alter($criteria, $updates, $ctx->alterOptionsBag[$index]);
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
        $service = $this->getCrudService()->get($this->getMetaDataService()->getModel($doc)['id']);

        switch ($service->getExpectedTypeCount()) {
            case 1:
                return $service->get($id, $realFetchedFields, $options);
            case 2:
                return $service->get($globalOptions['parentId'], $id, $realFetchedFields, $options);
            default:
                throw $this->createFailedException("Unsupported type count for service '%d'", $service->getExpectedTypeCount());
        }
    }
}
