<?php
if (!$pix->canAccess('transactions')) {
    $pix->addmsg('Access denied!');
    $pix->redirect();
}
(function ($pix, $pixdb, $evg) {
    if (isset($_GET['trnid'])) {
        $tid = intval($_GET['trnid']);
        if ($tid) {
            $trData = $pixdb->get(
                'txn_items',
                [
                    'txnId' => $tid
                ]
            );
            $transDt = $pixdb->getRow(
                'transactions',
                ['id' => $tid],
                'id,date'
            );
            //
            $trnsDate = !empty($transDt->date) ? date('d/m/Y', strtotime($transDt->date)) : '';
            if ($trData) {
                $members = [];
                $memberInfo = [];
                $memberShips = [];
                $beneficiaries = collectObjData($trData->data, 'benefitTo');
                if ($beneficiaries) {
                    $members = $pixdb->fetchAssoc(
                        'members',
                        ['id' => $beneficiaries],
                        'id, firstName, lastName, email, memberId, regOn, verified,enabled',
                        'id'
                    );
                    $memberInfo = $pixdb->fetchAssoc(
                        'members_info',
                        ['member' => $beneficiaries],
                        'member, country, state, city, address, address2, zipcode, phone',
                        'member'
                    );
                    $memberShips = $pixdb->fetchAssoc(
                        'memberships',
                        [
                            'member' => $beneficiaries,
                            '#SRT' => 'id asc'
                        ],
                        'member,created,expiry',
                        'member'
                    );
                }

                $trasactionalAr = [];
                if (
                    !empty($members) &&
                    !empty($memberInfo) &&
                    !empty($memberShips)
                ) {
                    $i = 0;
                    foreach ($members as $ky => $mbr) {
                        $trasactionalAr[$ky]['firstName'] = $mbr->firstName ?? '';
                        $trasactionalAr[$ky]['lastName'] = $mbr->lastName ?? '';
                        $trasactionalAr[$ky]['memberId'] = $mbr->memberId ?? '';
                        $trasactionalAr[$ky]['email'] = $mbr->email ?? '';
                        $trasactionalAr[$ky]['regOn'] = !empty($mbr->regOn) ? date('d/m/Y', strtotime($mbr->regOn)) : '';
                        $trasactionalAr[$ky]['verified'] = ($mbr->verified == 'Y') ? 'Yes' : 'No';
                        $trasactionalAr[$ky]['membership'] = $trData->data[$i]->title ?? '';
                        $trasactionalAr[$ky]['status'] = ($mbr->enabled == 'Y') ? 'Active' : 'Inactive';
                        $trasactionalAr[$ky]['validity'] = $memberShips[$ky]->expiry ?? '';
                        $trasactionalAr[$ky]['lastpaid'] = $trnsDate;
                        $trasactionalAr[$ky]['country'] = $memberInfo[$ky]->country ? $evg->getNation($memberInfo[$ky]->country)->name : '';
                        $trasactionalAr[$ky]['state'] = $memberInfo[$ky]->state ? $evg->getState($memberInfo[$ky]->state)->name : '';
                        $trasactionalAr[$ky]['city'] = $memberInfo[$ky]->city ?? '';
                        $trasactionalAr[$ky]['address'] = $memberInfo[$ky]->address ?? '';
                        $trasactionalAr[$ky]['zipcode'] = $memberInfo[$ky]->zipcode ?? '';
                        $trasactionalAr[$ky]['phone'] = $memberInfo[$ky]->phone ?? '';
                        $i++;
                    }
                }
                $data = [
                    [
                        'First Name',
                        'Last Name',
                        'Member ID',
                        'Email',
                        'Registred On',
                        'Verified',
                        'Membership',
                        'Status',
                        'Validity',
                        'Last Paid',
                        'Country',
                        'State',
                        'City',
                        'Address',
                        'Zip Code',
                        'Phone'

                    ]
                ];
                $data = array_merge($data, $trasactionalAr);

                $filename = 'POS_Transactions_Export_' . date('Y_m(F)_d') . '-' . time() . '.csv';

                header('Content-Type: text/csv');
                header('Content-Disposition: attachment;filename=' . $filename);

                $output = fopen('php://output', 'w');
                foreach ($data as $row) {
                    fputcsv($output, $row);
                }
                fclose($output);
                $pix->remsg();
            }
        }
    }
    exit;
})($pix, $pixdb, $evg);
