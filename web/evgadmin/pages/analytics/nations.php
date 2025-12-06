<div class="btm-cards nation">
    <div class="card-hed">
        Nation with the Most Members
    </div>
    <div class="card-lists">
        <?php
        $nations = $pixdb->get(
            'nations',
            [
                '#SRT' => 'members desc',
                '__limit' => 10
            ],
            'id,name,createdAt,members'
        )->data;
        foreach ($nations as $row) {
            $dtlLink = $pix->adminURL . '?page=nation';
        ?>
            <div class="card-list">
                <a href="<?php echo $dtlLink; ?>">
                    <span class="card-dtl">
                        <span class="crd-info">
                            <span class="loc-dtl">
                                <span class="loc-name">
                                    <?php
                                    echo $row->name;
                                    ?>
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