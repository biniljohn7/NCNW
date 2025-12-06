<?php
if (!$pix->canAccess('location')) {
    $pix->addmsg('Access denied!');
    $pix->redirect();
}

$_ = $_POST;

if (
    isset(
        $_['name']
    )
) {
    $name = ucwords(escape($_['name']));
    $status = isset($_['status']) ? 'Y' : 'N';
    $afid = esc($_['afid'] ?? '');
    $new = !$afid;

    if ($name) {
        $data = false;
        if ($afid) {
            $data = $pixdb->get(
                'affiliates',
                [
                    'id' => $afid,
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
            $exAffiliate = $pixdb->getRow(
                'affiliates',
                ['name' => $name],
                'id'
            );
            if (
                $exAffiliate &&
                $exAffiliate->id == $afid
            ) {
                $exAffiliate = false;
            }
            if (!$exAffiliate) {
                $dbData = [
                    'name' => $name,
                    'enabled' => $status
                ];
                if ($new) {
                    $dbData['createdAt'] = $datetime;
                    $iid = $pixdb->insert(
                        'affiliates',
                        $dbData
                    );
                } else {
                    $iid = $afid;
                    $dbData['updatedAt'] = $datetime;
                    $pixdb->update(
                        'affiliates',
                        [
                            'id' => $iid
                        ],
                        $dbData
                    );
                }
                if ($iid) {
                    $pix->addmsg('Affiliate saved', 1);
                    $pix->redirect('?page=affiliates');
                }
            } else {
                $pix->addmsg('The affiliate already exist');
            }
        }
    }
}
