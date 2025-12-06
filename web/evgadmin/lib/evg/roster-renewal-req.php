<?php
global $pixdb, $date;

if (is_array($list)) {
    $mmbrshpIds = [];
    $paidBenefitIds = [];
    $err = null;
    $res = (object)['message' => $err];

    // $showAmount = 0;
    foreach ($list as $req) {
        // $showAmount += $req->amount;
        if (preg_match('/^([a-zA-Z]+)(\d+)$/', $req->id, $matches)) {
            $prefix = $matches[1];
            $id = $matches[2];

            if ($prefix && $id) {
                if ($prefix == 'mmbrshp') {
                    $mmbrshpIds[] = $id;
                } elseif ($prefix == 'paidbnfts') {
                    $paidBenefitIds[] = $id;
                } else {
                    $err = 'invalid Request';
                    break;
                }
            } else {
                $err = 'invalid Request';
                break;
            }
        } else {
            $err = 'invalid Request';
            break;
        }
    }

    if (!$err) {
        $memberships = [];
        $paidBenefits = [];
        $validReq = true;
        $mmbrIds = [];
        $mmberInfo = [];

        if ($mmbrshpIds) {
            $memberships = $pixdb->get(
                [
                    ['memberships', 'm', 'planId'],
                    ['membership_plans', 'p', 'id']
                ],
                [
                    'm.id' => $mmbrshpIds,
                    'p.active' => 'Y'
                ],
                'm.*,
                p.code'
            )->data;
            $mmbrIds = array_merge($mmbrIds, collectObjData($memberships, 'member'));
        }
        if ($paidBenefitIds) {
            $paidBenefits = $pixdb->get(
                [
                    ['paid_benefits', 'b', 'payCategoryId'],
                    ['products', 'p', 'id']
                ],
                ['b.id' => $paidBenefitIds],
                'b.*,
                p.code'
            )->data;
            $mmbrIds = array_merge($mmbrIds, collectObjData($memberships, 'members'));
        }

        if ($mmbrIds) {
            $mmberInfo = $this->getMemberInfo($mmbrIds, [], ['cruntChptr', 'collegiateSection']);
        }

        foreach ($memberships as $row) {
            if (isset($mmberInfo[$row->member])) {
                $validReq = $row->expiry && $date > $row->expiry;
                if ($accountId) {
                    $validReq = $accountId == $row->member || $accountId == $row->giftedBy;
                } elseif ($leader && $this->isMemberLeader($mmberInfo[$row->member], $leader)) {
                    $validReq = false;
                    break;
                } else {
                    $validReq = false;
                    break;
                }
            } else {
                $validReq = false;
                break;
            }
        }

        foreach ($paidBenefits as $row) {
            if (isset($mmberInfo[$row->members])) {
                $validReq = $row->expiry && $date > $row->expiry;
                if ($accountId) {
                    $validReq = $accountId == $row->member;
                } elseif ($leader && $this->isMemberLeader($mmberInfo[$row->member], $leader)) {
                    $validReq = false;
                    break;
                } else {
                    $validReq = false;
                    break;
                }
            } else {
                $validReq = false;
                break;
            }
        }

        if (!$validReq) {
            $err = 'invalid Request';
            $res->message = $err;
            return $res;
        }
    } else {
        $err = 'invalid Request';
        $res->message = $err;
        return $res;
    }

    if (!$err) {
        $payload = [];
        $payAmount = 0;
        foreach ($memberships as $row) {
            $details = (object)[
                'target' => 'membership',
                'targetId' => $row->id
            ];
            if ($row->installment && $row->instllmntPhase < $row->installment) {
                $amount = $row->amount / $row->installment;
            } else {
                $amount = $row->amount;
            }
            $payload[] = (object)[
                'code' => $row->code,
                'details' => $details,
                'sectionId' => $row->section,
                'affiliateId' => $row->affiliate,
                'amount' => $amount
            ];
            $payAmount += $amount;
        }
        foreach ($paidBenefits as $row) {
            $details = (object)[
                'target' => 'paid_benefits',
                'targetId' => $row->id
            ];

            $member = $mmberInfo[$row->members];
            $payload[] = (object)[
                'code' => $row->code,
                'details' => $details,
                'sectionId' => $member->cruntChptr,
                'affiliateId' => $member->affilateOrgzn,
                'amount' => $row->amount
            ];
            $payAmount += $amount;
        }

        $stripeItms = [];
        foreach ($payload as $idx => $itm) {
            $stripeItms[] = [
                'name' => $itm->code,
                'amount' => $this->dollarInCents($itm->amount),
                'quantity' => 1,
                'item_id' => "item$idx"
            ];
        }

        $txnData = [
            'type' => 'renewal',
            'member' => $leader ? $leader->id : $accountId,
            'amount' => $payAmount,
            'title' => 'Renewal',
            'items' => $payload
        ];

        $nTxnId = $this->createTxn($txnData);

        if ($nTxnId) {
            if ($checkout) {
                // process payment from here
                $stripeSession = loadModule('stripe')->createCheckoutSession($stripeItms, $nTxnId, $stripeUser);

                if (is_object($stripeSession) && $stripeSession->url) {
                    $res->message = 'ok';
                    $res->paymentUrl = $stripeSession->url;
                } else {
                    $res->message = 'Oops! some error has been occured. Please, try again.';
                }
            } else {
                return $nTxnId;
            }
        }
    }

    return $res;
}
