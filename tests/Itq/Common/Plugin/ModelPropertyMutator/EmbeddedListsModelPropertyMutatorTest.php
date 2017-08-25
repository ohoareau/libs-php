<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common\Plugin\ModelPropertyMutator;

use Itq\Common\Plugin\ModelPropertyMutator\EmbeddedListsModelPropertyMutator;
use Itq\Common\Tests\Plugin\ModelPropertyMutator\Base\AbstractModelPropertyMutatorTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group plugins
 * @group plugins/models
 * @group plugins/models/property-mutators
 * @group plugins/models/property-mutators/embedded-lists
 */
class EmbeddedListsModelPropertyMutatorTest extends AbstractModelPropertyMutatorTestCase
{
    /**
     * @return EmbeddedListsModelPropertyMutator
     */
    public function m()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::m();
    }
}
