<?php
$info = $pix->getData('email-templates/advocacy');
?>

<form method="post" action="<?php echo ADMINURL, 'actions/anyadmin/'; ?>">
    <input type="hidden" name="method" value="email-template-save" />
    <input type="hidden" name="template" value="advocacy" />

    <div class="bold-600 mb5">
        Body Text
    </div>
    <div class="mb30">
        <textarea cols="100" name="body" rows="4"><?php echo $info->body ?? ''; ?></textarea>
    </div>

    <div class="bold-600 mb5">
        Footer Text 1
    </div>
    <div class="mb30">
        <textarea cols="100" name="ftext1" rows="4"><?php echo $info->ftext1 ?? ''; ?></textarea>
    </div>

    <div class="bold-600 mb5">
        Footer Text 2
    </div>
    <div class="mb30">
        <textarea cols="100" name="ftext2" rows="4"><?php echo $info->ftext2 ?? ''; ?></textarea>
    </div>

    <input type="submit" value="Save" class="pix-btn lg site bold-500">
</form>