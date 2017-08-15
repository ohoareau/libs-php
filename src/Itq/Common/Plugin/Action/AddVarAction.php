<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\Action;

use Itq\Common\Bag;
use Itq\Common\Traits;
use /** @noinspection PhpUnusedAliasInspection */ Itq\Common\Annotation;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class AddVarAction extends Base\AbstractAction
{
    use Traits\ContainerAwareTrait;
    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->setContainer($container);
    }
    /**
     * @param Bag $params
     * @param Bag $context
     *
     * @Annotation\Action("add_var", description="add a variable to context")
     */
    public function execute(Bag $params, Bag $context)
    {
        $value = null;

        if ($params->has('service')) {
            $value = call_user_func_array([$this->getContainer()->get($params->get('service')), $params->get('method')], $params->get('params', []));
        } elseif ($params->has('value')) {
            $value = $params->get('value');
        }

        $context->set($params->get('name'), $value);
    }
}
