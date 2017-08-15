<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\FileGenerator\Base;

use TCPDF;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class AbstractPdfFileGenerator extends AbstractFileGenerator
{
    /**
     * @param array $vars
     * @param array $options
     *
     * @return string
     */
    public function generate($vars = [], $options = [])
    {
        $pdf = $this->createPdf($vars, $options);

        $this->drawPdf($pdf, $vars, $options);

        return $pdf->output(null, 'S');
    }
    /**
     * @param array $vars
     * @param array $options
     *
     * @return TCPDF
     *
     * @throws \Exception
     */
    protected function createPdf(array $vars = [], array $options = [])
    {
        unset($vars);
        unset($options);

        if (!class_exists('TCPDF')) {
            throw $this->createRequiredException('TCPDF required');
        }

        return new TCPDF();
    }
    /**
     * @param TCPDF $pdf
     * @param array $vars
     * @param array $options
     *
     * @return $this
     */
    protected function drawPdf(TCPDF $pdf, array $vars = [], array $options = [])
    {
        unset($pdf);
        unset($vars);
        unset($options);

        return $this;
    }
}
