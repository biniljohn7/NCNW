<?php
$mbConds = [
    '#SRT' => 'id asc'
];
$planInfo = $pixdb->fetchAll(
    'SELECT COUNT(1) AS cnt, planId FROM memberships GROUP BY planId'
);

$planArr = [];
foreach ($planInfo as $pln) {
    $planName = $pixdb->fetchAssoc(
        'membership_plans',
        ['id' => $pln->planId],
        'id,title'
    );
    foreach ($planName as $row) {
        $planArr[$row->title] = $pln->cnt;
    }
}
$membershiPlans = $pixdb->get(
    'membership_plans',
    $mbConds,
    'id,title'
);
$planList = [];
?>
<div class="memb-plans">
    <div class="plan-hed">
        Membership Plans
    </div>
    <div class="plan-list">
        <?php
        foreach ($membershiPlans->data as $row) {
            $planList[] = [$row->id, $row->title];
        }

        $planCountMax = 0;
        foreach ($planList as $i => $plan) {
            $planCount = $planArr[$plan[1]] ?? 0;
            $planList[$i][] = $planCount;
            if ($planCount > $planCountMax) {
                $planCountMax = $planCount;
            }
        }
        function planSort($x, $y)
        {
            return $x[2] < $y[2] ? 1 : 0;
        }
        usort($planList, 'planSort');

        $planDetail = function ($name, $count) use ($planCountMax) {
        ?>
            <div class="plan-details">
                <div class="plan-info">
                    <div class="info-top">
                        <div class="plan-name">
                            <?php echo $name; ?>
                        </div>
                        <div class="plan-cnt">
                            <?php echo $count; ?>
                        </div>
                    </div>
                    <div class="info-btm">
                        <div class="prgr-bar">
                            <div class="br-growth" style="width:<?php echo $planCountMax > 0 ? (intval($count) / $planCountMax) * 100 : 0; ?>%;"></div>
                        </div>
                    </div>
                </div>
            </div>
        <?php
        };

        foreach ($planList as $row) {
            $planDetail(
                $row[1],
                $row[2]
            );
        }
        ?>
    </div>
</div>