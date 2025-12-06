<?php
$nations = $pixdb->get(
    'nations',
    [
        '#SRT' => 'id desc'
    ],
    'id,name'
);
$nData = [];
foreach ($nations->data as $row) {
    $nData[$row->id] = [
        'id' => $row->id,
        'name' => $row->name
    ];
}
?>
<div class="nation-graph">
    <div class="graph-details">
        <div class="grp-box" id="gpNations"></div>
    </div>
</div>
<?php
$nt = new stdClass();
$nt->nations = $nData;
?>
<script type="text/javascript">
    var nData = <?php echo json_encode($nt); ?>
</script>