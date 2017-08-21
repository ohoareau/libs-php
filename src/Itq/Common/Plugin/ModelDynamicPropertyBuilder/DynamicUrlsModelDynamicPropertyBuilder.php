<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\ModelDynamicPropertyBuilder;

use Itq\Common\Traits;
use Itq\Common\Service;
use Itq\Common\ModelInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class DynamicUrlsModelDynamicPropertyBuilder extends Base\AbstractModelDynamicPropertyBuilder
{
    use Traits\ServiceAware\DynamicUrlServiceAwareTrait;
    /**
     * @param Service\DynamicUrlService $dynamicUrlService
     */
    public function __construct(Service\DynamicUrlService $dynamicUrlService)
    {
        $this->setDynamicUrlService($dynamicUrlService);
    }
    /**
     * @param ModelInterface $doc
     * @param string         $k
     * @param array          $m
     *
     * @return bool
     */
    public function supports($doc, $k, array &$m)
    {
        return true === isset($m['dynamicUrls'][$k]);
    }
    /**
     * @param ModelInterface $doc
     * @param string         $k
     * @param array          $m
     * @param array          $options
     *
     * @return mixed
     */
    public function build($doc, $k, array &$m, array $options = [])
    {
        return $this->getDynamicUrlService()->compute($doc, $m['dynamicUrls'][$k], ['requestedField' => $k] + $options);
    }
}
