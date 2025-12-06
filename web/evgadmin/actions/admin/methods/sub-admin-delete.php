<?php
(function ($pix, $pixdb) {
    $_ = $_REQUEST;

    if (isset($_['id'])) {
        $id = esc($_['id']);

        if ($id) {
            $admin = $pixdb->getRow('admins', ['id' => $id], 'type');
            if ($admin) {
                $pixdb->delete('admins', ['id' => $id]);

                $pix->addmsg('Sub-admin removed !', 1);
                $pix->redirect('?page=sub-admins');
            }
        }
    }
    devMode();
    exit;
})($pix, $pixdb);
