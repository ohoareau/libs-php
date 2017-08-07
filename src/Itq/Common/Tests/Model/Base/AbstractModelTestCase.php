<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Tests\Model\Base;

use Itq\Common\Model\Base\AbstractModel;
use Itq\Common\Tests\Base\AbstractTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractModelTestCase extends AbstractTestCase
{
    /**
     * @return AbstractModel
     */
    public function m()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::o();
    }
    /**
     * @return AbstractModel
     */
    protected function getModel()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return $this->getObject();
    }
    /**
     * @return string
     */
    protected function getModelClass()
    {
        return $this->getObjectClass();
    }
}
