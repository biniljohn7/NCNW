<?php
$info = $pix->getData('email-templates/account-verification');
?>

<form method="post" action="<?php echo ADMINURL, 'actions/anyadmin/'; ?>">
    <input type="hidden" name="method" value="email-template-save" />
    <input type="hidden" name="template" value="account-verification" />

    <div class="bold-600 mb5">
        Body Text
    </div>
    <div class="mb30">
        <textarea cols="100" name="body" rows="4"><?php echo $info->body ?? ''; ?></textarea>
    </div>

    <div class="bold-600 mb5">
        Button Text
    </div>
    <div class="mb30">
        <input type="text" name="btntext" value="<?php echo $info->btntext ?? ''; ?>" size="40" />
    </div>

    <div class="bold-600 mb5">
        Alternate Link Description
    </div>
    <div class="mb30">
        <textarea cols="100" name="altlinktext" rows="4"><?php echo $info->altlinktext ?? ''; ?></textarea>
    </div>

    <input type="submit" value="Save" class="pix-btn lg site bold-500">
</form>