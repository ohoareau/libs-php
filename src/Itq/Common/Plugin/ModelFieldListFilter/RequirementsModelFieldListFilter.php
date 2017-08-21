<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\ModelFieldListFilter;

use Itq\Common\Traits;
use Itq\Common\Service;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class RequirementsModelFieldListFilter extends Base\AbstractModelFieldListFilter
{
    use Traits\ServiceAware\MetaDataServiceAwareTrait;
    /**
     * @param Service\MetaDataService $metaDataService
     */
    public function __construct(Service\MetaDataService $metaDataService)
    {
        $this->setMetaDataService($metaDataService);
    }
    /**
     * @param string $model
     * @param array  $fields
     * @param array  $options
     */
    public function filter($model, array &$fields, array $options = [])
    {
        foreach ($fields as $k => $v) {
            $requirements = $this->getMetaDataService()->getModelPropertyRequirements($model, $k);

            if (!isset($requirements['fields']) || !is_array($requirements['fields'])) {
                continue;
            }

            foreach ($requirements['fields'] as $_k) {
                $fields[$_k] = true;
            }
        }
    }
}
