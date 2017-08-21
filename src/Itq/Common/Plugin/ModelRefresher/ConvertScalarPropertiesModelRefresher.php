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

use Itq\Common\ModelInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class ConvertScalarPropertiesModelRefresher extends Base\AbstractMetaDataAwareModelRefresher
{
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

        $types = $this->getMetaDataService()->getModelTypes($doc);

        foreach ($types as $property => $type) {
            if (!$this->isPopulableModelProperty($doc, $property, ['populateNulls' => false] + $options)) {
                continue;
            }
            switch ($type['type']) {
                case "DateTime<'c'>":
                    if ('' === $doc->$property) {
                        $doc->$property = null;
                    }
                    break;
            }
        }

        return $doc;
    }
}
