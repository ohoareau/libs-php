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

use Itq\Common\Traits;
use PHPUnit_Framework_TestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
abstract class AbstractBasicTestCase extends PHPUnit_Framework_TestCase
{
    use Traits\MockTrait;
    use Traits\ExceptionThrowerTrait;
    use Traits\TestMock\LoggerTestMockTrait;
    use Traits\TestMock\ContainerTestMockTrait;
    use Traits\TestMock\SerializerTestMockTrait;
    use Traits\TestMock\TemplatingTestMockTrait;
    use Traits\TestMock\TranslatorTestMockTrait;
    use Traits\TestMock\SdkServiceTestMockTrait;
    use Traits\TestMock\DocServiceTestMockTrait;
    use Traits\TestMock\PhpServiceTestMockTrait;
    use Traits\TestMock\JsonServiceTestMockTrait;
    use Traits\TestMock\FormFactoryTestMockTrait;
    use Traits\TestMock\MathServiceTestMockTrait;
    use Traits\TestMock\TaskServiceTestMockTrait;
    use Traits\TestMock\YamlServiceTestMockTrait;
    use Traits\TestMock\DateServiceTestMockTrait;
    use Traits\TestMock\FormServiceTestMockTrait;
    use Traits\TestMock\HttpServiceTestMockTrait;
    use Traits\TestMock\CrudServiceTestMockTrait;
    use Traits\TestMock\EventServiceTestMockTrait;
    use Traits\TestMock\TokenStorageTestMockTrait;
    use Traits\TestMock\ErrorManagerTestMockTrait;
    use Traits\TestMock\RequestStackTestMockTrait;
    use Traits\TestMock\ModelServiceTestMockTrait;
    use Traits\TestMock\VaultServiceTestMockTrait;
    use Traits\TestAssert\DateTimeTestAssertTrait;
    use Traits\TestMock\PollerServiceTestMockTrait;
    use Traits\TestAssert\ExceptionTestAssertTrait;
    use Traits\TestMock\TenantServiceTestMockTrait;
    use Traits\TestMock\StringServiceTestMockTrait;
    use Traits\TestMock\SystemServiceTestMockTrait;
    use Traits\TestMock\ActionServiceTestMockTrait;
    use Traits\TestMock\GoogleServiceTestMockTrait;
    use Traits\TestMock\TrackerServiceTestMockTrait;
    use Traits\TestMock\AddressServiceTestMockTrait;
    use Traits\TestMock\PartnerServiceTestMockTrait;
    use Traits\TestMock\SymfonyServiceTestMockTrait;
    use Traits\TestMock\StorageServiceTestMockTrait;
    use Traits\TestMock\JobTypeServiceTestMockTrait;
    use Traits\TestMock\ClientProviderTestMockTrait;
    use Traits\TestMock\RequestServiceTestMockTrait;
    use Traits\TestMock\ContextServiceTestMockTrait;
    use Traits\TestMock\DispatchServiceTestMockTrait;
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
    use Traits\TestMock\FormatterServiceTestMockTrait;
    use Traits\TestMock\AnnotationReaderTestMockTrait;
    use Traits\TestMock\GeneratorServiceTestMockTrait;
    use Traits\TestMock\CriteriumServiceTestMockTrait;
    use Traits\TestMock\TypeGuessServiceTestMockTrait;
    use Traits\TestMock\ModelStatsServiceTestMockTrait;
    use Traits\TestMock\FilesystemServiceTestMockTrait;
    use Traits\TestMock\DynamicUrlServiceTestMockTrait;
    use Traits\TestMock\RuleEngineServiceTestMockTrait;
    use Traits\TestMock\ExpressionServiceTestMockTrait;
    use Traits\TestMock\RepositoryServiceTestMockTrait;
    use Traits\TestMock\AttachmentServiceTestMockTrait;
    use Traits\TestMock\CustomizerServiceTestMockTrait;
    use Traits\TestMock\ConnectionServiceTestMockTrait;
    use Traits\TestMock\ExpressionLanguageTestMockTrait;
    use Traits\TestMock\SupervisionServiceTestMockTrait;
    use Traits\TestMock\BusinessRuleServiceTestMockTrait;
    use Traits\TestMock\DataProviderServiceTestMockTrait;
    use Traits\TestMock\UserProviderServiceTestMockTrait;
    use Traits\TestMock\NotificationProviderTestMockTrait;
    use Traits\TestMock\AuthorizationCheckerTestMockTrait;
    use Traits\TestMock\PollableSourceServiceTestMockTrait;
    use Traits\TestMock\QueueCollectionServiceTestMockTrait;
    use Traits\TestMock\DocumentBuilderServiceTestMockTrait;
}
