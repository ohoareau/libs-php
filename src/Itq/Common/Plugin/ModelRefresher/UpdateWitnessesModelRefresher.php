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
class UpdateWitnessesModelRefresher extends Base\AbstractMetaDataAwareModelRefresher
{
    /**
     * @param ModelInterface $doc
     * @param array          $options
     *
     * @return ModelInterface
     */
    public function refresh($doc, array $options = [])
    {
        unset($options);

        $witnesses = $this->getMetaDataService()->getModelWitnesses($doc);

        foreach ((array) $doc as $k => $v) {
            $value = true;
            if (null === $v) {
                continue;
            }
            if (!isset($witnesses[$k])) {
                continue;
            }
            if ('*cleared*' === $v) {
                $value = false;
            }
            foreach ($witnesses[$k] as $witness) {
                if (!isset($doc->{$witness['property']})) {
                    $doc->{$witness['property']} = $value;
                }
            }
        }

        return $doc;
    }
}
