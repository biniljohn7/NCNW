<?php
require_once $pix->basedir . 'includes/libs/tcpdf/tcpdf.php';

ob_clean();

$lgUsrInfo = $pixdb->getRow('members_info', ['member' => $lgUser->id]);
if (!$lgUsrInfo) {
    $lgUsrInfo = new stdClass();
}

$state = '';
if (isset($lgUsrInfo->state)) {
    $fetchState = $pixdb->getRow(
        'states',
        ['id' => $lgUsrInfo->state],
        'name'
    );
    if ($fetchState) {
        $state = $fetchState->name;
    }
}

$membership = $pixdb->getRow(
    'memberships',
    [
        'member' => $lgUser->id,
        '#SRT' => 'id desc',
        '#QRY' => '((giftedBy IS NOT NULL AND accepted = "Y") OR giftedBy IS NULL)'
    ]
);
$address = "<br>";
if ($lgUsrInfo->address) {
    $address .= $lgUsrInfo->address . "<br>";
}
if ($lgUsrInfo->address2) {
    $address .= $lgUsrInfo->address2 . "<br>";
}
if ($lgUsrInfo->city) {
    $address .= $lgUsrInfo->city;
}
if ($state) {
    $address .= ($lgUsrInfo->city ? '<br>' : '') . $state;
}
if ($lgUsrInfo->zipcode) {
    $address .= ' ' . $lgUsrInfo->zipcode;
}
?>
<br>
<p><strong>To: <?php echo (isset($lgUsrInfo->prefix) ? $evg::prefix[$lgUsrInfo->prefix] . ' ' : '') . $lgUser->firstName . ' ' . $lgUser->lastName; ?></strong>
    <?php echo  $address; ?>
    <br>
</p>
<p><?php echo date('F d, Y', strtotime($datetime)); ?><br></p>
<p>Dear <strong><?php echo (isset($lgUsrInfo->prefix) ? $evg::prefix[$lgUsrInfo->prefix] . ' ' : '') .  $lgUser->firstName . ' ' . $lgUser->lastName; ?>,</strong>
</p>

<p>On behalf of the Board of Directors of NCNW—Thank You! Your <strong><?php echo $membership && $membership->planName ? strtoupper($membership->planName) : ''; ?></strong> membership and ongoing support helps us to build on our rich legacy of leadership and service.
</p>

<p>NCNW's Core 4 programmatic priorities are STEAM, Health Equity , Economic Empowerment, and Social Justice. As we continue to face tremendous social and economic challenges, our efforts on behalf of Black women remain as urgent and important as ever! Thank you for investing in our mission-critical work.
</p>

<p>To stay abreast of our national initiatives, find a local section near you or learn more about our history, visit us at www.ncnw.org.
    <br>
</p>

<p>Sincerely,</p>

<p>David Glenn, Jr., <br>VP of Membership</p>
<?php
$letterTxt = ob_get_clean();

// pdf

class MyPDF extends TCPDF
{
    public function Header()
    {
        global $pix;
        $this->SetFillColor(96, 38, 95);
        $this->Rect(
            0,
            0,
            $this->getPageWidth(),
            5,
            'F'
        );

        $logo = $pix->domain . 'evgadmin/assets/images/ncnw-logo-new.png';
        $logoWidth = 50;

        $xLogo = ($this->getPageWidth() - $logoWidth) / 2;
        $yLogo = 15;

        $this->Image($logo, $xLogo, $yLogo, $logoWidth);

        $lineY = $yLogo + 30;
        $this->SetDrawColor(96, 38, 95);
        $this->SetLineWidth(1);

        $lineWidth = 150;
        $xLineStart = ($this->getPageWidth() - $lineWidth) / 2;
        $xLineEnd   = $xLineStart + $lineWidth;

        $this->Line($xLineStart, $lineY, $xLineEnd, $lineY);
    }
    public function Footer()
    {
        $this->SetFillColor(96, 38, 95);
        $this->SetY(-20);
        $this->Rect(
            0,
            $this->getPageHeight() - 10,
            $this->getPageWidth(),
            10,
            'F'
        );
        $this->SetY(-10);
        $this->SetFont('helvetica', '', 9);
        $this->SetTextColor(255, 255, 255);

        $footerText = '633 Pennsylvania Avenue, NW, Washington, DC 20004  |  Call: 202-737-0120  |  Email: info@ncnw.org';

        $this->Cell(0, 10, $footerText, 0, 0, 'C');
    }
}

$pdf = new MyPDF();
$pdf->SetCreator('NCNW');
$pdf->SetAuthor('NCNW Team');
$pdf->SetTitle('Recommendation Letter');
$pdf->SetSubject('Recommendation Letter');

$pdf->SetPrintHeader(true);
$pdf->SetPrintFooter(true);

$pdf->SetMargins(15, 45, 15);
$pdf->SetFooterMargin(30);

$pdf->AddPage();
$pdf->SetFont('helvetica', '', 12);
$pdf->SetTextColor(54, 63, 63);
$pdf->SetCellHeightRatio(1.5);
$pdf->writeHTML($letterTxt, true, false, true, false, '');
$pdf->Output();
exit;
