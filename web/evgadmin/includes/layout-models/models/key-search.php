<?php
function KeySearch(
    $name,
    $value = '',
    $placeHolder = 'Search by keywords',
    $btnLabel = 'Search'
) {
?>
    <div class="pix-key-search">
        <input type="text" name="<?php echo $name; ?>" size="20" placeholder="<?php echo $placeHolder; ?>" class="srch" value="<?php echo $value; ?>">
        <span class="material-symbols-outlined icn">
            search
        </span>
        <div class="sh-btn">
            <button type="submit">
                <?php echo $btnLabel; ?>
            </button>
        </div>
    </div>
<?php
}
?>