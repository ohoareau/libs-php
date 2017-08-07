<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\DataFilter\Base;

use Itq\Common\Service\ContextService;
use Itq\Common\Plugin\DataFilterInterface;
use Itq\Common\Plugin\Base\AbstractPlugin;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractDataFilter extends AbstractPlugin implements DataFilterInterface
{
    /**
     * @param mixed $data
     *
     * @return bool
     */
    public function supports(/** @noinspection PhpUnusedParameterInspection */ $data)
    {
        return true;
    }
    /**
     * @param mixed          $data
     * @param ContextService $ctx
     * @param \Closure       $pipeline
     *
     * @return mixed
     */
    public function filter($data, ContextService $ctx, \Closure $pipeline)
    {
        return $data;
    }
}
