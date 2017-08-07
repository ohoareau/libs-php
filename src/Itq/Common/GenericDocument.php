<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class GenericDocument extends Document
{
    /**
     * @param mixed  $content
     * @param string $format
     */
    public function __construct($content, $format)
    {
        list($contentType, $fileName) = $this->describe($format);

        parent::__construct($content, $contentType, $fileName);
    }
    /**
     * @param string $format
     *
     * @return array
     */
    protected function describe($format)
    {
        $map = [
            'svg' => 'image/svg+xml',
            'pdf' => 'application/pdf',
            'jpg' => 'image/jpeg',
            'png' => 'image/png',
            'xml' => 'application/xml',
            '*'   => 'application/octet-stream',
        ];

        if (isset($map[$format])) {
            return [$map[$format], 'content.'.$format];
        }

        return [$map['*'], 'content.'.$format];
    }
}
