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
interface ModelFieldListFilterAwareInterface
{
    /**
     * @param Plugin\ModelFieldListFilterInterface $fieldListFilter
     *
     * @return $this
     */
    public function addModelFieldListFilter(Plugin\ModelFieldListFilterInterface $fieldListFilter);
    /**
     * @return Plugin\ModelFieldListFilterInterface[]
     */
    public function getModelFieldListFilters();
}
