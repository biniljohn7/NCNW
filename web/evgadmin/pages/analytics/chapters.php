<div class="btm-cards btm chapter">
    <div class="card-hed">
        Section with the Most Members
    </div>
    <div class="card-lists">
        <?php
        $chapters = $pixdb->get(
            'chapters',
            [
                '#SRT' => 'members desc',
                '__limit' => 10
            ],
            'id,
            name,
            state,
            createdAt,
            enabled,
            members'
        );
        $stateIds = [];
        foreach ($chapters->data as $row) {
            if ($row->state) {
                $stateIds[] = $row->state;
            }
        }
        $stateData = $evg->getStates($stateIds, 'id,name,region');
        $regionIds = [];
        foreach ($stateData as $row) {
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
        foreach ($chapters->data as $row) {
            $dtlLink = $pix->adminURL . '?page=chapter';
            $stDatas = $stateData[$row->state] ?? null;
            $rgDatas = $stDatas ? $regionData[$stDatas->region] ?? null : null;
            $ntDatas = $rgDatas ? $nationData[$rgDatas->nation] ?? null : null;
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
                                        if ($cpData = $stateData[$row->state] ?? false) {
                                            echo $cpData->name;
                                        }
                                        ?>
                                    </span>
                                    <span class="nt-sub">
                                        <?php
                                        echo $rgDatas->name . ', ' ?? '';
                                        echo $ntDatas->name ?? '';
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