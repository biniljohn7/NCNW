<?php
$advData = $pixdb->fetchAll(
    'SELECT COUNT(1) as cnt, advocacy FROM member_advocacy GROUP BY advocacy ORDER BY cnt desc'
);

$advArr = [];
$advocacyIds = [];
foreach ($advData as $adv) {
    $advocacyIds[] = $adv->advocacy;
}
$advocacyDetails = $pixdb->fetchAssoc(
    'advocacies',
    ['id' => $advocacyIds],
    'id,title'
);
foreach ($advData as $adv) {
    if (isset($advocacyDetails[$adv->advocacy])) {
        $advDetails = $advocacyDetails[$adv->advocacy];
        $advArr[$advDetails->title] = $adv->cnt;
    }
}
$advDatas = $pixdb->get(
    'advocacies',
    [
        '__limit' => 10
    ],
    'id,title,image'
);
$advList = [];
?><div class="top-cards adv">
    <div class="card-hed">
        Top Signed Advocacy
    </div>
    <div class="card-lists">
        <?php
        foreach ($advDatas->data as $row) {
            $advList[] = [$row->id, $row->title, $row->image];
        }
        foreach ($advList as $key => $value) {
            $advCount = $advArr[$value[1]] ?? 0;
            $advList[$key][] = $advCount;
        }
        function advSort($x, $y)
        {
            return $x[3] < $y[3] ? 1 : 0;
        }
        usort($advList, 'advSort');

        foreach ($advList as $row) {
            $dtlLink = $pix->adminURL . '?page=advocacy&sec=details&id=' . $row[0];
        ?>
            <div class="card-list">
                <a href="<?php echo $dtlLink; ?>">
                    <span class="card-dtl">
                        <span class="crd-thumb adv">
                            <?php
                            if (isset($row[2])) {
                                $advImage = $pix->uploadPath . 'advocacy-image/' . $pix->thumb($row[2], '150x150');
                            ?>
                                <img src="<?php echo $advImage; ?>">
                            <?php
                            } else {
                            ?>
                                <span class="no-thumb">
                                    <span class="material-symbols-outlined no-thmb">
                                        hide_image
                                    </span>
                                </span>
                            <?php
                            }
                            ?>
                        </span>
                        <span class="crd-info">
                            <span class="crd-name">
                                <?php
                                echo $row[1];
                                ?>
                            </span>
                            <span class="crd-qnt">
                                <?php
                                echo $row[3];
                                ?>
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