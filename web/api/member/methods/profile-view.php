<?php
devMode();
if ($lgUser->id) {
    $cntryArray = array();
    $expertises = array();
    $volunteers = array();
    $educations = array();
    $stateArray = array();
    $ctprArray = array();
    $userCertifns = array();
    $affiliations = array();
    $ocpnInfo = false;
    $indstryInfo = false;
    $regionInfo = false;
    $affiliationInfo = false;
    $collegiateInfo = false;

    $OPTIONS = $pix->PROFILE_OPTIONS;

    $memberInfo = $pixdb->get(
        'members_info',
        [
            'member' => $lgUser->id,
            'single' => 1
        ]
    );

    if ($memberInfo) {
        $mbrSwitchs = $pixdb->get(
            'members_switch',
            [
                'member' => $lgUser->id,
                'single' => 1
            ]
        );

        /////   country
        $cntry = array();
        $cntry[] = $memberInfo->country ?? '';
        $cntry[] = $memberInfo->nationId ?? '';
        $cntry = array_unique(array_filter(array_map('esc', $cntry)));
        if (!empty($cntry)) {
            $cntryInfo = $pixdb->get(
                'nations',
                [
                    '#QRY' => 'id in (' . implode(', ', $cntry) . ')'
                ],
                'id, name'
            )->data;
            foreach ($cntryInfo as $info) {
                $cntryArray[$info->id] = $info->name;
            }
        }

        ////   expertises
        $exprtsInfo = $pixdb->get(
            'members_expertise',
            [
                'member' => $memberInfo->member
            ]
        );
        if ($exprtsInfo->data) {
            $optnInfo = $OPTIONS['expertise'] ?? array();
            foreach ($exprtsInfo->data as $info) {
                $expertises[] = array(
                    'name' => $optnInfo[$info->expertise] ?? '',
                    'id' => intval($info->expertise)
                );
            }
        }

        // volunteers
        if ($memberInfo->volunteerId) {
            $volunteerInfo = json_decode($memberInfo->volunteerId);

            $volntInfo = $OPTIONS['volunteerInterest'] ?? [];
            foreach ($volunteerInfo as $info) {
                $volunteers[] = [
                    'name' => $volntInfo[$info] ?? '',
                    'id' => intval($info)
                ];
            }
        }

        /////  occupation
        if ($memberInfo->ocpnId) {
            $ocpnInfo = $pixdb->get(
                'occupation',
                [
                    'id' => $memberInfo->ocpnId,
                    'single' => 1
                ],
                'name'
            );
        }

        /////  industry
        if ($memberInfo->indstryId) {
            $indstryInfo = $pixdb->get(
                'industry',
                [
                    'id' => $memberInfo->indstryId,
                    'single' => 1
                ],
                'name'
            );
        }

        /////   education
        $choseUnicity = array();
        $eduInfo = $pixdb->get(
            'members_education',
            [
                'member' => $memberInfo->member
            ],
            'university, degree'
        );
        if ($eduInfo->data) {
            $uarr = array();
            foreach ($eduInfo->data as $info) {
                $uarr[] = $info->university;
            }
            $uarr = array_unique(array_filter(array_map('esc', $uarr)));
            $university = $pixdb->get(
                'university',
                [
                    '#QRY' => 'id in (' . implode(', ', $uarr) . ')'
                ]
            )->data;
            foreach ($university as $info) {
                $choseUnicity[$info->id] = $info->name;
            }

            $optnInfo = $OPTIONS['degree'] ?? array();
            foreach ($eduInfo->data as $info) {
                $tmp = array();
                $tmp['university'] = array(
                    'name' => $choseUnicity[$info->university] ?? '',
                    'id' => intval($info->university)
                );
                $tmp['degree'] = array(
                    'name' =>  $optnInfo[$info->degree] ?? '',
                    'id' => intval($info->degree)
                );

                $educations[] = $tmp;
            }
        }

        /////   state
        $ste = array();
        $ste[] = $memberInfo->state ?? '';
        $ste[] = $memberInfo->orgznSteId ?? '';
        $ste = array_unique(array_filter(array_map('esc', $ste)));
        if (!empty($ste)) {
            $stateInfo = $pixdb->get(
                'states',
                [
                    '#QRY' => 'id in (' . implode(', ', $ste) . ')'
                ],
                'id, name'
            )->data;
            foreach ($stateInfo as $info) {
                $stateArray[$info->id] = $info->name;
            }
        }

        /////   chapter
        $ctpr = array();
        $ctpr[] = $memberInfo->chptrOfInitn ?? '';
        $ctpr[] = $memberInfo->cruntChptr ?? '';
        $ctpr = array_unique(array_filter(array_map('esc', $ctpr)));
        if (!empty($ctpr)) {
            $ctprInfo = $pixdb->get(
                'chapters',
                [
                    '#QRY' => 'id in (' . implode(', ', $ctpr) . ')'
                ],
                'id, name'
            )->data;
            foreach ($ctprInfo as $info) {
                $ctprArray[$info->id] = $info->name;
            }
        }

        /////  certifications
        $chooseCertifcn = array();
        $crtfnInfo = $pixdb->get(
            'members_certification',
            [
                'member' => $memberInfo->member
            ]
        );
        if ($crtfnInfo->data) {
            $cerArr = array();
            foreach ($crtfnInfo->data as $info) {
                $cerArr[] = $info->certification;
            }
            $cerArr = array_unique(array_filter(array_map('esc', $cerArr)));
            $certification = $pixdb->get(
                'certification',
                [
                    '#QRY' => 'id in (' . implode(', ', $cerArr) . ')'
                ]
            )->data;
            foreach ($certification as $info) {
                $chooseCertifcn[$info->id] = $info->name;
            }
            foreach ($crtfnInfo->data as $info) {
                $userCertifns[] = array(
                    'name' => $chooseCertifcn[$info->certification] ?? '',
                    'id' => intval($info->certification)
                );
            }
        }

        /////  region
        if ($memberInfo->regionId) {
            $regionInfo = $pixdb->get(
                'regions',
                [
                    'id' => $memberInfo->regionId,
                    'single' => 1
                ],
                'name'
            );
        }

        /////  affiliation
        // if ($memberInfo->affilateOrgzn) {
        //     $affiliationInfo = $pixdb->get(
        //         'affiliates',
        //         [
        //             'id' => $memberInfo->affilateOrgzn,
        //             'single' => 1
        //         ],
        //         'name'
        //     );
        // }
        $chooseAffln = [];
        $afflnInfo = $pixdb->get(
            'members_affiliation',
            [
                'member' => $memberInfo->member
            ]
        );
        if ($afflnInfo->data) {
            $affArr = [];
            foreach ($afflnInfo->data as $info) {
                $affArr[] = $info->affiliation;
            }
            $affArr = array_unique(array_filter(array_map('esc', $affArr)));
            $affiliates = $pixdb->get(
                'affiliates',
                [
                    '#QRY' => 'id in (' . implode(', ', $affArr) . ')'
                ]
            )->data;
            foreach ($affiliates as $info) {
                $chooseAffln[$info->id] = $info->name;
            }
            foreach ($afflnInfo->data as $info) {
                $affiliations[] = array(
                    'name' => $chooseAffln[$info->affiliation] ?? '',
                    'id' => intval($info->affiliation)
                );
            }
        }

        /////  collegiate section
        if ($memberInfo->collegiateSection) {
            $collegiateInfo = $pixdb->get(
                'collegiate_sections',
                [
                    'id' => $memberInfo->collegiateSection,
                    'single' => 1
                ],
                'name'
            );
        }
    }

    // calculate age
    $dob = $memberInfo && $memberInfo->dob ? $memberInfo->dob : null;
    $birthDay = '';
    $ageRange = '';
    if ($dob) {
        $dobDate = new DateTime($dob);
        $today   = new DateTime('today');
        $age     = $dobDate->diff($today)->y;

        // Member Birth Date & Month
        $birthDay = $dobDate->format('d F');

        // Age Drop-Down Range mapping
        if ($age <= 12) {
            $ageRange = '0-12';
        } elseif ($age <= 17) {
            $ageRange = '13-17';
        } elseif ($age <= 25) {
            $ageRange = '18-25';
        } elseif ($age <= 40) {
            $ageRange = '26-40';
        } elseif ($age <= 60) {
            $ageRange = '41-60';
        } else {
            $ageRange = '61+';
        }
    }

    //membership
    $status = 'Inactive';
    $mmbrshpCategory = '';
    $joinedDate = '';
    $LifeMembership = null;
    $LegacyLifeMmmshp = null;
    $mmbrshpValidity = null;
    $getMmbrshp = $pixdb->get('memberships', [
        'member' => $lgUser->id,
        '#QRY' => '((giftedBy IS NOT NULL AND accepted = "Y") OR giftedBy IS NULL)',
        '#SRT' => 'id desc'
    ], 'id, planName, enabled, expiry, created, payStatus, installment, instllmntPhase, amount, pinStatus')->data;

    foreach ($getMmbrshp as $idx => $mmbrshp) {
        if ($idx == 0) {
            $mmbrshpCategory = $mmbrshp->planName;
            $mmbrshpValidity = $mmbrshp->expiry ?  $mmbrshp->expiry : 'Lifelong';
            $joinedDate = $mmbrshp->created ?? null;
            if (
                $mmbrshp->enabled === 'Y' &&
                (
                    (!empty($mmbrshp->expiry) && time() <= strtotime($mmbrshp->expiry)) ||
                    empty($mmbrshp->expiry)
                )
            ) {
                $status = 'Active';
            }
        }
        if (preg_match('/legacy/i', $mmbrshp->planName)) {
            $LegacyLifeMmmshp = (object)[
                'payStatus' => $mmbrshp->payStatus,
                'paid' => $mmbrshp->installment ? ($mmbrshp->amount / $mmbrshp->installment) * $mmbrshp->instllmntPhase : $mmbrshp->amount,
                'pinStatus' => $mmbrshp->pinStatus
            ];
        } elseif (preg_match('/life/i', $mmbrshp->planName)) {
            $LifeMembership = (object)[
                'payStatus' => $mmbrshp->payStatus,
                'paid' => $mmbrshp->installment ? ($mmbrshp->amount / $mmbrshp->installment) * $mmbrshp->instllmntPhase : $mmbrshp->amount,
                'pinStatus' => $mmbrshp->pinStatus
            ];
        }
    }

    // ncnw officer positions
    $ldrshpRoles = [];
    if ($lgUser->role) {
        $ldrshpRoles = loadModule('members')->getRoles($lgUser);
    }

    // admin panel
    $isAdminPanel = $pixdb->getRow(
        'admins',
        ['memberid' => $lgUser->id],
        'id'
    );

    $r->status = 'ok';
    $r->success = 1;
    $r->data = [
        'visible' => [
            'suffix' => ($mbrSwitchs->suffix ?? 'Y') == 'Y',
            'profileImage' => ($mbrSwitchs->profileImg ?? 'Y') == 'Y',
            'educations' => ($mbrSwitchs->educations ?? 'Y') == 'Y',
            'currentChapter' => ($mbrSwitchs->crentChapter ?? 'Y') == 'Y',
            'city' => ($mbrSwitchs->city ?? 'Y') == 'Y',
            'email' => ($mbrSwitchs->email ?? 'Y') == 'Y',
            'occupation' => ($mbrSwitchs->occupation ?? 'Y') == 'Y',
            'lastName' => ($mbrSwitchs->lastName ?? 'Y') == 'Y',
            'country' => ($mbrSwitchs->country ?? 'Y') == 'Y',
            'nation' => ($mbrSwitchs->nation ?? 'Y') == 'Y',
            'yearOfInitiation' => ($mbrSwitchs->yerOfInitn ?? 'Y') == 'Y',
            'prefix' => ($mbrSwitchs->prefix ?? 'Y') == 'Y',
            'state' => ($mbrSwitchs->state ?? 'Y') == 'Y',
            'expertise' => ($mbrSwitchs->expertise ?? 'Y') == 'Y',
            'leadershipRole' => ($mbrSwitchs->ldrShpRole ?? 'Y') == 'Y',
            'industry' => ($mbrSwitchs->industry ?? 'Y') == 'Y',
            'biography' => ($mbrSwitchs->biography ?? 'Y') == 'Y',
            'phoneNumber' => ($mbrSwitchs->phone ?? 'Y') == 'Y',
            'region' => ($mbrSwitchs->region ?? 'Y') == 'Y',
            'statusUpdate' => ($mbrSwitchs->stusUpdate ?? 'Y') == 'Y',
            'address' => ($mbrSwitchs->address ?? 'Y') == 'Y',
            'address2' => ($mbrSwitchs->address2 ?? 'Y') == 'Y',
            'household' => ($mbrSwitchs->household ?? 'Y') == 'Y',
            'employmentStatus' => ($mbrSwitchs->empStatus ?? 'Y') == 'Y',
            'racialIdentity' => ($mbrSwitchs->racial ?? 'Y') == 'Y',
            'volunteerInterest' => ($mbrSwitchs->volunteer ?? 'Y') == 'Y',
            'dob' => ($mbrSwitchs->dob ?? 'Y') == 'Y',
            'organizationalState' => ($mbrSwitchs->orgztonState ?? 'Y') == 'Y',
            'memberCode' => ($mbrSwitchs->memberCode ?? 'Y') == 'Y',
            'salaryRange' => ($mbrSwitchs->salaryRange ?? 'Y') == 'Y',
            'chapterOfInitiation' => ($mbrSwitchs->chptrOfIntian ?? 'Y') == 'Y',
            'certification' => ($mbrSwitchs->certification ?? 'Y') == 'Y',
            'firstName' => ($mbrSwitchs->firstName ?? 'Y') == 'Y',
            'zipcode' => ($mbrSwitchs->zipcode ?? 'Y') == 'Y',
            'affilateOrgzn' => ($mbrSwitchs->affilateOrgzn ?? 'Y') == 'Y',
            // 'collegiateSection' => ($mbrSwitchs->collegiateSection ?? 'Y') == 'Y',
            'middleName' => ($mbrSwitchs->middleName ?? 'Y') == 'Y',
            'businessEmail' => ($mbrSwitchs->businessEmailAddress ?? 'Y') == 'Y',
            'employerName' => ($mbrSwitchs->employerName ?? 'Y') == 'Y',
            'regVotWrdDist' => ($mbrSwitchs->registeredVoting ?? 'Y') == 'Y',
            'gpFirstName' => ($mbrSwitchs->gpFirstName ?? 'Y') == 'Y',
            'gpLastName' => ($mbrSwitchs->gpLastName ?? 'Y') == 'Y',
            'gpPhone' => ($mbrSwitchs->gpPhone ?? 'Y') == 'Y',
            'gpEmail' => ($mbrSwitchs->gpEmail ?? 'Y') == 'Y',
        ],
        'profile' => [
            'adminPanel' => $isAdminPanel ? true : false,
            'adminPanelLink' => $isAdminPanel ? ADMINURL : null,
            'lastName' => html_entity_decode($lgUser->lastName ?? '', ENT_QUOTES, 'UTF-8'),
            'country' => [
                'name' => $memberInfo && $memberInfo->country ? ($cntryArray[$memberInfo->country] ?? '') : '',
                'id' => intval($memberInfo->country ?? 0)
            ],
            'expertises' => $expertises,
            'occupation' => [
                'name' => $ocpnInfo->name ?? '',
                'id' => intval($memberInfo->ocpnId ?? 0)
            ],
            'city' => $memberInfo->city ?? '',
            'nation' => [
                'name' => $memberInfo && $memberInfo->nationId ? ($cntryArray[$memberInfo->nationId] ?? '') : '',
                'id' => intval($memberInfo->nationId ?? 0)
            ],
            'prefix' => [
                'name' => $memberInfo && $memberInfo->prefix ? ($OPTIONS['prefix'][$memberInfo->prefix] ?? '') : '',
                'id' => intval($memberInfo->prefix ?? 0)
            ],
            'yearOfInitiation' => $memberInfo && $memberInfo->yearOfInitn ? date('d/m/Y', strtotime($memberInfo->yearOfInitn)) : '',
            'dob' => $memberInfo && $memberInfo->dob ? date('d/m/Y', strtotime($memberInfo->dob)) : '',
            'birthDay' => $birthDay,
            'ageRange' => $ageRange,
            'industry' => [
                'name' => $indstryInfo->name ?? '',
                'id' => intval($memberInfo->indstryId ?? 0)
            ],
            'educations' => $educations,
            'profileImage' => $evg->getAvatar($lgUser->avatar),
            'suffix' => [
                'name' => $memberInfo && $memberInfo->suffix ? ($OPTIONS['suffix'][$memberInfo->suffix] ?? '') : '',
                'id' => intval($memberInfo->suffix ?? 0)
            ],
            'leadershipRole' => $memberInfo->ldrShipRole ?? '',
            'state' => [
                'name' => $memberInfo && $memberInfo->state ? ($stateArray[$memberInfo->state] ?? '') : '',
                'id' => intval($memberInfo->state ?? 0)
            ],
            'currentChapter' => [
                'name' => $memberInfo && $memberInfo->cruntChptr ? ($ctprArray[$memberInfo->cruntChptr] ?? '') : 'National Member',
                'id' => intval($memberInfo->cruntChptr ?? 0)
            ],
            'email' => $lgUser->email,
            'memberCode' => $lgUser->memberId,
            'salaryRange' => [
                'name' => $memberInfo && $memberInfo->slryRangeId ? ($OPTIONS['salaryRange'][$memberInfo->slryRangeId] ?? '') : '',
                'id' => intval($memberInfo->slryRangeId ?? 0)
            ],
            'organizationalState' => [
                'name' => $memberInfo && $memberInfo->orgznSteId ? ($stateArray[$memberInfo->orgznSteId] ?? '') : '',
                'id' => intval($memberInfo->orgznSteId ?? 0)
            ],
            'address' => $memberInfo->address ?? '',
            'address2' => $memberInfo->address2 ?? '',
            'statusUpdate' => $memberInfo->statusUpdate ?? '',
            'biography' => $memberInfo->biography ?? '',
            'certifications' => $userCertifns,
            'chapterOfInitiation' => [
                'name' => $memberInfo && $memberInfo->chptrOfInitn ? ($ctprArray[$memberInfo->chptrOfInitn] ?? '') : '',
                'id' => intval($memberInfo->chptrOfInitn ?? 0)
            ],
            'zipcode' => $memberInfo->zipcode ?? '',
            'firstName' => html_entity_decode($lgUser->firstName ?? '', ENT_QUOTES, 'UTF-8'),
            'middleName' => html_entity_decode($lgUser->middleName ?? '', ENT_QUOTES, 'UTF-8'),
            'phoneNumber' => $memberInfo->phone ?? '',
            'household' => [
                'name' => $memberInfo && $memberInfo->houseHldId ? ($OPTIONS['houseHold'][$memberInfo->houseHldId] ?? '') : '',
                'id' => intval($memberInfo->houseHldId ?? 0)
            ],
            'employmentStatus' => [
                'name' => $memberInfo && $memberInfo->emplymntStatId ? ($OPTIONS['employmentStatus'][$memberInfo->emplymntStatId] ?? '') : '',
                'id' => intval($memberInfo->emplymntStatId ?? 0)
            ],
            'racialIdentity' => [
                'name' => $memberInfo && $memberInfo->racialId ? ($OPTIONS['racialIdentity'][$memberInfo->racialId] ?? '') : '',
                'id' => intval($memberInfo->racialId ?? 0)
            ],
            'volunteers' => $volunteers,
            'region' => [
                'name' => $regionInfo->name ?? '',
                'id' => intval($memberInfo->regionId ?? 0)
            ],
            'affilateOrgzn' => $affiliations,
            'collegiateSection' => [
                'name' => $collegiateInfo->name ?? '',
                'id' => intval($memberInfo->collegiateSection ?? 0)
            ],
            'role' => $ldrshpRoles,
            'businessEmail' => $memberInfo->bEmail ?? '',
            'employerName' => $memberInfo->employerName ?? '',
            'regVotWrdDist' => $memberInfo->registeredVoting ?? '',
            'gpConsent' => $memberInfo->gpConsent ?? 'N',
            'gpFirstName' => $memberInfo->gpFirstName ?? '',
            'gpLastName' => $memberInfo->gpLastName ?? '',
            'gpPhone' => $memberInfo->gpPhone ?? '',
            'gpEmail' => $memberInfo->gpEmail ?? '',
            'joinedDate' => $lgUser->regOn ? date('jS M Y', strtotime($lgUser->regOn)) : '',
            'mmbrshpCategory' => $mmbrshpCategory,
            'mmbrshpStatus' => $status,
            'mmbrshpExpiry' => $mmbrshpValidity,
            'deceased' => $memberInfo && $memberInfo->deceased ? ($memberInfo->deceased == 'Y' ? true : false) : false,
            'legacyTracker' => $LegacyLifeMmmshp,
            'lifeTracker' => $LifeMembership
        ]
    ];
    $r->message = 'Data Retrieved Successfully!';
}
