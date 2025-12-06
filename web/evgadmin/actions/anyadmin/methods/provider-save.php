<?php
if (!$pix->canAccess('benefit')) {
    $pix->addmsg('Access denied!');
    $pix->redirect();
}

$_ = $_POST;

if (
    isset(
        $_['name'],
        $_['email'],
        $_['cntryCode'],
        $_['phone'],
        $_['website'],
        $_['address']
    )
) {
    $pid = esc($_['pid'] ?? '');
    $name = esc($_['name']);
    $email = esc($_['email']);
    $cntryCode = esc($_['cntryCode']);
    $phone = esc($_['phone']);
    $website = esc($_['website']);
    $address = esc($_['address']);
    $status = isset($_['status']);
    $new = !$pid;

    if (
        $name &&
        is_mail($email) &&
        $cntryCode &&
        $phone &&
        $website &&
        $address
    ) {
        $data = false;
        if ($pid) {
            $data = $pixdb->get(
                'benefit_providers',
                [
                    'id' => $pid,
                    'single' => 1
                ],
                'id'
            );
        }
        if (
            $new ||
            (
                !$new &&
                $data
            )
        ) {
            $dbData = [
                'name' => $name,
                'address' => $address,
                'website' => $website,
                'email' => $email,
                'cntryCode' => $cntryCode,
                'phone' => substr($phone, 0, 20),
                'status' => $status ? 'active' : 'inactive'
            ];
            if ($new) {
                $iid = $pixdb->insert(
                    'benefit_providers',
                    $dbData
                );
            } else {
                $iid = $pid;
                $pixdb->update(
                    'benefit_providers',
                    [
                        'id' => $pid,
                    ],
                    $dbData
                );
            }
            if ($iid) {
                $pix->addmsg('Benefit provider saved', 1);
                $pix->redirect('?page=provider&sec=details&id=' . $iid);
            }
        }
    }
}
