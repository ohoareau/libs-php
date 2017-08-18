<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Form\Type;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class FlagType extends Base\AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) {
                $data = $event->getData();
                $event->setData(('1' === $data || 1 === $data || ($this->isNonEmptyString($data) && '0' !== $data)) ? '1' : '0');
            }
        );
        $builder->addEventListener(
            FormEvents::SUBMIT,
            function (FormEvent $event) {
                $data = $event->getData();
                $event->setData(1 === $data ? true : false);
            }
        );
    }
    /**
     * @return string
     */
    public function getParent()
    {
        return 'choice';
    }
    /**
     * @return string
     */
    public function getName()
    {
        return 'app_flag';
    }
}
