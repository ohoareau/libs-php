<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Itq\Common\Exception;

use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Itq\Common\Exception\FormValidationException;
use Itq\Common\Tests\Exception\Base\AbstractExceptionTestCase;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 *
 * @group exceptions
 * @group exceptions/form-validation
 */
class FormValidationExceptionTest extends AbstractExceptionTestCase
{
    /**
     * @return FormValidationException
     */
    public function e()
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */

        return parent::e();
    }
    /**
     * @return array
     */
    public function constructor()
    {
        return [$this->mocked('form', FormInterface::class)];
    }
    /**
     * @group unit
     */
    public function testGetters()
    {
        $this->assertEquals('Malformed data', $this->e()->getMessage());
        $this->assertEquals(412, $this->e()->getCode());
        $this->assertEquals($this->mocked('form'), $this->e()->getForm());

        $this->mocked('form')->expects($this->any())->method('getErrors')->willReturn([]);
        $this->mocked('form')->expects($this->any())->method('all')->willReturn([]);

        $this->assertEquals([], $this->e()->getErrors());
        $this->assertEquals('Data validation errors:', rtrim($this->e()->getErrorsAsString()));
    }
    /**
     * @group unit
     */
    public function testGetErrorsForNoChildrenAndNoPrefix()
    {
        $errors = [new FormError('the error 1'), new FormError('the error 2')];

        $this->mocked('form')->expects($this->any())->method('getName')->willReturn('theFormName');
        $this->mocked('form')->expects($this->any())->method('getErrors')->willReturn($errors);
        $this->mocked('form')->expects($this->any())->method('all')->willReturn([]);

        $this->assertEquals(
            [
                //'theFormName' => ['the error 1', 'the error 2'],
            ],
            $this->e()->getErrors()
        );
    }
    /**
     * @group unit
     */
    public function testGetErrorsForChildren()
    {
        $this->mocked('childForm1', FormInterface::class);
        $this->mocked('childForm2', FormInterface::class);

        //$errorsForm       = [new FormError('the error 1 (form)'), new FormError('the error 2 (form)')];
        $errorsChildForm1 = [new FormError('the error 1 (childForm1)'), new FormError('the error 2 (childForm1)')];
        $errorsChildForm2 = [new FormError('the error 1 (childForm2)'), new FormError('the error 2 (childForm2)')];

        $this->mocked('form')->expects($this->any())->method('getName')->willReturn('formName');
        $this->mocked('childForm1')->expects($this->any())->method('getName')->willReturn('childForm1Name');
        $this->mocked('childForm2')->expects($this->any())->method('getName')->willReturn('childForm2Name');
        //$this->mocked('form')->expects($this->once())->method('getErrors')->willReturn($errorsForm);
        $this->mocked('childForm1')->expects($this->once())->method('getErrors')->willReturn($errorsChildForm1);
        $this->mocked('childForm2')->expects($this->once())->method('getErrors')->willReturn($errorsChildForm2);
        $this->mocked('form')->expects($this->once())->method('all')->willReturn([$this->mocked('childForm1'), $this->mocked('childForm2')]);
        $this->mocked('childForm1')->expects($this->once())->method('all')->willReturn([]);
        $this->mocked('childForm2')->expects($this->any())->method('all')->willReturn([]);

        $this->assertEquals(
            [
                //'theFormName' => ['the error 1', 'the error 2'],
                'childForm1Name'  => ['the error 1 (childForm1)', 'the error 2 (childForm1)'],
                'childForm2Name'  => ['the error 1 (childForm2)', 'the error 2 (childForm2)'],
            ],
            $this->e()->getErrors()
        );
    }
    /**
     * @group unit
     */
    public function testGetErrorsAsStringForChildren()
    {
        $this->mocked('childForm1', FormInterface::class);
        $this->mocked('childForm2', FormInterface::class);

        //$errorsForm       = [new FormError('the error 1 (form)'), new FormError('the error 2 (form)')];
        $errorsChildForm1 = [new FormError('the error 1 (childForm1)'), new FormError('the error 2 (childForm1)'), 'An other error'];
        $errorsChildForm2 = [new FormError('the error 1 (childForm2)'), new FormError('the error 2 (childForm2)')];

        $this->mocked('form')->expects($this->any())->method('getName')->willReturn('formName');
        $this->mocked('childForm1')->expects($this->any())->method('getName')->willReturn('childForm1Name');
        $this->mocked('childForm2')->expects($this->any())->method('getName')->willReturn('childForm2Name');
        //$this->mocked('form')->expects($this->once())->method('getErrors')->willReturn($errorsForm);
        $this->mocked('childForm1')->expects($this->once())->method('getErrors')->willReturn($errorsChildForm1);
        $this->mocked('childForm2')->expects($this->once())->method('getErrors')->willReturn($errorsChildForm2);
        $this->mocked('form')->expects($this->once())->method('all')->willReturn([$this->mocked('childForm1'), $this->mocked('childForm2')]);
        $this->mocked('childForm1')->expects($this->once())->method('all')->willReturn([]);
        $this->mocked('childForm2')->expects($this->any())->method('all')->willReturn([]);

        $this->assertEquals(
            join(
                PHP_EOL,
                [
                    'Data validation errors: ',
                    '  childForm1Name:',
                    '    - the error 1 (childForm1)',
                    '    - the error 2 (childForm1)',
                    '    - An other error',
                    '  childForm2Name:',
                    '    - the error 1 (childForm2)',
                    '    - the error 2 (childForm2)',
                ]
            ),
            rtrim($this->e()->getErrorsAsString())
        );
    }
}
