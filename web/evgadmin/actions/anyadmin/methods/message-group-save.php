<?php
if (!$pix->canAccess('messages')) {
    $pix->addmsg('Access denied!');
    $pix->redirect();
}

$_ = $_POST;

if (
    isset(
        $_['groupName']
    )
) {
    $groupName = esc($_['groupName']);
    $id = esc($_['id'] ?? '');
    $new = !$id;

    if (
        $groupName
    ) {

        $filter = [
            'title' => $groupName,
            'single' => 1
        ];
        if (!$new) {
            $filter['#QRY'] = 'id != ' . $id;
        }

        $data = $pixdb->get(
            'message_groups',
            $filter
        );

        if (!$data) {
            $dbData = [
                'title' => $groupName,
            ];

            if ($new) {
                $dbData['createdBy'] = $lgUser->id;
                $dbData['createdOn'] = $datetime;
                $iid = $pixdb->insert(
                    'message_groups',
                    $dbData
                );
            } else {
                $iid = $id;
                $pixdb->update(
                    'message_groups',
                    [
                        'id' => $iid
                    ],
                    $dbData
                );
            }
            if ($iid) {
                $pix->addmsg('Group name saved', 1);
                $pix->redirect('?page=groups' . (!$new ? ('&sec=group-view&id=' . $id) : ''));
            }
        } else {
            $pix->addmsg('The group name already exist');
        }
    }
}
