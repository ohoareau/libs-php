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

use Symfony\Component\Form\Guess\TypeGuess;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
interface TypeGuessBuilderInterface
{
    /**
     * @param array $definition
     * @param array $options
     *
     * @return TypeGuess
     */
    public function build(array $definition, array $options = []);
}
