<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\DataFilter;

use Itq\Common\Traits;
use Itq\Common\Service;
use Itq\Common\ModelInterface;
use Itq\Common\Plugin\DataFilter\Base\AbstractDataFilter;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class ModelRestrictDataFilter extends AbstractDataFilter
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
     * @param mixed $data
     *
     * @return bool
     */
    public function supports($data)
    {
        return $data instanceof ModelInterface;
    }
    /**
     * @param mixed    $data
     * @param object   $ctx
     * @param \Closure $pipeline
     *
     * @return mixed
     */
    public function filter($data, $ctx, \Closure $pipeline)
    {
        if (!$this->getMetaDataService()->isModel($data)) {
            return $data;
        }

        $modelRestricts = $this->getMetaDataService()->getModelExposeRestricts($data);

        foreach (array_keys(get_object_vars($data)) as $property) {
            if (!isset($data->$property)) {
                continue;
            }
            if (is_array($data->$property)) {
                $pipeline($data->$property);
                continue;
            }
            if (!isset($modelRestricts[$property])) {
                continue;
            }
            foreach ($modelRestricts[$property] as $restrict) {
                if ($this->isDenied($restrict, $ctx)) {
                    unset($data->$property);
                    break;
                }
            }
            unset($modelRestricts[$property]);
        }

        return $data;
    }
    /**
     * @param array $definition
     * @param mixed $ctx
     *
     * @return bool
     */
    protected function isDenied($definition, $ctx)
    {
        if (isset($definition['roles'])) {
            if (!$this->isContextHavingRole($definition['roles'], $ctx)) {
                return true;
            }
        }

        return false;
    }
    /**
     * @param array  $roles
     * @param object $ctx
     *
     * @return bool
     */
    protected function isContextHavingRole($roles, $ctx)
    {
        $havingAllOfTheseRoles = array_fill_keys($ctx->user->getRoles(), true);

        foreach ($roles as $role) {
            if (isset($havingAllOfTheseRoles[$role])) {
                return true;
            }
        }

        return false;
    }
}
