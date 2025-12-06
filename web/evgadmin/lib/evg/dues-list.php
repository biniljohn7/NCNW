<?php
global $date, $pixdb;

if (!is_array($memberIds)) {
    $memberIds = (array)$memberIds;
}

if ($memberIds) {
    $dues = [];
    //membership
    $mmshpCnd = [
        '#QRY' => 'h.expiry is not null and h.expiry < "' . $date . '"',
        'h.payStatus' => 'completed',
        '__QUERY__' => []
    ];
    $memberIdsStr = implode(',', $memberIds);
    if ($accountId) {
        $mmshpCnd['__QUERY__'][] = "(h.member in ($memberIdsStr) or h.giftedBy in ($memberIdsStr))";
    } else {
        $mmshpCnd['__QUERY__'][] = "h.member in ($memberIdsStr)";
    }

    $memberships = $pixdb->get(
        [
            ['memberships', 'h', 'planId'],
            ['membership_plans', 'p', 'id']
        ],
        $mmshpCnd,
        'h.id,
        h.member,
        h.giftedBy,
        h.payStatus,
        h.installment,
        h.instllmntPhase,
        h.accepted,
        h.amount,
        p.title as name,
        p.duration,
        p.active as activePlan,
        p.ttlCharge,
        p.duration'
    )->data;
    foreach ($memberships as $row) {
        $info = null;
        if ($row->activePlan == 'Y') {
            $eligible = true;
            $isGift = $accountId && $row->giftedBy == $accountId;
            if ($isGift) {
                $eligible = $row->accepted == 'Y';
            }
            if ($eligible) {
                if ($row->instllmntPhase < $row->installment) {
                    $info = [
                        'title' => $row->name . ($isGift ? '(gift)' : '') . ", Installment $row->instllmntPhase of $row->installment",
                        'amount' => $row->amount / $row->installment,
                        'id' => "mmbrshp$row->id"
                    ];
                } elseif ($row->payStatus == 'completed' && $row->duration) {
                    $info = [
                        'title' => $row->name . ($isGift ? ' (gift)' : '') . ($row->duration ? " (validity $row->duration)" : '') . ", Renewal",
                        'amount' => $row->amount,
                        'id' => "mmbrshp$row->id"
                    ];
                }
            }
        }
        if ($info) {
            if (!isset($dues[$row->member])) {
                $dues[$row->member] = [];
            }
            $dues[$row->member][] = (object)$info;
        }
    }

    $paidBenefits = $pixdb->get(
        [
            ['paid_benefits', 'b', 'payCategoryId'],
            ['products', 'p', 'id']
        ],
        [
            'b.members' => $memberIds,
            '#QRY' => 'b.expiry < "' . $date . '"'
        ],
        'b.id,
        b.members as member,
        p.name,
        p.amount,
        p.validity,
        p.enabled'
    )->data;
    foreach ($paidBenefits as $row) {
        if ($row->enabled == 'Y' && $row->validity) {
            if (!isset($dues[$row->member])) {
                $dues[$row->member] = [];
            }
            $dues[$row->member][] = (object)[
                'title' => $row->name . ($row->validity ? " (validity $row->validity)" : ''),
                'amount' => $row->amount,
                'id' => "paidbnfts$row->id"
            ];
        }
    }
    return $dues;
}
