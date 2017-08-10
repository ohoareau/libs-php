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

use Itq\Common\Traits as CommonTraits;
use Itq\common\Service as CommonService;
use Itq\Common\Plugin\TypeGuessBuilder\Base\AbstractTypeGuessBuilder;

use Symfony\Component\Form\Guess\Guess;
use Symfony\Component\Form\Guess\TypeGuess;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class EmbeddedListTypeGuessBuilder extends AbstractTypeGuessBuilder
{
    use CommonTraits\ServiceAware\MetaDataServiceAwareTrait;
    /**
     * @param CommonService\MetaDataService $metaDataService
     */
    public function __construct(CommonService\MetaDataService $metaDataService)
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
        $embeddedList = $this->getMetaDataService()
            ->getModelEmbeddedListByProperty($options['class'], $options['property'])
        ;

        return new TypeGuess(
            'collection',
            [
                'type'         => 'app_'.str_replace('.', '_', $embeddedList['type']).'_'.$options['operation'],
                'allow_add'    => true,
                'allow_delete' => true,
            ],
            Guess::HIGH_CONFIDENCE
        );
    }
}
