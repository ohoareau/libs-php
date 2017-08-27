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
interface ModelRestricterAwareInterface
{
    /**
     * @param Plugin\ModelRestricterInterface $restricter
     *
     * @return $this
     */
    public function addModelRestricter(Plugin\ModelRestricterInterface $restricter);
    /**
     * @return Plugin\ModelRestricterInterface[]
     */
    public function getModelRestricters();
}
