<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common\Plugin\CriteriumType;

use Itq\Common\Plugin\CriteriumType\ValueCriteriumType;
use Itq\Common\Tests\Plugin\CriteriumType\Base\AbstractCriteriumTypeTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group plugins
 * @group plugins/criterium-types
 * @group plugins/criterium-types/value
 */
class ValueCriteriumTypeTest extends AbstractCriteriumTypeTestCase
{
    /**
     * @return ValueCriteriumType
     */
    public function c()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::c();
    }
    /**
     * @return array
     */
    public function constructor()
    {
        return [null];
    }
}
