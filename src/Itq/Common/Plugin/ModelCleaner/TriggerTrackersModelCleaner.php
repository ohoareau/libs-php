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

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class TriggerTrackersModelCleaner extends Base\AbstractMetaDataAwareModelCleaner
{
    use Traits\ServiceAware\TrackerServiceAwareTrait;
    /**
     * @param Service\MetaDataService $metaDataService
     * @param Service\TrackerService  $trackerService
     */
    public function __construct(Service\MetaDataService $metaDataService, Service\TrackerService $trackerService)
    {
        parent::__construct($metaDataService);
        $this->setTrackerService($trackerService);
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
        $data   = clone $doc;
        $tracks = [];
        $map    = [
            'create' => [['key' => '{model}.created']],
            'delete' => [['key' => '{model}.deleted']],
            'update' => [['key' => '{model}.updated'], ['key' => '{model}.{property}', 'if' => 'status']],
        ];

        if (isset($options['operation']) && isset($map[$options['operation']])) {
            foreach ($map[$options['operation']] as $def) {
                if (isset($def['if'])) {
                    if (property_exists($doc, $def['if']) && isset($doc->{$def['if']})) {
                        $tracks[] = str_replace(
                            ['{model}', '{property}'],
                            [$this->getMetaDataService()->getModelIdForClass($doc), $doc->{$def['if']}],
                            $def['key']
                        );
                    }
                    continue;
                }
                $tracks[] = str_replace('{model}', $this->getMetaDataService()->getModelIdForClass($doc), $def['key']);
            }
        }

        foreach ($tracks as $track) {
            foreach ($this->getMetaDataService()->getOperationTrackers($track) as $type => $definition) {
                $this->getTrackerService()->track($type, $definition, $data, $options);
            }
        }
    }
}
