<div class="transaction-details">
    <div class="trtn-info">
        <div class="info-details">
            <div class="eachitm">
                <div class="pymnt-label">
                    POS Done By
                </div>
                <div class="pymnt-vals sts">
                    <?php
                    if ($trData->posDoneBy) {
                        $admin = $pixdb->getRow('admins', ['id' => $trData->posDoneBy], 'id,name,type');
                        if ($admin) {
                    ?>
                            <div class="success">
                                <span class="material-symbols-outlined">
                                    account_circle
                                </span>
                                <?php
                                echo $admin->name . '<br/>' .
                                    '<span class="text-09 text-g2">' . ucwords($admin->type) . '</span>';
                                ?>
                            </div>
                    <?php
                        } else {
                            echo 'Unknown Admin';
                        }
                    } else {
                        echo 'Unknown';
                    }
                    ?>
                </div>
            </div>
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
    $members = [];
    $beneficiaries = collectObjData($txnItems, 'benefitTo');
    if ($beneficiaries) {
        $members = $pixdb->fetchAssoc(
            'members',
            ['id' => $beneficiaries],
            'id, firstName, lastName, email, memberId, regOn, verified',
            'id'
        );
        $memberInfo = $pixdb->fetchAssoc(
            'members_info',
            ['member' => $beneficiaries],
            'member, country, state, city, address, address2, zipcode, phone',
            'member'
        );
    }
    ?>
    <table border="1" cellpadding="6" cellspacing="0" width="100%">
        <tr class="table-header">
            <th>Txn Item</th>
            <th>Beneficiary</th>
            <th>Amount</th>
        </tr>
        <?php
        foreach ($txnItems as $itm) {
            $beneficiaryDetails = 'UNKNOWN';
            if (isset($members[$itm->benefitTo])) {
                $m  = $members[$itm->benefitTo];
                $mi = $memberInfo[$itm->benefitTo] ?? null;
                $countryInfo = $mi && $mi->country ? $evg->getNation($mi->country) : '';
                $stateInfo = $mi && $mi->state ? $evg->getState($mi->state) : '';

                $beneficiaryDetails  = $m->firstName . ' ' . $m->lastName . '<br>';
                $beneficiaryDetails .= 'Email: ' . $m->email . '<br>';
                if ($mi) {
                    $beneficiaryDetails .= 'Address: ' . $mi->address . ' ' . $mi->address2 . ',<br>';
                    $beneficiaryDetails .= $mi->city . ', ' . ($stateInfo ? $stateInfo->name : '') . ',<br>';
                    $beneficiaryDetails .= ($countryInfo ? $countryInfo->name : '') . ' - ' . $mi->zipcode . ',<br>';
                    $beneficiaryDetails .= $mi->phone . '<br>';
                }
            }
        ?>
            <tr>
                <td><?php echo htmlspecialchars($itm->title) ?></td>
                <td><?php echo $beneficiaryDetails ?></td>
                <td><?php echo number_format($itm->amount, 2) ?></td>
            </tr>
        <?php
        }
        ?>
    </table>
    <?php
    if ($trData->status === 'success') {
    ?>
        <div class="pt20">
            <a class="pix-btn site rounded mr5" id="exportTransactionsBtn" href="<?php echo ADMINURL ?>actions/anyadmin/?method=pos-export&trnid=<?php echo $tid; ?>">
                <span class="material-symbols-outlined fltr">upgrade</span>
                Export Transactions
            </a>
        </div>
    <?php
    }
    ?>
</div>