<?php
(function (&$r) {
    if (isset($_FILES['file'])) {
        $file = $_FILES['file'];
        if ($file['tmp_name'] ?? 0) {

            $r->status = 'ok';
            $r->records = [];

            if (($handle = fopen($file['tmp_name'], 'r')) !== FALSE) {
                while (($row = fgetcsv($handle)) !== FALSE) {
                    $r->records[] = $row;
                }
                fclose($handle);
            }
        }
    }
})($r);
