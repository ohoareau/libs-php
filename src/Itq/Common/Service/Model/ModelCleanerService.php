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

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class ModelCleanerService extends Base\AbstractModelCleanerService
{
    /**
     * @param mixed $doc
     * @param array $options
     *
     * @return mixed
     */
    public function clean($doc, $options = [])
    {
        foreach ($this->getModelCleaners() as $cleaner) {
            $cleaner->clean($doc, $options);
        }

        return $doc;
    }
}
