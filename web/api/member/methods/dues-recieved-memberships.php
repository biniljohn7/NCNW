<?php
devMode();

if (isset($_GET['id'])) {
    $id = esc($_GET['id']);

    if($id) {
        $giftMemberships = $pixdb->get(
            'memberships',
            [
                '#QRY' => "member = $id AND giftedBy IS NOT NULL"
            ]
        );

        $data = [];
        
        if($giftMemberships->data) {
            $membIds = [];
            $planIds = [];

            foreach($giftMemberships->data as $row) {
                if($row->giftedBy) {
                    $membIds[] = $row->giftedBy;
                }
                if($row->planId) {
                    $planIds[] = $row->planId;
                }
            }
            $membDatas = $evg->getMembers($membIds, 'id, firstName, lastName');
            $planDatas = $evg->getMembershipPlans($planIds, 'id, duration');

            foreach($giftMemberships->data as $row) {
                $firstName = $membDatas[$row->giftedBy]->firstName ?? '--';
                $lastName = $membDatas[$row->giftedBy]->lastName ?? '--';
                $duration = $planDatas[$row->planId]->duration ?? 'Lifelong';

                $data[] = [
                    'id' => $row->id,
                    'validity' => $duration,
                    'gifter' => $firstName . ' ' . $lastName,
                    'price' => $row->amount,
                    'plans' => $row->planName,
                    'have' => $row->accepted == null ? true : false,
                    'date' => date('F j, Y', strtotime($row->created)),
                    'accepted' => $row->accepted,
                    'instalment' => $row->payStatus == 'pending' ? true : false
                ];                
            }

        }

        $r->status = 'ok';
        $r->success = 1;
        $r->data = $data;
        $r->message = 'Data Shown Successfully!';
        var_dump($r);
    }
}
// exit;
?>