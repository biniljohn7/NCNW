<?php
$_ = $_REQUEST;
if ($pix->canAccess('members-mod')) {
    if (isset(
        $_['membId'],
        $_['shpdDate']
    )) {
        $mid = intval($_['membId']);
        $shpdDate = esc($_['shpdDate']);
        $membership = $pixdb->getRow(
            'memberships',
            [
                'member' => $mid,
                '#QRY' => '((giftedBy IS NOT NULL AND accepted = "Y") OR giftedBy IS NULL)',
                '#SRT' => 'id desc'
            ],
            'id,
            member,
            planId,
            pinStatus'
        );
        if (
            $membership &&
            ($membership->planId == 2 || $membership->planId == 3) &&
            $membership->pinStatus
        ) {
            if ($membership->pinStatus != 'shipped') {
                $sdate = date('Y-m-d h:i:s', strtotime($shpdDate));
                $pixdb->update(
                    'memberships',
                    ['id' => $membership->id],
                    [
                        'pinStatus' => 'shipped',
                        'shpdOn' => $sdate,
                    ]
                );
                $r->status = 'ok';
                $r->msg = 'Status Changed';
                $r->shpdOn = date('d-M-Y h:i:s A', strtotime($sdate));
            } else {
                $r->msg = 'Status already set to Shipped';
            }
        } else {
            $r->msg = 'Invalid Membership Plan or Pin Status!';
        }
    }
} else {
    $r->msg = 'Access Denied!';
}
