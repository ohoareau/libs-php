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
class EmbeddedTypeGuessBuilder extends Base\AbstractTypeGuessBuilder
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
        $embedded = $this->getMetaDataService()->getModelEmbeddedByProperty($options['class'], $options['property']);

        return new TypeGuess(
            'app_'.str_replace('.', '_', $embedded['type']).'_'.$options['operation'],
            [],
            Guess::HIGH_CONFIDENCE
        );
    }
}
