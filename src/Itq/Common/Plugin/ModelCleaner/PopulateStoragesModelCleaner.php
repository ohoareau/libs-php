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

use Itq\Common\Traits;
use Itq\Common\Service;
use Itq\Common\ModelInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class PopulateStoragesModelCleaner extends Base\AbstractMetaDataAwareModelCleaner
{
    use Traits\ServiceAware\StorageServiceAwareTrait;
    /**
     * @param Service\MetaDataService $metaDataService
     * @param Service\StorageService  $storageService
     */
    public function __construct(Service\MetaDataService $metaDataService, Service\StorageService $storageService)
    {
        parent::__construct($metaDataService);
        $this->setStorageService($storageService);
    }
    /**
     * @param ModelInterface $doc
     * @param array          $options
     *
     * @return void
     */
    public function clean($doc, array $options = [])
    {
        if (!isset($options['operation']) || ('create' !== $options['operation'] && 'update' !== $options['operation'])) {
            return;
        }

        if (!is_object($doc)) {
            return;
        }

        $this->getStorageService()->populate($doc, $this->getMetaDataService()->getModelStorages($doc), $options);
    }
}
