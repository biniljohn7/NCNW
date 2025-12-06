<?php
devMode();
$r->status = 'ok';
$r->success = 1;
$r->message = 'Data Retrieved Successfully!';


$cnds = [
    '#SRT' => 'date desc',
];
$paidByOthers = $pixdb->fetchAssoc(
    [
        ['transactions', 't', 'id'],
        ['txn_items', 'i', 'txnid']
    ],
    [
        'i.benefitTo' => $lgUser->id,
        't.status' => 'success'
    ],
    't.id,
    t.status,
    i.title,
    t.date as paidAt,
    i.amount,
    i.benefitTo',
    'id'
);

$txnids = array_keys($paidByOthers);
if ($txnids) {
    $cnds['#QRY'] = '(member=' . $lgUser->id . ' or id in (' . implode(',', $txnids) . '))';
} else {
    $cnds['member'] = $lgUser->id;
}

$allTxns = $pixdb->get(
    'transactions',
    $cnds,
    'id,
    member,
    status,
    title as chargesTitle,
    date as paidAt,
    amount as totalAmount'
)->data;

$memberIds = collectObjData($allTxns, 'member');
$memberIds = array_diff($memberIds, [$lgUser->id]);
$otherMembers = [];
if ($memberIds) {
    $otherMembers = $evg->getMemberInfo($memberIds, ['firstName', 'lastName', 'memberId', 'avatar'], ['city', 'zipcode']);
}

//
$giftedIds = [];
$gitedTxn = [];
$giftedByTxnId = [];
foreach ($allTxns as $row) {
    $giftedIds[] = $row->id;
}

if ($giftedIds) {
    $gitedTxn = $pixdb->get(
        'txn_items',
        ['#QRY' => '(txnid IN (' . implode(',', $giftedIds) . ') AND benefitTo != ' . $lgUser->id . ')']
    );
    foreach ($gitedTxn->data as $gTxn) {
        $giftedByTxnId[$gTxn->txnId][] = $gTxn;
    }
}
//

foreach ($allTxns as $idx => $itm) {
    if ($itm->member != $lgUser->id) {
        if (isset($otherMembers[$itm->member]) && $paidByOthers[$itm->id]) {
            $txnItem = $paidByOthers[$itm->id];
            $itm->chargesTitle = $txnItem->title ?? '';
            $itm->totalAmount = floatval($txnItem->amount);

            $paidBy = $otherMembers[$itm->member];
            $paidBy->avatar = $paidBy->avatar ? $evg->getAvatar($paidBy->avatar) : '';
            $itm->benefitTo = $paidBy;
        } else {
            unset($allTxns[$idx]);
        }
    } else {
        //
        if (isset($giftedByTxnId[$itm->id])) {
            $itm->giftedDetails = [];
            $membData = [];
            $membDataDetails = [];

            foreach ($giftedByTxnId[$itm->id] as $gTxn) {
                $membData[] = $gTxn->benefitTo;
            }
            if (!empty($membData)) {
                $membDataDetails = $evg->getMemberInfo($membData, ['firstName', 'lastName']);
            }

            foreach ($giftedByTxnId[$itm->id] as $gTxn) {
                $memberObj = $membDataDetails[$gTxn->benefitTo] ?? null;

                $itm->giftedDetails[] = [
                    'membership' => $gTxn->title,
                    'giftTo' => $memberObj ? ($memberObj->firstName . ' ' . $memberObj->lastName) : '',
                    'paidDate' => $itm->paidAt
                ];
            }
        }
        //
    }
    unset($itm->id, $itm->member);
}
$r->data = $allTxns;
// exit;