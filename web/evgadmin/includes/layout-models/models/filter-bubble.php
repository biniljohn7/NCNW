<?php
function filterBubble(
    $options = array()
) {

    global $pix;
    $options = (object)$options;

    function dateRange($key)
    {
        $r = '';

        if (isset($_GET[$key])) {
            $dates = $_GET[$key];
            $stDate = isset($dates['start']) ? esc($dates['start']) : null;
            $enDate = isset($dates['end']) ? esc($dates['end']) : null;

            if ($stDate && $enDate) {
                $r = "$stDate &nbsp; to &nbsp; $enDate";
            } elseif ($stDate) {
                $r = "from $stDate";
            } elseif ($enDate) {
                $r = "upto $enDate";
            }
        }
        return $r;
    }

    if (isset($options->items)) {
        $output = [];
        foreach ($options->items as $op => $opn) {
            if (isset($_GET[$op])) {
                $labelText = $opn['label'];
                $valueText = '';
                $value = $_GET[$op];

                if (is_array($value)) {
                    $valueText = implode(',', $value);
                } else {
                    $valueText = esc(have($_GET[$op]));
                }

                if (
                    isset($opn['value']) &&
                    $opn['value'] &&
                    isset($_GET[$op])
                ) {
                    $getVal = $valueText;
                    $valueText = $opn['value'];

                    // key based
                    if (is_array($valueText)) {
                        $valueText = $valueText[$getVal] ?? $getVal;
                    }
                }


                if (
                    isset($opn['valueRender']) &&
                    $opn['valueRender']
                ) {
                    $valueText = call_user_func(
                        $opn['valueRender'],
                        $op
                    );
                }

                if ($valueText != '') {
                    $s = $_GET;
                    $urlSec = '';

                    foreach ($s as $key => $value) {
                        if (
                            // $key === 'page' ||
                            strpos($key, 'v') === 0
                        ) {
                            $urlSec .= $value . '/';
                            unset(
                                $s[$key]
                            );
                        }
                    }

                    unset(
                        $s[$op],
                        $s['pgn']
                    );

                    $output[] = [
                        $labelText,
                        $valueText,
                        $urlSec . '?' . http_build_query($s)
                    ];
                }
            }
        }
        if (!empty($output)) {
?>
            <div class="<?php echo $options->class ?? ''; ?>">
                <?php
                foreach ($output as $out) {
                ?>
                    <div class="bubble-item ">
                        <span>
                            <?php echo $out[0]; ?>
                        </span>
                        <?php echo $out[1]; ?>
                        <a href="<?php echo ADMINURL, $out[2]; ?>" class="bubble-close">
                            <span class="material-symbols-outlined">
                                close
                            </span>
                        </a>
                    </div>
                <?php
                }
                ?>
            </div>
<?php
        }
    }
}
?>