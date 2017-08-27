<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Service\Model;

use Itq\Common\Aware\ModelRestricterAwareInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
interface ModelRestricterServiceInterface extends ModelRestricterAwareInterface
{
    /**
     * @param mixed $doc
     * @param array $options
     */
    public function restrict($doc, array $options = []);
}
