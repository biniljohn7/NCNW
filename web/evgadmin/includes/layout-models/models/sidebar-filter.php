<?php
function sidebarFilter(
    $title = 'Filter Results',
    $inputs = array()
) {
    /*

    type
        hidden
        text
            placeholder - string

        select
            options
                [
                    key:value,
                    key:value
                ]

        check-group
            options
                [
                    [label, val, true/false(for check)],
                    [label, val],
                ]

        radio-group
            options
                    [
                        [label, val, true/false(for check)],
                        [label, val],
                    ]

        number-range
            rangeType
                text
            reverse - true/false
            min
            max
            step
            default
                [start val, end val]

        date-range
            default
                [start val, end val]

        color-filter


    label - label for input


    name - input name
        string except:
        // number range, date range
            [start_name, end_name]


    getKey - get varable for filter
        string except:
        // number range, date range
            [start_key, end_key]

    

    */
?>
    <div class='sidebar-filter' id='sidebarFilterModel'>
        <div class='filter-hed'>
            <span class='material-symbols-outlined f-icon'>filter_alt</span>
            <sppan class='f-txt'>
                <?php echo $title;
                ?>
                </span>
        </div>
        <form method='get'>
            <div class='inner-section'>
                <div class='safe-right'>
                    <?php
                    foreach ($inputs as $inp) {
                        $inp = (object)$inp;
                        $type = have($inp->type, 'text');

                        if ($type == 'hidden') {
                            echo '<input 
                                type="hidden"
                                name="' . have($inp->name) . '"
                                value="' . have($inp->value) . '"
                            />';
                        } else {

                            $inputValue = '';
                            $getKey = '';

                            if ($type == 'text') {
                                $inputValue = have($inp->default);
                                $getKey = have($inp->getKey);

                                if (
                                    $getKey &&
                                    isset($_GET[$getKey])
                                ) {
                                    $getVal = esc($_GET[$getKey]);
                                    if ($getVal) {
                                        $inputValue = $getVal;
                                    }
                                }
                            }
                    ?>
                            <div class='form-row mb30'>
                                <div class='form-label'>
                                    <?php echo have($inp->label, 'Un-Labeled Search');
                                    ?>
                                </div>
                                <div class='form-input'>
                                    <?php
                                    if ($type == 'text') {
                                        echo '<input
                                            type="text"
                                            name="' . have($inp->name) . '"
                                            class="text-input ' . have($inp->class) . '"
                                            value="' . $inputValue . '"
                                            autocomplete="off"
                                            placeholder="' . have($inp->placeholder) . '"
                                        >';
                                    } elseif ($type == 'date-range') {
                                        $startDate = '';
                                        $endDate = '';

                                        if (
                                            isset($inp->getKey) &&
                                            count($inp->getKey) == 2
                                        ) {
                                            $startDate = have($_GET[$inp->getKey[0]]) ?
                                                esc($_GET[$inp->getKey[0]]) : '';
                                            $endDate = have($_GET[$inp->getKey[1]]) ?
                                                esc($_GET[$inp->getKey[1]]) : '';
                                        }

                                        if (
                                            isset($inp->default) &&
                                            count($inp->default) == 2
                                        ) {
                                            $startDate = $startDate ?: $inp->default[0];
                                            $endDate = $endDate ?: $inp->default[1];
                                        }
                                    ?>
                                        <div class="date-range-box">
                                            <div class="dt-box">
                                                <?php
                                                echo '<input
                                                    type="text"
                                                    placeholder="from"
                                                    class="dt-rng-input rng-start"
                                                    name="' . have($inp->name[0]) . '"
                                                    autocomplete="off"
                                                    value="' . $startDate . '"
                                                />';
                                                ?>
                                            </div>
                                            <div class="dt-sep">
                                                to
                                            </div>
                                            <div class="dt-box">
                                                <?php
                                                echo '<input
                                                    type="text"
                                                    placeholder="up-to"
                                                    class="dt-rng-input rng-end"
                                                    autocomplete="off"
                                                    name="' . have($inp->name[1]) . '"
                                                    value="' . $endDate . '"
                                                />';
                                                ?>
                                            </div>
                                        </div>
                                    <?php
                                    } elseif ($type == 'check-group') {
                                        if (isset($inp->options)) {
                                            $inpName = have($inp->name);
                                            $getVar = have($inp->getKey);
                                            $getVals = array();
                                            $haveGet = false;
                                            if (
                                                $getVar &&
                                                isset($_GET[$getVar])
                                            ) {
                                                $haveGet = true;
                                                $getVals = $_GET[$getVar];
                                                if (is_array($getVals)) {
                                                    $getVals = array_map('esc', $getVals);
                                                } else {
                                                    $getVals = array();
                                                }
                                            }
                                            foreach ($inp->options as $opn) {
                                                $label = $opn;
                                                $value = $opn;
                                                $defaultChkd = isset($opn[2]) && $opn[2];

                                                if (is_array($opn)) {
                                                    $label = have($opn[0]);
                                                    $value = have($opn[1]);
                                                }

                                                echo '<div class="mb10">
                                                    <label class="pix-check">
                                                        <input 
                                                            type="checkbox" 
                                                            autocomplete="off" 
                                                            value="', $value, '"
                                                            ', $inpName ? 'name="' . $inpName . '[]"' : '', '
                                                            ', ($haveGet ? in_array($value, $getVals) : $defaultChkd) ? 'checked' : '', '
                                                        />
                                                        <span class="pix-check-tik material-symbols-outlined">
                                                            check
                                                        </span>
                                                        <span class="chk-txt">
                                                            ', $label, '
                                                        </span>
                                                    </label>
                                                </div>';
                                            }
                                        }
                                    } elseif ($type == 'number-range') {
                                        $minNum = 0;
                                        $maxNum = 20;
                                        $incStep = 1;
                                        $minOps = '<option value="">Any</option>';
                                        $maxOps = '<option value="">Any</option>';
                                        $stName = '';
                                        $enName = '';
                                        $actStart = null;
                                        $actEnd = null;
                                        $stGkey = '';
                                        $enGkey = '';
                                        $reverse = isset($inp->reverse) && $inp->reverse;

                                        if (isset($inp->min)) {
                                            $minNum = $inp->min;
                                        }
                                        if (isset($inp->max)) {
                                            $maxNum = $inp->max;
                                        }
                                        if (isset($inp->step)) {
                                            $incStep = $inp->step;
                                        }
                                        if (isset($inp->name[0])) {
                                            $stName = $inp->name[0];
                                        }
                                        if (isset($inp->name[1])) {
                                            $enName = $inp->name[1];
                                        }
                                        if (isset($inp->getKey[1])) {
                                            $stGkey = $inp->getKey[0];
                                        }
                                        if (isset($inp->getKey[1])) {
                                            $enGkey = $inp->getKey[1];
                                        }


                                        if (isset($_GET[$stGkey])) {
                                            $actStart = esc($_GET[$stGkey]);
                                        }
                                        if (isset($_GET[$enGkey])) {
                                            $actEnd = esc($_GET[$enGkey]);
                                        }

                                        if (
                                            !$actStart &&
                                            isset($inp->default[0])
                                        ) {
                                            $actStart = $inp->default[0];
                                        }
                                        if (
                                            !$actEnd &&
                                            isset($inp->default[1])
                                        ) {
                                            $actEnd = $inp->default[1];
                                        }

                                        for (
                                            $i = ($reverse ? $maxNum : $minNum);
                                            ($reverse ?
                                                $i >= $minNum :
                                                $i <= $maxNum
                                            );
                                            $i += $incStep * ($reverse ? -1 : 1)
                                        ) {
                                            $minOps .= '<option' .
                                                ($i == $actStart && $actStart !== null ? ' selected' : '') .
                                                '>' . $i . '</option>';

                                            $maxOps .= '<option' .
                                                ($i == $actEnd && $actEnd !== null ? ' selected' : '') .
                                                '>' . $i . '</option>';
                                        }
                                    ?>
                                        <div class="date-range-box">
                                            <div class="dt-box">
                                                <?php
                                                if (
                                                    isset($inp->rangeType) &&
                                                    $inp->rangeType == "text"
                                                ) {
                                                    echo '<input
                                                        type="text"
                                                        placeholder="from"
                                                        name="' . $stName . '"
                                                        autocomplete="off"
                                                        value="' . $actStart . '"
                                                    />';
                                                } else {
                                                    echo '<select
                                                        name="' . $stName . '"
                                                        class="text-input"
                                                    >' . $minOps . '</select>';
                                                }
                                                ?>
                                            </div>
                                            <div class="dt-sep">
                                                to
                                            </div>
                                            <div class="dt-box">
                                                <?php
                                                if (
                                                    isset($inp->rangeType) &&
                                                    $inp->rangeType == "text"
                                                ) {
                                                    echo '<input
                                                        type="text"
                                                        placeholder="up-to"
                                                        name="' . $enName . '"
                                                        autocomplete="off"
                                                        value="' . $actEnd . '"
                                                    />';
                                                } else {
                                                    echo '<select
                                                        name="' . $enName . '"
                                                        class="text-input"
                                                    >' . $maxOps . '</select>';
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <?php
                                    } elseif ($type == 'radio-group') {
                                        if (isset($inp->options)) {
                                            $inpName = have($inp->name);
                                            $getVar = have($inp->getKey);
                                            $getVal = null;
                                            if (
                                                $getVar &&
                                                isset($_GET[$getVar])
                                            ) {
                                                $getVal = esc($_GET[$getVar]);
                                            }
                                            foreach ($inp->options as $opn) {
                                                $label = $opn;
                                                $value = $opn;

                                                if (is_array($opn)) {
                                                    $label = have($opn[0]);
                                                    $value = have($opn[1]);
                                                }

                                                $check = $getVal ?
                                                    $getVal == $value :
                                                    is_array($opn) && isset($opn[2]);


                                                echo '<div class="mb10">
                                                    <label class="pix-radio">
                                                        <input 
                                                            type="radio" 
                                                            name="' . $inpName . '" 
                                                            value="' . $value . '" 
                                                            ' . ($check ? 'checked' : '') . '
                                                        />
                                                        <span class="rddot"></span>
                                                        <span class="rdtxt">' . $label . '</span>
                                                    </label>
                                                </div>';
                                            }
                                        }
                                    } elseif ($type == 'select') {
                                        $opns       = '<option value="">Any</optipn>';
                                        $gKey       = have($inp->getKey);
                                        $inputValue = '';

                                        if (
                                            $gKey &&
                                            isset($_GET[$gKey])
                                        ) {
                                            $getVal = esc($_GET[$gKey]);
                                            if ($getVal) {
                                                $inputValue = $getVal;
                                            }
                                        }

                                        if (
                                            isset($inp->option) &&
                                            is_array($inp->option)
                                        ) {
                                            foreach ($inp->option as $ky => $val) {
                                                $opns .= '<option' .
                                                    ($ky == $inputValue && $inputValue !== '' ? ' selected' : '') .
                                                    ' value="' . $ky . '">' . $val . '</optipn>';
                                            }
                                        }
                                        echo '<select
                                            name="' . have($inp->name) . '"
                                            class="text-input ' . have($inp->class) . '"
                                        >' . $opns . '</select>';
                                    } elseif ($type == 'color-filter') {
                                        $inpName    = have($inp->name);
                                        $getVar     = have($inp->getKey);
                                        $getVals    = array();
                                        $haveGet    = false;
                                        if (
                                            $getVar &&
                                            isset($_GET[$getVar])
                                        ) {
                                            $haveGet = true;
                                            $getVals = $_GET[$getVar];
                                            if (is_array($getVals)) {
                                                $getVals = array_map('esc', $getVals);
                                            } else {
                                                $getVals = array();
                                            }
                                        }

                                        if (
                                            isset($inp->clrList)
                                        ) {
                                        ?>
                                            <div class="colour-box">
                                                <?php
                                                $colorsList = $inp->clrList;
                                                foreach ($colorsList as $colKey => $colData) {
                                                    if (
                                                        isset(
                                                            $colData->name,
                                                            $colData->code
                                                        )
                                                    ) {
                                                        echo '<label class="clr-item" style="background-color:#', $colData->code, '">
                                                            <input 
                                                                type="checkbox" 
                                                                class="' . have($inp->class) . '"
                                                                value="', $colKey, '"
                                                                ', $inpName ? 'name="' . $inpName . '[]"' : '', '
                                                                ', in_array($colKey, $getVals) ? 'checked' : '', '
                                                            >
                                                            <span class="material-symbols-outlined">
                                                                check
                                                            </span>
                                                            <span class="hover-text text-09">
                                                                ', $colData->name, '
                                                            </span>
                                                        </label>';
                                                    }
                                                }
                                                ?>
                                            </div>
                                    <?php
                                        }
                                    } elseif ($type == 'multiple-select') {
                                        if($inp->funName) {
                                            echo call_user_func_array(
                                                $inp->funName,
                                                [json_decode(json_encode($inp), true)]
                                            );
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                    <?php
                        }
                    }
                    ?>
                </div>
            </div>
            <div class='actions'>
                <button class='pix-btn site' type='submit'>Apply Filters</button>
                <span class='pix-btn' id='cancelSidebarFilter'>Cancel</span>
            </div>
        </form>
    </div>


<?php
}
?>