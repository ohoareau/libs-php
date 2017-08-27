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

use Itq\Common\ModelInterface;
use Itq\Common\ObjectPopulatorInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class CachedListsModelPropertyMutator extends Base\AbstractModelPropertyMutator
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
        return true === isset($m['cachedLists'][$k]);
    }
    /**
     * @param ModelInterface           $doc
     * @param string                   $k
     * @param mixed                    $v
     * @param array                    $m
     * @param array                    $data
     * @param ObjectPopulatorInterface $objectPopulator
     * @param array                    $options
     *
     * @return mixed
     */
    public function mutate($doc, $k, $v, array &$m, array &$data, ObjectPopulatorInterface $objectPopulator, array $options = [])
    {
        $tt = isset($m['cachedLists'][$k]['class']) ? $m['cachedLists'][$k]['class'] : (isset($m['types'][$k]) ? $m['types'][$k]['type'] : null);
        if (null !== $tt) {
            $tt = preg_replace('/^array<([^>]+)>$/', '\\1', $tt);
        }
        if (!is_array($v)) {
            $v = [];
        }
        $subDocs = [];
        foreach ($v as $kk => $vv) {
            $subDocs[$kk] = $objectPopulator->populateObject($this->createModelInstance(['model' => $tt]), $vv, $options);
        }

        return $subDocs;
    }
    /**
     * @param array $options
     *
     * @return object
     */
    protected function createModelInstance(array $options)
    {
        $class = $options['model'];

        return new $class();
    }
}
