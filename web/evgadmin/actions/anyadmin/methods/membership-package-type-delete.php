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
            $pixdb->delete('membership_plans', ['type' => $id]);
            $pixdb->delete('membership_types', ['id' => $id]);

            $pix->addmsg('Type removed!', 1);
            $pix->redirect('?page=member-packages');
        }
    }
})($pix, $pixdb);
