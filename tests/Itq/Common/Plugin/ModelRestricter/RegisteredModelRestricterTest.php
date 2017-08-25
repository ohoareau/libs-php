<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common\Plugin\ModelRestricter;

use Itq\Common\Plugin\ModelRestricter\RegisteredModelRestricter;
use Itq\Common\Tests\Plugin\ModelRestricter\Base\AbstractModelRestricterTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group plugins
 * @group plugins/models
 * @group plugins/models/restricters
 * @group plugins/models/restricters/registered
 */
class RegisteredModelRestricterTest extends AbstractModelRestricterTestCase
{
    /**
     * @return RegisteredModelRestricter
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
        return [$this->mockedMetaDataService(), $this->mockedCrudService(), $this->mockedExpressionService()];
    }
}
