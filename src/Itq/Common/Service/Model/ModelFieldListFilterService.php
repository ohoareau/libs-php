<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Service\Model;

use Itq\Common\Plugin;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class ModelFieldListFilterService extends Base\AbstractModelFieldListFilterService
{
    /**
     * @param Plugin\ModelFieldListFilterInterface $fieldListFilter
     *
     * @return $this
     */
    public function addModelFieldListFilter(Plugin\ModelFieldListFilterInterface $fieldListFilter)
    {
        return $this->pushArrayParameterItem('fieldListFilters', $fieldListFilter);
    }
    /**
     * @return Plugin\ModelFieldListFilterInterface[]
     */
    public function getModelFieldListFilters()
    {
        return $this->getArrayParameter('fieldListFilters');
    }
    /**
     * @param string $model
     * @param array  $fields
     * @param array  $options
     *
     * @return array
     */
    public function prepareFields($model, $fields, array $options = [])
    {
        $cleanedFields = [];

        foreach ((is_array($fields) ? $fields : []) as $k => $v) {
            if (is_numeric($k)) {
                if (!is_string($v)) {
                    $v = (string) $v;
                }
                $k = $v;
                $v = true;
            } else {
                if (!is_bool($v)) {
                    $v = (bool) $v;
                }
            }
            $cleanedFields[$k] = $v;
        }

        foreach ($this->getModelFieldListFilters() as $fieldListFilter) {
            $fieldListFilter->filter($model, $cleanedFields, $options);
        }

        return $cleanedFields;
    }
}
