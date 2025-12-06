<div class="btm-cards btm state">
    <div class="card-hed">
        State with the Most Members
    </div>
    <div class="card-lists">
        <?php
        $states = $pixdb->get(
            'states',
            [
                '#SRT' => 'members desc',
                '__limit' => 10
            ],
            'id,
            name,
            region,
            createdAt,
            members'
        );
        $regionIds = [];
        foreach ($states->data as $row) {
            if ($row->region) {
                $regionIds[] = $row->region;
            }
        }
        $regionData = $evg->getRegions($regionIds, 'id,name,nation');
        $nationIds = [];
        foreach ($regionData as $row) {
            if ($row->nation) {
                $nationIds[] = $row->nation;
            }
        }
        $nationData = $evg->getNations($nationIds, 'id,name');
        foreach ($states->data as $row) {
            $dtlLink = $pix->adminURL . '?page=state';
            $rDatas = $regionData[$row->region] ?? null;
            $nDatas = $rDatas ? $nationData[$rDatas->nation] ?? null : null;
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
                                        if ($rgData = $regionData[$row->region] ?? false) {
                                            echo $rgData->name;
                                        }
                                        ?>
                                    </span>
                                    <span class="nt-sub">
                                        <?php
                                        echo $nDatas->name ?? '';
                                        ?>
                                    </span>
                                </span>
                                <span class="loc-date">
                                    <?php
                                    echo date('F d, Y', strtotime($row->createdAt));
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