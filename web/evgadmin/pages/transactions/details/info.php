<div class="transaction-details">
    <div class="trtn-memb">
        <div class="memb name">
            <span class="material-symbols-outlined icn">
                account_circle
            </span>
            <?php
            if ($memeberData) {
                echo $memeberData->firstName . ' ' . $memeberData->lastName;
            }
            ?>
        </div>
        <div class="memb email">
            <span class="material-symbols-outlined icn">
                mail
            </span>
            <?php
            if ($memeberData) {
                echo $memeberData->email;
            }
            ?>
        </div>
        <div class="memb mb-id">
            <span class="material-symbols-outlined icn">
                card_membership
            </span>
            <?php
            if ($memeberData) {
                echo $memeberData->memberId;
            }
            ?>
        </div>
    </div>
    <div class="trtn-info">
        <div class="info-details">
            <div class="eachitm">
                <div class="pymnt-label">
                    Payment Status
                </div>
                <div class="pymnt-vals sts">
                    <?php
                    if ($trData->status == 'success') {
                    ?>
                        <div class="success">
                            <span class="material-symbols-outlined">
                                check_circle
                            </span>
                            Success
                        </div>
                    <?php
                    } else {
                    ?>
                        <div class="pending">
                            <span class="material-symbols-outlined">
                                pending
                            </span>
                            <?php echo $trData->status; ?>
                        </div>
                        <div class="pt10 mb10">
                            <span class="trcn-mark-paid pix-btn sm rounded" id="trcnMarkPaid" data-id="<?php echo $trData->id ?? ''; ?>">
                                <span class="material-symbols-outlined">
                                    done_all
                                </span>
                                mark as paid
                            </span>
                        </div>
                    <?php
                    }
                    ?>
                </div>
            </div>
            <div class="eachitm">
                <div class="pymnt-label">
                    Payment Method
                </div>
                <div class="pymnt-vals">
                    <?php
                    echo ucwords($trData->method);
                    ?>
                </div>
            </div>
            <div class="eachitm">
                <div class="pymnt-label">
                    Transaction ID
                </div>
                <div class="pymnt-vals">
                    <?php
                    echo $trData->id;
                    ?>
                </div>
            </div>
            <div class="eachitm">
                <div class="pymnt-label">
                    Transaction Date & Time
                </div>
                <div class="">
                    <?php echo date('d M Y - h:i A', strtotime($trData->date)); ?>
                </div>
            </div>
            <?php
            if ($trData->status == 'success') {
            ?>
                <div class="eachitm">
                    <div class="pymnt-label">
                        Txn.Reference
                    </div>
                    <div class="">
                        <span class="text-09 bold-400">
                            <?php echo $trData->refNumber ?: '--'; ?>
                        </span>
                    </div>
                </div>
            <?php
            }
            ?>
        </div>
        <div class="bold-400 text-g2">
            <?php
            echo $trData->title;
            ?>
        </div>
    </div>
    <?php
    $txnItems = $pixdb->get('txn_items', ['txnId' => $trData->id])->data;
    foreach ($txnItems as $itm) {
        $info = [];
        if ($itm->type == 'membership') {
            $details = json_decode($itm->details);
            if (is_object($details)) {
                if (isset($details->target, $details->targetId)) {
                    if ($details->target == 'membership') {
                        $membership = $pixdb->getRow(
                            'memberships',
                            ['id' => $details->targetId]
                        );
                        if ($membership) {
                            $info[] = (object)[
                                'title' => 'Membership',
                                'value' => $membership->planName
                            ];
                            if ($membership->installment) {
                                $info[] = (object)[
                                    'title' => 'Installment Type',
                                    'value' => $membership->installment == 4 ? 'Quaterly' : ($membership->installment == 2 ? 'Biannual' : '')
                                ];
                            }
                        }
                    }
                } elseif (isset($details->planName)) {
                    $info[] = (object)[
                        'title' => 'Membership',
                        'value' => $details->planName
                    ];
                    if (isset($details->installment)) {
                        $info[] = (object)[
                            'title' => 'Installment Type',
                            'value' => $details->installment == 4 ? 'Quaterly' : ($details->installment == 2 ? 'Biannual' : '')
                        ];
                    }
                }
            }
        } else {
            $info[] = (object)[
                'title' => 'Product',
                'value' => $itm->title
            ];
        }
    ?>
        <div class="trtn-charges">
            <div class="chrg-details">
                <div class="dtl-hed">
                    <?php
                    echo $itm->pdtCode;
                    ?>
                </div>
                <?php
                foreach ($info as $row) {
                ?>
                    <div class="dtl-itm">
                        <div class="itm-label">
                            <?php echo  $row->title; ?>
                        </div>
                        <div class="itm-vals">
                            <?php
                            echo $row->value;
                            ?>
                        </div>
                    </div>
                <?php
                }
                ?>
                <div class="dtl-itm">
                    <div class="itm-label">
                        Amount
                    </div>
                    <div class="itm-vals">
                        <?php
                        echo dollar($itm->amount) ?? 0;
                        ?>
                    </div>
                </div>
            </div>
        </div>
    <?php
    }
    ?>
</div>