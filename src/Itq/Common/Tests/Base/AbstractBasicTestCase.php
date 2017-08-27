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

use DateTime;
use Exception;
use Itq\Common\Traits;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_MockObject_MockObject;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractBasicTestCase extends PHPUnit_Framework_TestCase
{
    use Traits\ExceptionThrowerTrait;
    use Traits\TestMock\LoggerTestMockTrait;
    use Traits\TestMock\ContainerTestMockTrait;
    use Traits\TestMock\SerializerTestMockTrait;
    use Traits\TestMock\TemplatingTestMockTrait;
    use Traits\TestMock\TranslatorTestMockTrait;
    use Traits\TestMock\SdkServiceTestMockTrait;
    use Traits\TestMock\FormFactoryTestMockTrait;
    use Traits\TestMock\MathServiceTestMockTrait;
    use Traits\TestMock\TaskServiceTestMockTrait;
    use Traits\TestMock\YamlServiceTestMockTrait;
    use Traits\TestMock\DateServiceTestMockTrait;
    use Traits\TestMock\FormServiceTestMockTrait;
    use Traits\TestMock\HttpServiceTestMockTrait;
    use Traits\TestMock\CrudServiceTestMockTrait;
    use Traits\TestMock\TokenStorageTestMockTrait;
    use Traits\TestMock\ErrorManagerTestMockTrait;
    use Traits\TestMock\RequestStackTestMockTrait;
    use Traits\TestMock\ModelServiceTestMockTrait;
    use Traits\TestMock\VaultServiceTestMockTrait;
    use Traits\TestMock\BatchServiceTestMockTrait;
    use Traits\TestMock\TenantServiceTestMockTrait;
    use Traits\TestMock\StringServiceTestMockTrait;
    use Traits\TestMock\SystemServiceTestMockTrait;
    use Traits\TestMock\ActionServiceTestMockTrait;
    use Traits\TestMock\GoogleServiceTestMockTrait;
    use Traits\TestMock\TrackerServiceTestMockTrait;
    use Traits\TestMock\AddressServiceTestMockTrait;
    use Traits\TestMock\PartnerServiceTestMockTrait;
    use Traits\TestMock\StorageServiceTestMockTrait;
    use Traits\TestMock\JobTypeServiceTestMockTrait;
    use Traits\TestMock\ClientProviderTestMockTrait;
    use Traits\TestMock\RequestServiceTestMockTrait;
    use Traits\TestMock\ContextServiceTestMockTrait;
    use Traits\TestMock\MetaDataServiceTestMockTrait;
    use Traits\TestMock\ShippingServiceTestMockTrait;
    use Traits\TestMock\WorkflowServiceTestMockTrait;
    use Traits\TestMock\CallableServiceTestMockTrait;
    use Traits\TestMock\PasswordServiceTestMockTrait;
    use Traits\TestMock\ResponseServiceTestMockTrait;
    use Traits\TestMock\TemplateServiceTestMockTrait;
    use Traits\TestMock\DatabaseServiceTestMockTrait;
    use Traits\TestMock\EventDispatcherTestMockTrait;
    use Traits\TestMock\MigrationServiceTestMockTrait;
    use Traits\TestMock\ExceptionServiceTestMockTrait;
    use Traits\TestMock\ConverterServiceTestMockTrait;
    use Traits\TestMock\AnnotationReaderTestMockTrait;
    use Traits\TestMock\GeneratorServiceTestMockTrait;
    use Traits\TestMock\CriteriumServiceTestMockTrait;
    use Traits\TestMock\TypeGuessServiceTestMockTrait;
    use Traits\TestMock\FilesystemServiceTestMockTrait;
    use Traits\TestMock\DynamicUrlServiceTestMockTrait;
    use Traits\TestMock\RuleEngineServiceTestMockTrait;
    use Traits\TestMock\ExpressionServiceTestMockTrait;
    use Traits\TestMock\RepositoryServiceTestMockTrait;
    use Traits\TestMock\AttachmentServiceTestMockTrait;
    use Traits\TestMock\CustomizerServiceTestMockTrait;
    use Traits\TestMock\ConnectionServiceTestMockTrait;
    use Traits\TestMock\ExpressionLanguageTestMockTrait;
    use Traits\TestMock\BusinessRuleServiceTestMockTrait;
    use Traits\TestMock\AuthorizationCheckerTestMockTrait;
    use Traits\TestMock\DocumentBuilderServiceTestMockTrait;
    /**
     * @var array
     */
    protected $mocks;
    /**
     * @param DateTime $expected
     * @param DateTime $actual
     */
    public static function assertDateTimeEquals(\DateTime $expected, \DateTime $actual)
    {
        static::assertEquals($expected->format('c'), $actual->format('c'));
    }
    /**
     * @param Exception $e
     *
     * @return $this
     */
    protected function expectExceptionThrown(Exception $e)
    {
        $this->expectException(get_class($e));
        $this->expectExceptionCode($e->getCode());
        $this->expectExceptionMessage($e->getMessage());

        return $this;
    }
    /**
     * @return $this
     */
    protected function registerMocks()
    {
        $this->mocks();

        return $this;
    }
    /**
     *
     */
    protected function mocks()
    {
    }
    /**
     * @param string            $name
     * @param null|string|mixed $class
     * @param null|array        $methods
     *
     * @return mixed|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function mocked($name, $class = null, $methods = null)
    {
        if (!isset($this->mocks[$name])) {
            return $this->mock($name, $class, $methods);
        }

        return $this->mock($name);
    }
    /**
     * @param string            $name
     * @param null|string|mixed $class
     * @param null|array        $methods
     *
     * @return PHPUnit_Framework_MockObject_MockObject|mixed
     *
     * @throws Exception
     */
    protected function mock($name, $class = null, $methods = null)
    {
        if (1 === func_num_args()) {
            if (!isset($this->mocks[$name])) {
                throw $this->createRequiredException("[Test] Unknown mock '%s'", $name);
            }

            return $this->mocks[$name];
        }

        if (is_object($class)) {
            $mock = $class;
        } else {
            $mock = $this->getMockBuilder($class)->disableOriginalConstructor()->setMethods($methods ?: [])->getMock();
        }

        $this->mocks[$name] = $mock;

        return $mock;
    }
}
