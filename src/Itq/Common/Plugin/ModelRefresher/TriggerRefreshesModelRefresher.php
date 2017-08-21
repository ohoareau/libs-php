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
use Itq\Common\ModelInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class TriggerRefreshesModelRefresher extends Base\AbstractMetaDataAwareModelRefresher
{
    /**
     * @param ModelInterface $doc
     * @param array          $options
     *
     * @return ModelInterface
     *
     * @throws Exception
     */
    public function refresh($doc, array $options = [])
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
}
