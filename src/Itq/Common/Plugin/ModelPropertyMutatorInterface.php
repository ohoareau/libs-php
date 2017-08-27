<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin;

use Closure;
use Itq\Common\ModelInterface;
use Itq\Common\ObjectPopulatorInterface;

/**
 * Model Property Mutator Interface.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
interface ModelPropertyMutatorInterface
{
    /**
     * @param ModelInterface $doc
     * @param string         $k
     * @param array          $m
     *
     * @return bool
     */
    public function supports($doc, $k, array &$m);
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
    public function mutate($doc, $k, $v, array &$m, array &$data, ObjectPopulatorInterface $objectPopulator, array $options = []);
}
