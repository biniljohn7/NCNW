<div class="noti">
    <div class="noti-top">
        <div class="noti-hed">
            Notifications
        </div>
        <div class="noti-view">
            <a href="<?php echo ADMINURL, '?page=notifications'; ?>">View all</a>
        </div>
    </div>
    <?php
    $nData = $evg->loadNotification(
        $isAnyAdmin ? 'admin' : $lgUser->id
    );
    $nData = array_slice($nData, 0, 20);
    ?>
    <div class="noti-lists">
        <?php
        foreach ($nData as $nd) {
            $link = $evg->getNotificationLink($nd);
        ?>
            <div class="noti-list">
                <a href="<?php echo $link; ?>">
                    <span class="noti-lb">
                        <?php echo $nd->title ?? ''; ?>
                    </span>
                    <span class="noti-time-ago">
                        <?php echo getRelDate($nd->time ?? $date); ?>
                    </span>
                </a>
            </div>
        <?php
        }
        ?>
    </div>
</div>