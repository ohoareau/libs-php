<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\Formatter;

use Itq\Common\Traits;
use Itq\Common\Plugin\Base\AbstractPlugin;
use /** @noinspection PhpUnusedAliasInspection */ Itq\Common\Annotation;

use JMS\Serializer\SerializerInterface;
use JMS\Serializer\SerializationContext;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class JsonFormatter extends AbstractPlugin
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
     * @Annotation\Formatter("application/json")
     * @Annotation\Formatter("text/json")
     */
    public function format($data, array $options = [])
    {
        $context = SerializationContext::create();

        if (isset($options['groups'])) {
            $context->setGroups($options['groups']);
        }

        return $this->getSerializer()->serialize($data, 'json', $context);
    }
}
