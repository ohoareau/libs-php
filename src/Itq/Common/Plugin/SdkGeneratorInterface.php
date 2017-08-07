<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin;

use Itq\Common\SdkDescriptorInterface;

/**
 * Sdk Generator Interface.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
interface SdkGeneratorInterface
{
    /**
     * @param SdkDescriptorInterface $descriptor
     * @param array                  $options
     *
     * @return $this
     */
    public function describe(SdkDescriptorInterface $descriptor, array $options = []);
    /**
     * @param SdkDescriptorInterface $sdkDescriptor
     * @param array                  $options
     *
     * @return mixed
     */
    public function generate(SdkDescriptorInterface $sdkDescriptor, array $options = []);
}
