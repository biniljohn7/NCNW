<?php
(function ($pix, $pixdb, $evg) {
    $pid = esc($_GET['id'] ?? 'new');
    $new = $pid == 'new';

    $sections = $pixdb->get(
        'chapters',
        ['#SRT' => 'name asc'],
        'id, name'
    );
    $sections = $sections->data;

    $itemArr = [];
    $mbIds = [];
    $pdIds = [];
    $mbPlans = $pixdb->get(
        'membership_plans',
        ['#SRT' => 'id asc'],
        'id, title, ttlCharge'
    );

    $products = $pixdb->get(
        'products',
        [
            '#SRT' => 'id asc',
            'type' => 'fee'
        ],
        'id, name, amount'
    );

    foreach ($mbPlans->data as $mb) {
        $itemArr['mb_' . $mb->id] = [
            'mb_id' => $mb->id,
            'mb_name' => $mb->title,
            'mb_amnt' => $mb->ttlCharge
        ];
        $mbIds['mb_' . $mb->id] = $mb->title;
    }

    foreach ($products->data as $pt) {
        $itemArr['pd_' . $pt->id] = [
            'pd_id' => $pt->id,
            'pd_name' => $pt->name,
            'pd_amnt' => $pt->amount
        ];
        $pdIds['pd_' . $pt->id] = $pt->name;
    }

    echo '<script type="text/javascript">
        var sections = ' . json_encode($sections) . ',
            items = ' . json_encode($itemArr) . ',
            mbIds = ' . json_encode($mbIds) . ',
            pdIds = ' . json_encode($pdIds) . ';
    </script>';

    loadStyle('pages/pos/cart');
    loadScript('pages/pos/cart');
?>
    <h1>POS Cart</h1>
    <?php
    breadcrumbs(
        [
            'Transactions',
            '?page=transactions'
        ],
        !$new ? [
            'Sample POS'
        ] : null,
        [
            $new ? 'Create' : 'Modify'
        ]
    );
    ?>
    <form action="" method="post" id="savePOS">
        <input type="hidden" name="method" value="pos-save">
        <div>
            <span class="pix-btn site add-itm" id="addItem">
                Add Item
            </span>
        </div>
        <div class="wrap-sec" id="wrapSec">
            <div class="cart-no-result" id="cartNoRes">
                No items found
            </div>
            <div class="cart-wrap" id="cartWrap"></div>
            <div class="cart-ttl-charge" id="ttlCharge">
                <div class="chrg-lf"></div>
                <div class="chrg-rg">
                    <div class="ttl-left">
                        <div class="lf-label">
                            Total Amount
                        </div>
                        <div class="lf-amnt" id="chrgAmnt"></div>
                    </div>
                    <div class="ttl-rg">
                        <input
                            type="submit"
                            class="pix-btn site sm bold-500 pos-submit"
                            name="savePOS"
                            value="Make Payment"
                            data-type="make-payment">

                        <input
                            type="submit"
                            class="pix-btn sm bold-500 pos-submit"
                            name="savePOS"
                            value="Mark As Paid"
                            data-type="mark-paid">
                    </div>
                </div>
            </div>
        </div>
    </form>
<?php
})($pix, $pixdb, $evg);
?>