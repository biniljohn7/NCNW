<?php
(function ($pix, $pixdb, $evg) {
    $member = false;
    $OPTIONS = $pix->PROFILE_OPTIONS;

    if (isset($_GET['id'])) {
        $id = esc($_GET['id']);
        if ($id) {
            $member = $pixdb->getRow(
                'members',
                ['id' => $id]
            );
        }
    }
    if (!$member) {
        $pix->addmsg('Invalid member.');
        $pix->redirect('?page=members');
    }

    $pgData = (object)[
        'memberId' => $id
    ];

    loadStyle('pages/members/details');
    loadScript('pages/members/details');

    $info = $pixdb->getRow('members_info', ['member' => $id]);
    if (!$info) {
        $info = new stdClass();
    }

    $membership = $pixdb->getRow('memberships', [
        'member' => $id,
        '#QRY' => '((giftedBy IS NOT NULL AND accepted = "Y") OR giftedBy IS NULL)',
        '#SRT' => 'id desc'
    ]);

    if ($membership) {
        if ($membership && !empty($membership->amtCalc)) {
            $membership->amtCalc = json_decode($membership->amtCalc);
        } else {
            $membership->amtCalc = null;
        }
    }



    $refBy = $pixdb->getRow(
        [
            ['members', 'm', 'id'],
            ['members_referrals', 'r', 'refBy']
        ],
        ['r.user' => $id],
        'm.id, m.firstName, m.lastName'
    );

    $memberRoles = explode(',', $member->role ?? '');
    $matchedRoles = [];

    foreach ($memberRoles as $roleKey) {
        if (isset($evg::memberRole[$roleKey])) {
            $matchedRoles[] = $evg::memberRole[$roleKey];
        }
    }
    loadStyle("common/jquery.datetimepicker");
    loadScript("common/jquery.datetimepicker");

?>

    <h1>Members</h1>

    <a name="general"></a>

    <?php
    breadcrumbs(
        ['Members', '?page=members'],
        [$member->firstName . ' ' . $member->middleName . ' ' . $member->lastName]
    );
    ?>


    <div class="usr-details">
        <div class="lt-col" id="stickySide">
            <div class="user-avatar">
                <?php
                if ($member->avatar) {
                ?>
                    <img src="<?php echo $evg->getAvatar($member->avatar, '350x350'); ?>" />
                <?php
                } else {
                    $name = $member->firstName . $member->lastName;
                    $fsLetter = strtoupper($name[0] ?? '');
                ?>
                    <div class="empty-thumb letter-<?php echo strtolower($fsLetter); ?>">
                        <div class="txt">
                            <?php echo $fsLetter; ?>
                        </div>
                    </div>
                <?php
                }
                ?>
            </div>

            <div class="sd-section">
                <div class="sc-heading">
                    <span class="hd-text">
                        Status
                    </span>
                </div>

                <span class="usr-attrs">
                    <span class="u-attr">
                        <?php echo ($info->pointsBalance ?? 0) ?: 0; ?>
                        Points
                    </span>
                    <span class="u-attr">
                        <?php echo ($info->referralsTotal ?? 0) ?: 0; ?>
                        Referrals Made
                    </span>
                    <span class="u-attr atr-<?php echo $member->verified == 'Y' ? 'green' : 'red'; ?>">
                        <?php echo $member->verified == 'Y' ? '' : 'Un-'; ?>Verified
                    </span>

                    <?php
                    if ($membership && $membership->expiry) {
                        if (time() <= strtotime($membership->expiry)) {
                    ?>
                            <span class="u-attr atr-green">
                                Active Membership
                            </span>
                        <?php
                        } else {
                        ?>
                            <span class="u-attr atr-red">
                                Membership Expired
                            </span>
                    <?php
                        }
                    }
                    ?>
                    <?php
                    if (
                        $membership  &&
                        ($membership->planId == 2 || $membership->planId == 3) &&
                        $membership->pinStatus
                    ) {

                        $shipped = (isset($membership->pinStatus) && $membership->pinStatus == 'shipped') ? true : false;
                    ?>
                        <span class="u-attr atr-<?php echo $shipped ? 'green' : 'grey'; ?>" id="pinDisb">
                            Pin Distribution <?php echo ($shipped && $membership->shpdOn) ? 'on : ' . date('d-M-Y h:i:s A', strtotime($membership->shpdOn)) : ''; ?>
                        </span>
                    <?php
                    }
                    ?>
                </span>
                <?php
                if (
                    $membership  &&
                    ($membership->planId == 2 || $membership->planId == 3) &&
                    $membership->pinStatus &&
                    $membership->pinStatus == 'pending'
                ) {
                ?>

                    <div class="pin-status-drop">

                        <span class="pin-heading">Pin Distribution Status</span>

                        <span class="pix-btn site" id="pinStatus">Mark as Shipped</span>

                    </div>
                <?php } ?>

            </div>

            <div class=" sd-section">
                <div class="sc-heading">
                    <span class="hd-text">
                        Sections
                    </span>
                </div>
                <div class="pg-navs">
                    <?php
                    function pgNav($link, $label)
                    {
                    ?>
                        <div class="pg-nav">
                            <a href="#<?php echo $link; ?>" class="pg-btn">
                                <?php echo $label; ?>
                            </a>
                        </div>
                    <?php
                    }

                    pgNav('general', 'General Info');
                    pgNav('contact-info', 'Contact Information');
                    pgNav('professional-info', 'Professional Information');
                    pgNav('gp-info', 'Parental Information');
                    pgNav('org-info', 'Organizational Information');
                    pgNav('ofcr-positions', 'Officer Positions');
                    pgNav('membership', 'Membership');
                    pgNav('payments', 'Payments');
                    pgNav('referrals', 'Referrals Made');
                    $pix->canAccess('members-mod') ? pgNav('reset-pwd', 'Password') : '';
                    $pix->canAccess('members-mod') ? pgNav('manage', 'Manage') : '';
                    ?>
                </div>
            </div>
        </div>
        <div class="rt-col">
            <?php
            if ($pix->canAccess('members-mod')) {
            ?>
                <div class="text-right">
                    <a href="<?php echo DOMAIN, 'evgadmin/?page=members&sec=mod&id=', $member->id; ?>" class="pix-btn site sm ">
                        <span class="material-symbols-outlined icn">
                            edit
                        </span> Edit
                    </a>
                </div>
            <?php }
            ?>
            <div class="heading-1 bold-600 mb20">
                <?php
                echo
                isset($info->prefix) ? $evg::prefix[$info->prefix] : '', ' ',
                $member->firstName, ' ',
                $member->middleName, ' ',
                $member->lastName, '',
                isset($info->suffix) ? ', ' . $evg::suffix[$info->suffix] : '';

                ?>
            </div>

            <table class="data-table">
                <tr>
                    <th>Member ID</th>
                    <td>
                        <?php echo $member->memberId ?? '--'; ?>
                    </td>
                </tr>
                <tr>
                    <th>Date Of Birth</th>
                    <td>
                        <?php
                        echo isset($info->dob) ? date('d/m/Y', strtotime($info->dob)) : '--';
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>Racial Identity</th>
                    <td>
                        <?php
                        echo isset($info->racialId) ? $evg::racialIdentity[$info->racialId] : '--';
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>Household</th>
                    <td>
                        <?php
                        echo isset($info->houseHldId) ? $evg::houseHold[$info->houseHldId] : '--';
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>Biography</th>
                    <td>
                        <?php
                        echo $info->biography ?? '--';
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>Registered On</th>
                    <td>
                        <?php echo date('d M Y, g:ia', strtotime($member->regOn)); ?>
                    </td>
                </tr>
                <tr>
                    <th>Referral Code</th>
                    <td>
                        <?php echo $info->refcode ?? '--'; ?>
                    </td>
                </tr>
                <?php
                if ($refBy && isset($refBy->firstName, $refBy->lastName)) {
                ?>
                    <tr>
                        <th>Referred By</th>
                        <td>
                            <a href="<?php echo $pix->adminURL, '?page=members&sec=details&id=', $refBy->id; ?>">
                                <?php echo $refBy->firstName, ' ', $refBy->lastName; ?>
                            </a>
                        </td>
                    </tr>
                <?php
                }
                ?>
                <tr>
                    <th>NCNW SECTION/GUILD</th>
                    <td>
                        <?php
                        $chapterInfo = false;
                        if (isset($info->cruntChptr)) {
                            $chapterInfo = $evg->getChapter($info->cruntChptr, 'name, nation, region, state');
                        }
                        echo $chapterInfo ? $chapterInfo->name : '--';
                        if ($chapterInfo && $chapterInfo->state) {
                            $state = $evg->getState($chapterInfo->state, 'name');
                            if ($state) {
                                echo '<div><span class="text-g4">State : </span>', $state->name, '</div>';
                            }
                        }
                        if ($chapterInfo && $chapterInfo->region) {
                            $region = $evg->getRegion($chapterInfo->region, 'name');
                            if ($region) {
                                echo '<div><span class="text-g4">Region : </span>', $region->name, '</div>';
                            }
                        }
                        if ($chapterInfo && $chapterInfo->nation) {
                            $nation = $evg->getNation($chapterInfo->nation, 'name');
                            if ($nation) {
                                echo '<div><span class="text-g4">Country : </span>', $nation->name, '</div>';
                            }
                        }
                        ?>
                    </td>
                </tr>
            </table>

            <a name="contact-info"></a>
            <div class="heading-4 bold-500 mb20 pt50">
                Contact Information
            </div>

            <table class="data-table">
                <tr>
                    <th>Phone Number</th>
                    <td>
                        <?php
                        echo $info->phone ?? '--';
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>
                        <?php
                        echo $member->email ?? '--';
                        if ($member->email == 'N') {
                        ?>
                            <br />
                            <span class="text-red text-09">
                                ( un-verified )
                            </span>
                        <?php
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>Business Email Address</th>
                    <td>
                        <?php
                        echo $info->bEmail ?? '--';
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>Street Address</th>
                    <td>
                        <?php
                        echo $info->address ?? '--';
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>Street Address 2</th>
                    <td>
                        <?php
                        echo $info->address2 ?? '--';
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>City</th>
                    <td>
                        <?php
                        echo $info->city ?? '--';
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>State</th>
                    <td>
                        <?php
                        if (isset($info->state)) {
                            $fetchState = $pixdb->getRow(
                                'states',
                                ['id' => $info->state],
                                'name'
                            );
                            if ($fetchState) {
                                echo $fetchState->name;
                            } else {
                                echo '--';
                            }
                        } else {
                            echo '--';
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>Country</th>
                    <td>
                        <?php
                        if (isset($info->country)) {
                            $fetchCountry = $pixdb->getRow(
                                'nations',
                                ['id' => $info->country],
                                'name'
                            );
                            if ($fetchCountry) {
                                echo  $fetchCountry->name;
                            } else {
                                echo '--';
                            }
                        } else {
                            echo '--';
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>Zip Code</th>
                    <td>
                        <?php echo $info->zipcode ?? '--'; ?>
                    </td>
                </tr>
                <tr>
                    <th>Registered Voting Precinct / Ward / District</th>
                    <td>
                        <?php echo $info->registeredVoting ?? '--'; ?>
                    </td>
                </tr>
            </table>

            <a name="professional-info"></a>
            <div class="heading-4 bold-500 mb20 pt50">
                Professional Information
            </div>

            <table class="data-table">
                <tr>
                    <th>Employer Name</th>
                    <td>
                        <?php echo $info->employerName ?? '--'; ?>
                    </td>
                </tr>
                <tr>
                    <th>Employment Status</th>
                    <td>
                        <?php echo isset($info->emplymntStatId) ? $evg::employmentStatus[$info->emplymntStatId] : '--'; ?>
                    </td>
                </tr>
                <tr>
                    <th>Business Email Address</th>
                    <td>
                        <?php
                        echo $info->bEmail ?? '--';
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>Occupation</th>
                    <td>
                        <?php
                        if (isset($info->ocpnId)) {
                            $fetchOccupation = $pixdb->getRow(
                                'occupation',
                                ['id' => $info->ocpnId],
                                'name'
                            );
                            if ($fetchOccupation) {
                                echo  $fetchOccupation->name;
                            } else {
                                echo '--';
                            }
                        } else {
                            echo '--';
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>Salary Range</th>
                    <td>
                        <?php
                        echo isset($info->slryRangeId) ? $evg::salaryRange[$info->slryRangeId] : '--';
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>Industry</th>
                    <td>
                        <?php
                        if (isset($info->indstryId)) {
                            $fetchIndustry = $pixdb->getRow(
                                'industry',
                                ['id' => $info->indstryId],
                                'name'
                            );
                            if ($fetchIndustry) {
                                echo  $fetchIndustry->name;
                            } else {
                                echo '--';
                            }
                        } else {
                            echo '--';
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>Education</th>
                    <td>
                        <?php
                        $educations = [];
                        $choseUnicity = [];

                        $eduInfo = $pixdb->get(
                            'members_education',
                            ['member' => $member->id],
                            'university, degree'
                        );

                        if ($eduInfo->data) {
                            $uarr = [];
                            foreach ($eduInfo->data as $row) {
                                $uarr[] = $row->university;
                            }
                            $uarr = array_unique(array_filter(array_map('esc', $uarr)));
                            $university = $pixdb->get(
                                'university',
                                ['#QRY' => 'id in(' . implode(', ', $uarr) . ')']
                            )->data;
                            foreach ($university as $row) {
                                $choseUnicity[$row->id] = $row->name;
                            }

                            $optnInfo = $OPTIONS['degree'] ?? array();
                            foreach ($eduInfo->data as $row) {
                                $tmp = [];
                                $tmp['university'] = array(
                                    'name' => $choseUnicity[$row->university] ?? '',
                                    'id' => intval($row->university)
                                );
                                $tmp['degree'] = array(
                                    'name' =>  $optnInfo[$row->degree] ?? '',
                                    'id' => intval($row->degree)
                                );

                                $educations[] = $tmp;
                            }

                            if (!empty($educations)) {
                                foreach ($educations as $row) {
                                    echo '<div class="education-block">';
                                    echo '<div>
                                        <span class="edu-lbl">
                                        University
                                        </span><br>
                                        <span>
                                        ' . $row['university']['name'] . '
                                        </span>
                                        </div>';
                                    echo '<div>
                                        <span class="edu-lbl">
                                        Degree
                                        </span><br>
                                        <span>
                                        ' . $row['degree']['name'] . '
                                        </span>
                                        </div>';
                                    echo '</div>';
                                }
                            } else {
                                echo '--';
                            }
                        } else {
                            echo '--';
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>Certifications</th>
                    <td>
                        <?php
                        $chooseCertifcn = [];

                        $crtnfnInfo = $pixdb->get(
                            'members_certification',
                            ['member' => $member->id]
                        );

                        if (!empty($crtnfnInfo->data)) {
                            $cerArr = [];
                            foreach ($crtnfnInfo->data as $row) {
                                if (!empty($row->certification)) {
                                    $cerArr[] = intval($row->certification);
                                }
                            }

                            $cerArr = array_unique(array_filter(array_map('esc', $cerArr)));
                            if (!empty($cerArr)) {
                                $certification = $pixdb->get(
                                    'certification',
                                    ['#QRY' => 'id IN(' . implode(', ', $cerArr) . ')']
                                )->data ?? [];

                                if (!empty($certification)) {
                                    foreach ($certification as $row) {
                                        if (!empty($row->id) && !empty($row->name)) {
                                            $chooseCertifcn[$row->id] = $row->name;
                                        }
                                    }
                                }
                            }

                            if (!empty($chooseCertifcn)) {
                                foreach ($crtnfnInfo->data as $row) {
                                    echo ($chooseCertifcn[$row->certification] ?? '--') . '<br>';
                                }
                            } else {
                                echo "No certification found.<br>";
                            }
                        } else {
                            echo "No certification records available.<br>";
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>Expertise</th>
                    <td>
                        <?php
                        $exprtsInfo = $pixdb->get(
                            'members_expertise',
                            ['member' => $member->id]
                        );

                        if ($exprtsInfo->data) {
                            $optnInfo = $OPTIONS['expertise'] ?? [];
                            foreach ($exprtsInfo->data as $row) {
                                echo ($optnInfo[$row->expertise] ?? '--') . '<br>';
                            }
                        } else {
                            echo '--';
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>Volunteer Interests</th>
                    <td>
                        <?php
                        if (isset($info->volunteerId)) {
                            $volunteerInfo = json_decode($info->volunteerId, true);

                            if (!is_array($volunteerInfo)) {
                                $volunteerInfo = [$info->volunteerId];
                            }

                            $volntInfo = $OPTIONS['volunteerInterest'] ?? [];
                            foreach ($volunteerInfo as $row) {
                                echo ($volntInfo[$row] ?? '--') . '<br>';
                            }
                        } else {
                            echo '--';
                        }
                        ?>
                    </td>
                </tr>
            </table>

            <a name="gp-info"></a>
            <div class="heading-4 bold-500 mb20 pt50">
                Guardian / Parental information
            </div>

            <table class="data-table">
                <tr>
                    <th>First Name</th>
                    <td>
                        <?php echo $info->gpFirstName ?? '--'; ?>
                    </td>
                </tr>
                <tr>
                    <th>Last Name</th>
                    <td>
                        <?php echo $info->gpLastName ?? '--'; ?>
                    </td>
                </tr>
                <tr>
                    <th>Phone Number</th>
                    <td>
                        <?php echo $info->gpPhone ?? '--'; ?>
                    </td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>
                        <?php echo $info->gpEmail ?? '--'; ?>
                    </td>
                </tr>
            </table>

            <a name="org-info"></a>
            <div class="heading-4 bold-500 mb20 pt50">
                Organizational Information
            </div>

            <table class="data-table">
                <tr>
                    <th>Current Section</th>
                    <td>
                        <?php
                        if (isset($info->cruntChptr)) {
                            $fetchCrnChap = $pixdb->getRow(
                                'chapters',
                                ['id' => $info->cruntChptr],
                                'name'
                            );
                            if ($fetchCrnChap) {
                                echo $fetchCrnChap->name;
                            } else {
                                echo '--';
                            }
                        } else {
                            echo '--';
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>Section of Initiation</th>
                    <td>
                        <?php
                        if (isset($info->chptrOfInitn)) {
                            $fetchChapInt = $pixdb->getRow(
                                'chapters',
                                ['id' => $info->chptrOfInitn],
                                'name'
                            );
                            if ($fetchChapInt) {
                                echo $fetchChapInt->name;
                            } else {
                                echo '--';
                            }
                        } else {
                            echo '--';
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>Collegiate Section</th>
                    <td>
                        <?php
                        $colInfo = false;
                        if (isset($info->collegiateSection)) {
                            $colInfo = $evg->getCollgueSection($info->collegiateSection, 'name');
                        }
                        echo $colInfo ? $colInfo->name : '--';
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>Affiliate Organization</th>
                    <td>
                        <?php
                        $chooseAffln = [];

                        $affInfo = $pixdb->get(
                            'members_affiliation',
                            ['member' => $member->id]
                        );
                        if (!empty($affInfo->data)) {
                            $affArr = [];
                            foreach ($affInfo->data as $row) {
                                if (!empty($row->affiliation)) {
                                    $affArr[] = intval($row->affiliation);
                                }
                            }

                            $affArr = array_unique(array_filter(array_map('esc', $affArr)));
                            if (!empty($affArr)) {
                                $affiliates = $pixdb->get(
                                    'affiliates',
                                    ['#QRY' => 'id IN(' . implode(', ', $affArr) . ')']
                                )->data ?? [];

                                if (!empty($affiliates)) {
                                    foreach ($affiliates as $row) {
                                        if (!empty($row->id) && !empty($row->name)) {
                                            $chooseAffln[$row->id] = $row->name;
                                        }
                                    }
                                }
                            }

                            if (!empty($chooseAffln)) {
                                foreach ($affInfo->data as $row) {
                                    echo ($chooseAffln[$row->affiliation] ?? '--') . '<br>';
                                }
                            } else {
                                echo "--";
                            }
                        } else {
                            echo '--';
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>Date of Initiation</th>
                    <td>
                        <?php
                        if (isset($info) && isset($info->yearOfInitn)) {
                            echo date('d/m/Y', strtotime($info->yearOfInitn));
                        } else {
                            echo '--';
                        }
                        ?>
                    </td>
                </tr>
            </table>

            <a name="ofcr-positions"></a>
            <div class="heading-4 bold-500 mb20 pt50">
                Elected/Appointed Officer Position
            </div>

            <table class="data-table">
                <tr>
                    <th>Appointed NCNW Officer Positions</th>
                    <td>
                        <?php
                        $mbrRoles = loadModule('members')->getRoles($member);
                        if (!empty($mbrRoles)) {
                            foreach ($mbrRoles as $row) {
                                echo $row->role . ' (' . $row->circle . ')' . '<br>';
                            }
                        } else {
                            echo '--';
                        }
                        ?>
                    </td>
                </tr>
            </table>

            <a name="membership"></a>

            <div class="heading-4 bold-500 mb20 pt50 membership-sec">
                <div class="sec-left">
                    Membership
                </div>
                <?php if ($pix->canAccess('members-mod')) { ?>
                    <div class="sec-right">
                        <a href="<?php echo ADMINURL, '/?page=members&sec=mod-membership&mid=', $id, '&id=', $membership ? $membership->id : 'new'; ?>" class="pix-btn sm">
                            EDIT
                        </a>
                    </div>
                <?php } ?>
            </div>
            <?php
            if ($membership) {
            ?>
                <table class="data-table">
                    <tr>
                        <th>Plan</th>
                        <td>
                            <?php echo $membership->planName; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Created</th>
                        <td>
                            <?php echo $membership->created ? date('j M Y, g:ia', strtotime($membership->created)) : '--'; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Valid Upto</th>
                        <td>
                            <?php echo $membership->expiry ? date('j M Y', strtotime($membership->expiry)) : '--'; ?>
                        </td>
                    </tr>
                    <tr>
                        <th>National Dues</th>
                        <td>
                            <?php echo dollar($membership->amtCalc->natDue ?? 0, 1, 0); ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Payment Processing Fee</th>
                        <td>
                            <?php echo dollar($membership->amtCalc->natLtFee ?? 0, 1, 0); ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Section Donation</th>
                        <td>
                            <?php echo dollar($membership->amtCalc->chapDon ?? 0, 1, 0); ?>
                        </td>
                    </tr>
                    <tr>
                        <th>National Donation</th>
                        <td>
                            <?php echo dollar($membership->amtCalc->natDon ?? 0, 1, 0); ?>
                        </td>
                    </tr>
                    <?php
                    if (
                        isset($membership->installment) &&
                        in_array($membership->installment, [2, 4], true)
                    ) {
                    ?>
                        <tr>
                            <th>Installment</th>
                            <td>
                                <?php
                                $installmentsMap = [
                                    2 => 'Biannual Installments (Automatic)',
                                    4 => 'Quarterly Installments (Automatic)'
                                ];

                                echo $installmentsMap[$membership->installment] ?? '--';
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Current Paid</th>
                            <td>
                                <?php echo $membership->instllmntPhase ? $membership->instllmntPhase . ' installment' : 0; ?>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                    <tr>
                        <th>Total Amount</th>
                        <td>
                            <span class="bold-600">
                                <?php echo dollar($membership->amount ?? 0, 1, 0); ?>
                            </span>
                        </td>
                    </tr>
                </table>
            <?php
                // 
            } else {
                echo '<div class="">
                No Membership
            </div>';
            }
            ?>

            <a name="payments"></a>
            <div class="heading-4 bold-500 mb20 pt50">
                Payments
            </div>
            <?php
            $payments = $pixdb->get(
                'transactions',
                [
                    'member' => $id,
                    '#SRT' => 'id desc limit 11'
                ],
                'date, title, amount, status'
            );
            $listCount = count($payments->data);
            if ($listCount > 0) {
                $haveMore = $listCount > 10;
                if ($haveMore) {
                    $payments->data = array_slice($payments->data, 0, 10);
                }
            ?>
                <table class="list-table payments-tbl">
                    <tr>
                        <th>Date & Time</th>
                        <th>Title</th>
                        <th class="text-right">Amount</th>
                    </tr>
                    <?php
                    foreach ($payments->data as $pay) {
                    ?>
                        <tr>
                            <td>
                                <?php echo date('j M Y, g:ia', strtotime($pay->date)); ?>
                            </td>
                            <td>
                                <?php echo $pay->title; ?>
                            </td>
                            <td class="text-right">
                                <span class="bold-600">
                                    <?php echo dollar($pay->amount, 1, 0); ?>
                                </span>
                                <?php
                                if ($pay->status != 'success') {
                                ?>
                                    <br />
                                    <span class="text-orange text-09">
                                        pending
                                    </span>
                                <?php
                                }
                                ?>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </table>
                <?php
                if ($haveMore) {
                ?>
                    <div class="">
                        <a href="?page=transactions" class="pix-btn sm">
                            View All
                        </a>
                    </div>
                <?php
                }
                ?>
            <?php
                // 
            } else {
                echo '<div>No Payments Found</div>';
            }
            ?>

            <a name="referrals"></a>
            <div class="heading-4 bold-500 mb20 pt50">
                Referrals Made
            </div>
            <?php
            $referrals = $pixdb->get(
                [
                    ['members', 'm', 'id'],
                    ['members_referrals', 'r', 'user']
                ],
                [
                    'r.refBy' => $id,
                    '#SRT' => 'm.id desc limit 11'
                ],
                'm.id, 
            m.firstName, 
            m.lastName,
            m.regOn,
            r.refCode,
            r.pt4share,
            r.pt4use'
            );
            $listCount = count($referrals->data);
            if ($listCount > 0) {
            ?>
                <table class="list-table rfl-made-list mb10">
                    <tr>
                        <th>Member</th>
                        <th>Registered On</th>
                        <th>Code</th>
                        <th class="text-right">Points for Share</th>
                        <th class="text-right">Points for Use</th>
                    </tr>
                    <?php
                    foreach ($referrals->data as $ref) {
                    ?>
                        <tr>
                            <td>
                                <a href="<?php echo $pix->adminURL, '?page=members&sec=details&id=', $ref->id; ?>">
                                    <?php echo $ref->firstName, ' ', $ref->lastName; ?>
                                </a>
                            </td>
                            <td>
                                <?php echo date('j M Y', strtotime($ref->regOn)); ?>
                            </td>
                            <td class="code">
                                <?php echo $ref->refCode; ?>
                            </td>
                            <td class="text-right pts4share">
                                <?php echo $ref->pt4share; ?>
                            </td>
                            <td class="text-right pts4use">
                                <?php echo $ref->pt4use; ?>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </table>
            <?php
            } else {
                echo '<div>No Referrals Found</div>';
            }
            ?>

            <?php
            if ($pix->canAccess('members-mod')) {
            ?>
                <a name="reset-pwd"></a>
                <div class="heading-4 bold-500 mb20 pt50">
                    Password
                </div>
                <div class="">
                    <div class="mb5">
                        <?php
                        echo $member->password
                            ? '********'
                            : 'Password not set';
                        ?>
                    </div>
                    <div>
                        <span class="pix-btn sm" id="resetPwd">
                            Reset
                        </span>
                    </div>
                </div>
            <?php
            }
            ?>

            <?php if ($pix->canAccess('members-mod')) { ?>
                <a name="manage"></a>
                <div class="heading-4 bold-500 mb20 pt50">
                    Manage
                </div>
                <div class="enable-chk mb30">
                    <span class="heading-6 bold-400">
                        Enable
                    </span>
                    <?php
                    ToggleBtn([
                        'id' => 'membStatus',
                        'class' => 'enable-inp',
                        'value' => 1,
                        'checked' => ($member->enabled == 'Y' ? 'checked' : '')
                    ]);
                    ?>
                </div>
            <?php
                DangerZone(
                    'Remove Member',
                    'Do you wish to remove this member and their data?',
                    'Yes. Remove',
                    '#'
                );
            }
            ?>
        </div>
    </div>

    <script>
        const pgData = <?php echo json_encode($pgData); ?>;
    </script>
<?php
})($pix, $pixdb, $evg);
?>