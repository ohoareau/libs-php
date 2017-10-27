<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Tests\Base;

use Exception;
use Itq\Common\Traits\TestMock\AccessibleTestMockTrait;
use PHPUnit_Framework_MockObject_Matcher_Invocation;
use ReflectionClass;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractTestCase extends AbstractBasicTestCase
{
    use AccessibleTestMockTrait;
    /**
     * @var object
     */
    protected $o;
    /**
     * current test path
     * @var string
     */
    protected $testPath;
    /**
     * @return array
     */
    public function constructor()
    {
        return [];
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
                $this->getMockBuilder($this->getObjectClass())->setMethods($this->getMockedMethod())
                     ->setConstructorArgs($this->getConstructorArguments())->getMock()
            );
        }

        $this->initializer();
    }
    /**
     *
     */
    public function initializer()
    {
    }
    /**
     * @return object|PHPUnit_Framework_MockObject_MockObject
     */
    public function o()
    {
        return $this->getObject();
    }
    /**
     * @group unit
     */
    public function testConstruct()
    {
        $this->assertNotNull($this->o());
    }
    /**
     * @param object $object
     *
     * @return $this
     */
    protected function setObject($object)
    {
        $this->o = $object;

        return $this;
    }
    /**
     * @return object
     */
    protected function getObject()
    {
        $this->checkObjectExist();

        return $this->o;
    }
    /**
     * @return $this
     *
     * @throws Exception
     */
    protected function checkObjectExist()
    {
        if (!$this->hasObject()) {
            throw $this->createRequiredException('[Test] No object set');
        }

        return $this;
    }
    /**
     * @return bool
     */
    protected function hasObject()
    {
        return isset($this->o);
    }
    /**
     * @param null|array $args
     *
     * @return object
     */
    protected function instantiate($args = null)
    {
        $rClass = new ReflectionClass($this->getObjectClass());

        return $rClass->newInstanceArgs($args ?: $this->getConstructorArguments());
    }
    /**
     * @return string
     */
    protected function getObjectClass()
    {
        return preg_replace('/Test$/', '', preg_replace('/Tests\\\/', '', get_class($this)));
    }
    /**
     * @return array
     */
    protected function getConstructorArguments()
    {
        return $this->constructor();
    }
    /**
     * @param PHPUnit_Framework_MockObject_MockObject $mocked
     * @param string                                  $method
     * @param int|callable                            $will
     */
    protected function mockedReturn($mocked, $method, $will)
    {
        if (!is_callable($will)) {
            $will = function (...$args) use ($will) {
                return $args[$will];
            };
        }

        $mocked
            ->expects($this->any())->method($method)
            ->will($this->returnCallback($will));
    }
    /**
     * @param string $class
     * @param array  $array
     * @return mixed
     */
    protected function toObject($class, $array = [])
    {
        $object = new $class();
        foreach ($array as $key => $value) {
            $object->$key = $value;
        }

        return $object;
    }
    /**
     * mock current object method, must been added into getMockedMethod
     *
     * @param PHPUnit_Framework_MockObject_MockObject              $mock
     * @param string                                               $method
     * @param null|mixed                                           $args
     * @param null|mixed                                           $return
     * @param null|PHPUnit_Framework_MockObject_Matcher_Invocation $expect
     *
     * @return $this
     */
    protected function mockMethod($mock, $method, $args = null, $return = null, $expect = null)
    {
        if (null === $expect) {
            $expect = $this->once();
        }
        $observer = $mock->expects($expect)->method($method);

        if (null !== $args) {
            if (!is_array($args)) {
                $args = [$args];
            }
            $observer->with(...$args);
        }

        if (null !== $return) {
            $observer->will($this->returnValue($return));
        }

        return $this;
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
        return $this->checkMethodIsMockable($method)->mockMethod($this->o(), $method, $args, $return);
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
        return $this->checkMethodIsMockable($method)->mockMethod($this->o(), $method, $args, $return, $this->at($at));
    }
    /**
     * get path to the tests
     *
     * @param string $testFilename
     *
     * @return string
     */
    protected function getTestPath($testFilename)
    {
        if (false === isset($this->testPath)) {
            $pos = strpos($testFilename, '/tests');
            if (false === $pos) {
                throw new \RuntimeException(sprintf('Unable to determine root tests path from %s', $testFilename), 404);
            }
            $this->testPath = substr($testFilename, 0, $pos + 6);
        }

        return $this->testPath;
    }
    /**
     * path to test result sets
     *
     * @param string $testFilename
     *
     * @return string
     */
    protected function getResultSetsPath($testFilename)
    {
        return $this->getTestPath($testFilename).'/resultSets';
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
        $testFilename = $backtrace[0]['file'];
        $resultFilename = $this->getResultFilename();
        $resultSetPath = $this->getResultSetsPath($testFilename);
        $resultFileFullPath = $resultSetPath.'/'.$resultFilename;
        $expected = $this->getResultSet($resultFileFullPath, $fct);
        try {
            $this->assertEquals(unserialize($expected), $actual);
        } catch (\Exception $e) {
            $resultSetName = str_replace('.php', '_fix_DONOTCOMMIT.txt', $resultFilename);
            $fixFileResultSet = $resultSetPath.'/'.$resultSetName;
            $resultSet = "'$fct' => '".str_replace("'", "\\'", serialize($actual))."',";

            print_r(
                [
                    'file'            => $$testFilename.' line '.$backtrace[0]['line'],
                    'test'            => $backtrace[1]['function'].(
                        $dataProviderIndex ? '() - dataProvider[\''.$dataProviderIndex.'\']' : '()'
                        ),
                    'expected'        => ($expected ? unserialize($expected) : 'to be setted'),
                    'actual'          => $actual,
                    'fixed into file' => $fixFileResultSet,
                    'file to fix'     => $resultFileFullPath,
                ]
            );

            $dirname = dirname($resultFileFullPath);
            if (!file_exists($dirname)) {
                mkdir($dirname, 0744, true);
            }

            if (!file_exists($resultFileFullPath)) {
                file_put_contents($resultFileFullPath, "<?php\n\nreturn [];\n");
            }

            file_put_contents($fixFileResultSet, $resultSet.PHP_EOL, FILE_APPEND);
            throw $e;
        };

        return $this;
    }
    /**
     * get Resultset
     *
     * @param string $resultSetFileName
     * @param string $key
     *
     * @return array|false false if resultSet not defined
     */
    protected function getResultSet($resultSetFileName, $key)
    {
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
    protected function checkMethodIsMockable($method)
    {
        if (false === in_array($method, $this->getMockedMethod())) {
            throw new \RuntimeException(
                sprintf("'%s' method is not mockable, add it by settings getMockedMethod()", $method),
                404
            );
        }

        return $this;
    }
    /**
     * @return array
     */
    protected function getMockedMethod()
    {
        return [];
    }
}
