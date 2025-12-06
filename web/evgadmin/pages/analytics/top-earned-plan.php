<?php
$planCond = [
    '#SRT' => 'id desc'
];
$planInfo = $pixdb->fetchAll(
    'SELECT SUM(amount) as amt, planId FROM memberships GROUP BY planId'
);

$planArr = [];
foreach ($planInfo as $pln) {
    $planName = $pixdb->fetchAssoc(
        'membership_plans',
        ['id' => $pln->planId],
        'id,title'
    );
    foreach ($planName as $row) {
        $planArr[$row->title] = $pln->amt;
    }
}
$membershipPlans = $pixdb->get(
    'membership_plans',
    $planCond,
    'id,title,type'
);
$planList = [];
?>
<div class="top-cards camp">
    <div class="card-hed">
        Top Earned Plan
    </div>
    <div class="card-lists">
        <?php
        foreach ($membershipPlans->data as $row) {
            $planList[] = [$row->id, $row->title, $row->type];
        }
        foreach ($planList as $key => $value) {
            $planErng = $planArr[$value[1]] ?? 0;
            $planList[$key][] = $planErng;
        }
        function planSort($x, $y)
        {
            return $x[3] < $y[3] ? 1 : 0;
        }
        usort($planList, 'planSort');

        foreach ($planList as $row) {
            $dtlLink = $pix->adminURL . '?page=member-packages&sec=details&id=' . $row[2];
        ?>
            <div class="card-list">
                <a href="<?php echo $dtlLink; ?>">
                    <span class="card-dtl">
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