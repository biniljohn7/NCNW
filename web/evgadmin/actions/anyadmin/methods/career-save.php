<?php
if (!$pix->canAccess('career')) {
    $pix->addmsg('Access denied!');
    $pix->redirect();
}

$_ = $_POST;

if (
    isset(
        $_['title'],
        $_['type'],
        $_['address'],
        $_['desc']
    )
) {
    $title = esc($_['title']);
    $type = esc($_['type']);
    $address = esc($_['address']);
    $desc = esc($_['desc']);
    $status = isset($_['status']) ? 'Y' : 'N';
    $cid = esc($_['cid'] ?? '');
    $new = !$cid;

    if (
        $title &&
        $type &&
        $address &&
        $desc
    ) {
        $data = false;
        if ($cid) {
            $data = $pixdb->get(
                'careers',
                [
                    'id' => $cid,
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
            $exCareertype = $pixdb->get('career_types', ['id' => $type, 'single' => 1]);
            $dbData = [
                'title' => $title,
                'type' => $type,
                'enabled' => $status,
                'address' => $address,
                'description' => $desc
            ];
            if ($exCareertype) {
                if ($new) {
                    $dbData['createdAt'] = $datetime;
                    $dbData['source'] = 'admin';
                    $iid = $pixdb->insert(
                        'careers',
                        $dbData
                    );
                } else {
                    $iid = $cid;
                    $pixdb->update(
                        'careers',
                        [
                            'id' => $iid
                        ],
                        $dbData
                    );
                }
                if ($iid) {
                    $pix->addmsg('Career saved', 1);
                    $pix->redirect('?page=career&sec=details&id=' . $iid);
                }
            } else {
                $pix->addmsg('Career type does not exist');
            }
        }
    }
}
