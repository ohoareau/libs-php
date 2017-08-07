<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Form\DataTransformer;

use Itq\Common\Form\Type\BooleanType;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class BooleanTypeToBooleanTransformer implements DataTransformerInterface
{
    /**
     * @param mixed $value
     *
     * @return int
     */
    public function transform($value)
    {
        if (BooleanType::VALUE_NULL === $value) {
            return BooleanType::VALUE_NULL;
        }
        if (false === $value || BooleanType::VALUE_FALSE === $value || '0' === $value || 'false' === $value || 'no' === $value || '' === $value) {
            return BooleanType::VALUE_FALSE;
        }

        return BooleanType::VALUE_TRUE;
    }
    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function reverseTransform($value)
    {
        if (BooleanType::VALUE_NULL === $value) {
            return BooleanType::VALUE_NULL;
        }
        if (BooleanType::VALUE_FALSE === (int) $value) {
            return false;
        }

        return true;
    }
}
