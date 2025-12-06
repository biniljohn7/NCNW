<?php
if ($isSuperAdmin) {
    (function ($pix, $pixdb, $evg, $date) {
        loadStyle('pages/notifications');
        // loadScript('pages/notifications');
?>
        <h1>
            Notifications
        </h1>
        <?php
        breadcrumbs(['Notifications']);

        $nData = $evg->loadNotification('admin');
        $ntUids = [];
        foreach ($nData as $nd) {
            if ($nd->from ?? false) {
                $ntUids[] = $nd->from;
            }
        }
        $ntUids = array_unique(
            array_filter(
                $ntUids
            )
        );
        $members = $evg->getMembers($ntUids, 'id, avatar');
        if (!empty($nData)) {
        ?>
            <div class="notifications">
                <?php
                foreach ($nData as $nd) {
                    $link = $evg->getNotificationLink($nd);
                ?>
                    <a href="<?php echo $link; ?>" class="nt-item">
                        <span class="nt-thumb">
                            <?php
                            if ($nd->from ?? null) {
                                $user = $members[$nd->from] ?? false;
                                if ($user->avatar ?? false) {
                                    echo '<img src="', $evg->getAvatar($user->avatar), '">';
                                } else {
                            ?>
                                    <span class="user-thumb">
                                        <span class="material-symbols-outlined">
                                            person
                                        </span>
                                    </span>
                                <?php
                                }
                            } else {
                                ?>
                                <span class="nt-admin">
                                    T
                                </span>
                            <?php
                            }
                            ?>
                        </span>
                        <span class="nt-body">
                            <span class="nt-title">
                                <?php echo $nd->title ?? ''; ?>
                            </span>
                            <span class="nt-msg">
                                <?php echo $nd->msg ?? ''; ?>
                            </span>
                            <span class="nt-time">
                                <?php echo getRelDate($nd->time ?? $date); ?>
                            </span>
                        </span>
                    </a>
                <?php
                }
                ?>
            </div>
<?php
        }
        // 
    })($pix, $pixdb, $evg, $date);
} else {
    AccessDenied();
}
