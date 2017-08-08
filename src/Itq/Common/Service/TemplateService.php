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

use Itq\Common\Traits;

use Symfony\Component\Templating\EngineInterface;

use RuntimeException;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class TemplateService
{
    use Traits\ServiceTrait;
    use Traits\TemplatingAwareTrait;
    /**
     * @param EngineInterface $templating
     */
    public function __construct(EngineInterface $templating)
    {
        $this->setTemplating($templating);
    }
    /**
     * Renders a template.
     *
     * @param string $name
     * @param array  $parameters
     *
     * @return string
     *
     * @throws RuntimeException
     */
    public function render($name, array $parameters = [])
    {
        return $this->getTemplating()->render($name, $parameters);
    }
    /**
     * Returns true if the template exists.
     *
     * @param string $name
     *
     * @return bool
     *
     * @throws RuntimeException
     */
    public function exists($name)
    {
        return $this->getTemplating()->exists($name);
    }
}
