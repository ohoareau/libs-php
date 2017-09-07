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

use Itq\Common\DocDescriptorInterface;

/**
 * Doc Generator Interface.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
interface DocGeneratorInterface
{
    /**
     * @param DocDescriptorInterface $descriptor
     * @param array                  $options
     *
     * @return $this
     */
    public function describe(DocDescriptorInterface $descriptor, array $options = []);
    /**
     * @param DocDescriptorInterface $docDescriptor
     * @param array                  $options
     *
     * @return mixed
     */
    public function generate(DocDescriptorInterface $docDescriptor, array $options = []);
}
