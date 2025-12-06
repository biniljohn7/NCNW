<div class="rcnt-itm">
    <div class="itm-top">
        <div class="itm-hed">
            Recent Transactions
        </div>
        <div class="itm-view">
            <a href="<?php echo ADMINURL, '?page=transactions'; ?>">View all</a>
        </div>
    </div>
    <?php
    $trConds = [
        '#SRT' => 'id desc',
        '__limit' => 10,
        '__QUERY__' => array()
    ];

    $transactions = $pixdb->get(
        'transactions',
        $trConds,
        'id,
        member,
        date,
        amount,
        status'
    );
    $memberIds = [];
    foreach ($transactions->data as $row) {
        if ($row->member) {
            $memberIds[] = $row->member;
        }
    }

    $memberDatas = $evg->getMembers($memberIds, 'id,firstName,lastName,avatar');
    $trStatus = [
        'success' => 'Successful',
        'pending' => 'Pending'
    ];
    ?>
    <div class="itm-lists">
        <?php
        foreach ($transactions->data as $row) {
            $firstName = $memberDatas[$row->member]->firstName ?? '--';
            $lastName = $memberDatas[$row->member]->lastName ?? '--';
        ?>
            <div class="lists-itm trtn">
                <a href="<?php echo ADMINURL, "?page=transactions&sec=details&id=$row->id"; ?>" class="list-item trtn">
                    <span class="trtn-item left">
                        <span class="trtn-itm id">
                            <span class="itm top">
                                # <span><?php echo $row->id; ?></span>
                            </span>
                            <span class="itm-btm">
                                <?php
                                echo date('d M Y \a\t H:i A', strtotime($row->date));
                                ?>
                            </span>
                        </span>
                        <span class="trtn-itm price">
                            <span class="itm top">
                                Amount
                            </span>
                            <span class="itm btm">
                                <?php echo dollar($row->amount); ?>
                            </span>
                        </span>
                    </span>
                    <span class="trtn-item right">
                        <span class="trtn-item memb">
                            <span class="memb-thumb">
                                <?php
                                if (isset($memberDatas[$row->member]->avatar)) {
                                ?>
                                    <img src="<?php echo $evg->getAvatar($memberDatas[$row->member]->avatar); ?>">
                                <?php
                                } else {
                                ?>
                                    <span class="no-thumb">
                                        <span class="material-symbols-outlined no-thmb">
                                            person
                                        </span>
                                    </span>
                                <?php
                                }
                                ?>
                            </span>
                            <span class="itm-info">
                                <span class="itm top">
                                    Member
                                </span>
                                <span class="itm btm">
                                    <?php
                                    echo $firstName, ' ', $lastName;
                                    ?>
                                </span>
                            </span>
                        </span>
                        <span class="trtn-item status">
                            <span class="itm top">
                                Status
                            </span>
                            <?php
                            if ($row->status) {
                                $sts = $row->status == 'success' ? 'success' : 'pending';
                            ?>
                                <span class="itm btm sts <?php echo $sts; ?>">
                                    <span class="material-symbols-outlined">
                                        <?php echo $sts == 'success' ? 'check_circle' : 'pending' ?>
                                    </span>
                                    <?php
                                    echo $trStatus[$sts];
                                    ?>
                                </span>
                            <?php
                            }
                            ?>
                        </span>
                    </span>
                </a>
            </div>
        <?php
        }
        ?>
    </div>
</div>