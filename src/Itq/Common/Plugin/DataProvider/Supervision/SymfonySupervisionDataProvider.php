<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\DataProvider\Supervision;

use Itq\Common\Traits;
use Itq\Common\Service;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class SymfonySupervisionDataProvider extends Base\AbstractSupervisionDataProvider
{
    use Traits\ServiceAware\SymfonyServiceAwareTrait;
    /**
     * @param Service\SymfonyService $symfonyService
     */
    public function __construct(Service\SymfonyService $symfonyService)
    {
        $this->setSymfonyService($symfonyService);
    }
    /**
     * @param array $options
     *
     * @return array
     */
    public function provide(array $options = [])
    {
        return ['symfony' => $this->getSymfonyService()->describe()];
    }
}
