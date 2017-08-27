<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
interface ObjectPopulatorInterface
{
    /**
     * @param mixed $doc
     * @param array $data
     * @param array $options
     *
     * @return mixed
     */
    public function populateObject($doc, $data = [], $options = []);
    /**
     * @param mixed  $doc
     * @param mixed  $data
     * @param string $propertyName
     * @param array  $options
     *
     * @return mixed
     */
    public function populateObjectProperty($doc, $data, $propertyName, $options = []);
}
