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
use Itq\Common\Plugin\DataFilterInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class DataFilterService
{
    use Traits\ServiceTrait;
    use Traits\RequestStackAwareTrait;
    use Traits\TokenStorageAwareTrait;
    /**
     * @param RequestStack          $requestStack
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(RequestStack $requestStack, TokenStorageInterface $tokenStorage)
    {
        $this->setRequestStack($requestStack);
        $this->setTokenStorage($tokenStorage);
    }
    /**
     * @param mixed $data
     * @param array $options
     *
     * @return mixed
     */
    public function filter($data, array $options = [])
    {
        $token = $this->getTokenStorage()->getToken();
        $user  = $token ? $token->getUser() : null;
        $ctx   = (object) (['user' => $user] + $options);

        return $this->applyFiltering($data, $ctx);
    }
    /**
     * @param DataFilterInterface $dataFilter
     *
     * @return $this
     */
    public function add(DataFilterInterface $dataFilter)
    {
        return $this->pushArrayParameterItem('filters', $dataFilter);
    }
    /**
     * @return DataFilterInterface[]
     */
    public function all()
    {
        return $this->getArrayParameter('filters');
    }
    /**
     * @param mixed  $data
     * @param object $ctx
     *
     * @return mixed
     */
    public function applyFiltering(&$data, $ctx)
    {
        if (is_array($data)) {
            foreach (array_keys($data) as $k) {
                $this->applyFiltering($data[$k], $ctx);
            }

            return $this;
        }

        if (!is_object($data)) {
            return $this;
        }

        $that = $this;

        foreach ($this->all() as $filter) {
            if (!$filter->supports($data)) {
                continue;
            }

            $data = $filter->filter(
                $data,
                $ctx,
                function (&$data) use ($ctx, $that) {
                    return $that->applyFiltering($data, $ctx);
                }
            );
        }

        return $data;
    }
}
