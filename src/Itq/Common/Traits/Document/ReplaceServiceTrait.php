<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Traits\Document;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
trait ReplaceServiceTrait
{
    /**
     * Replace all the specified documents.
     *
     * @param array $data
     * @param array $options
     *
     * @return mixed
     */
    public function replaceAll($data, $options = [])
    {
        $this->saveDeleteFound([], $options);
        $this->event('emptied');

        return $this->createBulk($data, $options);
    }
    /**
     * Replace all the specified documents.
     *
     * @param array $bulkData
     * @param array $options
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function replaceBulk($bulkData, $options = [])
    {
        $this->checkBulkData($bulkData, $options);

        $ids = [];

        foreach ($bulkData as $k => $v) {
            if (!isset($v['id'])) {
                throw $this->createRequiredException('Missing id for item #%s', $k);
            }
            $ids[] = $v['id'];
        }

        $this->deleteBulk($ids, $options);

        unset($ids);

        return $this->createBulk($bulkData, $options);
    }
}
