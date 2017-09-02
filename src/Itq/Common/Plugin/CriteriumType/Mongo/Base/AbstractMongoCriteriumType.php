<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\CriteriumType\Mongo\Base;

use MongoId;
use Exception;
use Itq\Common\Plugin\CriteriumType\Base\AbstractCriteriumType;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractMongoCriteriumType extends AbstractCriteriumType
{
    /**
     * @param string|array $k
     * @param mixed        $v
     *
     * @return mixed
     */
    protected function prepare($k, $v)
    {
        if ('_id' === $k || 'id' === $k) {
            return $this->ensureId($v);
        }

        return $v;
    }
    /**
     * @param string $id
     *
     * @return mixed
     *
     * @throws Exception if malformed
     */
    protected function ensureId($id)
    {
        if ($this->isValidId($id)) {
            return $id;
        }
        if (is_array($id)) {
            foreach ($id as $k => $iid) {
                $id[$k] = $this->ensureId($iid);
            }

            return $id;
        }

        return $this->castId($id);
    }
    /**
     * @param mixed $value
     *
     * @return bool
     */
    protected function isValidId($value)
    {
        return is_object($value) && $value instanceof MongoId;
    }
    /**
     * @param mixed $value
     *
     * @return mixed
     *
     * @throws Exception
     */
    protected function castId($value)
    {
        if (!preg_match('/^[a-f0-9]{24}$/', $value)) {
            throw $this->createMalformedException('db.id.malformed_mongo');
        }

        return new MongoId($value);
    }
}
