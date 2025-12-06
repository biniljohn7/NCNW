<?php
$_ = $_REQUEST;

if (
    $pix->canAccess('statistics') &&
    isset(
        $_['type'],
        $_['month']
    )
) {
    $type = str2url($_['type']);
    $month = esc($_['month']);

    if (
        $type &&
        $month
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
        $gpIncFile = dirname(__FILE__) . '/dash-graph/' . $type . '.php';
        if (is_file($gpIncFile)) {
            include $gpIncFile;
        }
    }
}
// exit;
