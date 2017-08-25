<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Tests\Plugin\CriteriumType\Mongo\Base;

use Itq\Common\Plugin\CriteriumType\Mongo\Base\AbstractMongoCriteriumType;
use Itq\Common\Tests\Plugin\CriteriumType\Base\AbstractCriteriumTypeTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractMongoCriteriumTypeTestCase extends AbstractCriteriumTypeTestCase
{
    /**
     * @return AbstractMongoCriteriumType
     */
    public function c()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::c();
    }
}
