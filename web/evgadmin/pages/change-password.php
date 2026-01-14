<?php
(function ($pix, $pixdb, $evg) {
    loadStyle('pages/change-password');
    loadScript('pages/change-password');
?>
    <h1>
        Change Password
    </h1>
    <?php
    breadcrumbs(['Change Password']);
    ?>
    <form action="<?php echo ADMINURL, 'actions/anyadmin/'; ?>" method="post" id="passwdForm">
        <input type="hidden" name="method" value="password-change">

        <div class="fm-field">
            <div class="fld-label">
                Current Password
            </div>
            <div class="fld-inp">
                <input type="password" size="30" name="curpass" data-type="string" data-label="password">
                <span class="material-symbols-outlined show-pwd">
                    visibility
                </span>
                <br />
            </div>
        </div>

        <div class="fm-field">
            <div class="fld-label">
                New Password
            </div>
            <div class="fld-inp">
                <input type="password" size="30" name="npass1" data-type="string" data-label="password" id="npass1">
                <span class="material-symbols-outlined show-pwd">
                    visibility
                </span>
                <br />
            </div>
        </div>

        <div class="fm-field">
            <div class="fld-label">
                Confirm Password
            </div>
            <div class="fld-inp">
                <input type="password" size="30" name="npass2" data-type="func" data-func="checkConfirmPass" data-errormsg="passwords are not matching">
                <span class="material-symbols-outlined show-pwd">
                    visibility
                </span>
                <br />
            </div>
        </div>

        <div class="fm-field">
            <div class="fld-label">
            </div>
            <div class="fld-inp">
                <input type="submit" class="pix-btn lg site bold-500" value="Change Password">
            </div>
        </div>

    </form>
<?php
})($pix, $pixdb, $evg);
