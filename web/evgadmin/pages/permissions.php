<?php
(function ($pix, $pixdb, $evg) {

    $type = esc($_GET['type'] ?? '');
    $permissions = false;

    if ($type) {
        $permissions = $pixdb->getRow(
            'permissions',
            ['type' => $type],
            'id, type, perms'
        );
    }

    loadStyle('pages/permissions/permissions');
    loadScript('pages/permissions/permissions');
?>
    <h1>Permissions</h1>
    <?php
    breadcrumbs(
        ['Permissions']
    );
    ?>
    <form action="<?php echo ADMINURL, 'actions/anyadmin/'; ?>" method="post" id="permForm">
        <input type="hidden" name="method" value="permissions-save">
        <div class="fm-field">
            <div class="fld-label">
                Admin Types
            </div>
            <div class="fld-inp">
                <select name="adminType" id="type" data-type="string" data-label="type">
                    <option value="" disabled selected>
                        Choose Type
                    </option>
                    <?php
                    $selType = $type ?? '';
                    foreach($evg::permissions as $ky => $vl){
                    ?>
                        <option <?php echo $selType == $ky ? 'selected' : ''; ?> value="<?php echo $ky; ?>"><?php echo $vl; ?></option>
                    <?php
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="fm-field">
            <div class="fld-label">
                Permissions
            </div>
            <div class="fld-inp">
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
                    [
                        'elect',
                        'Leadership Election',
                        'Admins are allowed to manage the leadership roles.'
                    ],
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
                $selPerms = ($permissions && isset($permissions->perms))
                            ? explode(',', $permissions->perms ?: '')
                            : [];

                foreach($perms as $pms) {
                ?>
                    <div class="perm-row">
                        <div class="perm-chk">
                            <?php
                            ToggleBtn([
                                'name' => 'perms[]',
                                'class' => 'perm-inp',
                                'value' => $pms[0],
                                'checked' => in_array($pms[0], $selPerms)
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
                            if ($pms[0] == 'members') {
                                $act = in_array('members', $selPerms) ? 'active' : '';
                            ?>
                                <div class="members-mod <?php echo $act; ?>">
                                    <?php
                                    ToggleBtn([
                                        'name' => 'perms[]',
                                        'class' => 'perm-inp',
                                        'value' => 'members-mod',
                                        'checked' => in_array('members-mod', $selPerms),
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
        </div>
        <div class="fm-field submit-box">
            <input type="submit" class="pix-btn lg site bold-500" value="Submit">
        </div>
    </form>
<?php
})($pix, $pixdb, $evg);
?>