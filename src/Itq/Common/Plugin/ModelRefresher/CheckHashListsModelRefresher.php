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
class CheckHashListsModelRefresher extends Base\AbstractMetaDataAwareModelRefresher
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

        foreach ($this->getMetaDataService()->getModelHashLists($doc) as $property => $list) {
            $list += ['type' => 'mixed'];
            if (!$this->isPopulableModelProperty($doc, $property, $options)) {
                continue;
            }
            if (is_object($doc->$property)) {
                $doc->$property = (array) $doc->$property;
            }
            if (!is_array($doc->$property)) {
                $doc->$property = [];
            }
            foreach ($doc->$property as $kk => $vv) {
                $theProperty = &$doc->$property;
                switch ($list['type']) {
                    case 'bool':
                        $theProperty[$kk] = $this->convertMixedToBool($vv);
                        break;
                    case 'string':
                        $theProperty[$kk] = $this->convertMixedToString($vv);
                        break;
                    case 'integer':
                        $theProperty[$kk] = $this->convertMixedToInteger($vv);
                        break;
                    case 'float':
                        $theProperty[$kk] = $this->convertMixedToFloat($vv);
                        break;
                    default:
                    case 'mixed':
                        break;
                }
            }
            $doc->$property = (object) $doc->$property;
        }

        return $doc;
    }
    /**
     * @param mixed $v
     *
     * @return bool
     */
    protected function convertMixedToBool($v)
    {
        return !(false === $v || 'false' === $v || '0' === $v || '' === $v || 0 === $v || null === $v);
    }
    /**
     * @param mixed $v
     *
     * @return string
     */
    protected function convertMixedToString($v)
    {
        return (string) $v;
    }
    /**
     * @param mixed $v
     *
     * @return int
     */
    protected function convertMixedToInteger($v)
    {
        return (int) $v;
    }
    /**
     * @param mixed $v
     *
     * @return float
     */
    protected function convertMixedToFloat($v)
    {
        return (float) $v;
    }
}
