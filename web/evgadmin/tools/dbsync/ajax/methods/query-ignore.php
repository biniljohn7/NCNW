<?php
$_ = $_POST;
if (isset(
    $_['sql']
)) {
    $sql = str2url($_['sql']);

    if (
        $sql
    ) {
        $sqlFile = $pix->basedir . 'queries/' . $sql . '.sql';
        if (is_file($sqlFile)) {
            $query = file_get_contents($sqlFile);
            if ($query) {
                $r->status = 'ok';
                $pix->markSync($sql);
            }
        }
    }
}
