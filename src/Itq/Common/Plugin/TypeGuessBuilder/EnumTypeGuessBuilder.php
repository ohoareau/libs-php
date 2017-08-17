<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\TypeGuessBuilder;

use Itq\Common\Traits;
use Itq\Common\Service;
use Symfony\Component\Form\Guess\Guess;
use Symfony\Component\Form\Guess\TypeGuess;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class EnumTypeGuessBuilder extends Base\AbstractTypeGuessBuilder
{
    use Traits\ServiceAware\MetaDataServiceAwareTrait;
    /**
     * @param Service\MetaDataService $metaDataService
     */
    public function __construct(Service\MetaDataService $metaDataService)
    {
        $this->setMetaDataService($metaDataService);
    }
    /**
     * @param array $definition
     * @param array $options
     *
     * @return TypeGuess
     */
    public function build(array $definition, array $options = [])
    {
        $enumValues = $definition['values'];

        if (is_string($enumValues) && '@' === substr($enumValues, 0, 1)) {
            $enumValues = $this->getMetaDataService()->getEnumValuesByType(substr($enumValues, 1));
        }
        if (!is_array($enumValues)) {
            $enumValues = [];
        }

        return new TypeGuess(
            'choice',
            ['choices' => array_combine($enumValues, $enumValues), 'choices_as_values' => false],
            Guess::HIGH_CONFIDENCE
        );
    }
}
