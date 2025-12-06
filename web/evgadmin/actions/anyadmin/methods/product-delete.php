<?php
if (!$pix->canAccess('paid-plans')) {
    $pix->addmsg('Access denied!');
    $pix->redirect();
}

(function ($pix, $pixdb) {
    $_ = $_REQUEST;

    if (isset($_['id'])) {
        $id = esc($_['id']);

        if ($id) {
            $pixdb->delete('products', ['id' => $id]);
            $pix->addmsg('product removed!', 1);
        }
    }
})($pix, $pixdb);
