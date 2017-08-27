<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Service\Model;

use Itq\Common\Plugin;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class ModelRestricterService extends Base\AbstractModelRestricterService
{
    /**
     * @param Plugin\ModelRestricterInterface $restricter
     *
     * @return $this
     */
    public function addModelRestricter(Plugin\ModelRestricterInterface $restricter)
    {
        return $this->pushArrayParameterItem('restricters', $restricter);
    }
    /**
     * @return Plugin\ModelRestricterInterface[]
     */
    public function getModelRestricters()
    {
        return $this->getArrayParameter('restricters');
    }
    /**
     * @param mixed $doc
     * @param array $options
     */
    public function restrict($doc, array $options = [])
    {
        foreach ($this->getModelRestricters() as $restricter) {
            $restricter->restrict($doc, $options);
        }
    }
}
