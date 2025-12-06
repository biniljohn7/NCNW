<?php
if (!$pix->canAccess('location')) {
    $pix->addmsg('Access denied!');
    $pix->redirect();
}

$_ = $_POST;

if (
    isset(
        $_['name'],
        $_['nation'],
        $_['region']
    )
) {
    $name = ucwords(esc($_['name']));
    $nation = esc($_['nation']);
    $region = esc($_['region']);
    $status = isset($_['status']) ? 'Y' : 'N';
    $sid = esc($_['sid'] ?? '');
    $new =  !$sid;

    if (
        $name &&
        $nation &&
        $region
    ) {
        $data = false;
        if ($sid) {
            $data = $pixdb->get(
                'states',
                [
                    'id' => $sid,
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
            if (
                $pixdb->getRow('regions', ['id' => $region], 'id') &&
                $pixdb->getRow('nations', ['id' => $nation], 'id')
            ) {
                $exState = $pixdb->getRow(
                    'states',
                    [
                        'name' => $name, 
                        'region' => $region
                    ],
                    'id'
                );
                if (
                    $exState &&
                    $exState->id == $sid
                ) {
                    $exState = false;
                }
                if (!$exState) {
                    $dbData = [
                        'nation' => $nation,
                        'region' => $region,
                        'name' => $name,
                        'enabled' => $status
                    ];
                    if ($new) {
                        $dbData['createdAt'] = $datetime;
                        $iid = $pixdb->insert(
                            'states',
                            $dbData
                        );
                    } else {
                        $iid = $sid;
                        $dbData['updatedAt'] = $datetime;
                        $pixdb->update(
                            'states',
                            [
                                'id' => $iid
                            ],
                            $dbData
                        );
                    }
                    if ($iid) {
                        $pix->addmsg('State saved', 1);
                        $pix->redirect('?page=state');
                    }
                } else {
                    $pix->addmsg('State already exist');
                }
            } else {
                $pix->addmsg('Region or Nation does not exist');
            }
        }
    }
}
