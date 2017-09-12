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

use Itq\Common\Plugin\ModelDescriptor\YamlFileModelDescriptor;
use Itq\Common\Tests\Plugin\ModelDescriptor\Base\AbstractModelDescriptorTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group plugins
 * @group plugins/model-descriptors
 * @group plugins/model-descriptors/yaml-file
 */
class YamlFileModelDescriptorTest extends AbstractModelDescriptorTestCase
{
    /**
     * @return YamlFileModelDescriptor
     */
    public function d()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::d();
    }
    /**
     * @return array
     */
    public function constructor()
    {
        return [$this->mockedFilesystemService(), $this->mockedYamlService()];
    }
}
