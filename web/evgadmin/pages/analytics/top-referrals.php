<div class="top-cards ref">
    <div class="card-hed">
        More Reffered Member
    </div>
    <div class="card-lists">
        <?php
        $refData = $pixdb->fetchAll(
            'SELECT COUNT(1) as cnt, refBy FROM `members_referrals` GROUP BY refBy ORDER BY cnt desc'
        );

        foreach ($refData as $row) {
            $refDetail = $pixdb->fetchAssoc(
                'members',
                ['id' => $row->refBy],
                'id,firstName,lastName,avatar'
            );
            foreach ($refDetail as $dtl) {
                $dtlLink = $pix->adminURL . '?page=members&sec=details&id=' . $dtl->id . '#referrals';
        ?>
                <div class="card-list">
                    <a href="<?php echo $dtlLink; ?>">
                        <span class="card-dtl">
                            <span class="crd-thumb">
                                <?php
                                if ($dtl->avatar) {
                                ?>
                                    <img src="<?php echo $evg->getAvatar($dtl->avatar); ?>" alt="member image" style="width: 40px; height:40px;">
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
                            <span class="crd-info">
                                <span class="crd-name">
                                    <?php
                                    echo $dtl->firstName, ' ', $dtl->lastName;
                                    ?>
                                </span>
                                <span class="crd-qnt">
                                    <?php
                                    echo $row->cnt;
                                    ?>
                                </span>
                            </span>
                        </span>
                    </a>
                </div>
        <?php
            }
        }
        ?>
    </div>
</div>