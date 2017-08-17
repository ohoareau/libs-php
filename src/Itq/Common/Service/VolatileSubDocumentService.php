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
class VolatileSubDocumentService extends Base\AbstractSubDocumentService
{
    use Traits\SubDocument\CreateServiceTrait;
    /**
     * @param mixed $parentId
     * @param array $array
     * @param array $options
     */
    protected function saveCreate($parentId, array $array, array $options = [])
    {
    }
    /**
     * @param mixed $parentId
     * @param array $arrays
     * @param array $options
     *
     * @return array
     */
    protected function saveCreateBulk($parentId, array $arrays, array $options = [])
    {
        unset($parentId, $options);

        return $arrays;
    }
    /**
     * @param array $arrays
     * @param array $array
     */
    protected function pushCreateInBulk(&$arrays, $array)
    {
        $arrays[] = $array;
    }
}
