<?php
(function ($pix, $pixdb, $datetime) {
    $_ = $_POST;
    if (
        isset(
            $_['name'],
            $_['username'],
            $_['email'],
            $_['phone']
        )
    ) {
        $name = esc($_['name']);
        $username = esc($_['username']);
        $email = esc($_['email']);
        $phone = esc($_['phone']);
        $password = esc($_['password'] ?? '');
        $id = intval($_['id'] ?? '');
        $enable = isset($_['enable']);
        $new = !$id;

        if (
            $name &&
            is_mail($email)
        ) {
            if (
                !$pixdb->getRow(
                    'admins',
                    [
                        '#QRY' => "(
                        email=\"$email\"" .
                            (
                                $username ?
                                " OR username=\"$username\"" :
                                ''
                            ) . "
                    ) " .
                            ($id ? " AND id!=\"$id\"" : "")
                    ],
                    'id'
                )
            ) {
                $dbData = [
                    'type' => 'sub-admin',
                    'enabled' => $enable ? 'Y' : 'N',
                    'name' => ucwords($name),
                    'username' => $username ?: null,
                    'email' => $email,
                    'phone' => $phone ?: null,
                    'password' => $password ?
                        $pix->encrypt($password) :
                        null
                ];
                if ($new) {
                    $iid = $pixdb->insert('admins', $dbData);
                    // 
                    $memberCode = $pix->makeMemberId();
                    $ins = $pixdb->insert(
                        'members',
                        [
                            'email' => $dbData['email'],
                            'firstName'  => $dbData['name'],
                            'lastName' => '',
                            'memberId' => $memberCode,
                            'verified' => 'Y',
                            'password' => $dbData['password'],
                            'regOn' => $datetime,
                        ]
                    );
                    if ($ins) {
                        $pixdb->update(
                            'admins',
                            ['id' => $iid],
                            ['memberid' => $ins]
                        );
                    }
                } else {
                    $iid = $id;
                    if (!$password) {
                        unset($dbData['password']);
                    }
                    $pixdb->update(
                        'admins',
                        ['id' => $id],
                        $dbData
                    );
                }
                if ($iid) {
                    $pix->addmsg('Admin account ' . ($new ? 'created' : 'details modified'), 1);
                    $pix->redirect('?page=sub-admins&sec=details&id=' . $iid);
                }
            } else {
                $pix->addmsg('User already exist');
            }
        }
    }
})($pix, $pixdb, $datetime);
