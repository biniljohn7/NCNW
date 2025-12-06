<?php
if (!$pix->canAccess('cms-pages')) {
    $pix->addmsg('Access denied!');
    $pix->redirect();
}

$_ = $_POST;

if (
    isset(
        $_['cmsName'],
        $_['cmsContent']
    )
) {
    $cmsName = esc($_['cmsName']);
    $cmsContent = $_['cmsContent'];
    $status = isset($_['status']);
    $cid = esc($_['cid'] ?? '');
    $new = !$cid;

    if ($cmsName) {

        $cmsContent = esc(urldecode($cmsContent));

        $cData = false;
        if ($cid) {
            $cData = $pixdb->getRow(
                'cms',
                [
                    'id' => $cid
                ],
                'id'
            );
        }
        if (
            $new ||
            (
                !$new &&
                $cData
            )
        ) {
            $dbData = [
                'cmsName' => $cmsName,
                'cmsContent' => $cmsContent,
                'enabled' => $status ? 'Y' : 'N'
            ];
            if ($new) {
                $dbData['createdAt'] = $datetime;
                $iid = $pixdb->insert(
                    'cms',
                    $dbData
                );
            } else {
                $iid = $cid;
                $dbData['updatedAt'] = $datetime;
                $pixdb->update(
                    'cms',
                    ['id' => $iid],
                    $dbData
                );
            }
            if ($iid) {
                $pix->addmsg('CMS saved', 1);
                $pix->redirect('?page=cms&sec=details&id=' . $iid);
            }
        }
    }
}
exit;
