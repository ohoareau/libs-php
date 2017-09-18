<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common\Plugin\ModelDescriptor;

use Itq\Common\Plugin\ModelDescriptor\MemoryModelDescriptor;
use Itq\Common\Tests\Plugin\ModelDescriptor\Base\AbstractModelDescriptorTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group plugins
 * @group plugins/model-descriptors
 * @group plugins/model-descriptors/memory
 */
class MemoryModelDescriptorTest extends AbstractModelDescriptorTestCase
{
    /**
     * @return MemoryModelDescriptor
     */
    public function d()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::d();
    }
}
