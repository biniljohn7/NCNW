<div class="recnt-members">
    <div class="memb-top">
        <div class="memb-hed">
            Recent Members
        </div>
        <div class="memb-view">
            <a href="<?php echo ADMINURL, '?page=members'; ?>">View all</a>
        </div>
    </div>
    <?php
    $mbConds = [
        '#SRT' => 'id desc',
        '__limit' => 10
    ];
    $members = $pixdb->get(
        'members',
        $mbConds,
        'id,
        firstName,
        lastName,
        memberId,
        avatar'
    );
    ?>
    <div class="memb-lists">
        <?php
        foreach ($members->data as $row) {
        ?>
            <div class="memb-list">
                <a href="<?php echo ADMINURL, "?page=members&sec=details&id=$row->id"; ?>">
                    <span class="memb">
                        <span class="memb-thumb">
                            <?php
                            if ($row->avatar) {
                            ?>
                                <img src="<?php echo $evg->getAvatar($row->avatar); ?>">
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
                        <span class="memb-info">
                            <span class="memb-name">
                                <?php echo $row->firstName, ' ', $row->lastName; ?>
                            </span>
                            <span class="memb-id">
                                <span class="id-lb">
                                    Member ID
                                </span>
                                <span class="id-val">
                                    <?php echo $row->memberId; ?>
                                </span>
                            </span>
                        </span>
                    </span>
                </a>
            </div>
        <?php
        }
        ?>
    </div>
</div>