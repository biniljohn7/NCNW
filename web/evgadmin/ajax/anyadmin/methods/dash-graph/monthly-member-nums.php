<?php
$fetch = true;
$cnd = [
    '#QRY' => 'regOn >= "' . $month . '-01' . ' 00:00:00" and regOn <= "' . $month . '-' . $numDays . ' 23:59:00" GROUP BY DATE(regOn)'
];
if ($fetch) {
    $members = $pixdb->get(
        'members',
        $cnd,
        'COUNT(1) as total,
        regOn'
    )->data;

    $cntByDay = array();
    foreach ($members as $memb) {
        $membDate = date('Y-m-d', strtotime($memb->regOn));
        $cntByDay[$membDate] = $memb->total;
    }

    for ($i = 1; $i <= $numDays; $i++) {
        $day = date('Y-m-d', strtotime($month . '-' . $i));
        $r->data[$i] = isset($cntByDay[$day]) ? $cntByDay[$day] : 0;
    }
}
