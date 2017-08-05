<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <cto@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source id.
 */

namespace Itq\Common\Service;

use Itq\Common\Bag;
use Itq\Common\Traits;
use Itq\Common\Plugin\CustomizerInterface;

/**
 * Customizer Service.
 *
 * @author Olivier Hoareau <olivier@itiqiti.com>
 */
class CustomizerService
{
    use Traits\ServiceTrait;
    /**
     * @param string              $id
     * @param CustomizerInterface $customizer
     */
    public function addCustomizer($id, CustomizerInterface $customizer)
    {
        $this->setArrayParameterKey('customizers', $id, $customizer);
    }
    /**
     * @return CustomizerInterface[]
     */
    public function getCustomizers()
    {
        return $this->getArrayParameter('customizers');
    }
    /**
     * @param string $type
     * @param string $key
     * @param Bag    $data
     *
     * @return Bag
     */
    public function customize($type, $key, Bag $data = null)
    {
        if (null === $data) {
            $data = new Bag();
        }

        foreach ($this->getCustomizers() as $customizer) {
            $customizer->customize($type, $key, $data);
        }

        return $data;
    }
}
