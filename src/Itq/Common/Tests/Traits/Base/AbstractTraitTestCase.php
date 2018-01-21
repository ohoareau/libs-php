<?php

/*
 * This file is part of the tests-ws package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Itq\Common\Tests\Traits\Base;

use Itq\Common\Tests\Base\AbstractTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractTraitTestCase extends AbstractTestCase
{
    /**
     * @var array
     */
    protected $abstractMethods = [];
    /**
     * @return object|\PHPUnit_Framework_MockObject_MockObject
     */
    public function t()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return $this->o();
    }
    /**
     *
     */
    public function setUp()
    {
        $this->setObject(
            $this->getMockForTrait($this->getObjectClass())
        );
    }
    /**
     * @param null|string $name
     * @param array       $data
     * @param string      $dataName
     */
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        // list abstract method => only abstract method can be mocked
        $trait = new \ReflectionClass($this->getObjectClass());
        foreach ($trait->getMethods() as $method) {
            if ($method->isAbstract()) {
                $this->abstractMethods[] = $method->getName();
            }
        }
    }
    /**
     * mock abstract trait method once
     *
     * @param string     $method
     * @param null|mixed $args
     * @param null|mixed $return
     *
     * @return $this
     */
    protected function mockMethodOnce($method, $args = null, $return = null)
    {
        return $this->checkMethodIsAbstract($method)->mockMethod($this->t(), $method, $args, $return);
    }
    /**
     * mock abstract trait method when executed at the given index
     *
     * @param int        $at
     * @param string     $method
     * @param null|mixed $args
     * @param null|mixed $return
     *
     * @return $this
     */
    protected function mockMethodAt($at, $method, $args = null, $return = null)
    {
        return $this->checkMethodIsAbstract($method)->mockMethod($this->t(), $method, $args, $return, $this->at($at));
    }
    /**
     * @param string $method
     *
     * @return $this
     */
    private function checkMethodIsAbstract($method)
    {
        if (false === in_array($method, $this->abstractMethods)) {
            throw new \RuntimeException(sprintf("'%s' method is not abstract, it cannot be mocked", $method), 404);
        }

        return $this;
    }
}
