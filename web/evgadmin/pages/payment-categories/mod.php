<?php
$id = esc($_GET['id'] ?? 'new');
$new = $id == 'new';

if (!$new) {
    $dataOk = false;
    if ($id) {
        $pdtData = $pixdb->getRow('products', ['id' => $id]);
        $dataOk = !!$pdtData;
    }
    if (!$dataOk) {
        $pix->addmsg('Unknown product');
        $pix->redirect('?page=payment-categories');
    }
}

$isFee = $new || ($pdtData->type == 'fee');

loadStyle('pages/pay-categories/mod');
loadScript('pages/pay-categories/mod');
?>
<h1>
    <?php
    echo $new ? 'Add a' : 'Modify'
    ?>
    Product
</h1>
<?php
breadcrumbs(
    [
        'Products',
        '?page=payment-categories'
    ],
    !$new ? [
        $pdtData->name,
        "?page=payment-categories&sec=details&id=$pdtData->id"
    ] : null,
    [
        $new ?
            'Create a Product' :
            'Modify Product'
    ]
)
?>
<form action="<?php echo ADMINURL, 'actions/anyadmin/'; ?>" method="post" id="pdtForm">
    <input type="hidden" name="method" value="product-save" />
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
        </div>
        <div class="fld-inp">
            <input type="text" size="70" name="name" value="<?php echo $new ? '' : $pdtData->name; ?>" data-type="string">
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
            Code
        </div>
        <div class="fld-inp">
            <input type="text" size="70" name="code" value="<?php echo $new ? '' : $pdtData->code; ?>" data-type="string">
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
            Visibility
        </div>
        <div class="fld-inp">
            <?php
            CheckBox(
                'Visible to Members',
                'enabled',
                1,
                $new ||
                    (!$new && $pdtData->enabled == 'Y')
            );
            ?>
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
            Type
        </div>
        <div class="fld-inp">
            <div class="mb5">
                <?php
                Radio(
                    'Fee',
                    'type',
                    'fee',
                    $isFee,
                    null,
                    'pdt-type'
                );
                ?>
            </div>
            <div>
                <!-- <?php
                        Radio(
                            'Donation',
                            'type',
                            'donation',
                            !$isFee,
                            null,
                            'pdt-type'
                        );
                        ?> -->
            </div>
        </div>
    </div>
    <div id="feeInfo" style="<?php echo !$isFee ? 'display:none' : ''; ?>">
        <div class="fm-field">
            <div class="fld-label">
                Amount
            </div>
            <div class="fld-inp dollar">
                <input
                    type="text"
                    size="20"
                    name="amount"
                    class="amt-input"
                    id="feeAmt"
                    value="<?php echo $new ? '' : $pdtData->amount; ?>"
                    <?php echo $isFee ?
                        'data-type="number"
                        data-min="0"'
                        : ''; ?>
                    data-label="amount">
            </div>
        </div>
        <div class="fm-field">
            <div class="fld-label">
                Validity
            </div>
            <div class="fld-inp">
                <select name="validity" id="validity" <?php echo $isFee ? 'data-type="string"' : ''; ?>>
                    <?php
                    $duration = [
                        'fiscal-year' => 'Fiscal Year (October 1 – September 30)',
                        'lifelong' => 'Lifelong'
                    ];
                    $selValidity = $new ? '' : $pdtData->validity;
                    foreach ($duration as $code => $value) {
                        echo '<option ', $selValidity == $code ? 'selected' : '', ' value="', $code, '">', $value, '</option>';
                    }
                    ?>
                </select>
            </div>
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
            Description
        </div>
        <div class="fld-inp">
            <textarea name="desc" class="tarea" cols="70" rows="8"
                data-type="string" data-label="description"><?php echo $new ? '' : $pdtData->description; ?></textarea>
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