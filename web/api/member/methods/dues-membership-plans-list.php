<?php
$r->status = 'ok';
$r->success = 1;
$plans =  $pixdb->get(
    'membership_plans',
    ['#SRT' => 'title asc'],
    'id as membershipPlanId,
    title as membershipPlanName,
    duration,
    addons,
    ttlCharge as membershipPlanCharge'
)->data;

$products = $pixdb->fetchAssoc(
    'products',
    [],
    'id,
    name,
    type,
    validity,
    amount',
    'id'
);

foreach ($plans as $plan) {
    if ($plan->duration == '1 year') {
        $plan->membershipPlanName .= ' - Renews Each Fiscal Year';
    }

    if ($plan->addons && is_array($plan->addons)) {
        foreach ($plan->addons as $adn) {
            if (isset($products[$adn])) {
                if ($products[$adn]->validity == 'fiscal-year') {
                    $plan->membershipPlanName .= ' + Yearly ' . $products[$adn]->name;
                }
            }
        }
    }
    unset($plan->addons, $plan->duration);
}

$others = [];
foreach ($products as $itm) {
    if ($itm->type == 'fee') {
        $itm->title = $itm->name;
        if ($itm->validity == 'fiscal-year') {
            $itm->title .= ' - Add Fee';
        }
        unset($itm->validity, $itm->name);
        $others[] = $itm;
    }
}
$r->data = (object)[
    'plans' => $plans,
    'others' => $others
];
