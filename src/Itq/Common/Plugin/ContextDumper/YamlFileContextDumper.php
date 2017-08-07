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

use Itq\Common\PreprocessorContext;
use Itq\Common\Plugin\ContextDumper\Base\AbstractContextDumper;

use Symfony\Component\Yaml\Yaml;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class YamlFileContextDumper extends AbstractContextDumper
{
    /**
     * @param PreprocessorContext $ctx
     */
    public function dump(PreprocessorContext $ctx)
    {
        file_put_contents(sprintf('%s/preprocessor.yml', $ctx->cacheDir), Yaml::dump((array) $ctx, 7, 2));
    }
}
