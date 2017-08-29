<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\CriteriumType\Collection;

use Itq\Common\Traits;
use Itq\Common\Service;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class SearchKeyCollectionCriteriumType extends Base\AbstractCollectionCriteriumType
{
    use Traits\ServiceAware\GeneratorServiceAwareTrait;
    /**
     * @param Service\GeneratorService $generatorService
     */
    public function __construct(Service\GeneratorService $generatorService)
    {
        $this->setGeneratorService($generatorService);
    }
    /**
     * @param string $v
     * @param string $k
     * @param array  $options
     *
     * @return array
     */
    public function build($v, $k, array $options = [])
    {
        return [
            [],
            [
                '+regex' => [
                    $k => str_replace(
                        '-',
                        '.+',
                        $this->getGeneratorService()->generate('search_key', $v)
                    ),
                ],
            ],
        ];
    }
}
