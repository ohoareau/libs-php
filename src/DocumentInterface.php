<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <cto@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common;

/**
 * Document Interface.
 *
 * @author Olivier Hoareau <olivier@itiqiti.com>
 */
interface DocumentInterface
{
    /**
     * @return mixed
     */
    public function getContent();

    /**
     * @return string
     */
    public function getContentType();
    /**
     * @return string
     */
    public function getFileName();
}
