<?php
(function ($pixdb, $pix) {
    $_ = $_REQUEST;
    if (isset($_['adminid'])) {
        $adminId = esc($_['adminid']);
        if ($adminId) {
            $admData = $pixdb->getRow('admins', ['id' => $adminId], 'name');
            if ($admData) {
                $pixdb->update(
                    'admins',
                    ['id' => $adminId],
                    ['type' => 'admin']
                );
                $pix->addmsg("Done! $admData->name is now a Super Admin.", 1);
                $pix->redirect('?page=sub-admins');
            }
        }
    }
})($pixdb, $pix);
