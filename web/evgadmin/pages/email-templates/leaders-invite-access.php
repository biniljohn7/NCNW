<?php
$info = $pix->getData('email-templates/leaders-invite-access');
?>

<form method="post" action="<?php echo ADMINURL, 'actions/anyadmin/'; ?>">
    <input type="hidden" name="method" value="email-template-save" />
    <input type="hidden" name="template" value="leaders-invite-access" />

    <div class="bold-600 mb5">
        Body Text
    </div>
    <div class="text-g1 text-09 mb10">
        Note:<br />
        [ROLE] - Leadership position (e.g., Section President, Section Leader....)<br />
        [SECTION] - Name of affiliate or section
    </div>
    <div class="mb30">
        <textarea cols="100" name="body" rows="4"><?php echo $info->body ?? ''; ?></textarea>
    </div>


    <div class="bold-600 mb5">
        Button Text
    </div>
    <div class="mb30">
        <textarea cols="100" name="btntext" rows="4"><?php echo $info->btntext ?? ''; ?></textarea>
    </div>

    <div class="bold-600 mb5">
        Button Label
    </div>
    <div class="mb30">
        <input type="text" name="btnlabel" value="<?php echo $info->btnlabel ?? ''; ?>" size="40" />
    </div>


    <input type="submit" value="Save" class="pix-btn lg site bold-500">
</form>