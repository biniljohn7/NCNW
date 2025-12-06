<?php
if (isset($_GET['type'])) {
    $type = esc($_GET['type']);
    if ($type) {
        $plans = $pixdb->get(
            'membership_plans',
            ['type' => $type],
            'id as membershipChargesId,
            title as chargesTitle,
            ttlCharge as totalCharges'
        );
        $r->status = 'ok';
        $r->success = 1;
        $r->data = $plans->data;
        $r->message = 'Data Shown Successfully!';
    }
}
