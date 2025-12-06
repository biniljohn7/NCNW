<?php
if (!$pix->canAccess('paid-plans')) {
    $pix->addmsg('Access denied!');
    $pix->redirect();
}

(function ($pix, $pixdb) {
    $_ = $_POST;

    if (isset($_['name'])) {
        $name = esc($_['name']);
        $visibility = isset($_['visibility']);
        $id = esc($_['id'] ?? '');
        $new = !$id;

        if ($name) {
            $dbData = [
                'name' => $name,
                'active' => $visibility ? 'Y' : 'N',
            ];
            if ($new) {
                $iid = $pixdb->insert(
                    'membership_types',
                    $dbData
                );
            } else {
                $iid = $id;
                $pixdb->update(
                    'membership_types',
                    ['id' => $iid],
                    $dbData
                );
            }
            if ($iid) {
                $pix->addmsg('Membership type saved', 1);
                $pix->redirect('?page=member-packages&sec=details&id=' . $iid);
            }
        }
    }
})($pix, $pixdb);
