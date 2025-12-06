<div class="rt-top card">
    <div class="items">
        <div class="itm-lt raised <?php echo $isAdmin ? 'admin' : ''; ?>">
            <?php
            echo $isAdmin ?
                '<img src="' . $pix->adminURL . 'assets/images/evergreen-logo.png' . '">' :
                strtoupper(
                    substr(
                        $raisedBy->name,
                        0,
                        2
                    )
                );
            ?>
        </div>
        <div class="itm-rt">
            <div class="itm-label">
                Raised By
            </div>
            <div class="itm-val">
                <?php
                if ($raisedBy) {
                    echo $raisedBy->name ?? 'Anonymous User';
                } else {
                    echo 'Anonymous User';
                }
                ?>
            </div>
        </div>
    </div>
    <div class="items">
        <div class="itm-lt <?php echo $rData->priority; ?>">
            <span class="material-symbols-outlined">
                fiber_manual_record
            </span>
        </div>
        <div class="itm-rt">
            <div class="itm-val">
                <?php echo $evg::priorities[$rData->priority]; ?>
            </div>
        </div>
    </div>
    <div class="items">
        <div class="itm-lt <?php echo $rData->reqType; ?>">
            <span class="material-symbols-outlined">
                star
            </span>
        </div>
        <div class="itm-rt">
            <div class="itm-val">
                <?php echo $evg::reqTypes[$rData->reqType]; ?>
            </div>
        </div>
    </div>
    <div class="items">
        <div class="itm-lt">
            <span class="date-icn">
                <span class="material-symbols-outlined">
                    calendar_today
                </span>
            </span>
        </div>
        <div class="itm-rt">
            <div class="itm-label">
                Created On
            </div>
            <div class="itm-val">
                <?php
                echo date('D, j M Y g:i A', strtotime($rData->createdAt));
                ?>
            </div>
        </div>
    </div>
    <?php
    if (isset($rData->lastActivity)) {
    ?>
        <div class="items">
            <div class="itm-lt">
                <span class="date-icn">
                    <span class="material-symbols-outlined">
                        calendar_today
                    </span>
                </span>
            </div>
            <div class="itm-rt">
                <div class="itm-label">
                    Last Activity
                </div>
                <div class="itm-val">
                    <?php
                    echo date('D, j M Y g:i A', strtotime($rData->lastActivity));
                    ?>
                </div>
            </div>
        </div>
    <?php
    }
    if ($rData->reqCompletion) {
    ?>
        <div class="items">
            <div class="itm-lt">
                <span class="date-icn">
                    <span class="material-symbols-outlined">
                        calendar_today
                    </span>
                </span>
            </div>
            <div class="itm-rt">
                <div class="itm-label">
                    Requested Completion Date
                </div>
                <div class="itm-val">
                    <?php
                    echo date('D, j M Y', strtotime($rData->reqCompletion));
                    ?>
                </div>
            </div>
        </div>
    <?php
    }

    if ($rData->estType) {
    ?>
        <div class="items">
            <div class="itm-lt">
                <span class="date-icn">
                    <span class="material-symbols-outlined">
                        summarize
                    </span>
                </span>
            </div>
            <div class="itm-rt">
                <div class="itm-label">
                    Estimate
                </div>
                <div class="itm-val">
                    Task marked as
                    <strong>
                        <?php
                        $estLabels = [
                            'bug' => 'Bug',
                            'new-feature' => 'New Feature',
                        ];
                        echo $estLabels[$rData->estType] ?? 'Unknown';
                        ?>
                    </strong>
                    and estimated time of completion is
                    <?php
                    $duration = explode(':', $rData->estTime);
                    $days = intval($duration[0] ?? 0);
                    $hours = intval($duration[1] ?? 0);

                    if ($days) {
                        echo '<strong>', $days, ' day', ($days > 1 ? 's' : ''), '</strong>';
                    }
                    if ($hours) {
                        echo $days ? ' and ' : '';
                        echo '<strong>', $hours, ' hour', ($hours > 1 ? 's' : ''), '</strong>';
                    }

                    if (
                        $developer &&
                        $rData->status == 'estimated'
                    ) {
                    ?>
                        <div class="pt10">
                            <span class="pix-btn md site " id="modifyEstimateBtn">
                                Modify Estimate
                            </span>
                        </div>
                    <?php
                    } elseif (
                        $cordinator &&
                        $rData->status == 'estimated'
                    ) {
                    ?>
                        <div class="pt10">
                            <a class="pix-btn md site " href="<?php echo $pix->adminURL . 'actions/anyadmin/?method=helpdesk/set-status&status=est-verified&task=' . $rId; ?>">
                                Verify Estimate
                            </a>
                        </div>
                    <?php
                    } elseif (
                        $ncnwTeam &&
                        $rData->status == 'est-verified'
                    ) {
                    ?>
                        <div class="pt10">
                            <a class="pix-btn md site " href="<?php echo $pix->adminURL . 'actions/anyadmin/?method=helpdesk/set-status&status=approved&task=' . $rId; ?>">
                                Approve Estimate
                            </a>
                        </div>
                    <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    <?php
    }
    ?>
</div>