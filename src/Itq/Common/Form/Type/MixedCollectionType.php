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
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class MixedCollectionType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'buildFields']);
        $builder->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'buildFields']);
    }
    /**
     * @param FormEvent $event
     */
    public function buildFields(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        if (empty($data)) {
            return;
        }

        foreach ($data as $key => $value) {
            $value = ($value === '0' || $value === 0 || $value === '1' || $value === 1) ? (bool) $value : $value;
            $type = gettype($value);

            switch ($type) {
                case 'boolean':
                    $form->add($key, 'app_boolean');
                    break;
                default:
                    $form->add($key, 'text');
                    break;
            }
        }
    }
    /**
     * @return string
     */
    public function getName()
    {
        return 'app_mixedcollection';
    }
}
