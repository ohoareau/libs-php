<?php

/*
 * This file is part of the WS package.
 *
 * (c) itiQiti SAS <cto@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\Formatter;

use Itq\Common\Traits;
use /** @noinspection PhpUnusedAliasInspection */ Itq\Common\Annotation;

use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class XmlFormatter extends Base\AbstractFormatter
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
     * @param mixed $data
     * @param array $options
     *
     * @return string
     *
     * @Annotation\Formatter("text/xml")
     */
    public function format($data, array $options = [])
    {
        $context = SerializationContext::create();

        if (isset($options['groups'])) {
            $context->setGroups($options['groups']);
        }

        return $this->getSerializer()->serialize($data, 'xml', $context);
    }
}
