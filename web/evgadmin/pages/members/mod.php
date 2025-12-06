<?php
if (!$pix->canAccess('members-mod')) {
    $pix->addmsg('Access denied!');
    $pix->redirect();
}
(function ($pix, $pixdb, $evg) {
    $mid = esc($_GET['id'] ?? 'new');
    $new = $mid == 'new';
    $mb = new stdClass();
    $selAff = [];
    $selAffInfo = [];
    $volunteers = [];

    if (!$new) {
        $validMember = false;
        if ($mid) {
            $member = $pixdb->getRow(
                'members',
                ['id' => $mid]
            );
            $validMember = !!$member;
        }

        if (!$validMember) {
            $pix->addmsg("Unknown member");
            $pix->redirect("?page=members");
        }
    }

    $info = $pixdb->getRow('members_info', ['member' => $mid]);
    $mbrSwitch = $pixdb->getRow('members_switch', ['member' => $mid]);
    if (!$info) {
        $info = new stdClass();
    }
    if(!$mbrSwitch) {
        $mbrSwitch = new stdClass();
    }

    $cntryData = $pixdb->get(
        'nations',
        ['#SRT' => 'id asc'],
        'id, name'
    )->data;

    $occupData = $pixdb->get(
        'occupation',
        ['#SRT' => 'id asc'],
        'id, name'
    )->data;

    $industryData = $pixdb->get(
        'industry',
        ['#SRT' => 'id asc'],
        'id, name'
    )->data;

    $affltnData = $pixdb->get(
        'affiliates',
        ['#SRT' => 'id asc'],
        'id, name'
    )->data;

    // $affList = [];
    // foreach($affltnData as $row) {
    //     $affList[$row->id] = [
    //         'id' => $row->id,
    //         'name' => $row->name
    //     ];
    // }

    $education = new stdClass();
    $unvData = $pixdb->get(
        'university',
        ['#SRT' => 'id asc'],
        'id, name'
    )->data;

    $universityInfo = [];
    foreach ($unvData as $row) {
        $universityInfo[$row->id] = [
            'id' => $row->id,
            'name' => $row->name
        ];
    }

    $degreeInfo = [];
    foreach ($evg::degree  as $key => $value) {
        $degreeInfo[$key] = [
            'id' => $key,
            'name' => $value
        ];
    }

    if ($mid) {
        $selCertificates = array_flip(
            $pixdb->getCol(
                'members_certification',
                ['member' => $mid],
                'certification'
            )
        );

        $selAffiliates = $pixdb->get(
            'members_affiliation',
            ['member' => $mid],
            'affiliation'
        );

        if($selAffiliates) {
            foreach($selAffiliates->data as $row) {
                $selAff[] = $row->affiliation;
            }
        }
        $affData = $evg->getAffiliations($selAff, 'id, name');

        $selExpertise = array_flip(
            $pixdb->getCol(
                'members_expertise',
                ['member' => $mid],
                'expertise'
            )
        );

        $selEducation = $pixdb->get(
            'members_education',
            ['member' => $mid],
            'university, degree'
        );

        if (!empty($info->volunteerId)) {
            $volInfo = json_decode($info->volunteerId, true);

            if (is_array($volInfo)) {
                foreach ($volInfo as $row) {
                    $volunteers[] = $row;
                }
            } else {
                $volunteers[] = $info->volunteerId;
            }
        }

    }
    loadScript('modules/form-components');
    loadScript('pages/members/mod');
    loadStyle('pages/members/mod');

?>

    <h1>Members</h1>
    <?php
    breadcrumbs(
        [
            'Members',
            '?page=members'
        ],
        !$new ? [
            $member->firstName . ' ' . $member->lastName,
            '?page=members&sec=details&id=' . $member->id
        ] : null,
        [
            $new ? 'Create' : 'Modify'
        ]
    );
    ?>

    <form action="<?php echo ADMINURL, 'actions/anyadmin/'; ?>" method="post" id="memberSave">
        <input type="hidden" name="method" value="member-save" />
        <?php
        if (!$new) {

        ?>
            <input type="hidden" name="mid" value="<?php echo $mid; ?>" />
        <?php
        }
        ?>
        <div class="fm-field">
            <h5 class="fld-label sub-hed">Personal Information</h5>
        </div>

        <div class="fm-field">
            <div class="fld-label">Prefix</div>
            <div class="fld-inp">
                <div>
                    <select name="prefix">
                        <option value="" disabled selected>
                            Choose Prefix
                        </option>
                        <?php
                        $selPrefix = $new ? "" : ($info->prefix ?? '');
                        foreach ($evg::prefix as $ky => $vl) {
                        ?>
                            <option <?php echo $selPrefix == $ky ? 'selected' : ''; ?> value="<?php echo $ky; ?>"><?php echo $vl; ?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
                <?php
                ToggleBtn([
                    'name'   => 'prefixSwitch',
                    'class'  => 'enable-inp',
                    'value'  => 1,
                    'checked'=> $new || (!$new && isset($mbrSwitch->prefix) && $mbrSwitch->prefix === 'Y')
                ]);
                ?>
            </div>
        </div>

        <div class="fm-field">
            <div class="fld-label">First Name</div>
            <div class="fld-inp">
                <div>
                    <input type="text" name="fname" placeholder="First Name" id="firstName" data-type="string" class="" value="<?php echo $new ? "" :  $member->firstName; ?>" size="35">
                </div>
            </div>
        </div>

        <div class="fm-field">
            <div class="fld-label">Middle Name</div>
            <div class="fld-inp">
                <div>
                    <input type="text" name="mname" placeholder="Middle Name" id="middleName" class="" value="<?php echo $new ? "" :  $member->middleName; ?>" size="35">
                </div>
            </div>
        </div>

        <div class="fm-field">
            <div class="fld-label">Last Name</div>
            <div class="fld-inp">
                <div>
                    <input type="text" name="lname" placeholder="Last Name" id="lastName" data-type="string" class="" value="<?php echo $new ? "" :  $member->lastName; ?>" size="35">
                </div>
            </div>
        </div>

        <div class="fm-field">
            <div class="fld-label">Suffix</div>
            <div class="fld-inp">
                <div>
                    <select name="suffix">
                        <option value="" disabled selected>
                            Choose Suffix
                        </option>
                        <?php
                        $selSuffix = $new ? "" : ($info->suffix ?? '');
                        foreach ($evg::suffix as $ky => $vl) {
                            ?>
                            <option <?php echo $selSuffix == $ky ? 'selected' : ''; ?> value="<?php echo $ky; ?>"><?php echo $vl; ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="fm-field">
            <div class="fld-label">Date Of Birth</div>
            <div class="fld-inp">
                <div>
                    <input
                        type="text"
                        id="dtOfBirth"
                        name="dob"
                        value="<?php echo $new ?
                                    '' : (!empty($info->dob)
                                        ?
                                        date('d-m-Y', strtotime($info->dob))
                                        :
                                        '');
                                ?>"
                        size="35"
                        placeholder="Date of birth">
                </div>
                <?php
                ToggleBtn([
                    'name' => 'dobSwitch',
                    'class' => 'enable-inp',
                    'value' => 1,
                    'checked'=> $new || (!$new && isset($mbrSwitch->dob) && $mbrSwitch->dob === 'Y')
                ]);
                ?>
            </div>
        </div>

        <div class="fm-field">
            <div class="fld-label">Racial Identity</div>
            <div class="fld-inp">
                <div>
                    <select name="racialId">
                        <option value="" disabled selected>
                            Choose Racial Identity
                        </option>
                        <?php
                        $selRacialId = $new ? "" : ($info->racialId ?? '');
                        foreach ($evg::racialIdentity as $ky => $vl) {
                        ?>
                            <option <?php echo $selRacialId == $ky ? 'selected' : ''; ?> value="<?php echo $ky; ?>"><?php echo $vl; ?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
                <?php
                ToggleBtn([
                    'name' => 'racialIdentitySwitch',
                    'class' => 'enable-inp',
                    'value' => 1,
                    'checked'=> $new || (!$new && isset($mbrSwitch->racial) && $mbrSwitch->racial === 'Y')
                ]);
                ?>
            </div>
        </div>

        <div class="fm-field">
            <div class="fld-label">Household</div>
            <div class="fld-inp">
                <div>
                    <select name="houseHldId">
                        <option value="" disabled selected>
                            Choose Household
                        </option>
                        <?php
                        $selHousehold = $new ? "" : ($info->houseHldId ?? '');
                        foreach ($evg::houseHold as $ky => $vl) {
                        ?>
                            <option <?php echo $selHousehold == $ky ? 'selected' : ''; ?> value="<?php echo $ky; ?>"><?php echo $vl; ?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
                <?php
                ToggleBtn([
                    'name' => 'householdSwitch',
                    'class' => 'enable-inp',
                    'value' => 1,
                    'checked'=> $new || (!$new && isset($mbrSwitch->household) && $mbrSwitch->household === 'Y')
                ]);
                ?>
            </div>
        </div>

        <div class="fm-field">
            <div class="fld-label">Biography</div>
            <div class="fld-inp">
                <div>
                    <textarea cols="70" name="biography" rows="6" placeholder="Biography" id="biography"><?php echo $new  ? "" : ($info->biography ?? ''); ?></textarea>
                </div>
                <?php
                ToggleBtn([
                    'name' => 'biographySwitch',
                    'class' => 'enable-inp',
                    'value' => 1,
                    'checked'=> $new || (!$new && isset($mbrSwitch->biography) && $mbrSwitch->biography === 'Y')
                ]);
                ?>
            </div>
        </div>

        <div class="fm-field">
            <h5 class="fld-label sub-hed">Contact Information</h5>
        </div>

        <div class="fm-field ">
            <div class="fld-label">Phone Number</div>
            <div class="fld-inp">
                <div>
                    <input type="text" name="phone" placeholder="Phone Number" data-type="string" id="phoneNumber" class="" value="<?php echo $new  ? "" : ($info->phone ?? ''); ?>" size="35">
                </div>
                <?php
                ToggleBtn([
                    'name' => 'phoneNumberSwitch',
                    'class' => 'enable-inp',
                    'value' => 1,
                    'checked'=> $new || (!$new && isset($mbrSwitch->phone) && $mbrSwitch->phone === 'Y')
                ]);
                ?>
            </div>
        </div>

        <div class="fm-field">
            <div class="fld-label">Email</div>
            <div class="fld-inp">
                <div>
                    <input type="text" name="email" placeholder="Email" data-type="string" value="<?php echo $new  ? "" : $member->email; ?>" size="35">
                </div>
                <?php
                ToggleBtn([
                    'name' => 'emailSwitch',
                    'class' => 'enable-inp',
                    'value' => 1,
                    'checked'=> $new || (!$new && isset($mbrSwitch->email) && $mbrSwitch->email === 'Y')
                ]);
                ?>
            </div>
        </div>

        <div class="fm-field">
            <div class="fld-label">Business Email Address</div>
            <div class="fld-inp">
                <div>
                    <input type="text" name="bEmail" placeholder="Business Email Address" data-type="func" data-func="validateBusEmail" data-errormsg="not a valid email address" value="<?php echo $new  ? "" : $info->bEmail; ?>" size="35">
                </div>
                <?php
                ToggleBtn([
                    'name' => 'bEmailSwitch',
                    'class' => 'enable-inp',
                    'value' => 1,
                    'checked'=> $new || (!$new && isset($mbrSwitch->bEmailSwitch) && $mbrSwitch->bEmailSwitch === 'Y')
                ]);
                ?>
            </div>
        </div>

        <div class="fm-field">
            <div class="fld-label">Street Address</div>
            <div class="fld-inp">
                <div>
                    <input type="text" name="address" placeholder="Street Address" id="address" class="" value="<?php echo $new  ? "" : ($info->address ?? ''); ?>" size="35">
                </div>
                <?php
                    ToggleBtn([
                        'name' => 'addressSwitch',
                        'class' => 'enable-inp',
                        'value' => 1,
                        'checked'=> $new || (!$new && isset($mbrSwitch->address) && $mbrSwitch->address === 'Y')
                    ]);
                ?>
            </div>
        </div>

        <div class="fm-field">
            <div class="fld-label">Street Address 2</div>
            <div class="fld-inp">
                <div>
                    <input type="text" name="address2" placeholder="Street Address 2" id="address2" class="" value="<?php echo $new  ? "" : ($info->address2 ?? ''); ?>" size="35">
                </div>
                <?php
                    ToggleBtn([
                        'name' => 'address2Switch',
                        'class' => 'enable-inp',
                        'value' => 1,
                        'checked'=> $new || (!$new && isset($mbrSwitch->address2) && $mbrSwitch->address2 === 'Y')
                    ]);
                ?>
            </div>
        </div>

        <div class="fm-field">
            <div class="fld-label">City</div>
            <div class="fld-inp">
                <div>
                    <input type="text" name="city" placeholder="City" id="city" class="" value="<?php echo $new  ? "" : ($info->city ?? ''); ?>" size="35">
                </div>
                <?php
                    ToggleBtn([
                        'name' => 'citySwitch',
                        'class' => 'enable-inp',
                        'value' => 1,
                        'checked'=> $new || (!$new && isset($mbrSwitch->city) && $mbrSwitch->city === 'Y')
                    ]);
                ?>
            </div>
        </div>

        <div class="fm-field">
            <div class="fld-label">Country</div>
            <div class="fld-inp">
                <div>
                    <select name="country" id="countrySel">
                        <option value="" disabled selected>
                            Choose Country
                        </option>
                        <?php
                        $selCountry = $new ? "" : ($info->country ?? '');
                        foreach ($cntryData as $cntry) {
                            ?>
                            <option <?php echo $selCountry == $cntry->id ? 'selected' : ''; ?> value="<?php echo $cntry->id; ?>"><?php echo $cntry->name; ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </div>
                <?php
                ToggleBtn([
                    'name' => 'countrySwitch',
                    'class' => 'enable-inp',
                    'value' => 1,
                    'checked'=> $new || (!$new && isset($mbrSwitch->country) && $mbrSwitch->country === 'Y')
                ]);
                ?>
            </div>
        </div>

        <div class="fm-field">
            <div class="fld-label">State</div>
            <div class="fld-inp">
                <div>
                    <select name="state" id="stateSel">
                        <option value="" disabled selected>
                            Choose State
                        </option>
                        <?php
                        if (!$new) {
                        $stData = $pixdb->get(
                            'states',
                            [
                                '#SRT' => 'id asc',
                                'nation' => $selCountry
                            ],
                            'id, name'
                            )->data;
                            $selState = $new ? "" : ($info->state ?? '');
                            foreach ($stData as $st) {
                                echo '<option ', $selState == $st->id ? 'selected' : '', ' value="', $st->id, '">', $st->name, '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
                <?php
                ToggleBtn([
                    'name' => 'stateSwitch',
                    'class' => 'enable-inp',
                    'value' => 1,
                    'checked'=> $new || (!$new && isset($mbrSwitch->state) && $mbrSwitch->state === 'Y')
                ]);
                ?>
            </div>
        </div>

        <div class="fm-field">
            <div class="fld-label">Zip</div>
            <div class="fld-inp">
                <div>
                    <input type="text" name="zipcode" placeholder="Zip" id="zip" class="" value="<?php echo $new  ? "" : ($info->zipcode ?? ''); ?>" size="35">
                </div>
                <?php
                ToggleBtn([
                    'name' => 'zipSwitch',
                    'class' => 'enable-inp',
                    'value' => 1,
                    'checked'=> $new || (!$new && isset($mbrSwitch->zipcode) && $mbrSwitch->zipcode === 'Y')
                ]);
                ?>
            </div>
        </div>

        <div class="fm-field">
            <h5 class="fld-label sub-hed">Professional Information</h5>
        </div>

        <div class="fm-field">
            <div class="fld-label">Employer Name</div>
            <div class="fld-inp">
                <div>
                    <input type="text" name="employerName" placeholder="Employer Name" id="employerName" class="" value="<?php echo $new  ? "" : ($info->employerName ?? ''); ?>" size="35">
                </div>
                <?php
                    ToggleBtn([
                        'name' => 'employerNameSwitch',
                        'class' => 'enable-inp',
                        'value' => 1,
                        'checked'=> $new || (!$new && isset($mbrSwitch->employerName) && $mbrSwitch->employerName === 'Y')
                    ]);
                ?>
            </div>
        </div>

        <div class="fm-field">
            <div class="fld-label">Occupation</div>
            <div class="fld-inp">
                <div>
                    <select name="ocpnId">
                        <option value="">
                            Choose Occupation
                        </option>
                        <?php
                        $selOccupation = $new ? '' : ($info->ocpnId ?? '');
                        foreach ($occupData as $occ) {
                            echo '<option ', $selOccupation == $occ->id ? 'selected' : '', ' value="', $occ->id, '">', $occ->name, '</option>';
                        }
                        ?>
                    </select>
                </div>
                <?php
                ToggleBtn([
                    'name' => 'occupationSwitch',
                    'class' => 'enable-inp',
                    'value' => 1,
                    'checked'=> $new || (!$new && isset($mbrSwitch->occupation) && $mbrSwitch->occupation === 'Y')
                ]);
                ?>
            </div>
        </div>

        <div class="fm-field">
            <div class="fld-label">Employment Status</div>
            <div class="fld-inp">
                <div>
                    <select name="emplymntStatId">
                        <option value="" disabled selected>
                            Choose Employment Status
                        </option>
                        <?php
                        $selEmplySts = $new ? "" : ($info->emplymntStatId ?? '');
                        foreach ($evg::employmentStatus as $ky => $vl) {
                        ?>
                            <option <?php echo $selEmplySts == $ky ? 'selected' : ''; ?> value="<?php echo $ky; ?>"><?php echo $vl; ?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
                <?php
                ToggleBtn([
                    'name' => 'employmentStatusSwitch',
                    'class' => 'enable-inp',
                    'value' => 1,
                    'checked'=> $new || (!$new && isset($mbrSwitch->empStatus) && $mbrSwitch->empStatus === 'Y')
                ]);
                ?>
            </div>
        </div>

        <div class="fm-field">
            <div class="fld-label">Volunteer/My Interest</div>
            <div class="fld-inp">
                <div>
                    <?php
                    foreach($evg::volunteerInterest as $ky => $vl) {
                        $checked = !empty($volunteers) && in_array($ky, $volunteers);
                        CheckBox(
                            $vl,
                            'volunteerId',
                            $ky,
                            $checked
                        );
                        echo '<br/>';
                    }
                    ?>
                </div>
                <?php
                ToggleBtn([
                    'name' => 'volunteerInterestSwitch',
                    'class' => 'enable-inp',
                    'value' => 1,
                    'checked'=> $new || (!$new && isset($mbrSwitch->volunteer) && $mbrSwitch->volunteer === 'Y')
                ]);
                ?>
            </div>
        </div>

        <div class="fm-field">
            <div class="fld-label">Industry</div>
            <div class="fld-inp">
                <div>
                    <select name="indstryId">
                        <option value="">
                            Choose Industry
                        </option>
                        <?php
                        $selIndustry = $new ? '' : ($info->indstryId ?? '');
                        foreach ($industryData as $ind) {
                            echo '<option ', $selIndustry == $ind->id ? 'selected' : '', ' value="', $ind->id, '">', $ind->name, '</option>';
                        }
                        ?>
                    </select>
                </div>
                <?php
                ToggleBtn([
                    'name' => 'industrySwitch',
                    'class' => 'enable-inp',
                    'value' => 1,
                    'checked'=> $new || (!$new && isset($mbrSwitch->industry) && $mbrSwitch->industry === 'Y')
                ]);
                ?>
            </div>
        </div>

        <div class="fm-field">
            <div class="fld-label">Education</div>
            <div class="fld-inp inp-flx edu">
                <div>
                    <div class="edc-fld-list" id="edcFlds">
                        <?php
                        if ($selEducation->data && isset($selEducation->data)) {
                            foreach ($selEducation->data as $row) {
                                $selUniversity = $new ? "" : ($row->university ?? '');
                                $selDegree = $new ? "" : ($row->degree ?? '');
                        ?>
                                <div class="edc-fld mb15">
                                    <div class="fld-lf">
                                        <div class="edc-tp">
                                            <select name="university[]">
                                                <option value="">
                                                    Choose University
                                                </option>
                                                <?php
                                                foreach ($unvData as $unv) {
                                                ?>
                                                    <option <?php echo $selUniversity == $unv->id ? 'selected' : ''; ?> value="<?php echo $unv->id; ?>">
                                                        <?php echo $unv->name; ?>
                                                    </option>
                                                <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="">
                                            <select name="degree[]">
                                                <option value="">
                                                    Choose Degree
                                                </option>
                                                <?php
                                                foreach ($evg::degree as $key => $value) {
                                                ?>
                                                    <option <?php echo $selDegree == $key ? 'selected' : ''; ?> value="<?php echo $key; ?>">
                                                        <?php echo $value; ?>
                                                    </option>
                                                <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="fld-rg">
                                        <span class="delete-edc">
                                            <span class="material-symbols-outlined">
                                                delete
                                            </span>
                                            Remove
                                        </span>
                                    </div>
                                </div>
                        <?php
                            }
                        }
                        ?>
                    </div>
                    <?php
                    $eduValid = '';
                    if ($selEducation->data) {
                        $eduValid = true;
                    }
                    ?>
                    <input type="hidden" name="education" value="<?php echo $new ? '' : $eduValid; ?>" id="eduValid" />
                    <span class="pix-btn sm" id="addField">
                        Add educations
                    </span>
                </div>
                <?php
                ToggleBtn([
                    'name' => 'educationSwitch',
                    'class' => 'enable-inp',
                    'value' => 1,
                    'checked'=> $new || (!$new && isset($mbrSwitch->educations) && $mbrSwitch->educations === 'Y')
                ]);
                ?>
            </div>
        </div>

        <div class="fm-field">
            <div class="fld-label">Certification</div>
            <div class="fld-inp inp-flx edu">
                <div>
                    <?php
                    $certificates = $pixdb->get('certification', ['#SRT' => 'name asc'])->data;
                    foreach ($certificates as $cert) {
                        $checked = $selCertificates && isset($selCertificates[$cert->id]);
                        CheckBox(
                            $cert->name,
                            'certifications',
                            $cert->id,
                            $checked
                        );
                        echo '<br/>';
                    }
                    ?>
                </div>
                <?php
                ToggleBtn([
                    'name' => 'certificationSwitch',
                    'class' => 'enable-inp',
                    'value' => 1,
                    'checked'=> $new || (!$new && isset($mbrSwitch->certification) && $mbrSwitch->certification === 'Y')
                ]);
                ?>
            </div>
        </div>

        <div class="fm-field">
            <div class="fld-label">Expertise</div>
            <div class="fld-inp inp-flx edu">
                <div>
                    <?php
                    foreach ($evg::expertise as $ky => $vl) {
                        $checked = $selExpertise && isset($selExpertise[$ky]);
                        CheckBox(
                            $vl,
                            'expertise',
                            $ky,
                            $checked
                        );
                        echo '<br/>';
                    }
                    ?>
                    <div class="mb30">
                        <input type="hidden" />
                    </div>
                </div>
                <?php
                ToggleBtn([
                    'name' => 'expertiseSwitch',
                    'class' => 'enable-inp',
                    'value' => $new ? '' : ($mbrSwitch->expertise ?? ''),
                    'checked'=> $new || (!$new && isset($mbrSwitch->salaryRange) && $mbrSwitch->salaryRange === 'Y')
                ]);
                ?>
            </div>
        </div>

        <div class="fm-field">
            <div class="fld-label">Salary Range</div>
            <div class="fld-inp">
                <div>
                    <select name="slryRangeId">
                        <option value="" disabled selected>
                            Choose Salary Range
                        </option>
                        <?php
                        $selSalRange = $new ? "" : ($info->slryRangeId ?? '');
                        foreach ($evg::salaryRange as $ky => $vl) {
                        ?>
                            <option <?php echo $selSalRange == $ky ? 'selected' : ''; ?> value="<?php echo $ky; ?>"><?php echo $vl; ?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
                <?php
                ToggleBtn([
                    'name' => 'salarySwitch',
                    'class' => 'enable-inp',
                    'value' => 1,
                    'checked'=> $new || (!$new && isset($mbrSwitch->salaryRange) && $mbrSwitch->salaryRange === 'Y')
                ]);
                ?>
            </div>
        </div>

        <div class="fm-field">
            <h5 class="fld-label sub-hed">Organizational Information</h5>
        </div>

        <div class="fm-field">
            <div class="fld-label">Affiliate Organization</div>
            <div class="fld-inp inp-flx edu" id="">
                <div id="affForm">
                <span class="pix-btn wide sm" id="chooseAff">
                    Choose Affiliates
                </span>
                <div id="affHidden">
                    <?php
                    if(!empty($selAffiliates->data)) {
                        foreach($selAffiliates->data as $sAff) {
                            $afName = $affData[$sAff->affiliation]->name ?? '';
                            $selAffInfo[] = [
                                'id' => $affData[$sAff->affiliation]->id ?? '',
                                'name' => $affData[$sAff->affiliation]->name ?? ''
                            ];
                    ?>
                        <input type="hidden" name="affilateOrgzn[]" id="selAff" value="<?php echo $sAff->affiliation; ?>">
                        <p class="aff-name"><?php echo $afName; ?></p>
                    <?php
                        }
                    }
                    ?>
                </div>
                </div>
                <?php
                ToggleBtn([
                    'name' => 'affilateOrgznSwitch',
                    'class' => 'enable-inp',
                    'value' => 1,
                    'checked'=> $new || (!$new && isset($mbrSwitch->affilateOrgzn) && $mbrSwitch->affilateOrgzn === 'Y')
                ]);
                ?>
            </div>
        </div>

        <div class="fm-field">
            <h5 class="fld-label sub-hed sec">Organizational Section</h5>
        </div>

        <div class="fm-field">
            <div class="fld-label sec-sub">Country</div>
            <div class="fld-inp">
                <div>
                    <select name="nationId" id="nationSel">
                        <option value="" disabled selected>
                            Choose Country
                        </option>
                        <?php
                        $selNation = $new ? "" : ($info->nationId ?? '');
                        foreach ($cntryData as $cntry) {
                        ?>
                            <option <?php echo $selNation == $cntry->id ? 'selected' : ''; ?> value="<?php echo $cntry->id; ?>"><?php echo $cntry->name; ?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
                <?php
                ToggleBtn([
                    'name' => 'nationSwitch',
                    'class' => 'enable-inp',
                    'value' => 1,
                    'checked'=> $new || (!$new && isset($mbrSwitch->nation) && $mbrSwitch->nation === 'Y')
                ]);
                ?>
            </div>
        </div>

        <div class="fm-field">
            <div class="fld-label sec-sub">Region</div>
            <div class="fld-inp">
                <div>
                    <select name="regionId" id="regionSel">
                        <option value="" disabled selected>
                            Choose Region
                        </option>
                        <?php
                        if (!$new) {
                            $rgData = $pixdb->get(
                                'regions',
                                [
                                    '#SRT' => 'id asc',
                                    'nation' => $selNation
                                ],
                                'id, name'
                            )->data;
                            $selRegion = $new ? '' : ($info->regionId ?? '');
                            foreach ($rgData as $rg) {
                                echo '<option ', $selRegion == $rg->id ? 'selected' : '', ' value="', $rg->id, '">', $rg->name, '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
                <?php
                ToggleBtn([
                    'name' => 'regionSwitch',
                    'class' => 'enable-inp',
                    'value' => 1,
                    'checked'=> $new || (!$new && isset($mbrSwitch->region) && $mbrSwitch->region === 'Y')
                ]);
                ?>
            </div>
        </div>

        <div class="fm-field">
            <div class="fld-label sec-sub">State</div>
            <div class="fld-inp">
                <div>
                    <select name="orgznSteId" id="orgStateId">
                        <option value="" disabled selected>
                            Choose State
                        </option>
                        <?php
                        if (!$new) {
                            $orgStData = $pixdb->get(
                                'states',
                                [
                                    '#SRT' => 'id asc',
                                    'region' => $selRegion
                                ],
                                'id, name'
                            )->data;
                            $selState = $new ? '' : ($info->orgznSteId ?? '');
                            foreach ($orgStData as $st) {
                                echo '<option ', $selState == $st->id ? 'selected' : '', ' value="', $st->id, '">', $st->name, '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
                <?php
                ToggleBtn([
                    'name' => 'organizationalStateSwich',
                    'class' => 'enable-inp',
                    'value' => 1,
                    'checked'=> $new || (!$new && isset($mbrSwitch->orgztonState) && $mbrSwitch->orgztonState === 'Y')
                ]);
                ?>
            </div>
        </div>

        <div class="fm-field">
            <div class="fld-label sec-sub">Section</div>
            <div class="fld-inp">
                <div>
                    <select name="cruntChptr" class="selected-sec">
                        <option value="" disabled selected>
                            Section
                        </option>
                        <?php
                        if (!$new) {
                            $crSecData = $pixdb->get(
                                'chapters',
                                [
                                    '#SRT' => 'id asc',
                                    'state' => $selState
                                ],
                                'id, name'
                            )->data;
                            $selCrSection = $new ? '' : ($info->cruntChptr ?? '');
                            foreach ($crSecData as $st) {
                                echo '<option ', $selCrSection == $st->id ? 'selected' : '', ' value="', $st->id, '">', $st->name, '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
                <?php
                ToggleBtn([
                    'name' => 'currentChapSwitch',
                    'class' => 'enable-inp',
                    'value' => 1,
                    'checked'=> $new || (!$new && isset($mbrSwitch->crentChapter) && $mbrSwitch->crentChapter === 'Y')
                ]);
                ?>
            </div>
        </div>

        <div class="fm-field position-relative">
            <div class="fld-label">Section of Initiation</div>
            <div class="fld-inp">
                <div>
                    <select name="chptrOfInitn" class="selected-sec">
                        <option value="" disabled selected>
                            Choose Section of Initiation
                        </option>
                        <?php
                        if (!$new) {
                            $scData = $pixdb->get(
                                'chapters',
                                [
                                    '#SRT' => 'id asc',
                                    'state' => $selState
                                ],
                                'id, name'
                            )->data;
                            $selSection = $new ? '' : ($info->chptrOfInitn ?? '');
                            foreach ($scData as $st) {
                                echo '<option ', $selSection == $st->id ? 'selected' : '', ' value="', $st->id, '">', $st->name, '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
                <?php
                ToggleBtn([
                    'name' => 'chapOfIniSwitch',
                    'class' => 'enable-inp',
                    'value' => 1,
                    'checked'=> $new || (!$new && isset($mbrSwitch->chptrOfIntian) && $mbrSwitch->chptrOfIntian === 'Y')
                ]);
                ?>
            </div>
        </div>

        <div class="fm-field">
            <div class="fld-label">Year of Initiation</div>
            <div class="fld-inp">
                <div>
                    <input
                        type="text"
                        id="yearOfIni"
                        name="yearOfInitn"
                        value="<?php echo $new ?
                                    '' : (!empty($info->yearOfInitn)
                                        ?
                                        date('d / F / Y', strtotime($info->yearOfInitn))
                                        :
                                        '');
                                ?>"
                        size="35"
                        placeholder="Year of initiation">
                </div>
                <?php
                ToggleBtn([
                    'name' => 'yearOfIniSwitch',
                    'class' => 'enable-inp',
                    'value' => 1,
                    'checked'=> $new || (!$new && isset($mbrSwitch->yerOfInitn) && $mbrSwitch->yerOfInitn === 'Y')
                ]);
                ?>
            </div>
        </div>

        <div class="fm-field">
            <div class="fld-label">Registered Voting Precinct / Ward / District</div>
            <div class="fld-inp">
                <div>
                    <input type="text" name="registeredVoting" data-type="string" placeholder="Registered Voting Precinct / Ward / District" id="registeredVoting" class="" value="<?php echo $new  ? "" : ($info->registeredVoting ?? ''); ?>" size="35">
                </div>
                <?php
                    ToggleBtn([
                        'name' => 'registeredVotingSwitch',
                        'class' => 'enable-inp',
                        'value' => 1,
                        'checked'=> $new || (!$new && isset($mbrSwitch->registeredVoting) && $mbrSwitch->registeredVoting === 'Y')
                    ]);
                ?>
            </div>
        </div>

        <div class="fm-field">
            <div class="fld-label sub-hed">
                <?php
                CheckBox(
                    'I consent that I am the Guardian or Parent completing this Youth’s Profile',
                    'gpConsent',
                    1,
                    isset($info->gpConsent) && $info->gpConsent === 'Y' && ($new || !$new),
                    'isMinor'
                );
                ?>
            </div>
        </div>

        <div class="fm-field gp-fields <?php echo isset($info->gpConsent) && $info->gpConsent === 'Y' && ($new || !$new) ? 'show' : ''; ?>">
            <h5 class="fld-label sub-hed">Guardian / Parental information</h5>
        </div>

        <div class="fm-field gp-fields <?php echo isset($info->gpConsent) && $info->gpConsent === 'Y' && ($new || !$new) ? 'show' : ''; ?>">
            <div class="fld-label">First Name</div>
            <div class="fld-inp">
                <div>
                    <input type="text" name="gpFirstName" placeholder="First Name" id="gpFirstName" class="" value="<?php echo $new  ? "" : ($info->gpFirstName ?? ''); ?>" size="35">
                </div>
                <?php
                    ToggleBtn([
                        'name' => 'gpFirstNameSwitch',
                        'class' => 'enable-inp',
                        'value' => 1,
                        'checked'=> $new || (!$new && isset($mbrSwitch->gpFirstName) && $mbrSwitch->gpFirstName === 'Y')
                    ]);
                ?>
            </div>
        </div>

        <div class="fm-field gp-fields <?php echo isset($info->gpConsent) && $info->gpConsent === 'Y' && ($new || !$new) ? 'show' : ''; ?>">
            <div class="fld-label">Last Name</div>
            <div class="fld-inp">
                <div>
                    <input type="text" name="gpLastName" placeholder="Last Name" id="gpLastName" class="" value="<?php echo $new  ? "" : ($info->gpLastName ?? ''); ?>" size="35">
                </div>
                <?php
                    ToggleBtn([
                        'name' => 'gpLastNameSwitch',
                        'class' => 'enable-inp',
                        'value' => 1,
                        'checked'=> $new || (!$new && isset($mbrSwitch->gpLastName) && $mbrSwitch->gpLastName === 'Y')
                    ]);
                ?>
            </div>
        </div>

        <div class="fm-field gp-fields <?php echo isset($info->gpConsent) && $info->gpConsent === 'Y' && ($new || !$new) ? 'show' : ''; ?>">
            <div class="fld-label">Phone Number</div>
            <div class="fld-inp">
                <div>
                    <input type="text" name="gpPhone" placeholder="Phone Number" id="gpPhone" data-type="func" data-func="validateGPhone" data-errormsg="not a valid phone number" class="" value="<?php echo $new  ? "" : ($info->gpPhone ?? ''); ?>" size="35">
                </div>
                <?php
                    ToggleBtn([
                        'name' => 'gpPhoneSwitch',
                        'class' => 'enable-inp',
                        'value' => 1,
                        'checked'=> $new || (!$new && isset($mbrSwitch->gpPhone) && $mbrSwitch->gpPhone === 'Y')
                    ]);
                ?>
            </div>
        </div>

        <div class="fm-field gp-fields <?php echo isset($info->gpConsent) && $info->gpConsent === 'Y' && ($new || !$new) ? 'show' : ''; ?>">
            <div class="fld-label">Email</div>
            <div class="fld-inp">
                <div>
                    <input type="text" name="gpEmail" placeholder="Email" id="gpEmail" data-type="func" data-func="validateGEmail" data-errormsg="not a valid email address" class="" value="<?php echo $new  ? "" : ($info->gpEmail ?? ''); ?>" size="35">
                </div>
                <?php
                    ToggleBtn([
                        'name' => 'gpEmailSwitch',
                        'class' => 'enable-inp',
                        'value' => 1,
                        'checked'=> $new || (!$new && isset($mbrSwitch->gpEmail) && $mbrSwitch->gpEmail === 'Y')
                    ]);
                ?>
            </div>
        </div>

        <div class="fm-field">
            <div class="fld-label sub-hed">
                <?php
                CheckBox(
                    'Deceased',
                    'deceased',
                    1,
                    $new || (!$new && $info->deceased == 'Y'),
                    isset($info->deceased)
                );
                ?>
            </div>
        </div>

        <?php
        if (isset($info->customData)) {
            $customData = json_decode($info->customData, true);
            if (is_array($customData)) {
                foreach ($customData as $field) {
                    if (isset($field['type']) && $field['label']) {
                        $inpName = $pix->makestring(10, 'ln');
        ?>
                        <div class="fm-field">
                            <div class="fld-label"><?php echo $field['label']; ?></div>
                            <div class="fld-inp">
                                <?php
                                echo "<input type='hidden' name='cuztm_{$inpName}_field' value='" . htmlspecialchars(json_encode($field), ENT_QUOTES, 'UTF-8') . "' />";
                                if ($field['type'] == 'text') {
                                    echo '<input type="text" name="cuztm_' . $inpName . '_value" value="' . ($field['value'] ?? '') . '" />';
                                } elseif ($field['type'] == 'select') {
                                    $options = isset($field['options']) ? $field['options'] : [];
                                    $selected = isset($field['value']) ? $field['value'] : '';

                                    echo '<select name="cuztm_' . $inpName . '_value">';
                                    foreach ($options as $option) {
                                        echo '<option ', $selected == $option ? 'selected' : '', ' value="', esc($option), '">', esc($option), '</option>';
                                    }
                                    echo '</select>';
                                }
                                ?>
                            </div>
                            <div class="field-actions">
                                <span class="field-delete">
                                    <span class="material-symbols-outlined icn">delete</span>
                                </span>
                            </div>
                        </div>
        <?php
                    }
                }
            }
        }
        ?>
        <div id="customFields"></div>
        <div class="submit-box">
            <input type="submit" class="pix-btn lg site bold-500" value="SAVE">
            <input type="button" class="pix-btn lg  bold-500" value="Add New Field" id="addNewField">
        </div>
    </form>
    <?php
    $education->university = $universityInfo;
    $education->degree = $degreeInfo;
    $mb->selAffInfo = !empty($selAffInfo) ? $selAffInfo : [];
    ?>
    <script type="text/javascript">
        var mb = <?php echo json_encode($mb); ?>;
        var education = <?php echo json_encode($education); ?>
    </script>
<?php
})($pix, $pixdb, $evg);
