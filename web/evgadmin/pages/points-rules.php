<?php
if ($pix->canAccess('point-rules')) {
    (function ($pix, $pixdb, $evg) {

        $ruleData = $pixdb->getRow('point_rules', ['id' => 1]);

        loadStyle('pages/point-rules/rules');
        loadScript('pages/point-rules/rules');

?>
        <h1>
            Point Rules
        </h1>
        <?php
        breadcrumbs(['Point Rules']);
        ?>
        <form action="<?php echo ADMINURL, 'actions/anyadmin/'; ?>" method="post" id="ptRuleForm">
            <input type="hidden" name="method" value="point-rules-save">

            <div class="fm-field">
                <div class="fld-label">
                    Points for Sharing
                </div>
                <div class="fld-inp">
                    <div>
                        <input type="text" size="20" name="share" class="amt-input nat-amt" value="<?php echo $ruleData->sharing ?? 1; ?>" data-type="number" data-min="1" data-label="points">
                    </div>
                    <div class="pt10 mb15 text-g2 text-09">
                        Points earned when a member invites another member using their unique referral link.
                    </div>
                </div>
            </div>

            <div class="fm-field">
                <div class="fld-label">
                    Points for Using
                </div>
                <div class="fld-inp">
                    <div>
                        <input type="text" size="20" name="use" class="amt-input nat-amt" value="<?php echo $ruleData->using ?? 1; ?>" data-type="number" data-min="1" data-label="points">
                    </div>
                    <div class="pt10 mb15 text-g2 text-09">
                        Points earned by the new member when they join via a referral link.
                    </div>
                </div>
            </div>

            <div class="fm-field">
                <div class="fld-label">
                    Minimum Points for Redeem
                </div>
                <div class="fld-inp">
                    <div>
                        <input type="text" size="20" name="minred" class="amt-input nat-amt" value="<?php echo $ruleData->minRedeem ?? 1; ?>" data-type="number" data-min="1" data-label="points">
                    </div>
                    <div class="pt10 mb15 text-g2 text-09">
                        Minimum points required to redeem against the membership fee.
                    </div>
                </div>
            </div>

            <div class="fm-field">
                <div class="fld-label">
                    Point's value for $1
                </div>
                <div class="fld-inp">
                    <div>
                        <input type="text" size="20" name="d1val" class="amt-input nat-amt" value="<?php echo $ruleData->ptsDollar ?? 1; ?>" data-type="number" data-min="1" data-label="points">
                    </div>
                    <div class="pt10 mb15 text-g2 text-09">
                        Set the number of points equivalent to $1.
                    </div>
                </div>
            </div>


            <div class="fm-field">
                <div class="fld-label">
                </div>
                <div class="fld-inp">
                    <input type="submit" class="pix-btn lg site bold-500" value="Save Rules">
                </div>
            </div>

        </form>
<?php
    })($pix, $pixdb, $evg);
} else {
    AccessDenied();
}
