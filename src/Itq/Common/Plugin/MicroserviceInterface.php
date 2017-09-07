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

use Itq\Common\Model;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
interface MicroserviceInterface
{
    /**
     * @param array  $message
     * @param string $source
     *
     * @return Model\Internal\Result\ResultInterface
     */
    public function consume(array $message, $source);
    /**
     * @param object $ctx
     *
     * @return void
     */
    public function idle($ctx);
    /**
     * @return void
     */
    public function start();
}
