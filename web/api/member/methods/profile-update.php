<?php
devMode();
$requestBody = file_get_contents('php://input');
$postData = json_decode($requestBody);

if (
    is_object($postData) &&
    isset(
        $postData->profileVisibility,
        $postData->firstName,
        $postData->lastName,
        $postData->phoneNumber
    )
) {

    $prefixId = $postData->prefixId ? intval($postData->prefixId) : null;
    $firstName = esc($postData->firstName);
    $middleName = $postData->middleName ? esc($postData->middleName) : null;
    $lastName = esc($postData->lastName);
    $countryId = $postData->countryId ? intval($postData->countryId) : null;
    $stateId = $postData->stateId ? intval($postData->stateId) : null;
    $city = $postData->cityId ? esc($postData->cityId) : null;
    $address = $postData->address ? esc($postData->address) : null;
    $address2 = $postData->address2 ? esc($postData->address2) : null;
    $zipcode = $postData->zipcode ? esc($postData->zipcode) : null;
    $phoneNumber = esc($postData->phoneNumber);
    $occupationId = $postData->occupationId ? intval($postData->occupationId) : null;
    $industryId = $postData->industryId ? intval($postData->industryId) : null;
    $householdId = $postData->householdId ? intval($postData->householdId) : null;
    $racialIdentityId = $postData->racialIdentityId ? intval($postData->racialIdentityId) : null;
    $employmentStatusId = $postData->employmentStatusId ? intval($postData->employmentStatusId) : null;
    $salaryRangeId = $postData->salaryRangeId ? intval($postData->salaryRangeId) : null;
    $nationId = $postData->nationId ? intval($postData->nationId) : null;
    $regionId = $postData->regionId ? intval($postData->regionId) : null;
    $organizationalStateId = $postData->organizationalStateId ? intval($postData->organizationalStateId) : null;
    $chapterOfInitiation = $postData->chapterOfInitiation ? intval($postData->chapterOfInitiation) : null;
    $currentChapter = $postData->currentChapter ? intval($postData->currentChapter) : null;
    $bEmail = $postData->bEmail ? esc($postData->bEmail) : null;
    $employerName = $postData->employerName ? esc($postData->employerName) : null;
    $registeredVoting = $postData->registeredVoting ? esc($postData->registeredVoting) : null;
    $gpConsent = $postData->gpConsent == true ? true : false;
    $gpFirstName = ($gpConsent && $postData->gpFirstName) ? esc($postData->gpFirstName) : null;
    $gpLastName = ($gpConsent && $postData->gpLastName) ? esc($postData->gpLastName) : null;
    $gpPhone = ($gpConsent && $postData->gpPhone) ? esc($postData->gpPhone) : null;
    $gpEmail = ($gpConsent && $postData->gpEmail) ? esc($postData->gpEmail) : null;

    $yearOfInitiation = $postData->yearOfInitiation ? validateAndFormatDate($postData->yearOfInitiation) : null;
    $dobDate = $postData->dob ? validateAndFormatDate($postData->dob) : null;

    $expertiseIds = is_array($postData->expertiseIds) ? $postData->expertiseIds : array();
    $expertiseIds = array_unique(array_filter(array_map('esc', $expertiseIds)));

    $certificationId = isset($postData->certificationId) ? $postData->certificationId : array();
    $certificationId = array_unique(array_filter(array_map('esc', $certificationId)));

    $educations = is_array($postData->educations) ? $postData->educations : array();
    $statusUpdate = isset($postData->statusUpdate) ? esc($postData->statusUpdate) : null;
    $suffixId = isset($postData->suffixId) ? intval($postData->suffixId) : null;
    $biography = isset($postData->biography) ? esc($postData->biography) : null;
    $affilateOrgznId = isset($postData->affilateOrgznId) ? $postData->affilateOrgznId : [];
    $affilateOrgznId = array_unique(array_filter(array_map('esc', $affilateOrgznId)));

    if (
        $firstName &&
        $lastName &&
        $phoneNumber
    ) {
        $validMail = true;
        $mailErroMsg = '';

        if (!is_mail($bEmail)) {
            $validMail = false;
            $mailErroMsg = 'Business email address is not valid!';
        }

        if ($gpEmail && !is_mail($gpEmail)) {
            $validMail = false;
            if ($mailErroMsg) {
                $mailErroMsg .= ' ';
            }
            $mailErroMsg .= 'Guardian / parental email is not valid!';
        }

        if ($validMail) {
            $member = $pixdb->get(
                'members',
                [
                    'id' => $lgUser->id,
                    'single' => 1
                ],
                'id'
            );

            if ($member) {
                $pixdb->update(
                    'members',
                    [
                        'id' => $lgUser->id
                    ],
                    [
                        'firstName' => $firstName,
                        'middleName' => $middleName,
                        'lastName' => $lastName
                    ]
                );

                $volunteerInterestId = [];
                if (
                    isset($postData->volunteerInterestId) &&
                    is_array($postData->volunteerInterestId)
                ) {
                    $volunteerInterestId = array_unique(array_filter(array_map('intval', $postData->volunteerInterestId)));
                }

                $pixdb->insert(
                    'members_info',
                    [
                        'member' => $lgUser->id,
                        'prefix' => $prefixId,
                        'suffix' => $suffixId,
                        'country' => $countryId,
                        'state' => $stateId,
                        'city' => $city,
                        'address' => $address,
                        'address2' => $address2,
                        'zipcode' => $zipcode,
                        'phone' => $phoneNumber,
                        'ocpnId' => $occupationId,
                        'indstryId' => $industryId,
                        'houseHldId' => $householdId,
                        'racialId' => $racialIdentityId,
                        'emplymntStatId' => $employmentStatusId,
                        'volunteerId' => json_encode($volunteerInterestId),
                        'slryRangeId' => $salaryRangeId,
                        'nationId' => $nationId,
                        'regionId' => $regionId,
                        'orgznSteId' => $organizationalStateId,
                        'chptrOfInitn' => $chapterOfInitiation,
                        'yearOfInitn' => $yearOfInitiation,
                        'dob' => $dobDate,
                        'cruntChptr' => $currentChapter,
                        'statusUpdate' => $statusUpdate,
                        'biography' => $biography,
                        'bEmail' => $bEmail,
                        'employerName' => $employerName,
                        'registeredVoting' => $registeredVoting,
                        'gpConsent' => $gpConsent ? 'Y' : 'N',
                        'gpFirstName' => $gpFirstName,
                        'gpLastName' => $gpLastName,
                        'gpPhone' => $gpPhone,
                        'gpEmail' => $gpEmail
                    ],
                    true
                );

                ### affiliations
                $affInsert = $affilateOrgznId;

                $existAff = $pixdb->getCol(
                    'members_affiliation',
                    ['member' => $lgUser->id],
                    'affiliation'
                );
                if ($existAff) {
                    foreach ($affilateOrgznId as $aff) {
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
                            'member' => $lgUser->id,
                            '#QRY' => 'affiliation in (' . implode(', ', $existAff) . ')'
                        ]
                    );
                }

                if ($affInsert) {
                    $valStr = '';
                    foreach ($affInsert as $aff) {
                        $valStr .= ($valStr != '' ? ', ' : '') . '(' . $lgUser->id . ', ' . $aff . ')';
                    }
                    $qry = 'INSERT INTO `members_affiliation` (`member`, `affiliation`) VALUES ' . $valStr;
                    $pixdb->run($qry);
                }

                ### certification
                $cerInsert = $certificationId;

                $existCer = $pixdb->getCol(
                    'members_certification',
                    [
                        'member' => $lgUser->id
                    ],
                    'certification'
                );
                if ($existCer) {
                    foreach ($certificationId as $cer) {
                        if (
                            in_array($cer, $existCer)
                        ) {
                            $existCer = array_diff($existCer, [$cer]);
                            $cerInsert = array_diff($cerInsert, [$cer]);
                        }
                    }
                }

                if ($existCer) {
                    $pixdb->delete(
                        'members_certification',
                        [
                            'member' => $lgUser->id,
                            '#QRY' => 'certification in (' . implode(', ', $existCer) . ')'
                        ]
                    );
                }

                if ($cerInsert) {
                    $valStr = '';
                    foreach ($cerInsert as $cer) {
                        $valStr .= ($valStr != '' ? ', ' : '') . '(' . $lgUser->id . ', ' . $cer . ')';
                    }
                    $qry = 'INSERT INTO `members_certification` (`member`, `certification`) VALUES ' . $valStr;
                    $pixdb->run($qry);
                }

                ### expertise
                $expertInsert = $expertiseIds;

                $existExpert = $pixdb->getCol(
                    'members_expertise',
                    [
                        'member' => $lgUser->id
                    ],
                    'expertise'
                );
                if ($existExpert) {
                    foreach ($expertiseIds as $exp) {
                        if (
                            in_array($exp, $existExpert)
                        ) {
                            $existExpert = array_diff($existExpert, [$exp]);
                            $expertInsert = array_diff($expertInsert, [$exp]);
                        }
                    }
                }

                if ($existExpert) {
                    $pixdb->delete(
                        'members_expertise',
                        [
                            'member' => $lgUser->id,
                            '#QRY' => 'expertise in (' . implode(', ', $existExpert) . ')'
                        ]
                    );
                }

                if ($expertInsert) {
                    $valStr = '';
                    foreach ($expertInsert as $exp) {
                        $valStr .= ($valStr != '' ? ', ' : '') . '(' . $lgUser->id . ', ' . $exp . ')';
                    }
                    $qry = 'INSERT INTO `members_expertise` (`member`, `expertise`) VALUES ' . $valStr;
                    $pixdb->run($qry);
                }

                ###  education            
                $eduInsert = array();
                $existEdu = $pixdb->fetchAssoc(
                    'members_education',
                    [
                        'member' => $lgUser->id
                    ],
                    'university, degree',
                    'university'
                );
                if ($existEdu) {
                    foreach ($educations as $edu) {
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
                    $eduInsert = $educations;
                }

                if ($existEdu) {
                    $eduDel = array();
                    foreach ($existEdu as $del) {
                        $eduDel[] = $del->university;
                    }
                    $pixdb->delete(
                        'members_education',
                        [
                            'member' => $lgUser->id,
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
                            $valStr .= ($valStr != '' ? ', ' : '') . '(' . $lgUser->id . ', ' . $edu->universityId . ', ' . $edu->degreeId . ')';
                        }
                    }
                    $qry = 'INSERT INTO `members_education` (`member`, `university`, `degree`) VALUES ' . $valStr;
                    $pixdb->run($qry);
                }

                ////  profile visibility
                $pv = $postData->profileVisibility;
                $swArray = array();

                $swArray['member'] = $lgUser->id;
                $swArray['firstName'] = isset($pv->firstName) ? ($pv->firstName == true ? 'Y' : 'N') : 'N';
                $swArray['middleName'] = isset($pv->middleName) ? ($pv->middleName == true ? 'Y' : 'N') : 'N';
                $swArray['lastName'] =  isset($pv->lastName) ? ($pv->lastName == true ? 'Y' : 'N') : 'N';
                $swArray['suffix'] =  isset($pv->suffix) ? ($pv->suffix == true ? 'Y' : 'N') : 'N';
                $swArray['profileImg'] =  isset($pv->profileImage) ? ($pv->profileImage == true ? 'Y' : 'N') : 'N';
                $swArray['stusUpdate'] =  isset($pv->statusUpdate) ? ($pv->statusUpdate == true ? 'Y' : 'N') : 'N';
                $swArray['prefix'] =  isset($pv->prefix) ? ($pv->prefix == true ? 'Y' : 'N') : 'N';
                $swArray['email'] =  isset($pv->email) ? ($pv->email == true ? 'Y' : 'N') : 'N';
                $swArray['memberCode'] =  isset($pv->memberCode) ? ($pv->memberCode == true ? 'Y' : 'N') : 'N';
                $swArray['country'] =  isset($pv->country) ? ($pv->country == true ? 'Y' : 'N') : 'N';
                $swArray['state'] =  isset($pv->state) ? ($pv->state == true ? 'Y' : 'N') : 'N';
                $swArray['city'] =  isset($pv->city) ? ($pv->city == true ? 'Y' : 'N') : 'N';
                $swArray['address'] =  isset($pv->address) ? ($pv->address == true ? 'Y' : 'N') : 'N';
                $swArray['address2'] =  isset($pv->address2) ? ($pv->address2 == true ? 'Y' : 'N') : 'N';
                $swArray['zipcode'] =  isset($pv->zipcode) ? ($pv->zipcode == true ? 'Y' : 'N') : 'N';
                $swArray['biography'] =  isset($pv->biography) ? ($pv->biography == true ? 'Y' : 'N') : 'N';
                $swArray['certification'] =  isset($pv->certification) ? ($pv->certification == true ? 'Y' : 'N') : 'N';
                $swArray['chptrOfIntian'] =  isset($pv->chapterOfInitiation) ? ($pv->chapterOfInitiation == true ? 'Y' : 'N') : 'N';
                $swArray['crentChapter'] =  isset($pv->currentChapter) ? ($pv->currentChapter == true ? 'Y' : 'N') : 'N';
                $swArray['educations'] =  isset($pv->educations) ? ($pv->educations == true ? 'Y' : 'N') : 'N';
                $swArray['expertise'] =  isset($pv->expertise) ? ($pv->expertise == true ? 'Y' : 'N') : 'N';
                $swArray['household'] =  isset($pv->household) ? ($pv->household == true ? 'Y' : 'N') : 'N';
                $swArray['racial'] =  isset($pv->racialIdentity) ? ($pv->racialIdentity == true ? 'Y' : 'N') : 'N';
                $swArray['empStatus'] =  isset($pv->employmentStatus) ? ($pv->employmentStatus == true ? 'Y' : 'N') : 'N';
                $swArray['volunteer'] =  isset($pv->volunteerInterest) ? ($pv->volunteerInterest == true ? 'Y' : 'N') : 'N';
                $swArray['industry'] =  isset($pv->industry) ? ($pv->industry == true ? 'Y' : 'N') : 'N';
                $swArray['occupation'] =  isset($pv->occupation) ? ($pv->occupation == true ? 'Y' : 'N') : 'N';
                $swArray['phone'] =  isset($pv->phoneNumber) ? ($pv->phoneNumber == true ? 'Y' : 'N') : 'N';
                $swArray['salaryRange'] =  isset($pv->salaryRange) ? ($pv->salaryRange == true ? 'Y' : 'N') : 'N';
                $swArray['yerOfInitn'] =  isset($pv->yearOfInitiation) ? ($pv->yearOfInitiation == true ? 'Y' : 'N') : 'N';
                $swArray['dob'] =  isset($pv->dob) ? ($pv->dob == true ? 'Y' : 'N') : 'N';
                $swArray['nation'] =  isset($pv->nation) ? ($pv->nation == true ? 'Y' : 'N') : 'N';
                $swArray['region'] =  isset($pv->region) ? ($pv->region == true ? 'Y' : 'N') : 'N';
                $swArray['orgztonState'] =  isset($pv->organizationalState) ? ($pv->organizationalState == true ? 'Y' : 'N') : 'N';
                $swArray['affilateOrgzn'] =  isset($pv->affilateOrgzn) ? ($pv->affilateOrgzn == true ? 'Y' : 'N') : 'N';
                $swArray['businessEmailAddress'] =  isset($pv->businessEmailAddress) ? ($pv->businessEmailAddress == true ? 'Y' : 'N') : 'N';
                $swArray['employerName'] =  isset($pv->employerName) ? ($pv->employerName == true ? 'Y' : 'N') : 'N';
                $swArray['registeredVoting'] =  isset($pv->registeredVoting) ? ($pv->registeredVoting == true ? 'Y' : 'N') : 'N';
                $swArray['gpFirstName'] =  isset($pv->gpFirstName) ? ($pv->gpFirstName == true ? 'Y' : 'N') : 'N';
                $swArray['gpLastName'] =  isset($pv->gpLastName) ? ($pv->gpLastName == true ? 'Y' : 'N') : 'N';
                $swArray['gpPhone'] =  isset($pv->gpPhone) ? ($pv->gpPhone == true ? 'Y' : 'N') : 'N';
                $swArray['gpEmail'] =  isset($pv->gpEmail) ? ($pv->gpEmail == true ? 'Y' : 'N') : 'N';

                $pixdb->insert(
                    'members_switch',
                    $swArray,
                    true
                );

                $r->status = 'ok';
                $r->success = 1;
                $r->data = [];
                $r->message = 'Data Updated Successfully!';
            }
        } else {
            $r->message = $mailErroMsg;
        }
    }
}
function validateAndFormatDate($dateString, $format = 'Y-m-d')
{
    if (!$dateString) {
        return null;
    }

    $dateParts = explode('/', esc($dateString));
    if (count($dateParts) === 3 && ctype_digit($dateParts[0]) && ctype_digit($dateParts[1]) && ctype_digit($dateParts[2])) {
        $formattedDate = $dateParts[2] . '-' . str_pad($dateParts[1], 2, '0', STR_PAD_LEFT) . '-' . str_pad($dateParts[0], 2, '0', STR_PAD_LEFT);
        $dateObject = DateTime::createFromFormat($format, $formattedDate);
        if ($dateObject && $dateObject->format($format) === $formattedDate) {
            return $formattedDate;
        }
    }

    return null;
}
