<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\ModelPropertyMutator;

use Closure;
use Itq\Common\ModelInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class TypesModelPropertyMutator extends Base\AbstractModelPropertyMutator
{
    /**
     * @param ModelInterface $doc
     * @param string         $k
     * @param array          $m
     *
     * @return bool
     */
    public function supports($doc, $k, array &$m)
    {
        return true === isset($m['types'][$k]);
    }
    /**
     * @param ModelInterface $doc
     * @param string         $k
     * @param mixed          $v
     * @param array          $m
     * @param array          $data
     * @param Closure        $objectMutator
     * @param array          $options
     *
     * @return mixed
     */
    public function mutate($doc, $k, $v, array &$m, array &$data, Closure $objectMutator, array $options = [])
    {
        switch (true) {
            case 'DateTime' === substr($m['types'][$k]['type'], 0, 8):
                $data = $this->revertDocumentMongoDateWithTimeZoneFieldToDateTime($data, $k);
                $v = $data[$k];
        }

        return  $v;
    }
    /**
     * @param array  $doc
     * @param string $fieldName
     *
     * @return array
     *
     * @throws \Exception
     */
    protected function revertDocumentMongoDateWithTimeZoneFieldToDateTime($doc, $fieldName)
    {
        if (!isset($doc[$fieldName])) {
            $doc[$fieldName] = null;

            return $doc;
        }

        if (!isset($doc[sprintf('%s_tz', $fieldName)])) {
            $doc[sprintf('%s_tz', $fieldName)] = date_default_timezone_get();
        }

        if (!$doc[$fieldName] instanceof \MongoDate) {
            if (!is_string($doc[$fieldName])) {
                throw $this->createMalformedException("Field '%s' must be a valid MongoDate", $fieldName);
            }
            $doc[$fieldName] = new \DateTime($doc[$fieldName]);
        } else {
            /** @var \MongoDate $mongoDate */
            $mongoDate = $doc[$fieldName];

            $dateObject = new \DateTime(sprintf('@%d', $mongoDate->sec));
            $dateObject->setTimezone(new \DateTimeZone($doc[sprintf('%s_tz', $fieldName)]));
            $doc[$fieldName] = $dateObject;
        }

        unset($doc[sprintf('%s_tz', $fieldName)]);

        return $doc;
    }
}
