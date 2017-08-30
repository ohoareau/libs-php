<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Service;

use DateTime;
use Itq\Common\Traits;
use Itq\Common\Adapter\SymfonyAdapterInterface;

/**
 * Symfony Service.
 *
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class SymfonyService
{
    use Traits\ServiceTrait;
    use Traits\AdapterAware\SymfonyAdapterAwareTrait;
    /**
     * @param SymfonyAdapterInterface $symfonyAdapter
     */
    public function __construct(SymfonyAdapterInterface $symfonyAdapter)
    {
        $this->setSymfonyAdapter($symfonyAdapter);
    }
    /**
     * @return array
     */
    public function describe()
    {
        return [
            'version'            => $this->getSymfonyAdapter()->getVersion(),
            'version_id'         => $this->getSymfonyAdapter()->getVersionId(),
            'major_version'      => $this->getSymfonyAdapter()->getMajorVersion(),
            'minor_version'      => $this->getSymfonyAdapter()->getMinorVersion(),
            'release_version'    => $this->getSymfonyAdapter()->getReleaseVersion(),
            'extra_version'      => $this->getSymfonyAdapter()->getExtraVersion(),
            'end_of_maintenance' => $this->parseEndOfDateString($this->getSymfonyAdapter()->getEndOfMaintenance()),
            'end_of_life'        => $this->parseEndOfDateString($this->getSymfonyAdapter()->getEndOfLife()),
        ];
    }

    /**
     * @param string $string
     *
     * @return DateTime
     */
    protected function parseEndOfDateString($string)
    {
        return new DateTime(
            sprintf(
                'last day of %s',
                implode('-', array_reverse(explode('/', $string)))
            )
        );
    }
}
