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
use Itq\Common\Service;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class ModelService extends Base\AbstractModelService
{
    use Traits\ServiceAware\Model\ModelCleanerServiceAwareTrait;
    use Traits\ServiceAware\Model\ModelRefresherServiceAwareTrait;
    use Traits\ServiceAware\Model\ModelRestricterServiceAwareTrait;
    use Traits\ServiceAware\Model\ModelUpdateEnricherServiceAwareTrait;
    use Traits\ServiceAware\Model\ModelObjectPopulatorServiceAwareTrait;
    use Traits\ServiceAware\Model\ModelFieldListFilterServiceAwareTrait;
    use Traits\ServiceAware\Model\ModelDynamicUrlBuilderServiceAwareTrait;
    use Traits\ServiceAware\Model\ModelPropertyLinearizerServiceAwareTrait;
    /**
     * @param Service\Model\ModelCleanerServiceInterface            $modelCleanerService
     * @param Service\Model\ModelRestricterServiceInterface         $modelRestricterService
     * @param Service\Model\ModelUpdateEnricherServiceInterface     $modelUpdateEnricherService
     * @param Service\Model\ModelObjectPopulatorServiceInterface    $modelObjectPopulatorService
     * @param Service\Model\ModelRefresherServiceInterface          $modelRefresherService
     * @param Service\Model\ModelFieldListFilterServiceInterface    $modelFieldListFilterService
     * @param Service\Model\ModelDynamicUrlBuilderServiceInterface  $modelDynamicUrlBuilderService
     * @param Service\Model\ModelPropertyLinearizerServiceInterface $modelPropertyLinearizerService
     */
    public function __construct(
        Service\Model\ModelCleanerServiceInterface $modelCleanerService,
        Service\Model\ModelRestricterServiceInterface $modelRestricterService,
        Service\Model\ModelUpdateEnricherServiceInterface $modelUpdateEnricherService,
        Service\Model\ModelObjectPopulatorServiceInterface $modelObjectPopulatorService,
        Service\Model\ModelRefresherServiceInterface $modelRefresherService,
        Service\Model\ModelFieldListFilterServiceInterface $modelFieldListFilterService,
        Service\Model\ModelDynamicUrlBuilderServiceInterface $modelDynamicUrlBuilderService,
        Service\Model\ModelPropertyLinearizerServiceInterface $modelPropertyLinearizerService
    ) {
        $this->setModelCleanerService($modelCleanerService);
        $this->setModelRestricterService($modelRestricterService);
        $this->setModelUpdateEnricherService($modelUpdateEnricherService);
        $this->setModelObjectPopulatorService($modelObjectPopulatorService);
        $this->setModelRefresherService($modelRefresherService);
        $this->setModelFieldListFilterService($modelFieldListFilterService);
        $this->setModelDynamicUrlBuilderService($modelDynamicUrlBuilderService);
        $this->setModelPropertyLinearizerService($modelPropertyLinearizerService);
    }
    /**
     * @param mixed  $doc
     * @param string $property
     * @param array  $options
     *
     * @return mixed
     */
    public function buildDynamicUrl($doc, $property, array $options = [])
    {
        return $this->getModelDynamicUrlBuilderService()->buildDynamicUrl($doc, $property, $options);
    }
    /**
     * @param mixed $doc
     * @param array $options
     *
     * @return mixed
     */
    public function clean($doc, $options = [])
    {
        return $this->getModelCleanerService()->clean($doc, $options);
    }
    /**
     * @param mixed $doc
     * @param array $options
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function convertObjectToArray($doc, $options = [])
    {
        return $this->getModelPropertyLinearizerService()->linearize($doc, $options);
    }
    /**
     * @param array  $data
     * @param string $class
     * @param array  $options
     *
     * @return array
     */
    public function enrichUpdates($data, $class, array $options = [])
    {
        return $this->getModelUpdateEnricherService()->enrichUpdates($data, $class, $options);
    }
    /**
     * @param string $model
     * @param array  $fields
     * @param array  $options
     *
     * @return array
     */
    public function prepareFields($model, $fields, array $options = [])
    {
        return $this->getModelFieldListFilterService()->prepareFields($model, $fields, $options);
    }
    /**
     * @param mixed $doc
     * @param array $data
     * @param array $options
     *
     * @return mixed
     */
    public function populateObject($doc, $data = [], $options = [])
    {
        return $this->getModelObjectPopulatorService()->populateObject($doc, $data, $options);
    }
    /**
     * @param mixed  $doc
     * @param mixed  $data
     * @param string $propertyName
     * @param array  $options
     *
     * @return mixed
     */
    public function populateObjectProperty($doc, $data, $propertyName, $options = [])
    {
        return $this->getModelObjectPopulatorService()->populateObjectProperty($doc, $data, $propertyName, $options);
    }
    /**
     * @param mixed $doc
     * @param array $options
     *
     * @return mixed
     */
    public function refresh($doc, $options = [])
    {
        return $this->getModelRefresherService()->refresh($doc, $options);
    }
    /**
     * @param mixed $doc
     * @param array $options
     */
    public function restrict($doc, array $options = [])
    {
        $this->getModelRestricterService()->restrict($doc, $options);
    }
}
