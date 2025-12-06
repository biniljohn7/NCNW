<?php
$bid = esc($_GET['id'] ?? 'new');
$new = $bid == 'new';

// collecting benefit data
if (!$new) {
    $validBenefit = false;
    if ($bid) {
        $bfData = $pixdb->get(
            'benefits',
            [
                'id' => $bid,
                'single' => 1
            ]
        );
        $validBenefit = !!$bfData;
    }
    if (!$validBenefit) {
        $pix->addmsg('Unknown benefit');
        $pix->redirect('?page=benefits');
    }
}
$ctryData = $pixdb->get(
    'categories',
    [
        '#SRT' => 'ctryName asc',
        'type' => 'Benefit'
    ],
    'id, ctryName'
)->data;

$pvdData = $pixdb->get(
    'benefit_providers',
    [
        '#SRT' => 'id asc'
    ],
    'id, name'
)->data;

$scope = [
    'national' => 'National',
    'state' => 'State',
    'regional' => 'Regional',
    'chapter' => 'Section'
];
loadStyle('pages/benefits/mod');
loadScript('pages/benefits/mod');

?>
<h1>
    <?php echo $new ? 'Create' : 'Modify' ?> Benefits
</h1>
<?php
breadcrumbs(
    [
        'Benefits',
        '?page=benefits'
    ],
    !$new ?
        [
            $bfData->name,
            "?page=benefits&sec=details&id=$bfData->id"
        ] :
        null,
    [
        $new ?
            'Create' : 'Modify'
    ]
)
?>
<form action="<?php echo ADMINURL, 'actions/anyadmin/'; ?>" method="post" id="benefitSave">
    <input type="hidden" name="method" value="benefit-save">
    <?php
    if (!$new) {
    ?>
        <input type="hidden" name="bid" value="<?php echo $bid; ?>" />
    <?php
    }
    ?>
    <div class="fm-field">
        <div class="fld-label">
            Benefit Name
        </div>
        <div class="fld-inp">
            <input type="text" size="35" name="name" value="<?php echo $new ? '' : $bfData->name; ?>" data-type="string">
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
            Category
        </div>
        <div class="fld-inp">
            <select name="category">
                <option value="">
                    Choose Category
                </option>
                <?php
                $selCat = $new ? '' : $bfData->ctryId;
                foreach ($ctryData as $ct) {
                    echo '<option ', $selCat == $ct->id ? 'selected' : '', ' value="', $ct->id, '">', $ct->ctryName, '</option>';
                }
                ?>
            </select>
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
            Provider
        </div>
        <div class="fld-inp">
            <select name="provider">
                <option value="">
                    Choose Provider
                </option>
                <?php
                $selPvd = $new ? '' : $bfData->provider;
                foreach ($pvdData as $pvd) {
                    echo '<option ', $selPvd == $pvd->id ? 'selected' : '', ' value="', $pvd->id, '">', $pvd->name, '</option>';
                }
                ?>
            </select>
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
            Scope
        </div>
        <div class="fld-inp">
            <select name="scope" data-type="string">
                <option value="">
                    Choose Scope
                </option>
                <?php
                $selScope = $new ? '' : $bfData->scope;
                foreach ($scope as $code => $value) {
                    echo '<option ', $selScope == $code ? 'selected' : '', ' value="', $code, '">', $value, '</option>';
                }
                ?>
            </select>
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
            Code
        </div>
        <div class="fld-inp">
            <input type="text" size="15" name="code" value="<?php echo $new ? '' : $bfData->code; ?>" data-type="string">
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
            Discount
        </div>
        <div class="fld-inp">
            <input type="text" size="10" name="discount" value="<?php echo $new ? '' : $bfData->discount; ?>" data-type="number">
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
            Short Description
        </div>
        <div class="fld-inp">
            <textarea cols="70" rows="3" name="shortDesc" data-type="string" data-label="short description"><?php echo $new ? '' : $bfData->shortDescr; ?></textarea>
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
            Description
        </div>
        <div class="fld-inp">
            <textarea cols="70" rows="8" name="desc" data-type="string" data-label="description"><?php echo $new ? '' : $bfData->descr; ?></textarea>
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
            Status
        </div>
        <div class="fld-inp">
            <?php
            CheckBox(
                'Enable Benefit',
                'status',
                1,
                $new || (!$new && $bfData->status == 'active'),
                isset($bfData->status)
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