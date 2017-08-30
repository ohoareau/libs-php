<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common\Plugin\DataProvider\Supervision;

use Itq\Common\Plugin\DataProvider\Supervision\SymfonySupervisionDataProvider;
use Itq\Common\Tests\Plugin\DataProvider\Base\AbstractDataProviderTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group plugins
 * @group plugins/data-providers
 * @group plugins/data-providers/supervision
 * @group plugins/data-providers/supervision/symfony
 */
class SymfonySupervisionDataProviderTest extends AbstractDataProviderTestCase
{
    /**
     * @return SymfonySupervisionDataProvider
     */
    public function p()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::p();
    }
    /**
     * @return array
     */
    public function constructor()
    {
        return [$this->mockedSymfonyService()];
    }
}
