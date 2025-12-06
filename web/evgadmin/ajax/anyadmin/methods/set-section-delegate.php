<?php
$_ = $_REQUEST;
if ($pix->canAccess('elect')) {
    if (isset(
        $_['mId'],
        $_['action'],
        $_['chapter']
    )) {
        $mid = intval($_['mId']);
        $action = esc($_['action']);
        $chapter = intval($_['chapter']);

        if (
            $mid &&
            $action &&
            $chapter
        ) {
            $delegates = $pixdb->get(
                'section_leaders',
                [
                    'secId' => $chapter,
                    'type' => 'delegate'
                ]
            );
            //
            $userCheck = $pixdb->getRow(
                'section_leaders',
                [
                    'secId' => $chapter,
                    'mbrId' => $mid,
                    'type' => 'delegate'
                ]
            );
            $roles = $pixdb->getRow(
                'members',
                ['id' => $mid],
                'role'
            );
            $rolesAr = array_filter(explode(',', $roles->role ?? ''));
            ////
            if (!$userCheck && $action == 'ADD') {
                if (count($delegates->data) < 3) {
                    $pixdb->insert(
                        'section_leaders',
                        [
                            'mbrId' => $mid,
                            'secId' => $chapter,
                            'type' => 'delegate'
                        ]
                    );
                    if (!in_array('section-delegate', $rolesAr)) {
                        $rolesAr[] = 'section-delegate';
                    }
                    $pixdb->update(
                        'members',
                        ['id' => $mid],
                        ['role' => !empty($rolesAr) ? implode(',', $rolesAr) : NULL]
                    );
                    $r->status = 'ok';
                    $r->msg = "Delegate added";
                } else {
                    $r->msg = "Already chose 3 delegates";
                }
            } else if ($userCheck && $action == 'ADD') {
                $r->msg = "Already chosen as a delegate";
            } else if ($userCheck && $action == 'REMOVE') {
                $pixdb->delete(
                    'section_leaders',
                    [
                        'mbrId' => $mid,
                        'secId' => $chapter,
                        'type' => 'delegate'
                    ]
                );
                if (in_array('section-delegate', $rolesAr)) {
                    $pos = array_search('section-delegate', $rolesAr);
                    if ($pos !== false) {
                        unset($rolesAr[$pos]);
                    }
                    $rolesAr = array_values($rolesAr);
                }
                $pixdb->update(
                    'members',
                    ['id' => $mid],
                    ['role' => !empty($rolesAr) ? implode(',', $rolesAr) : NULL]
                );
                $r->status = 'ok';
                $r->msg = "Delegate removed";
            } else {
                $r->msg = "Not set as a delegate";
            }
        }
    }
} else {
    $r->msg = 'Access Denied!';
}
