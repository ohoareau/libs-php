<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\CriteriumType\Mongo;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class NotMongoCriteriumType extends Base\AbstractMongoCriteriumType
{
    /**
     * @param string $v
     * @param string $k
     * @param array  $options
     *
     * @return array
     */
    public function build($v, $k, array $options = [])
    {
        return [
            ['$ne' => $this->prepare($k, $v)],
        ];
    }
}
