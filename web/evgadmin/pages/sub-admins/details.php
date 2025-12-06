<?php
$admin = false;
if (isset($_GET['id'])) {
    $id = esc($_GET['id']);
    if ($id) {
        $admin = $pixdb->getRow(
            'admins',
            ['id' => $id]
        );
    }
}
if (
    $admin &&
    $admin->type == 'admin'
) {
    $admin = false;
}
if (!$admin) {
    $pix->addmsg('Invalid admin.');
    $pix->redirect('?page=sub-admins');
}

$memberData = null;
if ($admin->memberid) {
    $moreInfo = $evg->getMemberInfo(
        [$admin->memberid],
        ['memberId,role'],
        [],
        true
    );
    if ($moreInfo && isset($moreInfo[$admin->memberid])) {
        $memberData = $moreInfo[$admin->memberid];
        if ($memberData->section) {
            $section = $memberData->section;
            $memberData->section = $section->name;
        }

        if ($memberData->affilate) {
            $affilate = $memberData->affilate;
            $affilates = '';
            foreach ($affilate as $aff) {
                $affilates .= ($affilates != '' ? ', ' : '') . $aff->name;
            }
            $memberData->affilate = $affilates;
        }

        $roles = loadModule('members')->getRoles(
            (object)[
                'id' => $admin->memberid,
                'role' => $memberData->role
            ]
        );
        $rlStr = '';
        foreach ($roles as $rl) {
            $rlStr .= ($rlStr != '' ? ', ' : '') . $rl->role;
        }
        $memberData->roles = $rlStr;

        $memberData->membership = '--';
        $membership = $pixdb->getRow('memberships', [
            'member' => $admin->memberid,
            '#QRY' => '((giftedBy IS NOT NULL AND accepted = "Y") OR giftedBy IS NULL)',
            '#SRT' => 'id desc'
        ], 'planName');
        if ($membership) {
            $memberData->membership = $membership->planName;
        }
    }
}

loadStyle('pages/sub-admins/details');
loadScript('pages/sub-admins/details');
?>
<h1>Sub Admin</h1>

<a name="general"></a>

<?php
breadcrumbs(
    ['Sub Admin', '?page=sub-admins'],
    [$admin->name]
);

$fsLetter = strtoupper($admin->name[0]);

$pgData = (object)[
    'adminId' => $id
];
?>

<div class="usr-details">
    <div class="lt-col" id="stickySide">
        <div class="user-avatar">
            <div class="empty-thumb letter-<?php echo strtolower($fsLetter); ?>">
                <div class="txt">
                    <?php echo $fsLetter; ?>
                </div>
            </div>
        </div>

        <div class="sd-section">
            <div class="sc-heading">
                <span class="hd-text">
                    Status
                </span>
            </div>

            <span class="usr-attrs">
                <span class="u-attr atr-<?php echo $admin->enabled == 'Y' ? 'green' : 'red'; ?>">
                    <?php echo $admin->enabled == 'Y' ? 'En' : 'Dis', 'abled';
                    ?>
                </span>
            </span>
        </div>
        <?php
        if ($admin->type == 'sub-admin') {
        ?>
            <div class="sd-section">
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
                    pgNav('permissions', 'Permissions');
                    pgNav('manage', 'Manage');
                    ?>
                </div>
            </div>
        <?php
        }
        ?>
    </div>
    <div class="rt-col">
        <div class="heading-1 bold-600 mb20">
            <?php echo $admin->name; ?>
        </div>

        <table class="data-table">
            <tr>
                <th>Email</th>
                <td>
                    <?php echo $admin->email; ?>
                </td>
            </tr>
            <tr>
                <th>Member ID</th>
                <td>
                    <?php echo $memberData && $memberData->memberId ? $memberData->memberId : '--'; ?>
                </td>
            </tr>
            <tr>
                <th>Elected/Appointed Officer Position</th>
                <td>
                    <?php echo $memberData && $memberData->roles ? $memberData->roles : '--'; ?>
                </td>
            </tr>
            <tr>
                <th>Membership category</th>
                <td>
                    <?php echo $memberData && $memberData->membership ? $memberData->membership : '--'; ?>
                </td>
            </tr>
            <tr>
                <th>Current section</th>
                <td>
                    <?php echo $memberData && $memberData->section ? $memberData->section : '--'; ?>
                </td>
            </tr>
            <tr>
                <th>Affiliate organization</th>
                <td>
                    <?php echo $memberData && $memberData->affilate ? $memberData->affilate : '--'; ?>
                </td>
            </tr>
            <tr>
                <th>Username</th>
                <td>
                    <?php echo $admin->username; ?>
                </td>
            </tr>
            <tr>
                <th>Phone</th>
                <td>
                    <?php echo $admin->phone; ?>
                </td>
            </tr>
        </table>
        <?php
        if ($admin->type == 'sub-admin') {
        ?>
            <div class="pt20">
                <a href="<?php echo ADMINURL, '?page=sub-admins&sec=mod&id=', $id; ?>" class="pix-btn rounded sm">
                    <span class="material-symbols-outlined">
                        edit
                    </span>
                    Modify details
                </a>
            </div>
            <a name="permissions"></a>

            <div class="heading-4 bold-500 mb20 pt50">
                Permissions
            </div>
            <div class="permissions-list">
                <?php
                $perms = [
                    [
                        'benefit',
                        'Benefit Management',
                        'Admins can oversee and manage member benefits, including the addition, modification, and removal of benefit options to enhance member experience.'
                    ],
                    [
                        'events',
                        'Event Coordination',
                        'Admins are responsible for organizing and managing events, from planning and scheduling to registration and follow-up activities.'
                    ],
                    [
                        'location',
                        'Location Oversight',
                        'Admins can manage location-specific information, ensuring accurate and up-to-date details for all physical and virtual locations related to the organization.'
                    ],
                    [
                        'members',
                        'Member Administration',
                        'Admins can only view, filter, and export member profiles.'
                    ],
                    $admin->memberid ?
                        [
                            'elect',
                            'Leadership Election',
                            'Admins are allowed to manage the leadership roles.'
                        ] : null,
                    [
                        'transactions',
                        'Transaction Management',
                        'Admins oversee all financial transactions, ensuring accurate processing, tracking, and reporting of payments and receipts.'
                    ],
                    [
                        'career',
                        'Career Services',
                        'Admins manage career-related resources, including job postings, and career development programs for members.'
                    ],
                    [
                        'advocacy',
                        'Advocacy Initiatives',
                        'Admins lead and manage advocacy efforts, working on campaigns, communications, and actions to support the organization’s mission and goals.'
                    ],
                    [
                        'paid-plans',
                        'Paid Plans',
                        'Admins can create, update, and manage paid plans, offering various tiers of membership and benefits to suit different needs.'
                    ],
                    [
                        'point-rules',
                        'Point System Rules',
                        'Admins set and manage the rules for any point-based systems, including earning, redeeming, and tracking points for member activities and engagement.'
                    ],
                    [
                        'messages',
                        'Message Center',
                        'Admins manage internal and external communications, including sending, receiving, and organizing messages for efficient communication flow.'
                    ],
                    [
                        'cms-pages',
                        'Content Management System (CMS)',
                        'Admins oversee the CMS, including creating, editing, and organizing web pages and content to ensure accurate and up-to-date information is presented.'
                    ],
                    [
                        'email-templates',
                        'Email Templates',
                        'Admins have access to email templates allows for creating, editing, and managing templates.'
                    ],
                    [
                        'contact-enquiries',
                        'Contact Inquiries',
                        'Admins handle all contact inquiries, managing and responding to questions, concerns, and feedback from members and the public.'
                    ],
                    [
                        'statistics',
                        'Analytics and Statistics',
                        'Admins have access to and can analyze various statistics and data related to member activities, transactions, event attendance, and other key metrics.'
                    ],
                    [
                        'helpdesk',
                        'Help Desk',
                        'Admins have access to and can create change request or report about bugs, and able to monitor the workflow.'
                    ],
                ];

                $admPerms = explode(',', $admin->perms ?: '');
                $hdRoles = [
                    'developer' => 'Developer',
                    'project-coordinators' => 'Project Coordinators',
                    'ncnw-team' => 'NCNW Team'
                ];
                $perms = array_filter($perms);
                foreach ($perms as $pms) {
                ?>
                    <div class="perm-row">
                        <div class="perm-chk">
                            <?php
                            ToggleBtn([
                                'class' => 'perm-inp',
                                'value' => $pms[0],
                                'checked' => in_array($pms[0], $admPerms)
                            ]);
                            ?>
                        </div>
                        <div class="perm-body">
                            <div class="pm-title">
                                <?php echo $pms[1]; ?>
                            </div>
                            <div class="pm-body">
                                <?php echo $pms[2]; ?>
                            </div>
                            <?php
                            $act = '';
                            if ($pms[0] == 'helpdesk') {
                                $act = in_array('helpdesk', $admPerms) ? 'active' : '';
                            ?>
                                <div class="hd-role-selector <?php echo $act; ?>">
                                    <?php foreach ($hdRoles as $key => $role): ?>
                                        <?php
                                        Radio(
                                            $role,
                                            'hd-role',
                                            $key,
                                            in_array('helpdesk/' . $key, $admPerms)
                                        );
                                        ?>
                                    <?php endforeach; ?>
                                </div>
                            <?php
                            } elseif ($pms[0] == 'members') {
                                $act = in_array('members', $admPerms) ? 'active' : '';
                            ?>
                                <div class="members-mod <?php echo $act; ?>">
                                    <?php
                                    ToggleBtn([
                                        'class' => 'perm-inp',
                                        'value' => 'members-mod',
                                        'checked' => in_array('members-mod', $admPerms),
                                        'id' => 'membersModAccess'
                                    ]);
                                    ?>
                                    <div>
                                        Admins can create or modify member profiles.
                                    </div>
                                </div>
                            <?php
                            }
                            ?>
                        </div>
                    </div>
                <?php
                }
                ?>
            </div>
        <?php
        }
        ?>
        <a name="manage"></a>
        <div class="heading-4 bold-500 mb20 pt50">
            Manage
        </div>
        <?php
        if ($isSuperAdmin) {
        ?>
            <div class="mb30">
                <a class="pix-btn rounded site confirm"
                    href="<?php echo ADMINURL, 'actions/admin/?', http_build_query([
                                'method' => 'promote-sub-admin',
                                'adminid' => $admin->id
                            ]); ?>"
                    class="pix-btn site rounded mr5">
                    Grant Super Admin Access
                </a>
            </div>
        <?php
        }
        DangerZone(
            'Remove Sub-admin',
            'Do you wish to remove this sub-admin and their data?',
            'Yes. Remove',
            ADMINURL . 'actions/admin/?method=sub-admin-delete&id=' . $id
        );
        ?>
    </div>
</div>

<script>
    const pgData = <?php echo json_encode($pgData); ?>;
</script>