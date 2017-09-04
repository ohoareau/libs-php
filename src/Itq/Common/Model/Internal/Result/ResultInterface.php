<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Model\Internal\Result;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
interface ResultInterface
{
    /**
     * @return string
     */
    public function getStatus();
    /**
     * @return array
     */
    public function serialize();
    /**
     * @return mixed
     */
    public function getData();
}
