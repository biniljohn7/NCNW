<?php
$id = esc($_GET['id'] ?? 'new');
$new = $id == 'new';

if (!$new) {
    $dataOk = false;
    if ($id) {
        $admData = $pixdb->getRow('admins', ['id' => $id]);
        if ($admData->type != 'sub-admin') {
            $admData = false;
        }
    }
    $dataOk = !!$admData;
    if (!$dataOk) {
        $pix->addmsg('Unknown admin');
        $pix->redirect('?page=sub-admins');
    }
}

loadStyle('pages/sub-admins/mod');
loadScript('pages/sub-admins/mod');
?>
<h1>
    <?php
    echo $new ? 'Add' : 'Modify'
    ?>
    Sub Admin
</h1>
<?php
breadcrumbs(
    [
        'Sub Admins',
        '?page=sub-admins'
    ],
    !$new ? [
        $admData->name,
        "?page=sub-admins&sec=details&id=$id"
    ] : null,
    [
        $new ? 'Add Sub Admin' : 'Modify'
    ]
)
?>
<form action="<?php echo ADMINURL, 'actions/admin/'; ?>" method="post" id="adminForm">
    <input type="hidden" name="method" value="sub-admin-save" />
    <?php
    if (!$new) {
    ?>
        <input type="hidden" name="id" value="<?php echo $id; ?>" />
    <?php
    }
    ?>
    <div class="fm-field">
        <div class="fld-label">
            Name
            <span class="text-red">
                *
            </span>
        </div>
        <div class="fld-inp">
            <input type="text" size="40" name="name" value="<?php echo $new ? '' : $admData->name; ?>" data-type="string">
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
            Username
        </div>
        <div class="fld-inp">
            <input type="text" size="40" name="username" value="<?php echo $new ? '' : $admData->username; ?>">
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
            Email
            <span class="text-red">
                *
            </span>
        </div>
        <div class="fld-inp">
            <input type="text" size="70" name="email" value="<?php echo $new ? '' : $admData->email; ?>" data-type="email">
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
            Phone
        </div>
        <div class="fld-inp">
            <input type="text" size="30" name="phone" value="<?php echo $new ? '' : $admData->phone; ?>">
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
            Activity
        </div>
        <div class="fld-inp pt5">
            <?php
            CheckBox(
                'Enable this account',
                'enable',
                1,
                $new || (!$new && $admData->enabled == 'Y')
            );
            ?>
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
            Password
        </div>
        <div class="fld-inp">
            <?php
            if ($new) {
            ?>
                <input type="text" size="30" name="password" autocomplete="off" />
            <?php
            } else {
            ?>
                <div class="pt5">
                    <?php
                    CheckBox(
                        'Reset password',
                        'change_pass',
                        1,
                        false,
                        'changePass'
                    );
                    ?>
                </div>
                <div class="pt10" id="passwdBox" style="display: none;">
                    <input type="text" size="30" name="password" id="passInp" autocomplete="off" disabled />
                </div>
            <?php
            }
            ?>
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
        </div>
        <div class="fld-inp">
            <input type="submit" class="pix-btn lg site bold-500" value="Save Details">
        </div>
    </div>
</form>