<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\Customizer\Base;

use Itq\Common\Bag;
use Itq\Common\Plugin\Base\AbstractPlugin;
use Itq\Common\Plugin\CustomizerInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractOwnerCustomizer extends AbstractPlugin implements CustomizerInterface
{
    /**
     * @return string
     */
    abstract public function getOwnerType();
    /**
     * @param string $type
     * @param string $key
     * @param Bag    $data
     *
     * @return void
     */
    public function customize($type, $key, Bag $data)
    {
        if (!$data->has($this->getOwnerType())) {
            return;
        }

        $method = 'customize'.ucfirst($type);

        if ('customize' !== $method && method_exists($this, $method)) {
            $this->$method($key, $data);
        }
    }
    /**
     * @param Bag $data
     *
     * @return string
     */
    abstract protected function getOwnerId(Bag $data);
    /**
     * @param string $type
     * @param string $owner
     * @param string $key
     *
     * @return mixed
     */
    abstract protected function findCustomization($type, $owner, $key);
    /**
     * @param string $template
     * @param Bag    $data
     */
    protected function customizeMail($template, Bag $data)
    {
        $data->set($this->getCustomizationByTypeAndOwner('mail', $this->getOwnerId($data), $template));
    }
    /**
     * @param string $template
     * @param Bag    $data
     */
    protected function customizeSms($template, Bag $data)
    {
        $data->set($this->getCustomizationByTypeAndOwner('sms', $this->getOwnerId($data), $template));
    }
    /**
     * @param string $type
     * @param string $owner
     * @param string $key
     *
     * @return array
     */
    protected function getCustomizationByTypeAndOwner($type, $owner, $key)
    {
        $customization = $this->findCustomization($type, $owner, $key);

        if (null === $customization) {
            return [];
        }

        return $customization;
    }
}
