<?php
(function () use (
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
        $records = json_decode($records);

        if (isset($records->data) && is_array($records->data)) {
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
                $cruntChptr,
                $affilateOrgzn,
                $mmbrshpType,
                $mmbrshpExpiry,
                $mmbrshpStatus,
                $prefix,
                $electedTitle,
                $electedDate,
                $electedExpiry
            ) = $records->data;

            $fName = ucwords(esc($fName));
            $lName = ucwords(esc($lName));
            $memberId = esc($memberId);
            $email = esc(strtolower($email));
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
            $cruntChptr = esc($cruntChptr);
            $affilateOrgzn = esc($affilateOrgzn);
            $mmbrshpType = esc($mmbrshpType);
            $mmbrshpExpiry = esc($mmbrshpExpiry);
            $mmbrshpStatus = esc($mmbrshpStatus);
            $prefix = esc($prefix);
            $electedTitle = esc($electedTitle);
            $electedDate = esc($electedDate);
            $electedExpiry = esc($electedExpiry);

            $exMemb = false;
            if (is_mail($email)) {
                $exMemb = $pixdb->getRow(
                    'members',
                    ['email' => $email],
                    'firstName, lastName, memberId, email'
                );
                if ($exMemb) {
                    $r->duplicate = 'email';
                }
            }

            if (!$exMemb && $phone) {
                $exMemb = $pixdb->getRow(
                    'members',
                    [
                        '#QRY' => "id IN(SELECT member FROM members_info WHERE phone = '$phone')",
                    ],
                    'id,
                    firstName,
                    lastName,
                    memberId,
                    email'
                );
                if ($exMemb) {
                    $r->duplicate = 'phone';
                }
            }

            if (!$exMemb && $memberId) {
                $exMemb = $pixdb->getRow(
                    'members',
                    ['memberId' => $memberId],
                    'firstName, lastName, memberId, email'
                );
                if ($exMemb) {
                    $r->duplicate = 'Member ID';
                }
            }

            if ($exMemb) {
                $r->status = 'exist';
                $r->member = [
                    'fname' => $exMemb->firstName,
                    'lname' => $exMemb->lastName,
                    'memberID' => $exMemb->memberId,
                    'email' => $exMemb->email
                ];
                $r->uploaded = [
                    'fname' => $fName,
                    'lname' => $lName,
                    'memberID' => $memberId,
                    'email' => $email,
                    'link' => DOMAIN . 'evgadmin/?page=members&sec=details&id=' . $exMemb->id
                ];
                return;
            }

            // continue if it is a new member
            $memberData = [
                'firstName' => substr($fName, 0, 60),
                'lastName' => substr($lName, 0, 60),
                'memberId' => $pix->makeMemberId(),
                'regOn' => $datetime,
                'verified' => strtolower($verified) == 'yes' ? 'Y' : 'N'
            ];
            if ($memberId) {
                $memberData['memberIdOld'] = $memberId;
            }
            $membId = $pixdb->insert(
                'members',
                $memberData
            );

            if ($membId) {
                $r->status = 'ok';

                $refCode = null;
                if (!$refCode) {
                    while (
                        !$refCode ||
                        (
                            $refCode &&
                            $pixdb->get(
                                'members_info',
                                [
                                    'refcode' => $refCode,
                                    'single' => 1
                                ],
                                'member'
                            )
                        )
                    ) {
                        $refCode = $pix->makestring(8, 'un');
                    }
                }

                $countryId = null;
                $nationId = null;
                $regionId = null;
                $stateId = null;
                $orgznSteId = null;
                $prefixId = null;
                $ocpnId = null;
                $indstryId = null;
                $chptrOfInitn = null;
                $cruntChptrId = null;

                if ($country) {
                    $country = ucwords(trim($country));
                    $countryId = $evg->getNationId($country);
                }

                if ($nation) {
                    $nation = ucwords(trim($nation));
                    $nationId = $evg->getNationId($nation);
                }

                if ($region) {
                    $region = ucwords(trim($region));
                    $regionId = $evg->getRegionId(
                        $region,
                        $nationId
                    );
                }

                if ($state) {
                    $state = ucwords(trim($state));
                    $stateId = $evg->getStateId(
                        $state,
                        $regionId,
                        $nationId
                    );
                }

                if (!$countryId && $stateId) {
                    $fetchNation = $pixdb->getRow('states', ['id' => $stateId], 'nation');
                    if ($fetchNation) {
                        $countryId = $fetchNation->nation;
                    }
                }

                if ($orgState) {
                    $orgState = ucwords(trim($orgState));
                    $orgznSteId = $evg->getStateId(
                        $orgState,
                        $regionId,
                        $nationId
                    );
                }

                if ($occupation) {
                    $occupation = ucwords(trim($occupation));
                    $ocpnId = $evg->getOccupationId($occupation);
                }

                if ($industry) {
                    $industry = ucwords(trim($industry));
                    $indstryId = $evg->getIndustryId($industry);
                }

                if ($sectionOfInitiation) {
                    $sectionOfInitiation = ucwords(trim($sectionOfInitiation));
                    $chptrOfInitn = $evg->getChapterId(
                        $sectionOfInitiation,
                        $nationId,
                        $regionId,
                        $orgznSteId
                    );
                }

                if ($cruntChptr) {
                    $qc = q($cruntChptr);
                    $cruntChptrChk = $pixdb->getRow(
                        'chapters',
                        ["#QRY" => "name like $qc"],
                        'id'
                    );

                    $cruntChptrId = ($cruntChptrChk && isset($cruntChptrChk->id)) ? $cruntChptrChk->id : NULL;
                }

                if ($prefix) {
                    $OPTIONS = $pix->PROFILE_OPTIONS;
                    foreach ($OPTIONS['prefix'] as $idx => $pfx) {
                        if ($pfx == ucwords($prefix)) {
                            $prefixId = $idx;
                        }
                    }
                }

                $membInfoData = [
                    'member' => $membId,
                    'refcode' => substr($refCode, 0, 8) ?: null,
                    'pointsBalance' => $pointBalance ?: 0,
                    'referralsTotal' => $ttlRef ?: null,
                    'prefix' => $prefixId,
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
                    'cruntChptr' => $cruntChptrId ?: null
                ];

                $pixdb->insert(
                    'members_info',
                    $membInfoData,
                    true
                );

                // member affiliations
                if ($affilateOrgzn) {
                    $qs = q($affilateOrgzn);
                    $affId = $pixdb->getRow(
                        'affiliates',
                        ['#QRY' => "name like $qs"],
                        'id'
                    );
                    if (
                        $affId &&
                        $affId->id
                    ) {
                        $pixdb->insert(
                            'members_affiliation',
                            [
                                'member' => $membId,
                                'affiliation' => $affId
                            ]
                        );
                    }
                }

                // update membership info
                if ($mmbrshpType) {
                    $pixdb->insert(
                        'memberships',
                        [
                            'member' => $membId,
                            'planId' => null,
                            'planName' => $mmbrshpType,
                            'Created' => null,
                            'expiry' => $mmbrshpExpiry ? date('Y-m-d', strtotime($mmbrshpExpiry)) : null,
                            'enabled' => strtolower($mmbrshpStatus) == 'active' ? 'Y' : 'N'
                        ]
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
                ///officer elect    
                $format = [
                    'd-m-Y',
                    'd/m/Y',
                    'd.m.Y',
                    'd-m-y',
                    'd/m/y',
                    'm-d-Y',
                    'm/d/Y',
                    'm-d-y',
                    'm/d/y',
                    'Y-m-d',
                    'Y/m/d',
                    'Y.m.d',
                    'd-M-Y',
                    'd-M-y',
                    'M-d-Y',
                    'M-d-y',
                    'M j, Y',
                    'j-M-Y',
                    'j-M-y',
                    'd F Y',
                    'F d, Y',
                    'F j Y',
                    'j F Y',
                    'jS F Y',
                    'F jS Y',
                    'D, d M Y',
                    'l, d F Y',
                    'Ymd',
                    'd M Y H:i',
                    'd-M-Y H:i:s',
                    'M-Y',
                    'm-Y',
                    'm-y',
                    'M-y'
                ];

                $validateDate = function ($date) use ($format) {
                    foreach ($format as $ft) {
                        $d = DateTime::createFromFormat($ft, $date);
                        if ($d && $d->format($ft) === $date) {
                            return $d;
                        }
                        //
                        $ftNoLeadingZeros = str_replace(['d', 'm'], ['j', 'n'], $ft);
                        if ($ftNoLeadingZeros !== $ft) {
                            $d2 = DateTime::createFromFormat($ftNoLeadingZeros, $date);
                            if ($d2 && $d2->format($ftNoLeadingZeros) === $date) {
                                return $d2;
                            }
                        }
                    }
                    return false;
                };
                //
                if (
                    $electedTitle &&
                    /*$validateDate($electedDate) &&
                    $validateDate($electedExpiry) &&*/
                    $cruntChptrId
                ) {
                    $elected = $validateDate($electedDate) ? date('Y-m-d', strtotime($electedDate)) : NULL;
                    $expiry = $validateDate($electedExpiry) ? date('Y-m-d', strtotime($electedExpiry)) : NULL;
                    $titleId = $evg->getOfficerTitleId($electedTitle);

                    if ($titleId) {
                        $officers = [
                            1 => 'officer-president',
                            2 => 'first-vice-president',
                            3 => 'second-vice-president',
                            8 => 'treasurer',
                            19 => 'collegiate-liaison'
                        ];
                        //
                        $hasOfficer = $pixdb->get(
                            'officers',
                            [
                                '#QRY' => 'memberId!=' . $membId,
                                'title' => $titleId,
                                'circle' => 'section',
                                'circleId' => $cruntChptrId
                            ]
                        );
                        //
                        $checkAdminExists = $pixdb->getRow(
                            'admins',
                            [
                                'email' => $email,
                                'memberid' => $membId,
                            ],
                            'id,
                            type,
                            perms'
                        );
                        //
                        $sectionSearch = $pixdb->getRow(
                            'chapters',
                            [
                                'id' => $cruntChptr
                            ],
                            'id,
                            ein,
                            secId'
                        );
                        //checking role of member if any
                        $roles = $pixdb->getRow(
                            'members',
                            [
                                'id' => $membId
                            ],
                            'role'
                        );
                        //Permissions check for new admin entry
                        $permissions = $pixdb->getRow(
                            'permissions',
                            ['type' => $officers[$titleId] ?? NULL],
                            'perms'
                        );
                        $allPermissions = ($permissions && isset($permissions->perms)) ? $permissions->perms : NULL;
                        $permArray = [];
                        if ($allPermissions) {
                            $permArray = explode(',', $allPermissions);
                        }
                        //Role process start here
                        if (empty($hasOfficer->data)) {
                            $rowId = $pixdb->getRow(
                                'officers',
                                [
                                    'memberId' => $membId,
                                    //'title' => $titleId,
                                    'circle' => 'section',
                                    'circleId' => $cruntChptrId
                                ],
                                'id,
                                memberId,
                                title'
                            );
                            //anonymus function for updating role
                            $updateMemberRole = function ($pixdb, $membId, $roles) {
                                if ($roles->role) {
                                    $rolesArray = explode(',', $roles->role);
                                    if (!in_array('section-officer', $rolesArray)) {
                                        $rolesArray[] = "section-officer";
                                    }
                                    $roleValue = implode(',', $rolesArray);
                                } else {
                                    $roleValue = 'section-officer';
                                }

                                $pixdb->update(
                                    'members',
                                    ['id' => $membId],
                                    ['role' => $roleValue != '' ? $roleValue : NULL]
                                );
                            };
                            //
                            if ($rowId) {
                                if ($rowId->title != $titleId) {
                                    $pixdb->update(
                                        'officers',
                                        ['id' => $rowId->id],
                                        ['title' => $titleId]
                                    );
                                    //
                                    if ($checkAdminExists) {
                                        if (in_array($checkAdminExists->type, $officers)) {
                                            if (isset($officers[$titleId])) {
                                                $update = [
                                                    'type' => $officers[$titleId]
                                                ];
                                                if ($checkAdminExists->perms) {
                                                    $adPermsArr = explode(',', $$checkAdminExists->perms);
                                                    if (!empty($adPermsArr)) {
                                                        $permArray = array_unique(array_merge($adPermsArr, $permArray));
                                                        $allPermissions = implode(',', $permArray);
                                                    }
                                                }
                                                $update['perms'] = $allPermissions;
                                                $pixdb->update(
                                                    'admins',
                                                    ['id' => $checkAdminExists->id],
                                                    $update
                                                );
                                            }
                                        }
                                    }
                                    //
                                    $updateMemberRole($pixdb, $membId, $roles);
                                }
                            } else {
                                $loggedUserMemberId = $pix->getLoggedUser()->memberid;
                                $officerData = [
                                    'memberId' => $membId,
                                    'title' => $titleId,
                                    'assignedBy' => $loggedUserMemberId,
                                    'electedOn' => $elected,
                                    'expiry' => $expiry,
                                    'circle' => 'section',
                                    'circleId' => $cruntChptrId
                                ];
                                //
                                $pixdb->insert(
                                    'officers',
                                    $officerData
                                );
                                //
                                if (!$checkAdminExists) {
                                    if (isset($officers[$titleId])) {
                                        $pixdb->insert(
                                            'admins',
                                            [
                                                'type' => $officers[$titleId],
                                                'enabled' => 'Y',
                                                'email' => $email,
                                                'name' => $memberData['firstName'] . ' ' . $memberData['lastName'],
                                                'userName' => strtolower(str_replace(' ', '', $memberData['firstName'])),
                                                'perms' => $allPermissions,
                                                'memberid' => $membId
                                            ],
                                            true
                                        );
                                    }
                                }
                                $updateMemberRole($pixdb, $membId, $roles);
                            }
                        }
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
})();
