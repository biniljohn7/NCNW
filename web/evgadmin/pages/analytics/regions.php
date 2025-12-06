<div class="btm-cards region">
    <div class="card-hed">
        Region with the Most Members
    </div>
    <div class="card-lists">
        <?php
        $regions = $pixdb->get(
            'regions',
            [
                '#SRT' => 'members desc',
                '__limit' => 10
            ],
            'id,name,nation,createdAt,members'
        );
        $nationIds = [];
        foreach ($regions->data as $row) {
            if ($row->nation) {
                $nationIds[] = $row->nation;
            }
        }
        $nationData = $evg->getNations($nationIds, 'id,name');
        foreach ($regions->data as $row) {
            $dtlLink = $pix->adminURL . '?page=region';
        ?>
            <div class="card-list">
                <a href="<?php echo $dtlLink; ?>">
                    <span class="card-dtl">
                        <span class="crd-info">
                            <span class="loc-dtl">
                                <span class="loc-name">
                                    <span class="">
                                        <?php
                                        echo $row->name;
                                        ?>
                                    </span>
                                    <span class="nt-sub">
                                        <?php
                                        if ($ntData = $nationData[$row->nation] ??  false) {
                                            echo $ntData->name;
                                        }
                                        ?>
                                    </span>
                                </span>
                                <span class="loc-date">
                                    <?php
                                    echo date('F d, Y', strtotime($row->createdAt))
                                    ?>
                                </span>
                            </span>
                            <span class="loc-memb">
                                <span class="memb-lbl">
                                    Members
                                </span>
                                <span class="memb-val">
                                    <?php
                                    echo $row->members;
                                    ?>
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