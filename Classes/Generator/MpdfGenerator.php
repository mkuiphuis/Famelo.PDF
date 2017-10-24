<?php
namespace Famelo\PDF\Generator;

/*                                                                        *
 * This script belongs to the FLOW3 package "Famelo.PDF".                 *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

use Famelo\PDF\Error\UnknownGeneratorOptionException;
use Mpdf\Mpdf;
use Neos\Flow\Annotations as Flow;

/**
 * @Flow\Scope("prototype")
 */
class MpdfGenerator implements PdfGeneratorInterface {

    /**
     * @var string
     */
    protected $format;

    /**
     * @var string
     */
    protected $header;

    /**
     * @var string
     */
    protected $footer;

    /**
     * @var string
     */
    protected $options = array(
        'encoding' => '',
        'format' => 'A4',
        'orientation' => 'P',
        'font_size' => 0,
        'font' => '',
        'margin_left' => 15,
        'margin_right' => 15,
        'margin_top' => 16,
        'margin_bottom' => 16,
        'margin_header' => 9,
        'margin_footer' => 9,
    );

    public function setFormat($format) {
        $this->setOption('format', $format);
    }

    public function setHeader($content) {
        $this->header = $content;
    }

    public function setFooter($content) {
        $this->footer = $content;
    }

    public function setOption($name, $value) {
        $backwardsCompatabilityOptionNames = array(
            'marginLeft' => 'margin_left',
            'marginRight' => 'margin_right',
            'marginTop' => 'margin_top',
            'marginBottom' => 'margin_bottom',
            'marginHeader' => 'margin_header',
            'marginFooter' => 'margin_footer',
            'fontSize' => 'default_font_size'
        );
        if (isset($backwardsCompatabilityOptionNames[$name])) {
            $name = $backwardsCompatabilityOptionNames[$name];
        }
        if (!isset($this->options[$name])) {
            throw new UnknownGeneratorOptionException('The option "' . $name . '" you\'re trying to set does not exist. This generator supports these options: ' . chr(10) . chr(10) . implode(chr(10), array_keys($this->options)), 1421314368);
        }
        $this->options[$name] = $value;
    }

    public function getMpdfInstance() {
        $mpdf = new Mpdf($this->options);

        if ($this->footer !== NULL) {
            $mpdf->SetHTMLFooter($this->footer);
        }
        if ($this->header !== NULL) {
            $mpdf->SetHTMLHeader($this->header);
        }
        return $mpdf;
    }

    public function sendPdf($content, $filename = NULL) {
        $previousErrorReporting = error_reporting(0);
        $pdf = $this->getMpdfInstance();
        $pdf->WriteHTML($content);
        $pdf->Output($filename, 'i');
        error_reporting($previousErrorReporting);
    }

    public function downloadPdf($content, $filename = NULL) {
        $previousErrorReporting = error_reporting(0);
        $pdf = $this->getMpdfInstance();
        $pdf->WriteHTML($content);
        $pdf->Output($filename, 'd');
        error_reporting($previousErrorReporting);
    }

    public function savePdf($content, $filename) {
        $previousErrorReporting = error_reporting(0);
        $pdf = $this->getMpdfInstance();
        $pdf->WriteHTML($content);
        $pdf->Output($filename, 'f');
        error_reporting($previousErrorReporting);
    }
}
