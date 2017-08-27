<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Service\Model;

use Itq\Common\ModelInterface;
use Itq\Common\ObjectPopulatorInterface;
use Itq\Common\Aware\ModelPropertyMutatorAwareInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
interface ModelPropertyMutatorServiceInterface extends ModelPropertyMutatorAwareInterface
{
    /**
     * @param ModelInterface           $doc
     * @param array                    $data
     * @param object                   $ctx
     * @param ObjectPopulatorInterface $objectPopulator
     * @param array                    $options
     *
     * @return void
     */
    public function mutate($doc, $data, $ctx, ObjectPopulatorInterface $objectPopulator, array $options = []);
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
    public function mutateProperty($doc, $k, $v, $m, $data, ObjectPopulatorInterface $objectPopulator, $options);
}
