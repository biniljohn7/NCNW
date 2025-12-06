<?php
$expFiles = $pixdb->getCol(
    'tmp_files',
    ['exp__l' => $datetime],
    'path'
);
foreach ($expFiles as $fl) {
    $pix->removeFile(BASEDIR . $fl);
}
if ($expFiles) {
    $pixdb->delete(
        'tmp_files',
        ['exp__l' => $datetime]
    );
}
