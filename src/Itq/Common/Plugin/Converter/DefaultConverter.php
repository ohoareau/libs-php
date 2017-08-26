<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\Converter;

use Exception;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class DefaultConverter extends Base\AbstractConverter
{
    /**
     * @param mixed $value
     *
     * @return string
     *
     * @Annotation\Converter("base64_to_plain")
     *
     * @throws Exception
     */
    public function convertBase64ToPlain($value)
    {
        $decoded = base64_decode($value);

        if ($this->hasPhpFunction('mb_detect_encoding') && !mb_detect_encoding($decoded, ['UTF-8', 'ASCII'], true)) {
            throw $this->createMalformedException("Value is not valid base64 encoded UTF-8/ASCII");
        }

        return $decoded;
    }
    /**
     * @param string $value
     *
     * @return string
     *
     * @Annotation\Converter("plain_to_base64")
     */
    public function convertPlainToBase64($value)
    {
        return base64_encode($value);
    }
}
