<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common\Plugin\ModelFieldListFilter;

use Itq\Common\Plugin\ModelFieldListFilter\RequirementsModelFieldListFilter;
use Itq\Common\Tests\Plugin\ModelFieldListFilter\Base\AbstractModelFieldListFilterTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group plugins
 * @group plugins/models
 * @group plugins/models/field-list-filters
 * @group plugins/models/field-list-filters/requirements
 */
class RequirementsModelFieldListFilterTest extends AbstractModelFieldListFilterTestCase
{
    /**
     * @return RequirementsModelFieldListFilter
     */
    public function f()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::f();
    }
    /**
     * @return array
     */
    public function constructor()
    {
        return [$this->mockedMetaDataService()];
    }
}
