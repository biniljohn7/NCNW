<?php
$aid = esc($_GET['id'] ?? 'new');
$new = $aid == 'new';
$chkLoc = [];

// Collecting advocacy data
if (!$new) {
    $validAdv = false;
    if ($aid) {
        $advData = $pixdb->getRow(
            'advocacies',
            [
                'id' => $aid
            ]
        );
        $validAdv = !!$advData;
    }
    if (!$validAdv) {
        $pix->addmsg('Unknown advocacy');
        $pix->redirect('?page=advocacy');
    }

    if ($advData->locations) {
        $advData->locations = json_decode($advData->locations);
    }
}

$advScope = [
    'national' => 'National',
    'regional' => 'Regional',
    'state' => 'State',
    'chapter' => 'Section'
];

loadStyle('pages/advocacy/mod');
loadScript('pages/advocacy/mod');

?>
<h1>
    <?php
    echo $new ? 'Create' : 'Modify'
    ?> Advocacy
</h1>
<?php
breadcrumbs(
    [
        'Advocacies',
        '?page=advocacy'
    ],
    !$new ? [
        $advData->title,
        "?page=advocacy&sec=details&id=$advData->id"
    ] : null,
    [
        $new ? 'Create' : 'Modify'
    ]
)
?>
<script src="<?php echo ADMINURL, 'assets/lib/tinymce/tinymce.min.js'; ?>"></script>


<form action="<?php echo ADMINURL, 'actions/anyadmin/'; ?>" method="post" id="advocacySave" enctype="multipart/form-data">
    <input type="hidden" name="method" value="advocacy-save" />
    <?php
    if (!$new) {

    ?>
        <input type="hidden" name="aid" value="<?php echo $aid; ?>" />
    <?php
    }
    ?>
    <div class="fm-field">
        <div class="fld-label">
            Title
        </div>
        <div class="fld-inp">
            <input type="text" size="35" name="title" value="<?php echo $new ? '' : $advData->title; ?>" data-type="string">
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
            Scope
        </div>
        <div class="fld-inp">
            <select name="scope" data-type="string" id="scopeSel">
                <option value="">
                    Select Scope
                </option>
                <?php
                $selAdvocacy = $new ? '' : $advData->scope;
                foreach ($advScope as $key => $val) {
                    echo '<option ', $selAdvocacy == $key ? 'selected' : '', ' value="', $key, '">', $val, '</option>';
                }
                ?>
            </select>
        </div>
    </div>
    <div class="fm-field scope <?php echo !$new ? 'show' : ''; ?>" id="dependScope">
        <?php
        if (!$new) {
        ?>
            <div class="fld-label">
                <?php
                echo ucfirst($evg->getLocationsTypes($advData->scope));
                ?>
            </div>
            <div class="fld-inp ctry-sec">
                <?php
                $spData = $pixdb->get(
                    $evg->getLocationsTypes($advData->scope),
                    [
                        '#SRT' => 'id asc'
                    ],
                    'id, name'
                )->data;
                $locations = $evg->getLocations(
                    $advData->scope,
                    $advData->locations,
                    'id, name'
                );
                foreach ($locations as $row) {
                    $chkLoc[$row->id] = $row->name;
                }
                foreach ($spData as $ctry) {
                    CheckBox(
                        $ctry->name,
                        'ctry',
                        $ctry->id,
                        isset($chkLoc[$ctry->id]),
                        null,
                        'chkbox'
                    );
                }
                ?>
            </div>
        <?php
        }
        ?>
    </div>
    <div class="fm-field">
        <div class="fld-label">
            Legislator
        </div>
        <div class="fld-inp">
            <input type="text" size="35" name="legislator" value="<?php echo $new ? '' : $advData->legislator; ?>">
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
            Senator
        </div>
        <div class="fld-inp">
            <input type="text" size="35" name="senator" value="<?php echo $new ? '' : $advData->senator; ?>">
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
            Contact
        </div>
        <div class="fld-inp">
            <input type="text" size="35" name="contact" value="<?php echo $new ? '' : $advData->contact; ?>" data-type="string">
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
            Recipient Name
        </div>
        <div class="fld-inp">
            <input type="text" size="35" name="recipient" value="<?php echo $new ? '' : $advData->recipient; ?>" data-type="string" data-label="recipient name">
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
            Recipient Email
        </div>
        <div class="fld-inp">
            <input type="text" size="35" name="recipEmail" value="<?php echo $new ? '' : $advData->recipEmail; ?>" data-type="string" data-label="recipient email">
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
            Address
        </div>
        <div class="fld-inp">
            <textarea cols="70" rows="3" name="address" data-type="string"><?php echo $new ? '' : $advData->recipAddr; ?></textarea>
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
            Description
        </div>
        <div class="fld-inp">
            <textarea cols="70" rows="10" name="desc" data-type="string" data-label="description"><?php echo $new ? '' : $advData->descrptn; ?></textarea>
        </div>
    </div>
    <div class="fm-field pdf">
        <div class="fld-label pdf">
            <div class="pdf">
                PDF
            </div>
            <div class="pdf-condtn">
                <span class="lbl">
                    Allowed variables:
                </span>
                <ul>
                    <li>
                        FULL_NAME <span>- Full name of member</span>
                    </li>
                    <li>
                        FIRST_NAME <span>- First name of member</span>
                    </li>
                    <li>
                        LAST_NAME <span>- Last name of member</span>
                    </li>
                    <li>
                        PREFIX <span>- Prefix selected by member</span>
                    </li>
                    <li>
                        MEMBERSHIP_CODE <span>- Unique mebership code assigned to member</span>
                    </li>
                    <li>
                        COUNTRY <span>- Country selected by member</span>
                    </li>
                    <li>
                        STATE <span>- State selected by member</span>
                    </li>
                    <li>
                        CITY <span>- City selected by member</span>
                    </li>
                </ul>
            </div>
        </div>
        <div class="fld-inp pdf">
            <textarea id="pdf">
                <?php echo $new ? '' : $advData->pdfContent; ?>
            </textarea>
            <input type="hidden" name="pdf" id="hdPDFInp" value="" />
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
            Enable
        </div>
        <div class="fld-inp">
            <?php
            CheckBox(
                'Enable Advocacy',
                'status',
                1,
                $new || (!$new && $advData->enabled == 'Y'),
                isset($advData->enabled)
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