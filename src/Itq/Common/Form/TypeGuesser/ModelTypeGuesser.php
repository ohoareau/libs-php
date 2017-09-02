<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Form\TypeGuesser;

use Itq\Common\Traits;
use Itq\Common\Service;
use Symfony\Component\Form\Guess\Guess;
use Symfony\Component\Form\Guess\TypeGuess;
use Symfony\Component\Form\Guess\ValueGuess;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class ModelTypeGuesser extends Base\AbstractTypeGuesser
{
    use Traits\ServiceAware\MetaDataServiceAwareTrait;
    use Traits\ServiceAware\TypeGuessServiceAwareTrait;
    /**
     * @param Service\MetaDataService  $metaDataService
     * @param Service\TypeGuessService $typeGuessService
     */
    public function __construct(Service\MetaDataService $metaDataService, Service\TypeGuessService $typeGuessService)
    {
        $this->setMetaDataService($metaDataService);
        $this->setTypeGuessService($typeGuessService);
    }
    /**
     * @param string $class
     * @param string $property
     *
     * @return TypeGuess|null
     */
    public function guessType($class, $property)
    {
        if (!$this->getMetaDataService()->isModel($class)) {
            return null;
        }

        $options      = ['operation' => 'create', 'class' => $class, 'property' => $property];
        $propertyType = $this->getMetaDataService()->getModelPropertyType($class, $property);
        $type         = 'unknown';

        if (null !== $propertyType) {
            $type = isset($propertyType['modelType']) ? $propertyType['modelType'] : $propertyType['type'];
        }

        return $this->getTypeGuessService()->create($type, $propertyType, $options);
    }
    /**
     * @param string $class
     * @param string $property
     *
     * @return ValueGuess|null
     */
    public function guessRequired($class, $property)
    {
        return $this->buildModelValueGuess($class, true, Guess::LOW_CONFIDENCE);
    }
    /**
     * @param string $class
     * @param string $property
     *
     * @return ValueGuess|null
     */
    public function guessMaxLength($class, $property)
    {
        return $this->buildModelValueGuess($class, null, Guess::LOW_CONFIDENCE);
    }
    /**
     * @param string $class
     * @param string $property
     *
     * @return ValueGuess|null
     */
    public function guessPattern($class, $property)
    {
        return $this->buildModelValueGuess($class, null, Guess::LOW_CONFIDENCE);
    }
    /**
     * @param string $class
     * @param mixed  $value
     * @param int    $confidence
     *
     * @return ValueGuess|null
     */
    protected function buildModelValueGuess($class, $value, $confidence)
    {
        if (!$this->getMetaDataService()->isModel($class)) {
            return null;
        }

        return new ValueGuess($value, $confidence);
    }
}
