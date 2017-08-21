<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\ModelRefresher;

use Exception;
use Itq\Common\Traits;
use Itq\Common\Service;
use Itq\Common\ModelInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class SaveStoragesModelRefresher extends Base\AbstractMetaDataAwareModelRefresher
{
    use Traits\ServiceAware\StorageServiceAwareTrait;
    /**
     * @param Service\MetaDataService $metaDataService
     * @param Service\StorageService  $storageService
     */
    public function __construct(
        Service\MetaDataService $metaDataService,
        Service\StorageService $storageService
    ) {
        parent::__construct($metaDataService);
        $this->setStorageService($storageService);
    }
    /**
     * @param ModelInterface $doc
     * @param array          $options
     *
     * @return ModelInterface
     */
    public function refresh($doc, array $options = [])
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
     * @param mixed $value
     * @param array $definition
     * @param mixed $vars
     *
     * @return string
     *
     * @throws Exception
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
}
