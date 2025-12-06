<?php
if (!$pix->canAccess('benefit')) {
    $pix->addmsg('Access denied!');
    $pix->redirect();
}

$_ = $_POST;

if (
    isset(
        $_['name'],
        $_['scope'],
        $_['code'],
        $_['discount'],
        $_['shortDesc'],
        $_['desc']
    )
) {
    $name = esc($_['name']);
    $category = esc($_['category']);
    $provider = esc($_['provider']);
    $scope = esc($_['scope']);
    $code = esc($_['code']);
    $discount = min(100, max(0, intval($_['discount'])));
    $shortDesc = esc($_['shortDesc']);
    $desc = esc($_['desc']);
    $status = isset($_['status']);

    $bid = esc($_['bid'] ?? '');
    $new = !$bid;

    if (
        $name &&
        (
            $scope == 'national' ||
            $scope == 'regional' ||
            $scope == 'state' ||
            $scope == 'chapter'
        ) &&
        $code &&
        $shortDesc &&
        $desc
    ) {
        $data = false;
        if ($bid) {
            $data = $pixdb->get(
                'benefits',
                [
                    'id' => $bid,
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
                'ctryId' => $category == '' ? null : $category,
                'provider' => $provider == '' ? null : $provider,
                'name' => $name,
                'scope' => $scope,
                'code' => $code,
                'discount' => $discount,
                'shortDescr' => $shortDesc,
                'descr' => $desc,
                'status' => $status ? 'active' : 'inactive'
            ];
            if ($new) {
                $iid = $pixdb->insert(
                    'benefits',
                    $dbData
                );
            } else {
                $iid = $bid;
                $pixdb->update(
                    'benefits',
                    [
                        'id' => $bid
                    ],
                    $dbData
                );
            }
            if ($iid) {
                $pix->addmsg('Benefit details saved', 1);
                $pix->redirect('?page=benefits&sec=details&id=' . $iid);
            }
        }
    }
}
exit;
