<?php
$fetch = true;
$cnd = [
    '#QRY' => 'date >= "' . $month . '-01' . ' 00:00:00" and date <= "' . $month . '-' . $numDays . ' 23:59:00" GROUP BY DATE(date)'
];
if ($fetch) {
    $txns = $pixdb->get(
        'transactions',
        $cnd,
        'SUM(amount) as total,
        date'
    )->data;
    $revnuByDay = array();
    foreach ($txns as $txn) {
        $date = date('Y-m-d', strtotime($txn->date));
        $revnuByDay[$date] = $txn->total;
    }

    for ($i = 1; $i <= $numDays; $i++) {
        $day = date('Y-m-d', strtotime($month . '-' . $i));
        $r->data[$i] = isset($revnuByDay[$day]) ? $revnuByDay[$day] : 0;
    }
}
