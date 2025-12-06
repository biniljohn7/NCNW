<?php
if (!$pix->canAccess('members')) {
    $pix->addmsg('Access denied!');
    $pix->redirect();
}

(function ($pix, $pixdb, $evg, $datetime) {
    $_ = $_POST;
    devMode();
    if (
        isset(
            $_['fname'],
            $_['lname'],
            $_['phone'],
            $_['email'],
            $_['registeredVoting']
        )
    ) {
        $prefix = isset($_['prefix']) ? intval(esc($_['prefix'])) : null;
        $fname = esc($_['fname']);
        $mname = esc($_['mname']);
        $lname = esc($_['lname']);
        $country = isset($_['country']) ? intval(esc($_['country'])) : null;
        $state = isset($_['state']) ? intval(esc($_['state'] ?? null)) : null;
        $city = esc($_['city']);
        $address = esc($_['address']);
        $address2 = esc($_['address2']);
        $zipcode = esc($_['zipcode']);
        $phone = esc($_['phone']);
        $email = esc($_['email']);
        $bEmail = esc($_['bEmail']);
        $employerName = esc($_['employerName']);
        $ocpnId = isset($_['ocpnId']) ? intval(esc($_['ocpnId'])) : null;
        $emplymntStatId = isset($_['emplymntStatId']) ? intval(esc($_['emplymntStatId'])) : null;
        $indstryId = isset($_['indstryId']) ? intval(esc($_['indstryId'])) : null;
        $houseHldId = isset($_['houseHldId']) ? intval(esc($_['houseHldId'])) : null;
        $slryRangeId = isset($_['slryRangeId']) ? intval(esc($_['slryRangeId'])) : null;
        $expertise = isset($_['expertise']) && is_array($_['expertise']) ? $_['expertise'] : [];
        $nationId = isset($_['nationId']) ? intval(esc($_['nationId'])) : null;
        $regionId = isset($_['regionId'])  ? intval(esc($_['regionId'])) : null;
        $orgznSteId = isset($_['orgznSteId']) ? intval(esc($_['orgznSteId'])) : null;
        $chptrOfInitn = isset($_['chptrOfInitn']) ? intval(esc($_['chptrOfInitn'])) : null;
        $yearOfInitn = esc($_['yearOfInitn']);
        $cruntChptr = isset($_['cruntChptr']) ? intval(esc($_['cruntChptr'])) : null;
        $dob = esc($_['dob']);
        $racialId = isset($_['racialId']) ? intval(esc($_['racialId'])) : null;
        $registeredVoting = esc($_['registeredVoting']);
        $gpConsent = isset($_['gpConsent']) == true ? true : false;
        $gpFirstName = ($gpConsent && isset($_['gpFirstName'])) ? esc($_['gpFirstName']) : null;
        $gpLastName = ($gpConsent && isset($_['gpLastName'])) ? esc($_['gpLastName']) : null;
        $gpPhone = ($gpConsent && isset($_['gpPhone'])) ? esc($_['gpPhone']) : null;
        $gpEmail = ($gpConsent && isset($_['gpEmail'])) ? esc($_['gpEmail']) : null;
        $deceased = isset($_['deceased']) == true ? true : false;

        $mid = esc($_['mid'] ?? '');
        $new = !$mid;

        $universities = array_values(array_filter(array_map('esc', $_['university'] ?? [])));
        $degrees = array_values(array_filter(array_map('esc', $_['degree'] ?? [])));
        $educationData = [];

        $count = min(count($universities), count($degrees));
        for ($i = 0; $i < $count; $i++) {
            $obj = new stdClass();
            $obj->universityId = (int)$universities[$i];
            $obj->degreeId = (int)$degrees[$i];
            $educationData[] = $obj;
        }

        if (!is_mail($email)) {
            $email = 'noreply-' . str2url($fname . $lname) . '-' . $pix->makestring(10, 'ln') . '@ncnw.org';
        }

        if ($yearOfInitn) {
            $yearOfInitn = strtotime(str_replace('/', '-', $yearOfInitn) . ' 23:59:59');
        } else {
            $yearOfInitn = null;
        }

        if ($dob) {
            $dob = strtotime($dob . ' 23:59:59');
        } else {
            $dob = null;
        }

        $expertise = array_filter(
            array_unique(
                array_map(
                    'esc',
                    $expertise
                )
            )
        );

        $volunteerId = isset($_['volunteerId']) && is_array($_['volunteerId']) ? $_['volunteerId'] : [];
        $volunteerId = array_filter(
            array_unique(
                array_map(
                    'intval',
                    $volunteerId
                )
            )
        );

        $certifications = isset($_['certifications']) && is_array($_['certifications']) ? $_['certifications'] : [];
        $certifications = array_filter(
            array_unique(
                array_map(
                    'esc',
                    $certifications
                )
            )
        );

        $suffix = isset($_['suffix']) ? intval(esc($_['suffix'])) : null;;
        $biography = isset($_['biography']) ? esc($_['biography']) : null;
        $affilitaions = isset($_['affilateOrgzn']) && is_array($_['affilateOrgzn']) ? $_['affilateOrgzn'] : [];
        $affilitaions = array_filter(
            array_unique(
                array_map(
                    'esc',
                    $affilitaions
                )
            )
        );
        $memberId = isset($_['memberId']) ? esc($_['memberId']) : null;

        if (!$memberId) {
            $memberId = $pix->makeMemberId();
        }

        if (
            $fname &&
            $lname &&
            $phone &&
            $email &&
            $registeredVoting
        ) {
            $validMail = true;
            $mailErrorMsg = '';

            if ($bEmail && !is_mail($bEmail)) {
                $validMail = false;
                $mailErrorMsg = 'Business email address is not valid!';
            }

            if ($gpEmail && !is_mail($gpEmail)) {
                $validMail = false;
                if ($mailErrorMsg) {
                    $mailErrorMsg .= ' ';
                }
                $mailErrorMsg .= 'Guardian / parental email is not valid!';
            }

            if ($validMail) {

                $data = false;
                if ($mid) {
                    $data = $pixdb->getRow(
                        'members',
                        ['id' => $mid],
                        'id'
                    );
                }
                if (
                    $new ||
                    (
                        !$new &&
                        $data
                    )
                ) {
                    $memberData = [
                        'email' => $email,
                        'firstName' => substr($fname, 0, 60),
                        'middleName' => substr($mname, 0, 60),
                        'lastName' => substr($lname, 0, 60),
                        'memberId' => $memberId,
                        'regOn' => $datetime,
                    ];

                    if ($new) {
                        $iid = $pixdb->insert(
                            'members',
                            $memberData
                        );
                    } else {
                        $iid = $mid;
                        $pixdb->update(
                            'members',
                            ['id' => $iid],
                            $memberData
                        );
                    }
                    if ($iid) {

                        $exInfo = false;
                        $refCode = null;

                        if ($data) {
                            $exInfo = $pixdb->getRow(
                                'members_info',
                                ['member' => $data->id]
                            );
                            if ($exInfo) {
                                $refCode = $exInfo->refcode;
                            }
                        }

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

                        if ($exInfo) {
                            $prefix = $prefix ?: $exInfo->prefix;
                            $suffix = $suffix ?: $exInfo->suffix;
                            $country = $country ?: $exInfo->country;
                            $state = $state ?: $exInfo->state;
                            $address = $address ?: $exInfo->address;
                            $address2 = $address2 ?: $exInfo->address2;
                            $zipcode = $zipcode ?: $exInfo->zipcode;
                            $phone = $phone ?: $exInfo->phone;
                            $ocpnId = $ocpnId ?: $exInfo->ocpnId;
                            $emplymntStatId = $emplymntStatId ?: $exInfo->emplymntStatId;
                            $volunteerId = $volunteerId ?: $exInfo->volunteerId;
                            $indstryId = $indstryId ?: $exInfo->indstryId;
                            $houseHldId = $houseHldId ?: $exInfo->houseHldId;
                            $slryRangeId = $slryRangeId ?: $exInfo->slryRangeId;
                            $nationId = $nationId ?: $exInfo->nationId;
                            $regionId = $regionId ?: $exInfo->regionId;
                            $orgznSteId = $orgznSteId ?: $exInfo->orgznSteId;
                            $chptrOfInitn = $chptrOfInitn ?: $exInfo->chptrOfInitn;
                            $yearOfInitn = $yearOfInitn ?: $exInfo->yearOfInitn;
                            $cruntChptr = $cruntChptr ?: $exInfo->cruntChptr;
                            $dob = $dob ?: $exInfo->dob;
                            $racialId = $racialId ?: $exInfo->racialId;
                            $biography = $biography ?: $exInfo->biography;
                            $bEmail = $bEmail ?: $exInfo->bEmail;
                            $employerName = $employerName ?: $exInfo->employerName;
                            $registeredVoting = $registeredVoting ?: $exInfo->registeredVoting;
                            $gpFirstName = $gpFirstName ?: $exInfo->gpFirstName;
                            $gpLastName = $gpLastName ?: $exInfo->gpLastName;
                            $gpPhone = $gpPhone ?: $exInfo->gpPhone;
                            $gpEmail = $gpEmail ?: $exInfo->gpEmail;
                        }

                        $memberInfoData = [
                            'member' => $iid,
                            'refCode' => substr($refCode, 0, 8) ?: null,
                            'prefix' => $prefix ?: null,
                            'suffix' => $suffix ?: null,
                            'country' => $country ?: null,
                            'state' => $state ?: null,
                            'city' => $city ?: null,
                            'address' => $address ? substr($address, 0, 100) : null,
                            'address2' => $address2 ? substr($address2, 0, 100) : null,
                            'zipcode' => $zipcode ? substr($zipcode, 0, 20) : null,
                            'phone' => substr($phone, 0, 10) ?: null,
                            'ocpnId' => $ocpnId ?: null,
                            'emplymntStatId' => $emplymntStatId ?: null,
                            'volunteerId' => json_encode($volunteerId) ?: null,
                            'indstryId' => $indstryId ?: null,
                            'houseHldId' => $houseHldId ?: null,
                            'slryRangeId' => $slryRangeId ?: null,
                            'nationId' => $nationId ?: null,
                            'regionId' => $regionId ?: null,
                            'orgznSteId' => $orgznSteId ?: null,
                            'chptrOfInitn' => $chptrOfInitn ?: null,
                            'yearOfInitn' =>  date('Y-m-d', $yearOfInitn),
                            'cruntChptr' => $cruntChptr ?: null,
                            'dob' =>  date('Y-m-d', $dob),
                            'racialId' => $racialId ?: null,
                            'biography' => $biography ?: null,
                            'bEmail' => $bEmail ?: null,
                            'employerName' => $employerName ?: null,
                            'registeredVoting' => $registeredVoting ?: null,
                            'gpConsent' => $gpConsent ? 'Y' : 'N',
                            'gpFirstName' => $gpFirstName ? substr($gpFirstName, 0, 60) : null,
                            'gpLastName' => $gpLastName ? substr($gpLastName, 0, 60) : null,
                            'gpPhone' => $gpPhone ? substr($gpPhone, 0, 10) : null,
                            'gpEmail' => $gpEmail ?: null,
                            'deceased' => $deceased ? 'Y' : 'N'
                        ];

                        //custom fields
                        $cuztmData = [];
                        foreach ($_ as $key => $value) {
                            if (strpos($key, 'cuztm_') === 0) {
                                $cuztmData[$key] = $value;
                            }
                        }
                        $cuztmData = array_unique(array_filter(array_map('esc', $cuztmData)));
                        $customFields = [];
                        foreach ($cuztmData as $key => $value) {
                            if (preg_match('/^cuztm_(.+)_field$/', $key, $matches)) {
                                $name = $matches[1];
                                $fieldData = json_decode(html_entity_decode($value, ENT_QUOTES, 'UTF-8'));
                                $valueKey = "cuztm_{$name}_value";
                                if (isset($_[$valueKey])) {
                                    $fieldData->value = $_[$valueKey];
                                }

                                $customFields[] = $fieldData;
                            }
                        }
                        $memberInfoData['customData'] = json_encode($customFields);

                        $pixdb->insert(
                            'members_info',
                            $memberInfoData,
                            true
                        );

                        // educations
                        $eduInsert = [];
                        $existEdu = $pixdb->fetchAssoc(
                            'members_education',
                            ['member' => $iid],
                            'university, degree',
                            'university'
                        );

                        if ($existEdu) {
                            foreach ($educationData as $edu) {
                                if (
                                    is_object($edu) &&
                                    isset($edu->universityId, $edu->degreeId)
                                ) {
                                    if (isset($existEdu[$edu->universityId])) {
                                        if ($existEdu[$edu->universityId]->degree == $edu->degreeId) {
                                            unset($existEdu[$edu->universityId]);
                                        } else {
                                            $eduInsert[] = $edu;
                                        }
                                    } else {
                                        $eduInsert[] = $edu;
                                    }
                                }
                            }
                        } else {
                            $eduInsert = $educationData;
                        }

                        if ($existEdu) {
                            $eduDel = [];
                            foreach ($existEdu as $del) {
                                $eduDel[] = $del->university;
                            }
                            $pixdb->delete(
                                'members_education',
                                [
                                    'member' => $iid,
                                    '#QRY' => 'university in (' . implode(', ', $eduDel) . ')'
                                ]
                            );
                        }

                        if ($eduInsert) {
                            $valStr = '';
                            foreach ($eduInsert as $edu) {
                                if (
                                    is_object($edu) &&
                                    isset($edu->universityId, $edu->degreeId)
                                ) {
                                    $valStr .= ($valStr != '' ? ', ' : '') . '(' . $iid . ', ' . $edu->universityId . ', ' . $edu->degreeId . ')';
                                }
                            }
                            $qry = 'INSERT INTO `members_education` (`member`, `university`, `degree`) VALUES ' . $valStr;
                            $pixdb->run($qry);
                        }

                        // certifications
                        $cerInsert = $certifications;

                        $existCer = $pixdb->getCol(
                            'members_certification',
                            ['member' => $iid],
                            'certification'
                        );
                        if ($existCer) {
                            foreach ($certifications as $cer) {
                                if (in_array($cer, $existCer)) {
                                    $existCer = array_diff($existCer, [$cer]);
                                    $cerInsert = array_diff($cerInsert, [$cer]);
                                }
                            }
                        }

                        if ($existCer) {
                            $pixdb->delete(
                                'members_certification',
                                [
                                    'member' => $iid,
                                    '#QRY' => 'certification in (' . implode(', ', $existCer) . ')'
                                ]
                            );
                        }

                        if ($cerInsert) {
                            $valStr = '';
                            foreach ($cerInsert as $cer) {
                                $valStr .= ($valStr != '' ? ', ' : '') . '(' . $iid . ', ' . $cer . ')';
                            }
                            $qry = 'INSERT INTO `members_certification` (`member`, `certification`) VALUES ' . $valStr;
                            $pixdb->run($qry);
                        }

                        // affiliates
                        $affInsert = $affilitaions;

                        $existAff = $pixdb->getCol(
                            'members_affiliation',
                            ['member' => $iid],
                            'affiliation'
                        );
                        if ($existAff) {
                            foreach ($affilitaions as $aff) {
                                if (in_array($aff, $existAff)) {
                                    $existAff = array_diff($existAff, [$aff]);
                                    $affInsert = array_diff($affInsert, [$aff]);
                                }
                            }
                        }
                        if ($existAff) {
                            $pixdb->delete(
                                'members_affiliation',
                                [
                                    'member' => $iid,
                                    '#QRY' => 'affiliation in (' . implode(', ', $existAff) . ')'
                                ]
                            );
                        }

                        if ($affInsert) {
                            $valStr = '';
                            foreach ($affInsert as $aff) {
                                $valStr .= ($valStr != '' ? ', ' : '') . '(' . $iid . ', ' . $aff . ')';
                            }
                            $qry = 'INSERT INTO `members_affiliation` (`member`, `affiliation`) VALUES ' . $valStr;
                            $pixdb->run($qry);
                        }

                        //expertise
                        $expertInsert = $expertise;

                        $existExpert = $pixdb->getCol(
                            'members_expertise',
                            ['member' => $iid],
                            'expertise'
                        );

                        if ($existExpert) {
                            foreach ($expertise as $exp) {
                                if (in_array($exp, $existExpert)) {
                                    $existExpert = array_diff($existExpert, [$exp]);
                                    $expertInsert = array_diff($expertInsert, [$exp]);
                                }
                            }
                        }

                        if ($existExpert) {
                            $pixdb->delete(
                                'members_expertise',
                                [
                                    'member' => $iid,
                                    '#QRY' => 'expertise in (' . implode(', ', $existExpert) . ')'
                                ]
                            );
                        }

                        if ($expertInsert) {
                            $valStr = '';
                            foreach ($expertInsert as $exp) {
                                $valStr .= ($valStr != '' ? ', ' : '') . '(' . $iid . ', ' . $exp . ')';
                            }
                            $qry = 'INSERT INTO `members_expertise` (`member`, `expertise`) VALUES ' . $valStr;
                            $pixdb->run($qry);
                        }

                        $swArry = [];

                        $swArry['member'] = $iid;
                        $swArry['firstName'] = 'Y';
                        $swArry['middleName'] = 'Y';
                        $swArry['lastName'] = 'Y';
                        $swArry['suffix'] = 'Y';
                        $swArry['profileImg'] = 'Y';
                        $swArry['stusUpdate'] = 'Y';
                        $swArry['prefix'] = isset($_['prefixSwitch']) ? 'Y' : 'N';
                        $swArry['email'] = isset($_['emailSwitch']) ? 'Y' : 'N';
                        $swArry['memberCode'] = 'Y';
                        $swArry['country'] = isset($_['countrySwitch']) ? 'Y' : 'N';
                        $swArry['state'] = isset($_['stateSwitch']) ? 'Y' : 'N';
                        $swArry['city'] = isset($_['citySwitch']) ? 'Y' : 'N';
                        $swArry['address'] = isset($_['addressSwitch']) ? 'Y' : 'N';
                        $swArry['address2'] = isset($_['address2Switch']) ? 'Y' : 'N';
                        $swArry['zipcode'] = isset($_['zipSwitch']) ? 'Y' : 'N';
                        $swArry['biography'] = isset($_['biographySwitch']) ? 'Y' : 'N';
                        $swArry['certification'] = isset($_['certificationSwitch']) ? 'Y' : 'N';
                        $swArry['chptrOfIntian'] = isset($_['chapOfIniSwitch']) ? 'Y' : 'N';
                        $swArry['crentChapter'] = isset($_['currentChapSwitch']) ? 'Y' : 'N';
                        $swArry['educations'] = isset($_['educationSwitch']) ? 'Y' : 'N';
                        $swArry['expertise'] = isset($_['expertiseSwitch']) ? 'Y' : 'N';
                        $swArry['household'] = isset($_['householdSwitch']) ? 'Y' : 'N';
                        $swArry['racial'] = isset($_['racialIdentitySwitch']) ? 'Y' : 'N';
                        $swArry['empStatus'] = isset($_['employmentStatusSwitch']) ? 'Y' : 'N';
                        $swArry['volunteer'] = isset($_['volunteerInterestSwitch']) ? 'Y' : 'N';
                        $swArry['industry'] = isset($_['industrySwitch']) ? 'Y' : 'N';
                        $swArry['occupation'] = isset($_['occupationSwitch']) ? 'Y' : 'N';
                        $swArry['phone'] = isset($_['phoneNumberSwitch']) ? 'Y' : 'N';
                        $swArry['salaryRange'] = isset($_['salarySwitch']) ? 'Y' : 'N';
                        $swArry['yerOfInitn'] = isset($_['yearOfIniSwitch']) ? 'Y' : 'N';
                        $swArry['dob'] = isset($_['dobSwitch']) ? 'Y' : 'N';
                        $swArry['nation'] = isset($_['nationSwitch']) ? 'Y' : 'N';
                        $swArry['region'] = isset($_['regionSwitch']) ? 'Y' : 'N';
                        $swArry['orgztonState'] = isset($_['organizationalStateSwich']) ? 'Y' : 'N';
                        $swArry['affilateOrgzn'] = isset($_['affilateOrgznSwitch']) ? 'Y' : 'N';
                        $swArry['affilateOrgzn'] = isset($_['affilateOrgznSwitch']) ? 'Y' : 'N';
                        $swArry['businessEmailAddress'] = isset($_['bEmailSwitch']) ? 'Y' : 'N';
                        $swArry['employerName'] = isset($_['employerNameSwitch']) ? 'Y' : 'N';
                        $swArry['registeredVoting'] = isset($_['registeredVotingSwitch']) ? 'Y' : 'N';
                        $swArry['gpFirstName'] = isset($_['gpFirstNameSwitch']) ? 'Y' : 'N';
                        $swArry['gpLastName'] = isset($_['gpLastNameSwitch']) ? 'Y' : 'N';
                        $swArry['gpPhone'] = isset($_['gpPhoneSwitch']) ? 'Y' : 'N';
                        $swArry['gpEmail'] = isset($_['gpEmailSwitch']) ? 'Y' : 'N';

                        $pixdb->insert(
                            'members_switch',
                            $swArry,
                            true
                        );

                        //update access token
                        $evg->changeAccessToken($iid);

                        $pix->addmsg('Member saved', 1);
                        $pix->redirect('?page=members&sec=details&id=' . $iid);
                    }
                }
            } else {
                $pix->addmsg($mailErrorMsg);
            }
        }
    }
})($pix, $pixdb, $evg, $datetime);
