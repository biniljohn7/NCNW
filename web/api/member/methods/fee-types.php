<?php
$r->status = 'ok';
$r->success = 1;
$feeTypes = [];
$plans =  $pixdb->get(
    'membership_plans',
    ['#SRT' => 'id asc'],
    'id,
    title,
    duration,
    addons,
    ttlCharge as amount'
)->data;
$addons = [];
foreach ($plans as $plan) {
    $adn = [];
    if ($plan->addons) {
        $adn = json_decode($plan->addons);
        if (is_array($adn)) {
            $addons = array_merge($addons, $adn);
        }
    }
    if (!is_array($adn)) {
        $adn = [];
    }
    $plan->addons = $adn;
}


$products = $pixdb->fetchAssoc(
    'products',
    ['type' => 'fee'],
    'id,
    name,
    validity,
    amount',
    'id'
);
foreach ($plans as $plan) {
    $plan->id = "mmbrshp$plan->id";
    if ($plan->duration == '1 year') {
        $plan->title .= ' - Renews Each Fiscal Year';
    }

    if ($plan->addons && is_array($plan->addons)) {
        foreach ($plan->addons as $adn) {
            if (isset($products[$adn])) {
                if ($products[$adn]->validity == 'fiscal-year') {
                    $plan->title .= ' + Yearly ' . $products[$adn]->name;
                }
            }
        }
    }
    unset($plan->addons, $plan->duration);
    $feeTypes[] = $plan;
}
foreach ($products as $itm) {
    $itm->id = "paidbnfts$itm->id";
    $itm->title = $itm->name;
    if ($itm->validity == 'fiscal-year') {
        $itm->title .= ' - Renews Each Fiscal Year';
    }
    unset($itm->validity, $itm->name);
    $feeTypes[] = $itm;
}
$r->data =  $feeTypes;
