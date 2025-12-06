<?php
$pid = esc($_GET['id'] ?? 'new');
$new = $pid == 'new';

// Collecting provider data
if (!$new) {
    $validProvider = false;
    if ($pid) {
        $pvdData = $pixdb->get(
            'benefit_providers',
            [
                'id' => $pid,
                'single' => 1
            ]
        );
        $validProvider = !!$pvdData;
    }
    if (!$validProvider) {
        $pix->addmsg('Unknown benefit provider');
        $pix->redirect('?page=provider');
    }
}

$cnrtyCode = [
    '+1-USA' => '+1-USA',
    '+1-CANADA' => '+1-CANADA'
];

loadScript('pages/provider');
loadStyle('pages/benefits/mod');

?>
<h1>
    <?php echo $new ? 'Create' : 'Modify' ?> Provider
</h1>
<?php
breadcrumbs(
    [
        'Providers',
        '?page=provider'
    ],
    !$new ? [
        $pvdData->name,
        "?page=provider&sec=details&id=$pvdData->id"
    ] :
        null,
    [
        $new ? 'Create' : 'Modify'
    ]
)
?>
<form action="<?php echo ADMINURL, 'actions/anyadmin/'; ?>" method="post" id="providerSave">
    <input type="hidden" name="method" value="provider-save" />
    <?php
    if (!$new) {
    ?>
        <input type="hidden" name="pid" value="<?php echo $pid; ?>" />
    <?php
    }
    ?>
    <div class="fm-field">
        <div class="fld-label">
            Provider Name
        </div>
        <div class="fld-inp">
            <input type="text" size="35" name="name" value="<?php echo $new ? '' : $pvdData->name; ?>" data-type="string">
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
            Email
        </div>
        <div class="fld-inp">
            <input type="text" size="35" name="email" value="<?php echo $new ? '' : $pvdData->email; ?>" data-type="email">
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
            Country Code
        </div>
        <div class="fld-inp">
            <select name="cntryCode" data-type="string" data-label="country code">
                <option value="">
                    Choose Country Code
                </option>
                <?php
                $selCntry = $new ? '' : $pvdData->cntryCode;
                foreach ($cnrtyCode as $code => $value) {
                    echo '<option ', $selCntry == $code ? 'selected' : '', ' value="', $code, '">', $value, '</option>';
                }
                ?>
            </select>
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
            Phone
        </div>
        <div class="fld-inp">
            <input type="text" size="35" name="phone" value="<?php echo $new ? '' : $pvdData->phone; ?>" data-type="string">
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
            Website
        </div>
        <div class="fld-inp">
            <input type="text" size="30" name="website" value="<?php echo $new ? '' : $pvdData->website; ?>" data-type="string">
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
            Address
        </div>
        <div class="fld-inp">
            <input type="text" size="40" name="address" value="<?php echo $new ? '' : $pvdData->address; ?>" data-type="string">
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
            Status
        </div>
        <div class="fld-inp">
            <?php
            CheckBox(
                'Enable Provider',
                'status',
                1,
                $new || (!$new && $pvdData->status == 'active'),
                isset($pvdData->status)
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