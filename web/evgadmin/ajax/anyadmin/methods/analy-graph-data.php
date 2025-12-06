<?php
$_ = $_REQUEST;

if (
    $pix->canAccess('statistics') &&
    isset(
        $_['type'],
        $_['month'],
        $_['id']
    )
) {
    $type = esc($_['type']);
    $month = esc($_['month']);
    $id = esc($_['id']);

    if (
        $type &&
        $month &&
        $id
    ) {
        $monthTime = strtotime($month . '-01');
        $numDays = intval(
            date('t', $monthTime)
        );

        $r->status = 'ok';
        $r->data = array();

        for ($i = 1; $i <= $numDays; $i++) {
            $r->data[$i] = 0;
        }
        $cnd = [
            '#QRY' => '`member` IN (SELECT `member` FROM members_info WHERE `nationId` = ' . $id .  ') and date >= "' . $month . '-01' . ' 00:00:00" and date <= "' . $month . '-' . $numDays . ' 23:59:00" GROUP BY DATE(date)'
        ];

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
}
// exit;
