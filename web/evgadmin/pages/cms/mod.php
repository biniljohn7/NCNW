<?php
$cid = esc($_GET['id'] ?? 'new');
$new = $cid == 'new';

if (!$new) {
    $validCMS = false;
    if ($cid) {
        $cData = $pixdb->getRow(
            'cms',
            [
                'id' => $cid
            ]
        );
        $validCMS = !!$cData;
    }
    if (!$validCMS) {
        $pix->addmsg('UnKnown cms');
        $pix->redirect('?page=cms');
    }
}

loadScript('pages/cms/mod');
loadStyle('pages/cms/mod');
?>
<h1>
    <?php echo $new ? 'Create' : 'Modify'; ?> CMS Page
</h1>
<?php
breadcrumbs(
    [
        'CMS Pages',
        '?page=cms'
    ],
    !$new ? [
        $cData->cmsName,
        "?page=cms&sec=details&id=$cData->id"
    ] : null,
    [
        $new ? 'Create' : 'Modify'
    ]
);
?>
<script src="<?php echo ADMINURL, 'assets/lib/tinymce/tinymce.min.js'; ?>"></script>


<form action="<?php echo ADMINURL, 'actions/anyadmin/'; ?>" method="post" id="cmsSave">
    <input type="hidden" name="method" value="cms-save" />
    <?php
    if (!$new) {
    ?>
        <input type="hidden" name="cid" value="<?php echo $cid; ?>" />
    <?php
    }
    ?>
    <div class="fm-field">
        <div class="fld-label">
            Name
        </div>
        <div class="fld-inp">
            <input type="text" size="35" name="cmsName" value="<?php echo $new ? '' : $cData->cmsName; ?>" data-type="string" data-label="name">
        </div>
    </div>
    <div class="fm-field cms">
        <div class="fld-label">
            Content
        </div>
        <div class="fld-inp cms">
            <textarea id="cmsContent" data-type="string" data-label="content">
                <?php echo $new ? '' : $cData->cmsContent; ?>
            </textarea>
            <input type="hidden" name="cmsContent" id="hdCmsInp" value="" />
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
            Enable
        </div>
        <div class="fld-inp">
            <?php
            CheckBox(
                'Enable CMS',
                'status',
                1,
                $new || (!$new && $cData->enabled == 'Y'),
                isset($cData->enabled)
            );
            ?>
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
        </div>
        <div class="fld-inp">
            <input type="submit" class="pix-btn lg site bold-500" value="Submit">
        </div>
    </div>
</form>