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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class IntegerType extends AbstractType
{
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'trim'        => true,
                'constraints' => [
                    new NotBlank(['groups' => ['required', 'create']]),
                    new Length(['groups' => ['required', 'optional', 'create', 'update'], 'min' => 1, 'max' => 10]),
                ],
            ]
        );
    }
    /**
     * @return string
     */
    public function getParent()
    {
        return 'integer';
    }
    /**
     * @return string
     */
    public function getName()
    {
        return 'app_integer';
    }
}
