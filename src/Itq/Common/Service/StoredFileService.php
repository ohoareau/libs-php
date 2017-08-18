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

use Itq\Common\Model;
use Itq\Common\Traits;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class StoredFileService
{
    use Traits\ServiceTrait;
    use Traits\ServiceAware\CrudServiceAwareTrait;
    use Traits\ServiceAware\MetaDataServiceAwareTrait;
    /**
     * @param MetaDataService $metaDataService
     * @param CrudService     $crudService
     */
    public function __construct(MetaDataService $metaDataService, CrudService $crudService)
    {
        $this->setMetaDataService($metaDataService);
        $this->setCrudService($crudService);
    }
    /**
     * @param string $id
     * @param array  $fields
     * @param array  $options
     *
     * @return Model\Internal\VirtualFile
     *
     * @throws \Exception
     */
    public function get($id, array $fields = [], array $options = [])
    {
        unset($fields); // currently fields list is ignored, all are returned.

        list ($prefix, $tokens, $extra) = $this->parseId($id);

        $options['extra'] = $extra;

        $storageInfo  = $this->getMetaDataService()->getRetrievableStorageByPrefix($prefix);
        $modelId      = $storageInfo['model'];
        $modelLevel   = substr_count($modelId, '.') + 1;
        $propertyName = $storageInfo['property'];
        $fieldMapping = [
            $propertyName               => 'content',
            $propertyName.'ContentType' => 'contentType',
            $propertyName.'FingerPrint' => 'fingerPrint',
            $propertyName.'Name'        => 'fileName',
        ];

        $fields = array_merge(['id'], array_keys($fieldMapping));

        if (count($tokens) !== $modelLevel) {
            throw $this->createMalformedException('doc.malformed_storedfile_modellevel', $modelLevel, count($tokens));
        }

        switch ($modelLevel) {
            case 1:
                $doc = $this->findOneDocument($modelId, $tokens, $fields, $options);
                break;
            case 2:
                $doc = $this->findOneSubDocument($modelId, $tokens, $fields, $options);
                break;
            default:
                throw $this->createMalformedException('doc.malformed_storedfile_badmodellevel', $modelLevel);
        }

        if (!$doc) {
            throw $this->createNotFoundException('doc.unknown_storedfile', $id);
        }

        $result = new Model\Internal\VirtualFile();

        foreach ($fieldMapping as $k => $v) {
            if (isset($doc->$k)) {
                $result->$v = $doc->$k;
            }
        }

        if (isset($storageInfo['sensitive']) && true === $storageInfo['sensitive']) {
            $result->sensitive = true;
            $result->cacheTtl = 0;
        }
        if (isset($storageInfo['cacheTtl']) && ((int) $storageInfo['cacheTtl']) >= 0) {
            $result->cacheTtl = (int) $storageInfo['cacheTtl'];
        }

        return $result;
    }
    /**
     * @param string $modelId
     * @param array  $tokens
     * @param array  $fields
     * @param array  $options
     *
     * @return object|null
     */
    protected function findOneDocument($modelId, $tokens, array $fields, array $options)
    {
        $token   = array_pop($tokens);
        $service = $this->getCrudService()->get($modelId);

        return $this->applyFirstAvailableMethod([
            [$service, 'getByToken', [$token, $fields, $options]],
            [$service, 'getBy', ['token', $token, $fields, $options]],
            [$service, 'findOne', [['token' => $token], $fields, 0, [], $options]],
        ]);
    }
    /**
     * @param string $modelId
     * @param array  $tokens
     * @param array  $fields
     * @param array  $options
     *
     * @return object|null
     */
    protected function findOneSubDocument($modelId, $tokens, array $fields, array $options)
    {
        $cs            = $this->getCrudService();
        $token         = array_pop($tokens);
        $parentToken   = array_pop($tokens);
        $parentModelId = substr($modelId, 0, strrpos($modelId, '.'));
        $parentField   = substr($modelId, strrpos($modelId, '.') + 1);
        $parentService = $cs->has($parentModelId) ? $cs->get($parentModelId) : null;
        $service       = $cs->has($modelId) ? $cs->get($modelId) : null;

        if ('y' === substr($parentToken, 0, 1)) {
            $parentToken = substr($parentToken, 1);

            $methods = [
                [$service, 'getByToken', [$parentToken, $token, $fields, $options]],
                [$service, 'getBy', [$parentToken, 'token', $token, $fields, $options]],
                [$service, 'findOne', [$parentToken, ['token' => $token], $fields, 0, [], $options]],
                [$parentService, sprintf('get%sByToken', ucfirst($parentField)), [$parentToken, $token, $fields, $options]],
                [$parentService, sprintf('get%sBy', ucfirst($parentField)), [$parentToken, 'token', $token, $fields, $options]],
                [$parentService, sprintf('findOne%s', ucfirst($parentField)), [$parentToken, ['token' => $token], $fields, 0, [], $options]],
                [$parentService, 'getEmbedded', [$parentToken, $parentField, $fields, ['token' => $token], $options]],
            ];
        } else {
            $methods = [
                [$service, 'getByTokenFromParentToken', [$parentToken, $token, $fields, $options]],
                [$service, 'getByFromParentToken', [$parentToken, 'token', $token, $fields, $options]],
                [$service, 'findOneFromParentToken', [$parentToken, ['token' => $token], $fields, 0, [], $options]],
                [$parentService, sprintf('get%sByTokenFromToken', ucfirst($parentField)), [$parentToken, $token, $fields, $options]],
                [$parentService, sprintf('get%sByFromToken', ucfirst($parentField)), [$parentToken, 'token', $token, $fields, $options]],
                [$parentService, sprintf('findOne%sFromToken', ucfirst($parentField)), [$parentToken, ['token' => $token], $fields, 0, [], $options]],
            ];
        }

        return $this->applyFirstAvailableMethod($methods);
    }
    /**
     * @param array $methods
     * @param mixed $defaultValue
     *
     * @return null
     */
    protected function applyFirstAvailableMethod(array $methods, $defaultValue = null)
    {
        $result = $defaultValue;

        foreach ($methods as $method) {
            list ($service, $methodName, $methodParams) = $method;

            if (null === $service || !is_object($service)) {
                continue;
            }
            if (method_exists($service, $methodName)) {
                $result = call_user_func_array([$service, $methodName], $methodParams);
                break;
            }
        }

        return $result;
    }
    /**
     * @param string $raw
     *
     * @return array
     *
     * @throws \Exception
     */
    protected function parseId($raw)
    {
        $items  = explode('z', $raw, 2);
        $prefix = array_shift($items);

        if (!count($items)) {
            throw $this->createMalformedException('doc.required_token');
        }

        $token             = array_shift($items);
        $parentParentToken = null;
        $parentToken       = null;

        if (false !== strpos($token, 'x')) {
            list ($parentToken, $token) = explode('x', $token, 2);

            if (false !== strpos($token, 'x')) {
                $parentParentToken = $parentToken;
                list ($parentToken, $token) = explode('x', $token, 2);
            }
        }

        $extra = null;

        if (false !== strpos($token, 'z')) {
            list ($token, $extra) = explode('z', $token, 2);
        }

        $tokens = array_merge(
            $parentParentToken ? [$parentParentToken] : [],
            $parentToken ? [$parentToken] : [],
            $token ? [$token] : []
        );

        return [$prefix, $tokens, $extra];
    }
}
