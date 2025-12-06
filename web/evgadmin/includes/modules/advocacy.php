<?php
require_once $pix->basedir . 'includes/libs/tcpdf/tcpdf.php';
if (!class_exists('advocacy')) {
    class advocacy
    {
        public function makeAdvMemAgreement($signature, $advocacy)
        {
            global $datetime, $pix;

            $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            $pdf->SetPageUnit('pt');
            $pdf->SetCreator('NCNW');
            $pdf->SetAuthor('NCNW Team');
            $pdf->SetTitle('Advocacy');
            $pdf->SetSubject($advocacy->title);
            $pdf->SetHeaderData('evergreen-logo.png', 18, 'NCNW', '');
            $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
            $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
            $pdf->SetMargins(20, 50, 20);
            $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
            $pdf->setPrintFooter(false);

            $pdf->AddPage();
            $pdf->writeHTML(html_entity_decode($advocacy->pdfContent), true, false, true, false, '');
            $htmlHeight = $pdf->GetY() + 10;
            $pdf->Image($signature, 20, $htmlHeight, 66, 66, exf($signature), '', '', true, 300);
            $pdf->writeHTMLCell(0, 0, 20, $htmlHeight + 80, '<p>Date & Time<br/> ' . date('m-d-Y g:i a', strtotime($datetime)) . '</p>', 0, 1, false, true, 'L');

            $dateDir = $pix->setDateDir('advocacy');
            $nName = $pix->makestring(50, 'ln') . '.pdf';
            $pdfFilePath = $dateDir->absdir . $nName;
            $pdf->Output($pdfFilePath, 'F');

            return $dateDir->uplroot . $nName;
        }
    }
}

return new advocacy();
