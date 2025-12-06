<?php
(function (
    &$r,
    $pix,
    $pixdb,
    $datetime,
    $evg
) {
    $_ = $_POST;

    if (
        $pix->canAccess('members') &&
        isset($_['records'])
    ) {
        $records = $_['records'];

        if (
            $records &&
            ($records = json_decode($records)) &&
            isset($records->data) &&
            $records->data
        ) {
            if (!is_array($records->data)) {
                $records->data = [];
            }

            list(
                $fName,
                $lName,
                $memberId,
                $email,
                $verified,
                $refCode,
                $pointBalance,
                $ttlRef,
                $country,
                $state,
                $city,
                $address,
                $zipCode,
                $phone,
                $certificates,
                $occupation,
                $industry,
                $leadershipRole,
                $nation,
                $region,
                $orgState,
                $sectionOfInitiation,
                $biography
            ) = $records->data;

            $fName = ucwords(esc($fName));
            $lName = ucwords(esc($lName));
            $memberId = esc($memberId);
            $email = esc($email);
            $verified = esc($verified);
            $refCode = esc($refCode);
            $pointBalance = esc($pointBalance);
            $ttlRef = esc($ttlRef);
            $country = esc($country);
            $state = esc($state);
            $city = esc($city);
            $address = esc($address);
            $zipCode = esc($zipCode);
            $phone = esc($phone);
            $occupation = esc($occupation);
            $industry = esc($industry);
            $leadershipRole = esc($leadershipRole);
            $nation = esc($nation);
            $region = esc($region);
            $orgState = esc($orgState);
            $sectionOfInitiation = esc($sectionOfInitiation);
            $biography = esc($biography);

            $memberData = [
                'firstName' => substr($fName, 0, 60),
                'lastName' => substr($lName, 0, 60),
                'memberId' => $memberId ?: null,
                'regOn' => $datetime,
                'verified' => strtolower($verified) == 'yes' ? 'Y' : 'N'
            ];

            if (
                $exMemb = $pixdb->getRow(
                    'members',
                    ['email' => $email],
                    'id'
                )
            ) {
                $membId = $exMemb->id;
                $pixdb->update(
                    'members',
                    ['id' => $membId],
                    $memberData
                );
            } else {
                $memberData['email'] = $email;
                $membId = $pixdb->insert(
                    'members',
                    $memberData
                );
            }

            if ($membId) {

                // update member info details

                if (!$pixdb->getRow(
                    'members_info',
                    ['refcode' => $refCode],
                    'refcode'
                )) {
                    $r->status = 'ok';

                    $country = ucwords(trim($country));
                    $countryId = $evg->getNationId($country);

                    $nation = ucwords(trim($nation));
                    $nationId = $evg->getNationId($nation);

                    $region = ucwords(trim($region));
                    $regionId = $evg->getRegionId(
                        $region,
                        $nationId
                    );

                    $state = ucwords(trim($state));
                    $stateId = $evg->getStateId(
                        $state,
                        $regionId,
                        $nationId ?: null
                    );

                    $orgState = ucwords(trim($orgState));
                    $orgznSteId = $evg->getStateId(
                        $orgState,
                        $regionId,
                        $nationId ?: null
                    );

                    $occupation = ucwords(trim($occupation));
                    $ocpnId = $evg->getOccupationId($occupation);

                    $industry = ucwords(trim($industry));
                    $indstryId = $evg->getIndustryId($industry);

                    $sectionOfInitiation = ucwords(trim($sectionOfInitiation));
                    $chptrOfInitn = $evg->getChapterId(
                        $sectionOfInitiation,
                        $nationId ?: null,
                        $regionId ?: null,
                        $orgznSteId
                    );

                    $membInfoData = [
                        'member' => $membId,
                        'refcode' => substr($refCode, 0, 8) ?: null,
                        'pointsBalance' => $pointBalance ?: 0,
                        'referralsTotal' => $ttlRef ?: null,
                        'country' => $countryId ?: null,
                        'state' => $stateId ?: null,
                        'city' => $city ?: null,
                        'address' => substr($address, 0, 100) ?: null,
                        'zipcode' => substr($zipCode, 0, 20) ?: null,
                        'phone' =>  substr($phone, 0, 20) ?: null,
                        'ocpnId' => $ocpnId ?: null,
                        'indstryId' => $indstryId ?: null,
                        'ldrShipRole' => substr($leadershipRole, 0, 50) ?: null,
                        'nationId' => $nationId ?: null,
                        'regionId' => $regionId ?: null,
                        'orgznSteId' => $orgznSteId ?: null,
                        'chptrOfInitn' => $chptrOfInitn ?: null,
                        'biography' => $biography ?: null
                    ];

                    $pixdb->insert(
                        'members_info',
                        $membInfoData,
                        true
                    );
                }

                // update member certificate details

                $newCertificates = [];
                $certificats = array_filter(explode(',', $certificates));
                $certificats = array_map('strtolower', $certificats);
                $dbCertificates = $evg->getAllCertificates();
                $allCertificates = array_map(
                    'strtolower',
                    collectObjData($dbCertificates, 'name')
                );
                foreach ($certificats as $itm) {
                    if (!in_array($itm, $allCertificates)) {
                        $newCertificates[] = ucwords($itm);
                    }
                }

                if (!empty($newCertificates)) {
                    $newCertStr = '';
                    foreach ($newCertificates as $cert) {
                        $newCertStr .= ($newCertStr != '' ? ', ' : '') . "('" . trim($cert) . "')";
                    }
                    $qry = 'INSERT INTO `certification` (`name`) VALUES ' . $newCertStr;

                    if (!empty($newCertStr)) {
                        $pixdb->run($qry);
                    }

                    $dbCertificates = $evg->getAllCertificates();
                }

                $selCert = [];
                $certIns = [];
                foreach ($dbCertificates as $itm) {
                    if (in_array(strtolower($itm->name), $certificats)) {
                        $selCert[] = $itm->id;
                    }
                }
                $exCerts = $pixdb->getCol(
                    'members_certification',
                    ['member' => $membId],
                    'certification'
                );
                $selCert = array_diff($selCert, $exCerts);
                if (!empty($selCert)) {
                    foreach ($selCert as $cerId) {
                        $certIns[] = $cerId;
                    }
                }

                if ($certIns) {
                    $valStr = '';
                    foreach ($certIns as $itm) {
                        $valStr .= ($valStr != '' ? ', ' : '') . '(' . $membId . ', ' . $itm . ')';
                    }

                    $qry = 'INSERT INTO `members_certification` (`member`, `certification`) VALUES ' . $valStr;
                    $pixdb->run($qry);
                }
            }
        }
    }
    // exit;
})(
    $r,
    $pix,
    $pixdb,
    $datetime,
    $evg
);
