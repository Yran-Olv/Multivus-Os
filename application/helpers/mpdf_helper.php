<?php

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once __DIR__ . '/../vendor/autoload.php';

function pdf_create($html, $filename, $stream = true, $landscape = false)
{
    if ($landscape) {
        $mpdf = new \Mpdf\Mpdf(['c', 'A4-L', 'tempDir' => FCPATH . 'assets/uploads/temp/']);
    } else {
        $mpdf = new \Mpdf\Mpdf(['c', 'A4', 'tempDir' => FCPATH . 'assets/uploads/temp/']);
    }

    $mpdf->showImageErrors = true;
    $mpdf->WriteHTML($html);

    if ($stream) {
        $mpdf->Output($filename . '.pdf', 'I');
    } else {
        $mpdf->Output(FCPATH . 'assets/uploads/temp/' . $filename . '.pdf', 'F');

        return FCPATH . 'assets/uploads/temp/' . $filename . '.pdf';
    }
}

/**
 * Cria PDF otimizado para WhatsApp (limita a uma página quando possível)
 */
function pdf_create_whatsapp($html, $filename, $stream = false)
{
    $mpdf = new \Mpdf\Mpdf([
        'mode' => 'utf-8',
        'format' => 'A4',
        'orientation' => 'P',
        'margin_left' => 3,
        'margin_right' => 3,
        'margin_top' => 3,
        'margin_bottom' => 3,
        'margin_header' => 0,
        'margin_footer' => 0,
        'tempDir' => FCPATH . 'assets/uploads/temp/',
        'autoScriptToLang' => true,
        'autoLangToFont' => true,
    ]);

    $mpdf->showImageErrors = true;
    $mpdf->SetDisplayMode('fullpage');
    
    // Limitar a uma página se possível
    $mpdf->SetHTMLHeader('');
    $mpdf->SetHTMLFooter('');
    
    $mpdf->WriteHTML($html);

    if ($stream) {
        $mpdf->Output($filename . '.pdf', 'I');
        return null;
    } else {
        $mpdf->Output(FCPATH . 'assets/uploads/temp/' . $filename . '.pdf', 'F');
        return FCPATH . 'assets/uploads/temp/' . $filename . '.pdf';
    }
}
