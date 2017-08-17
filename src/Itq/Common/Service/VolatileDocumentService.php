<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Service;

use Itq\Common\Traits;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class VolatileDocumentService extends Base\AbstractDocumentService
{
    use Traits\Document\CreateServiceTrait;
    /**
     * @param array $array
     * @param array $options
     */
    protected function saveCreate(array $array, array $options = [])
    {
    }
    /**
     * @param array $arrays
     * @param array $options
     *
     * @return array
     */
    protected function saveCreateBulk(array $arrays, array $options = [])
    {
        unset($options);

        return $arrays;
    }
}
