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
class SuccessResult extends Base\AbstractResult
{
    /**
     * @param mixed $data
     */
    public function __construct($data = null)
    {
        parent::__construct($data, 'success');
    }
}
