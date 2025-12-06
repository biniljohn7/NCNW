<?php
include_once '../lib/lib.php';

$qDir = $pix->datas . 'queue/';

if (is_dir($qDir)) {
    $trgtFile = array_diff(
        scandir($qDir),
        array('.', '..', 'working')
    );
    if (
        $trgtFile
    ) {

        include_once $pix->basedir . 'lib/run-queue.php';
    }
}
