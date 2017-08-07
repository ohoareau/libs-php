<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\Formatter;

use Itq\Common\Traits;
use Itq\Common\Service;
use Itq\Common\Plugin\Base\AbstractPlugin;
use /** @noinspection PhpUnusedAliasInspection */ Itq\Common\Annotation;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class ExcelFormatter extends AbstractPlugin
{
    use Traits\ServiceAware\DocumentBuilderServiceAwareTrait;
    /**
     * @param Service\DocumentBuilderService $documentBuilderService
     */
    public function __construct(Service\DocumentBuilderService $documentBuilderService)
    {
        $this->setDocumentBuilderService($documentBuilderService);
    }
    /**
     * @param mixed $data
     * @param array $options
     *
     * @return string
     *
     * @Annotation\Formatter("text/csv")
     */
    public function formatCsv($data, array $options = [])
    {
        return $this->getDocumentBuilderService()->build('excel', $data, ['filename' => 'result.csv'], $options);
    }
    /**
     * @param mixed $data
     * @param array $options
     *
     * @return string
     *
     * @Annotation\Formatter("text/html")
     */
    public function formatHtml($data, array $options = [])
    {
        return $this->getDocumentBuilderService()->build('excel', $data, ['filename' => 'result.html'], $options);
    }
    /**
     * @param mixed $data
     * @param array $options
     *
     * @return string
     *
     * @Annotation\Formatter("application/vnd.ms-excel")
     */
    public function formatXls($data, array $options = [])
    {
        return $this->getDocumentBuilderService()->build('excel', $data, ['filename' => 'result.xls'], $options);
    }
    /**
     * @param mixed $data
     * @param array $options
     *
     * @return string
     *
     * @Annotation\Formatter("application/vnd.oasis.opendocument.spreadsheet")
     */
    public function formatOds($data, array $options = [])
    {
        return $this->getDocumentBuilderService()->build('excel', $data, ['filename' => 'result.ods'], $options);
    }
    /**
     * @param mixed $data
     * @param array $options
     *
     * @return string
     *
     * @Annotation\Formatter("application/vnd.openxmlformats-officedocument.spreadsheetml.sheet")
     */
    public function formatXlsx($data, array $options = [])
    {
        return $this->getDocumentBuilderService()->build('excel', $data, ['filename' => 'result.xlsx'], $options);
    }
}
