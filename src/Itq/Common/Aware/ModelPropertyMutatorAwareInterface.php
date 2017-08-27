<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Aware;

use Itq\Common\Plugin;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
interface ModelPropertyMutatorAwareInterface
{
    /**
     * @param Plugin\ModelPropertyMutatorInterface $propertyMutator
     *
     * @return $this
     */
    public function addModelPropertyMutator(Plugin\ModelPropertyMutatorInterface $propertyMutator);
    /**
     * @return Plugin\ModelPropertyMutatorInterface[]
     */
    public function getModelPropertyMutators();
}
