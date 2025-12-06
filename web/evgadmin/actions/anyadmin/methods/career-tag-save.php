<?php
if (!$pix->canAccess('career')) {
    $pix->addmsg('Access denied!');
    $pix->redirect();
}

$_ = $_POST;

if (
    isset(
        $_['name']
    )
) {
    $name = esc($_['name']);
    $status = isset($_['status']) ? 'Y' : 'N';
    $tid = esc($_['tid'] ?? '');
    $new = !$tid;

    if (
        $name
    ) {
        $data = false;
        if ($tid) {
            $data = $pixdb->get(
                'career_types',
                [
                    'id' => $tid,
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
                'enabled' => $status
            ];
            if ($new) {
                $dbData['createdAt'] = $datetime;
                $iid = $pixdb->insert(
                    'career_types',
                    $dbData
                );
            } else {
                $iid = $tid;
                $pixdb->update(
                    'career_types',
                    [
                        'id' => $iid
                    ],
                    $dbData
                );
            }
            if ($iid) {
                $pix->addmsg('Career tag saved', 1);
                $pix->redirect('?page=cr-tags');
            }
        }
    }
}
exit;
