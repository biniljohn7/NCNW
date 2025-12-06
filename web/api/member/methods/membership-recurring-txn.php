<?php
$pl = file_get_contents('php://input');
$pl = $pl ? json_decode($pl) : false;

$membershipsReq = [];
if (is_array($pl)) {
    foreach ($pl as $data) {
        if (isset(
            $data->id,
            $data->label,
            $data->amount
        )) {
            $id = esc($data->id);
            $amount = esc($data->amount);
            $membershipsReq[$id] = (object)[
                'id' => $id,
                'amount' => $data->amount
            ];
        }
    }
}
$res = $evg->rosterRenewalReq($membershipsReq, $lgUser->id, null, $lgUser);
if ($res->message == 'ok') {
    $r->success = 1;
    $r->status = 'ok';
    $r->data->paymentUrl = $res->paymentUrl;
}
