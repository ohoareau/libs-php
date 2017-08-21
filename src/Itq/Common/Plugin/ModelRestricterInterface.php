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
use Itq\Common\ModelInterface;

/**
 * Model Restricter Interface.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
interface ModelRestricterInterface
{
    /**
     * @param ModelInterface $doc
     * @param array          $options
     *
     * @return void
     *
     * @throws Exception if an error occured or a restriction is in effect
     */
    public function restrict($doc, array $options = []);
}
