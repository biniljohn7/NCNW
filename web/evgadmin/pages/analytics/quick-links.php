<?php
$links = [];
if ($isSuperAdmin || ($pix->canAccess('elect') && $isSectionPresident)) {
    $links[] = (object)[
        'title' => 'Total Number of Delegates Selected',
        'url' => '?page=delegates'
    ];
}

// memberships
$plans = $pixdb->get(
    'membership_plans',
    ['active' => 'Y'],
    'id, title'
)->data;
foreach ($plans as $row) {
    $links[] = (object)[
        'title' => 'Total Members have ' . $row->title,
        'url' => '?page=members&sh_memb_sts[]=active&mp_sort=' . $row->id
    ];
}

//payment categories
// $products = $pixdb->get(
//     'products',
//     ['enabled' => 'Y'],
//     'id, name'
// )->data;
// foreach ($products as $row) {
//     $links[] = (object)[
//         'title' => 'Total Members have ' . $row->name,
//         'url' => '?page=members&sh_memb_sts[]=active&mp_sort=' . $row->id
//     ];
// }
?>
<div class="top-cards ref">
    <div class="card-hed">
        Quick Links
    </div>
    <div class="card-lists">
        <?php
        foreach ($links as $row) {
        ?>
            <div class="card-list">
                <a href="<?php echo $pix->adminURL . $row->url; ?>">
                    <?php echo $row->title; ?>
                </a>
            </div>
        <?php
        }
        ?>
    </div>
</div>