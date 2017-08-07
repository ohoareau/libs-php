<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\RuleType;

use Itq\Common\Plugin\RuleType\Base\AbstractRuleType;
use /** @noinspection PhpUnusedAliasInspection */ Itq\Common\Annotation;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class ValueRuleType extends AbstractRuleType
{
    /**
     * @param array $data
     * @param array $config
     * @param array $options
     *
     * @Annotation\RuleType("value")
     *
     * @return mixed|null
     */
    public function valueRule(array $data, array $config, array $options = [])
    {
        if ($this->skippedValue($data, 'value', $config, $options)) {
            return null;
        }

        if ($this->skippedIf($data, $config, $options)) {
            return null;
        }

        return $this->computedFloatValue($config, 'value', $data, $options);
    }
}
