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

use Itq\Common\Aware\ModelCleanerAwareInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
interface ModelCleanerServiceInterface extends ModelCleanerAwareInterface
{
    /**
     * @param mixed $doc
     * @param array $options
     *
     * @return mixed
     */
    public function clean($doc, $options = []);
}
