<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common\Service\Base;

use Tests\Itq\Common\Base\AbstractTestCase;

use Itq\Common\Traits\ServiceTrait;

use Itq\Common\Service as CommonService;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractServiceTestCase extends AbstractTestCase
{
    /**
     * @return ServiceTrait
     */
    public function s()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::o();
    }
    /**
     * @return ServiceTrait
     */
    protected function getService()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return $this->getObject();
    }
    /**
     * @return string
     */
    protected function getServiceClass()
    {
        return $this->getObjectClass();
    }
}
