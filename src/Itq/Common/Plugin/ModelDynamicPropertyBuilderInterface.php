<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin;

use Closure;
use Itq\Common\ModelInterface;

/**
 * Model Dynamic Property Builder Interface.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
interface ModelDynamicPropertyBuilderInterface
{
    /**
     * @param ModelInterface $doc
     * @param string         $k
     * @param array          $m
     *
     * @return bool
     */
    public function supports($doc, $k, array &$m);
    /**
     * @param ModelInterface $doc
     * @param string         $k
     * @param array          $m
     * @param array          $options
     *
     * @return mixed
     */
    public function build($doc, $k, array &$m, array $options = []);
}
