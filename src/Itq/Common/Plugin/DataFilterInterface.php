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

/**
 * Data Filter Interface.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
interface DataFilterInterface
{
    /**
     * @param mixed $data
     *
     * @return bool
     */
    public function supports($data);
    /**
     * @param mixed    $data
     * @param object   $ctx
     * @param \Closure $pipeline
     *
     * @return mixed
     */
    public function filter($data, $ctx, \Closure $pipeline);
}
