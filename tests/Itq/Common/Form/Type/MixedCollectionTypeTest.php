<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common\Form\Type;

use Itq\Common\Form\Type\MixedCollectionType;
use Itq\Common\Tests\Form\Type\Base\AbstractTypeFormTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group forms
 * @group forms/types
 * @group forms/types/mixed-collection
 */
class MixedCollectionTypeTest extends AbstractTypeFormTestCase
{
    /**
     * @return MixedCollectionType
     */
    public function t()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::t();
    }
    /**
     * @group unit
     */
    public function testGetName()
    {
        $this->assertEquals('app_mixedcollection', $this->t()->getName());
    }
}
