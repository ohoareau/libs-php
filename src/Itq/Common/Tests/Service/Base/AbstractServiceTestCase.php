<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Tests\Service\Base;

use Itq\Common\Traits;
use Itq\Common\Tests\Base\AbstractTestCase;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractServiceTestCase extends AbstractTestCase
{
    /**
     * current test path
     * @var string
     */
    protected $testPath;
    /**
     * @return object|Traits\ServiceTrait|PHPUnit_Framework_MockObject_MockObject
     */
    public function s()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return $this->o();
    }
    /**
     *
     */
    public function setUp()
    {
        if (empty($this->getMockedMethod())) {
            $this->setObject($this->instantiate());
        } else {
            $this->setObject(
                $this->getMockBuilder($this->getObjectClass())->setMethods($this->getMockedMethod())->setConstructorArgs($this->getConstructorArguments())->getMock()
            );
        }

        $this->initializer();
    }
    /**
     * @param string $type
     * @param string $pluginClass
     * @param array  $methods
     * @param string $getter
     * @param string $adder
     * @param string $optionalTypeForAdder
     * @param string $optionalSingleGetter
     * @param string $optionalGroupGetter
     */
    protected function handleTestPlugins($type, $pluginClass, array $methods, $getter, $adder, $optionalTypeForAdder = null, $optionalSingleGetter = null, $optionalGroupGetter = null)
    {
        $mock = $this->mocked($type, $pluginClass, $methods);

        $this->assertEquals([], $this->s()->$getter());
        if (null !== $optionalTypeForAdder) {
            $this->s()->$adder($optionalTypeForAdder, $mock);
            if (null !== $optionalGroupGetter) {
                $this->assertEquals([$optionalTypeForAdder => [$mock]], $this->s()->$getter());
            } else {
                $this->assertEquals([$optionalTypeForAdder => $mock], $this->s()->$getter());
            }
            if (null !== $optionalSingleGetter) {
                $this->assertEquals($mock, $this->s()->$optionalSingleGetter($optionalTypeForAdder));
            }
            if (null !== $optionalGroupGetter) {
                $this->assertEquals([$mock], $this->s()->$optionalGroupGetter($optionalTypeForAdder));
            }
        } else {
            $this->s()->$adder($mock);
            $this->assertEquals([$mock], $this->s()->$getter());
        }
    }
    /**
     * @return array
     */
    protected function getMockedMethod()
    {
        return [];
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
        return $this->checkMethodIsMockable($method)->mockMethod($this->s(), $method, $args, $return);
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
        return $this->checkMethodIsMockable($method)->mockMethod($this->s(), $method, $args, $return, $this->at($at));
    }
    /**
     * get path to the tests
     *
     * @return string
     */
    protected function getTestPath()
    {
        if (false === isset($this->testPath)) {
            $this->testPath = getcwd().'/tests';
        }

        return $this->testPath;
    }
    /**
     * path to test result sets
     *
     * @return string
     */
    protected function getResultSetsPath()
    {
        return $this->getTestPath().'/resultSets';
    }
    /**
     * Assert actual is equals to method results
     *
     * @param mixed $actual
     *
     * @throws \Exception
     * @return $this
     */
    protected function assertEqualsResultSet($actual)
    {
        $backtrace = debug_backtrace();
        $dataProviderIndex = $this->dataDescription();
        $fct = $backtrace[1]['function'].($dataProviderIndex ? '['.$dataProviderIndex.']' : '');
        $expected = $this->getResultSet($fct);
        try {
            $this->assertEquals(unserialize($expected), $actual);
        } catch (\Exception $e) {
            $fileResultSet = $this->getResultSetsPath().'/'.$this->getResultFilename();
            $resultSetName = str_replace('.php', '_fix_DONOTCOMMIT.txt', $this->getResultFilename());
            $fixFileResultSet = $this->getResultSetsPath().'/'.$resultSetName;
            $resultSet = "'$fct' => '".str_replace("'", "\\'", serialize($actual))."',";

            print_r(
                [
                    'file'            => $backtrace[0]['file'].' line '.$backtrace[0]['line'],
                    'test'            => $backtrace[1]['function'].(
                        $dataProviderIndex ? '() - dataProvider[\''.$dataProviderIndex.'\']' : '()'
                        ),
                    'expected'        => ($expected ? unserialize($expected) : 'to be setted'),
                    'actual'          => $actual,
                    'fixed into file' => $fixFileResultSet,
                    'file to fix'     => $fileResultSet,
                ]
            );

            $dirname = dirname($fileResultSet);
            if (!file_exists($dirname)) {
                mkdir($dirname, 0744, true);
            }

            if (!file_exists($fileResultSet)) {
                file_put_contents($fileResultSet, "<?php\n\nreturn [];\n");
            }

            file_put_contents($fixFileResultSet, $resultSet.PHP_EOL, FILE_APPEND);
            throw $e;
        };

        return $this;
    }
    /**
     * get Resultset
     *
     * @param string $key
     *
     * @return array|false false if resultSet not defined
     */
    protected function getResultSet($key)
    {
        $resultSetFileName = $this->getResultSetsPath().'/'.$this->getResultFilename();

        if (!file_exists($resultSetFileName)) {
            return false;
        }

        /** @noinspection PhpIncludeInspection */
        $rs = include($resultSetFileName);

        if (true === isset($rs[$key])) {
            return $rs[$key];
        }

        return false;
    }
    /**
     * get result file name
     *
     * @return string
     */
    protected function getResultFilename()
    {
        $class = $this->getObjectClass();
        $f = explode('\\', $class);

        return join('/', $f).'ResultSet.php';
    }
    /**
     * check method is mockable
     *
     * @param string $method
     *
     * @return $this
     */
    private function checkMethodIsMockable($method)
    {
        if (false === in_array($method, $this->getMockedMethod())) {
            throw new \RuntimeException(sprintf("'%s' method is not mockable, add it by settings getMockedMethod()", $method), 404);
        }

        return $this;
    }
}
