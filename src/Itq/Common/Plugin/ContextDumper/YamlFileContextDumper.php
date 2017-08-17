<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\ContextDumper;

use Itq\Common\Traits;
use Itq\Common\Service;
use Itq\Common\PreprocessorContext;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class YamlFileContextDumper extends Base\AbstractContextDumper
{
    use Traits\ServiceAware\YamlServiceAwareTrait;
    /**
     * @param Service\YamlService $yamlService
     */
    public function __construct(Service\YamlService $yamlService)
    {
        $this->setYamlService($yamlService);
    }
    /**
     * @param PreprocessorContext $ctx
     */
    public function dump(PreprocessorContext $ctx)
    {
        file_put_contents(sprintf('%s/preprocessor.yml', $ctx->cacheDir), $this->getYamlService()->serialize((array) $ctx, ['inlineLevel' => 7, 'indentSize' => 2]));
    }
}
