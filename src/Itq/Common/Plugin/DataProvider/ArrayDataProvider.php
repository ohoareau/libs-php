<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\DataProvider;

use Itq\Common\Traits;
use Itq\Common\Service;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class ArrayDataProvider extends Base\AbstractDataProvider
{
    /**
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->setParameter('data', $data);
    }
    /**
     * @param array $options
     *
     * @return array
     */
    public function provide(array $options = [])
    {
        return $this->getData();
    }
    /**
     * @return array
     */
    protected function getData()
    {
        return $this->getParameter('data');
    }
}
