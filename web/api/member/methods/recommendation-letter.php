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

$membership = $pixdb->getRow('memberships', ['member' => $lgUser->id]);
$address = "\n";
if ($lgUsrInfo->address) {
    $address .= $lgUsrInfo->address . "\n";
}
if ($lgUsrInfo->city) {
    $address .= $lgUsrInfo->city;
}
if ($state) {
    $address .= ($lgUsrInfo->city ? ', ' : '') . $state;
}
if ($lgUsrInfo->zipcode) {
    $address .= ' ' . $lgUsrInfo->zipcode;
}
?>
<p><?php echo date('F d, Y', strtotime($datetime)); ?>
</p>

<p>
    <strong>
        <?php
        echo (isset($lgUsrInfo->prefix) ? $evg::prefix[$lgUsrInfo->prefix] . ' ' : '') . $lgUser->firstName . ' ' . $lgUser->lastName, $address;
        ?>
    </strong>
</p>

<p>Dear <strong><?php echo (isset($lgUsrInfo->prefix) ? $evg::prefix[$lgUsrInfo->prefix] . ' ' : '') . $lgUser->lastName; ?>,</strong>
</p>

<p>On behalf of the Board of Directors of NCNW—Thank You! Your <strong><?php echo $membership && $membership->planName ? strtoupper($membership->planName) : ''; ?></strong> membership and ongoing support helps us to build on our rich legacy of leadership and service.
</p>

<p>NCNW's Core 4 programmatic priorities are STEAM, Health Equity , Economic Empowerment, and Social Justice. As we continue to face tremendous social and economic challenges, our efforts on behalf of Black women remain as urgent and important as ever! Thank you for investing in our mission-critical work.
</p>

<p>To stay abreast of our national initiatives, find a local section near you or learn more about our history, visit us at www.ncnw.org.
</p>

<p>Sincerely,</p>

<p>David Glenn, Jr., VP of Membership</p>
<?php
$letterTxt = ob_get_clean();

// pdf
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetPageUnit('pt');
$pdf->SetCreator('NCNW');
$pdf->SetAuthor('NCNW Team');
$pdf->SetTitle('Recommendation Letter');
$pdf->SetSubject('Recommendation Letter');
$pdf->SetHeaderData('../../../assets/images/ncnw-logo.png', 60, '', '');


$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->SetMargins(50, 80, 50);
$pdf->SetHeaderMargin(30);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
$pdf->setPrintFooter(false);

$pdf->AddPage();
$pdf->writeHTML($letterTxt, true, false, true, false, '');

$pdf->Output();
exit;
