<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin;

use Exception;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
interface ExceptionDescriptorInterface
{
    /**
     * @param Exception $exception
     *
     * @return bool
     */
    public function supports(Exception $exception);
    /**
     * @param Exception $exception
     *
     * @return array
     */
    public function describe(Exception $exception);
}
