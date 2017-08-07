<?php

/*
 * This file is part of the COMMON package.
 *
 * (c) itiQiti SAS <opensource@itiqiti.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Itq\Common\Plugin\DocumentBuilder;

use Itq\Common\Document;
use Itq\Common\DocumentInterface;
use Itq\Common\Plugin\Base\AbstractPlugin;

/**
 * @author itiQiti Dev Team <opensource@itiqiti.com>
 */
class ExcelDocumentBuilder extends AbstractPlugin
{
    /**
     * @param array $data
     * @param array $metas
     * @param array $options
     *
     * @return DocumentInterface
     *
     * @throws \Exception
     */
    public function build(array $data = [], $metas = [], array $options = [])
    {
        $metas += ['creator' => null, 'modifier' => null, 'filename' => 'spreadsheet.xlsx'];

        $path = tempnam(sys_get_temp_dir(), md5(__DIR__).'-excel');

        if (!class_exists('PHPExcel')) {
            throw $this->createRequiredException('phpexcel is required');
        }

        $excel = new \PHPExcel();

        $excel->getProperties()->setCreator($metas['creator']);
        $excel->getProperties()->setLastModifiedBy($metas['modifier']);

        $this->buildExcel($excel, $data);

        $extension = null;

        if (false !== ($p = strrpos($metas['filename'], '.'))) {
            $extension = substr($metas['filename'], $p + 1);
        }

        list($writer, $contentType) = $this->getWriterForExtension($excel, $extension);
        /** @var \PHPExcel_Writer_IWriter $writer */
        $writer->save($path);

        $content = file_get_contents($path);
        unlink($path);

        unset($options);

        return new Document($content, $contentType, $metas['filename']);
    }
    /**
     * @param \PHPExcel $excel
     * @param $data
     *
     * @throws \PHPExcel_Exception
     */
    protected function buildExcel(\PHPExcel $excel, $data)
    {
        $this->buildProperties($excel->getProperties(), $data);
        $excel->setActiveSheetIndex(0);
        $this->buildSheet($excel->getActiveSheet(), $data);
    }
    /**
     * @param \PHPExcel $excel
     * @param string    $extension
     *
     * @return array
     *
     * @throws \Exception
     */
    protected function getWriterForExtension(\PHPExcel $excel, $extension)
    {
        switch (strtolower(trim($extension))) {
            case 'xlsx':
                return [
                    new \PHPExcel_Writer_Excel2007($excel),
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                ];
            case 'xls':
                return [
                    new \PHPExcel_Writer_Excel5($excel),
                    'application/vnd.ms-excel',
                ];
            case 'ods':
                return [
                    new \PHPExcel_Writer_OpenDocument($excel),
                    'application/vnd.oasis.opendocument.spreadsheet',
                ];
            case 'html':
                return [
                    new \PHPExcel_Writer_HTML($excel),
                    'text/html',
                ];
            case 'csv':
                return [
                    new \PHPExcel_Writer_CSV($excel),
                    'text/csv',
                ];
            default:
                throw $this->createRequiredException("Unsupported excel writer '%s'", $extension);
        }
    }
    /**
     * @param \PHPExcel_DocumentProperties $properties
     * @param array                        $metas
     */
    protected function buildProperties(\PHPExcel_DocumentProperties $properties, $metas)
    {
        if (isset($metas['title'])) {
            $properties->setTitle($metas['title']);
        }

        if (isset($metas['subject'])) {
            $properties->setSubject($metas['subject']);
        }

        if (isset($metas['description'])) {
            $properties->setDescription($metas['description']);
        }
    }
    /**
     * @param \PHPExcel_Worksheet $sheet
     * @param array               $data
     */
    protected function buildSheet(\PHPExcel_Worksheet $sheet, $data)
    {
        if (!is_array($data)) {
            $data = [];
        }

        $headers = null;

        $row = 1;
        foreach ($data as $k => $v) {
            if (!is_array($v)) {
                $v = [];
            }
            if (null === $headers) {
                $headers = array_keys($v);
                foreach ($headers as $kk => $vv) {
                    $sheet->setCellValueByColumnAndRow($kk, 1, ucwords($vv));
                }
            }
            $row++;
            foreach ($headers as $kk => $vv) {
                $sheet->setCellValueByColumnAndRow($kk, $row, isset($v[$vv]) ? $this->formatCellValue($v[$vv]) : null);
            }
        }
    }
    /**
     * @param mixed $value
     *
     * @return string
     */
    protected function formatCellValue($value)
    {
        if (is_object($value)) {
            $value = (array) $value;
        }

        if (is_array($value)) {
            $value = json_encode($value);
        }

        return (string) $value;
    }
}
