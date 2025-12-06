<?php
(function ($pix, $pixdb, $evg) {
    $member = false;
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
        [$member->firstName  . ' ' . $member->lastName]
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
                    pgNav('membership', 'Membership');
                    pgNav('payments', 'Payments');
                    pgNav('referrals', 'Referrals Made');
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
                echo $member->firstName, ' ', $member->lastName;
                ?>
            </div>

            <table class="data-table">
                <tr>
                    <th>Email</th>
                    <td>
                        <?php
                        echo $member->email;
                        if ($member->verified == 'N') {
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
                    <th>Member ID</th>
                    <td>
                        <?php echo $member->memberId ?? '--'; ?>
                    </td>
                </tr>
                <tr>
                    <th>Prefix</th>
                    <td>
                        <?php
                        if (isset($info->prefix)) {
                            echo isset($pix->PROFILE_OPTIONS['prefix'][$info->prefix]) ? $pix->PROFILE_OPTIONS['prefix'][$info->prefix] . ' ' : '';
                        }
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
                if ($refBy) {
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
                                echo  $fetchState->name;
                            }
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <th>City</th>
                    <td>
                        <?php echo $info->city ?? '--'; ?>
                    </td>
                </tr>
                <tr>
                    <th>Address</th>
                    <td>
                        <?php echo $info->address ?? '--'; ?>
                    </td>
                </tr>
                <tr>
                    <th>Zip</th>
                    <td>
                        <?php echo $info->zipcode ?? '--'; ?>
                    </td>
                </tr>
                <tr>
                    <th>Phone</th>
                    <td>
                        <?php echo $info->phone ?? '--'; ?>
                    </td>
                </tr>
                <tr>
                    <th>Affiliate Organization</th>
                    <td>
                        <?php
                        // echo $info->affilateOrgzn ?? '--';
                        $affInfo = false;
                        if (isset($info->affilateOrgzn)) {
                            $affInfo = $evg->getAffiliation($info->affilateOrgzn, 'name');
                        }
                        echo $affInfo ? $affInfo->name : '--';
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
                <tr>
                    <th>Role</th>
                    <td>
                        <?php
                        if ($matchedRoles) {
                            echo implode('<br/>', $matchedRoles ?? '--');
                        } else {
                            echo 'None';
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