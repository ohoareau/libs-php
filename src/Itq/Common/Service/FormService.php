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
use Itq\Common\Exception;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class FormService
{
    use Traits\ServiceTrait;
    use Traits\FormFactoryAwareTrait;
    /**
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->setFormFactory($formFactory);
    }
    /**
     * @param string $type
     * @param string $mode
     * @param array  $data
     * @param array  $options
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function validate($type, $mode, $data, $options = [])
    {
        $options        += ['clearMissing' => true, 'cleanData' => [], 'unvalidableKeys' => []];
        $clearMissing    = $options['clearMissing'];
        $cleanData       = $options['cleanData'];
        $data            = is_array($data) ? $data : [];
        $unvalidableData = [];

        if (!$clearMissing) {
            foreach ($data as $k => $v) {
                if (null === $v) {
                    unset($data[$k]);
                }
            }
        }

        $validableData = $data;

        unset($data);

        if (count($options['unvalidableKeys'])) {
            $unvalidableData = array_intersect_key($validableData, $options['unvalidableKeys']);
            $validableData = array_diff_key($validableData, $options['unvalidableKeys']);
        }

        $form = $this
            ->cleanRawData($validableData)
            ->createForm($type, $mode, $options)
            ->submit($cleanData + $validableData, $clearMissing)
        ;

        if (!$form->isValid()) {
            throw new Exception\FormValidationException($form);
        }

        $validatedData = $form->getData();

        if (!is_object($validatedData)) {
            return $unvalidableData + (is_array($validatedData) ? $validatedData : []);
        }

        foreach ($unvalidableData as $k => $v) {
            if (!property_exists($validatedData, $k)) {
                continue;
            }
            $validatedData->$k = $v;
        }

        return $validatedData;
    }
    /**
     * @param string $type
     * @param string $mode
     * @param array  $options
     *
     * @return FormInterface
     */
    public function createForm($type, $mode = 'create', $options = [])
    {
        return $this->getFormFactory()
            ->createBuilder(
                sprintf('app_%s_%s', str_replace('.', '_', $type), $mode),
                null,
                [
                    'csrf_protection'    => false,
                    'validation_groups'  => isset($options['validation_groups']) ? (is_array($options['validation_groups']) ? $options['validation_groups'] : [$options['validation_groups']]) : [$mode],
                    'allow_extra_fields' => true,
                ]
            )
            ->getForm()
        ;
    }
    /**
     * Fix to avoid Symfony Form to replace false (bool) value by null values
     *
     * @param array $data
     *
     * @return $this
     */
    protected function cleanRawData(&$data)
    {
        if (is_array($data)) {
            foreach (array_keys($data) as $k) {
                $this->cleanRawData($data[$k]);
            }

            return $this;
        }

        if (is_bool($data) && false === $data) {
            $data = (int) 0;
        }

        return $this;
    }
}
