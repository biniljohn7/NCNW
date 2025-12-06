<?php
$inp = getJsonBody();
if (
    isset(
        $inp->membershipChargesId,
        $inp->membershipTypeId
    )
) {
    $membershipChargesId = $inp->membershipChargesId;
    $membershipTypeId = $inp->membershipTypeId;

    if (
        $membershipChargesId &&
        $membershipTypeId
    ) {
        $plan = $pixdb->get(
            'membership_plans',
            [
                'id' => $membershipChargesId,
                'single' => 1
            ],
            'id,
            type,
            title as chargesTitle,
            ttlLocChaptChrg as totalNationalCharges,
            ttlNatChaptChrg as totalChapterCharges,
            nationalDue as nationalDues,
            localDue as localDues,
            ttlCharge as totalCharges,
            natCapFee as nationalPerCapitalFee,
            natReinFee as nationalReinstatementFee,
            natLateFee as nationalLateFee'
        );
        if (
            $plan &&
            $plan->type == $membershipTypeId
        ) {
            $floatConv = [
                'totalNationalCharges',
                'totalChapterCharges',
                'nationalDues',
                'localDues',
                'totalCharges',
                'nationalPerCapitalFee',
                'nationalReinstatementFee',
                'nationalLateFee'
            ];
            foreach ($floatConv as $key) {
                $plan->{$key} = floatval($plan->{$key});
            }

            $r->status = 'ok';
            $r->success = 1;
            $r->data = $plan;
            $r->message = 'Plan Selected Successfully!';
        }
    }
}
