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
class CheckBasicListsModelRefresher extends Base\AbstractMetaDataAwareModelRefresher
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

        foreach ($this->getMetaDataService()->getModelBasicLists($doc) as $property => $list) {
            if (!$this->isPopulableModelProperty($doc, $property, $options)) {
                continue;
            }
            if (is_object($doc->$property)) {
                $doc->$property = (array) $doc->$property;
            }
            if (!is_array($doc->$property)) {
                $doc->$property = [];
            }
            $doc->$property = array_values($doc->$property);
        }

        return $doc;
    }
}
