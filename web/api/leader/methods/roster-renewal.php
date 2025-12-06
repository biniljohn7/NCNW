<?php
$pl = getJsonBody();
$membershipsReq = [];
if (is_array($pl->ids)) {
    foreach ($pl->ids as $id) {
        if ($id) {
            $membershipsReq[$id] = (object)[
                'id' => $id
            ];
        }
    }
}
$leaderInfo = $evg->getLeaderCircles($lgUser->id, $userRoles);
$leaderInfo->id = $lgUser->id;
$res = $evg->rosterRenewalReq($membershipsReq, $lgUser->id, $leaderInfo, $lgUser);
if ($res->message == 'ok') {
    $r->success = 1;
    $r->status = 'ok';
    $r->data->paymentUrl = $res->paymentUrl;
}
