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
class EmbeddedListsModelPropertyMutator extends Base\AbstractModelPropertyMutator
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
        return true === isset($m['embeddedLists'][$k]);
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
        $tt = isset($m['embeddedLists'][$k]['class']) ? $m['embeddedLists'][$k]['class'] : (isset($m['types'][$k]) ? $m['types'][$k]['type'] : null);
        if (null !== $tt) {
            $tt = preg_replace('/^array<([^>]+)>$/', '\\1', $tt);
        }
        if (!is_array($v)) {
            $v = [];
        }
        $subDocs = [];
        foreach ($v as $kk => $vv) {
            $subDocs[$kk] = $objectMutator($vv, $this->createModelInstance(['model' => $tt]), $options);
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
