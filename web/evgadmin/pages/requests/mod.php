<?php
if (!$pix->canAccess('helpdesk/ncnw-team')) {
    $pix->addmsg('Access denied!');
    $pix->redirect();
}
$rid = esc($_GET['id'] ?? 'new');
$new = $rid == 'new';
$rData = false;

$refPage = esc($_GET['refpage'] ?? '');
$refSec = esc($_GET['refsec'] ?? '');

$refUrl = '';
$refUrl = $refPage ? ADMINURL . '?page=' . $refPage : '';
$refUrl .= $refSec ? '&sec=' . $refSec : '';

// Collecting Request Data
if (!$new) {
    $validRequest = false;
    if ($rid) {
        $rData = $pixdb->get(
            'help_desk',
            [
                'id' => $rid,
                'single' => 1
            ]
        );
        $validRequest = !!$rData;
    }
    if (!$validRequest) {
        $pix->addmsg('Unknown request');
        $pix->redirect('?page=requests');
    }
}

$reqData = (object)[
    'reqId' => $rid
];

$files = $pixdb->get(
    'help_desk_files',
    [
        'request' => $rid,
        '#SRT' => 'id desc'
    ]
)->data;

loadStyle('pages/request/mod');
loadScript('pages/request/mod');
?>
<h1>
    <?php
    echo $new ? 'Create' : 'Modify';
    ?> Request
</h1>
<?php
breadcrumbs(
    [
        'Requests',
        '?page=requests'
    ],
    !$new ? [
        "#$rData->id",
        "?page=requests&sec=details&id=$rData->id"
    ] : null,
    [
        $new ? 'Create' : 'Modify'
    ]
);
?>
<form action="" method="post" id="saveRequest" enctype="multipart/form-data">
    <input type="hidden" name="method" value="request-save" />
    <input type="hidden" name="rid" id="reqId" value="<?php echo $new ? '' : $rid; ?>" />
    <div class="fm-field">
        <div class="fld-label">
            Request Type
        </div>
        <div class="fld-inp">
            <?php
            Radio(
                'Change Request',
                'reqType',
                'change',
                !$rData || ($rData && $rData->reqType == 'change')
            );
            Radio(
                'System Error/Bug',
                'reqType',
                'bug',
                $rData && $rData->reqType == 'bug'
            );
            ?>
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
            Priority Level
        </div>
        <div class="fld-inp">
            <?php
            Radio(
                'Medium
                <div class="text-g3 text-09">
                    (Partial functionality loss, moderate impact)
                </div>',
                'priority',
                'medium',
                !$rData || ($rData && $rData->priority == 'medium')
            );
            Radio(
                'Low
                <div class="text-g3 text-09">
                    (Minor issue, low impact)
                </div>',
                'priority',
                'low',
                $rData && $rData->priority == 'low'
            );
            Radio(
                'High
                <div class="text-g3 text-09">
                    (System down, major impact)
                </div>',
                'priority',
                'high',
                $rData && $rData->priority == 'high'
            );
            ?>
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
            Summary of the Issue/Request
        </div>
        <div class="fld-inp">
            <textarea name="summary" class="tarea" cols="120" rows="8" data-type="string" data-label="summary" placeholder="Please provide a brief summary of the issue or change request."><?php echo $new ? '' : $rData->summary; ?></textarea>
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
            Detailed Description
        </div>
        <div class="fld-inp">
            <textarea name="desc" class="tarea" cols="150" rows="8" data-type="string" data-label="description" placeholder="Please provide a detailed description of the problem or the requested change. Include steps to reproduce the issue for bugs or detailed requirements for change requests."><?php echo $new ? '' : $rData->desc; ?></textarea>
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
            Impact on Business Operations
        </div>
        <div class="fld-inp">
            <textarea name="impact" class="tarea" cols="100" rows="8" data-type="string" placeholder="Please describe how this issue/request is affecting business operations."><?php echo $new ? '' : $rData->impact; ?></textarea>
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
            Attachment(s)
        </div>
        <div id="fileList" class="file-list">
            <?php
            foreach ($files as $fl) {
            ?>
                <div class="file-name">
                    <a href="<?php echo $pix->uploadPath, 'request-images/', $pix->thumb($fl->file, 'w1500'); ?>" class="file-link">
                        <span class="material-symbols-outlined">
                            attach_file
                        </span>
                        <?php echo $fl->name; ?>
                    </a>
                    <span class="file-ttl">
                        <span class="material-symbols-outlined file-dlt" data-id="<?php echo $fl->id; ?>">
                            close
                        </span>
                    </span>
                </div>
            <?php
            }
            ?>
        </div>
        <div class="fld-inp mb40">
            <input
                type="file"
                name="attachFiles[]"
                id="attachFilles"
                class="attach-files"
                data-label="files"
                <?php echo !$new ? 'data-optional="1"' : ''; ?>
                data-type="func"
                data-func="fileCheck"
                accept=".jpeg,.jpg,.png,.gif,.pdf,.txt,.docx,.doc,.xls,.xlsx,.rtf,.ppt,.pptx,.ai,.psd,.ttf,.eps"
                multiple />
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
            Requested Completion Date
        </div>
        <div class="fld-inp">
            <input
                type="text"
                value="<?php echo $new ? '' : date('d M Y', strtotime($rData->reqCompletion)); ?>"
                name="reqCompletion"
                id="reqDate"
                size="15"
                autocomplete="off"
                class="req-date">
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
            Additional Comments
        </div>
        <div class="fld-inp">
            <textarea name="comments" class="tarea" cols="100" rows="8" placeholder="Please provide any additional information or comments that may help us understand and address your request."><?php echo $new ? '' : $rData->comments; ?></textarea>
        </div>
    </div>
    <div class="submit-box">
        <input type="hidden" name="referance" value="<?php echo $refUrl; ?>" />
        <input type="submit" class="pix-btn site bold-500" name="saveRequest" value="<?php echo $new ? 'Create' : 'Modify'; ?> Request">
    </div>
</form>
<?php
echo '<script>
        var reqData=', json_encode($reqData), ';
    </script>';
?>