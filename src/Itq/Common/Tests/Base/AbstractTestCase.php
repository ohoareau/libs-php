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

use Itq\Common\Service;

use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

use Psr\Log\LoggerInterface;

use DateTime;

use PHPUnit_Framework_TestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @var object
     */
    protected $o;
    /**
     * @var array
     */
    protected $mocks;
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
        $this->setObject($this->instantiate());

        $this->initializer();
    }
    /**
     *
     */
    public function initializer()
    {
    }
    /**
     * @return object
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
     * @param DateTime $expected
     * @param DateTime $actual
     */
    public static function assertDateTimeEquals(\DateTime $expected, \DateTime $actual)
    {
        static::assertEquals($expected->format('c'), $actual->format('c'));
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
     */
    protected function checkObjectExist()
    {
        if (!$this->hasObject()) {
            throw new \RuntimeException('[Test] No object set', 412);
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
     * @return object
     */
    protected function instantiate()
    {
        $rClass = new \ReflectionClass($this->getObjectClass());

        $this->registerMocks();

        return $rClass->newInstanceArgs($this->getConstructorArguments());
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
     * @return \PHPUnit_Framework_MockObject_MockObject|mixed
     */
    protected function mock($name, $class = null, $methods = null)
    {
        if (1 === func_num_args()) {
            if (!isset($this->mocks[$name])) {
                throw new \RuntimeException(sprintf("[Test] Unknown mock '%s'", $name), 412);
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
    /**
     * @return Service\CrudService|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockedCrudService()
    {
        return $this->mocked('crudService', Service\CrudService::class);
    }
    /**
     * @return Service\StorageService|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockedStorageService()
    {
        return $this->mocked('storageService', Service\StorageService::class);
    }
    /**
     * @return Service\GeneratorService|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockedGeneratorService()
    {
        return $this->mocked('generatorService', Service\GeneratorService::class);
    }
    /**
     * @return Service\PartnerService|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockedPartnerService()
    {
        return $this->mocked('partnerService', Service\PartnerService::class);
    }
    /**
     * @return Service\ShippingService|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockedShippingService()
    {
        return $this->mocked('shippingService', Service\ShippingService::class);
    }
    /**
     * @return Service\WorkflowService|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockedWorkflowService()
    {
        return $this->mocked('workflowService', Service\WorkflowService::class);
    }
    /**
     * @return Service\ContextService|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockedContextService()
    {
        return $this->mocked('contextService', Service\ContextService::class);
    }
    /**
     * @return TokenStorageInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockedTokenStorage()
    {
        return $this->mocked('tokenStorage', TokenStorageInterface::class);
    }
    /**
     * @return RequestStack|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockedRequestStack()
    {
        return $this->mocked('requestStack', RequestStack::class);
    }
    /**
     * @return FormFactoryInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockedFormFactory()
    {
        return $this->mocked('formFactory', FormFactoryInterface::class);
    }
    /**
     * @return EventDispatcherInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockedEventDispatcher()
    {
        return $this->mocked('eventDispatcher', EventDispatcherInterface::class);
    }
    /**
     * @return Service\ExpressionService|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockedExpressionService()
    {
        return $this->mocked('expressionService', Service\ExpressionService::class);
    }
    /**
     * @return Service\DatabaseServiceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockedDatabaseService()
    {
        return $this->mocked('databaseService', Service\DatabaseServiceInterface::class);
    }
    /**
     * @return AuthorizationCheckerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockedAuthorizationChecker()
    {
        return $this->mocked('authorizationChecker', AuthorizationCheckerInterface::class);
    }
    /**
     * @return LoggerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockedLogger()
    {
        return $this->mocked('logger', LoggerInterface::class);
    }
    /**
     * @return ContainerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockedContainer()
    {
        return $this->mocked('container', ContainerInterface::class);
    }
    /**
     * @return EngineInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockedTemplating()
    {
        return $this->mocked('templating', EngineInterface::class);
    }
    /**
     * @return TranslatorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockedTranslator()
    {
        return $this->mocked('translator', TranslatorInterface::class);
    }
    /**
     * @return Service\CallableService|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockedCallableService()
    {
        return $this->mocked('callableService', Service\CallableService::class);
    }
    /**
     * @return Service\JobTypeService|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockedJobTypeService()
    {
        return $this->mocked('jobTypeService', Service\JobTypeService::class);
    }
    /**
     * @return Service\VaultService|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockedVaultService()
    {
        return $this->mocked('vaultService', Service\VaultService::class);
    }
    /**
     * @return Service\PasswordService|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockedPasswordService()
    {
        return $this->mocked('passwordService', Service\PasswordService::class);
    }
    /**
     * @return Service\HttpService|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockedHttpService()
    {
        return $this->mocked('httpService', Service\HttpService::class);
    }
    /**
     * @return Service\ExceptionService|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockedExceptionService()
    {
        return $this->mocked('exceptionService', Service\ExceptionService::class);
    }
    /**
     * @return Service\ResponseService|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockedResponseService()
    {
        return $this->mocked('exceptionService', Service\ResponseService::class);
    }
    /**
     * @return Service\RequestService|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockedRequestService()
    {
        return $this->mocked('requestService', Service\RequestService::class);
    }
    /**
     * @return Service\FormService|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockedFormService()
    {
        return $this->mocked('formService', Service\FormService::class);
    }
    /**
     * @return Service\ConnectionService|\PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockedConnectionService()
    {
        return $this->mocked('connectionService', Service\ConnectionService::class);
    }
}
