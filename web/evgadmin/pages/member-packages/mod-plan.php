<?php
$tpData = false;
if (isset($_GET['type'])) {
    $type = esc($_GET['type']);
    if ($type) {
        $tpData = $pixdb->getRow('membership_types', ['id' => $type]);
    }
}
if (!$tpData) {
    $pix->addmsg('Unknown type');
    $pix->redirect('?page=member-packages');
}

$id = esc($_GET['id'] ?? 'new');
$new = $id == 'new';

if (!$new) {
    $dataOk = false;
    if ($id) {
        $plData = $pixdb->getRow('membership_plans', ['id' => $id]);
        $dataOk = !!$plData;
    }
    if (!$dataOk) {
        $pix->addmsg('Unknown plan');
        $pix->redirect('?page=member-packages&sec=details&id=' . $type);
    }
}

loadStyle('pages/member-packages/mod-plan');
loadScript('pages/member-packages/mod-plan');
?>
<h1>
    <?php
    echo $new ? 'Add a' : 'Modify'
    ?>
    Membership Plan
</h1>
<?php
breadcrumbs(
    [
        'Membership Plans',
        '?page=member-packages'
    ],
    [
        $tpData->name,
        '?page=member-packages&sec=details&id=' . $type
    ],
    [
        $new ?
            'Create a Membership Plan' :
            $plData->title . ' - Modify Plan'
    ]
)
?>
<form action="<?php echo ADMINURL, 'actions/anyadmin/'; ?>" method="post" id="planForm">
    <input type="hidden" name="method" value="membership-package-plan-save" />
    <input type="hidden" name="type" value="<?php echo $type; ?>" />
    <?php
    if (!$new) {
    ?>
        <input type="hidden" name="id" value="<?php echo $id; ?>" />
    <?php
    }
    ?>
    <div class="fm-field">
        <div class="fld-label">
            Title
        </div>
        <div class="fld-inp">
            <input type="text" size="70" name="title" value="<?php echo $new ? '' : $plData->title; ?>" data-type="string">
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
            Code
        </div>
        <div class="fld-inp">
            <input type="text" size="70" name="code" value="<?php echo $new ? '' : $plData->code; ?>" data-type="string">
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
            Visibility
        </div>
        <div class="fld-inp">
            <?php
            CheckBox(
                'Publish plan',
                'visibility',
                1,
                $new ||
                    (!$new && $plData->active == 'Y')
            );
            ?>
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
            Duration
        </div>
        <div class="fld-inp">
            <div class="mb5">
                <?php
                Radio(
                    'Lifetime',
                    'duration',
                    0,
                    $new || (!$new && is_null($plData->duration))
                );
                ?>
            </div>
            <div>
                <?php
                Radio(
                    '1 Year',
                    'duration',
                    1,
                    !$new && $plData->duration == '1 year'
                );
                ?>
            </div>
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
            Installment
        </div>
        <div class="fld-inp">
            <div>
                <?php
                $instment = !$new && $plData->installments ? explode(',', $plData->installments) : [];
                CheckBox(
                    'Biannual Installments',
                    'installment',
                    2,
                    in_array(2, $instment)
                );
                ?>
            </div>
            <div>
                <?php
                CheckBox(
                    'Quarterly Installments',
                    'installment',
                    4,
                    in_array(4, $instment)
                );
                ?>
            </div>
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
            National Dues
        </div>
        <div class="fld-inp dollar">
            <input type="text" size="20" name="natdue" class="amt-input nat-amt" value="<?php echo $new ? '' : $plData->nationalDue; ?>" data-type="number" data-min="0" data-label="amount">
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
            National Late Fee
        </div>
        <div class="fld-inp dollar">
            <input type="text" size="20" name="nlatefee" class="amt-input nat-amt" value="<?php echo $new ? '' : $plData->natLateFee; ?>" data-type="number" data-min="0" data-label="amount">
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
            National Per Capital Fee
        </div>
        <div class="fld-inp dollar">
            <input type="text" size="20" name="ncapfee" class="amt-input nat-amt" value="<?php echo $new ? '' : $plData->natCapFee; ?>" data-type="number" data-min="0" data-label="amount">
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
            National Reinstatement Fee
        </div>
        <div class="fld-inp dollar">
            <input type="text" size="20" name="nreinfee" class="amt-input nat-amt" value="<?php echo $new ? '' : $plData->natReinFee; ?>" data-type="number" data-min="0" data-label="amount">
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
            Local Dues
        </div>
        <div class="fld-inp dollar">
            <input type="text" size="20" name="locdue" class="amt-input loc-amt" value="<?php echo $new ? '' : $plData->localDue; ?>" data-type="number" data-min="0" data-label="amount">
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
            Total Local Section Charges
        </div>
        <div class="fld-inp pt5" id="ttlLocAmt">
            $0.00
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
            Total Country Section Charges
        </div>
        <div class="fld-inp pt5" id="ttlNatAmt">
            $0.00
        </div>
    </div>
    <div class="fm-field text-14">
        <div class="fld-label">
            Total Charges
        </div>
        <div class="fld-inp pt5 bold-700" id="ttlPlanAmt">
            $0.00
        </div>
    </div>
    <?php
    $products = $pixdb->get(
        'products',
        ['enabled' => 'Y','type'=>'fee']
    )->data;
    $addons = [];
    if ($plData) {
        $addons = json_decode($plData->addons);
        if (!is_array($addons)) {
            $addons = [];
        }
    }
    if ($products) {
    ?>

        <div class="fm-field">
            <div class="fld-label">
                Additional charges after membership payment completed
            </div>
            <div class="fld-inp">
                <?php
                foreach ($products as $row) {
                ?>
                    <div>
                        <?php
                        CheckBox(
                            $row->name,
                            'addons',
                            $row->id,
                            in_array($row->id, $addons)
                        );
                        ?>
                    </div>
                <?php
                }
                ?>
            </div>
        </div>
    <?php
    }
    ?>
    <div class="fm-field">
        <div class="fld-label">
        </div>
        <div class="fld-inp">
            <input type="submit" class="pix-btn lg site bold-500" value="Save Details">
        </div>
    </div>
</form>