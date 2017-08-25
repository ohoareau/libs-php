<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common\Plugin\ModelRefresher;

use Itq\Common\Plugin\ModelRefresher\ConvertScalarPropertiesModelRefresher;
use Itq\Common\Tests\Plugin\ModelRefresher\Base\AbstractModelRefresherTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group plugins
 * @group plugins/models
 * @group plugins/models/refreshers
 * @group plugins/models/refreshers/convert-scalar-properties
 */
class ConvertScalarPropertiesModelRefresherTest extends AbstractModelRefresherTestCase
{
    /**
     * @return ConvertScalarPropertiesModelRefresher
     */
    public function r()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::r();
    }
    /**
     * @return array
     */
    public function constructor()
    {
        return [$this->mockedMetaDataService()];
    }
}
