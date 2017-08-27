<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\Formatter\Base;

use Itq\Common\Traits;
use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;
use /** @noinspection PhpUnusedAliasInspection */ Itq\Common\Annotation;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractSerializerFormatter extends AbstractFormatter
{
    use Traits\SerializerAwareTrait;
    /**
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->setSerializer($serializer);
    }
    /**
     * @param mixed  $data
     * @param string $format
     * @param array  $options
     *
     * @return string
     */
    protected function handleFormat($data, $format, array $options = [])
    {
        $context = SerializationContext::create();

        if (isset($options['groups'])) {
            $context->setGroups($options['groups']);
        }

        return $this->getSerializer()->serialize($data, $format, $context);
    }
}
