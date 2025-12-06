<?php
if ($ncnwTeam) {
?>
    <div class="tr-btm card">
        <div class="req-btn">
            <a href="<?php echo $pix->adminURL . "?page=requests&sec=mod&id=$rData->id"; ?>" class="edt btn">
                Modify Request
            </a>
        </div>
        <div class="req-btn">
            <a href="<?php echo $pix->adminURL . "actions/anyadmin/?method=request-delete&id=$rData->id"; ?>" class="dlt btn confirm">
                Delete
            </a>
        </div>
    </div>
<?php
}
?>