<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Model\Internal\Result\Base;

use Itq\Common\Model\Base\AbstractBasicModel;
use Itq\Common\Model\Internal\Result\ResultInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractResult extends AbstractBasicModel implements ResultInterface
{
    /**
     * @var mixed
     */
    protected $data;
    /**
     * @var string
     */
    protected $status;
    /**
     * @param null|mixed  $data
     * @param null|string $status
     */
    public function __construct($data = null, $status = null)
    {
        parent::__construct(['data' => $data, 'status' => $status ?: 'unknown']);
    }
    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }
    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }
    /**
     * @return array
     */
    public function serialize()
    {
        return [
            'type'   => 'result',
            'status' => $this->getStatus(),
            'data'   => $this->getData(),
        ];
    }
}
