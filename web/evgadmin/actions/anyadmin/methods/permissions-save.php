<?php
devMode();
(function ($pix, $pixdb, $evg) {
    $_ = $_POST;
    if (isset($_['adminType'])) {
        $adminType = esc($_['adminType']);
        $perms = isset($_['perms']) && is_array($_['perms']) ? $_['perms'] : [];
        $perms = array_filter(
            array_unique(
                array_map(
                    'esc',
                    $perms
                )
            )
        );

        $isEx = false;
        $isDone = false;
        if (
            $adminType &&
            ($adminType != 'admin' || $adminType != 'sub-admin') &&
            array_key_exists($adminType, $evg::permissions)
        ) {
            $isEx = $pixdb->getRow(
                'permissions',
                ['type' => $adminType]
            );

            $perms = implode(',', $perms) ?: null;
            $dbData = [
                'type' => $adminType,
                'perms' => $perms
            ];
            if (!$isEx) {
                $pixdb->insert(
                    'permissions',
                    $dbData
                );

                $isDone = true;
            } else {
                $pixdb->update(
                    'permissions',
                    ['type' => $adminType],
                    $dbData
                );

                $isDone = true;
            }

            if ($isDone) {
                $pixdb->update(
                    'admins',
                    ['type' => $adminType],
                    ['perms' => $perms]
                );

                $pix->addmsg('Permissions have been set.', 1);
            }
        } else {
            $pix->addmsg('Please select an admin type');
        }
    }
    // exit;
})($pix, $pixdb, $evg);
