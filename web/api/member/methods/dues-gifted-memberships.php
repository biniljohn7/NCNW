<?php
devMode();

if (isset($_GET['id'])) {
    $id = esc($_GET['id']);

    if($id) {
        $giftedMemberships = $pixdb->get(
            'memberships',
            ['giftedBy' => $id]
        );

        $data = [];

        if($giftedMemberships->data) {
            $planIds = [];
            $membIds = [];

            foreach($giftedMemberships->data as $row) {
                if($row->planId) {
                    $planIds[] = $row->planId;
                }
                 if($row->giftedBy) {
                    $membIds[] = $row->member;
                }
            }

            $membDatas = $evg->getMembers($membIds, 'id, firstName, lastName');
            $planDatas = $evg->getMembershipPlans($planIds, 'id, duration');

            foreach($giftedMemberships->data as $row) {
                $duration = $planDatas[$row->planId]->duration ?? 'Lifelong';
                $firstName = $membDatas[$row->member]->firstName ?? '--';
                $lastName = $membDatas[$row->member]->lastName ?? '--';

                $data[] = [
                    'id' => $row->id,
                    'validity' => $duration,
                    'plans' => $row->planName,
                    'price' => $row->amount,
                    'date' => date('F j, Y', strtotime($row->created)),
                    'gifted' => $firstName . ' ' . $lastName,
                    'accepted' => $row->accepted 
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