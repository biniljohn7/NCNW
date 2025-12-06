<?php
function breadcrumbs($a)
{
    global $pix;
    $args = func_get_args();
?>
    <div class="breadcrumbs">
        <a href="<?php echo ADMINURL; ?>" class="cm-item">
            <span class="material-symbols-outlined">
                home
            </span>
            Home
        </a>
        <?php
        foreach ($args as $ag) {
            if ($ag) {
                if (isset($ag[1])) {
                    echo '<a href="', ADMINURL, $ag[1], '" class="cm-item">', $ag[0], '</a>';
                } else {
                    echo '<span class="cm-item">', $ag[0], '</span>';
                }
            }
        }
        ?>
    </div>
<?php
}
function pageHeading($title, $filterBtn = false)
{
?>
    <div class="top-section">
        <span class="heading-1">
            <?php echo $title; ?>
        </span>
        <?php
        if ($filterBtn) {
        ?>
            <div class="list-filter-btn" id="listFilterButton">
                <span class="material-symbols-outlined">
                    filter_alt
                </span>
            </div>
        <?php
        }
        ?>
    </div>
<?php
}
function getReadFullText($text, $limit = 150)
{
    if (strlen($text) > $limit) {
        return '<div>
            <div class="rdfull-short">
                ' . substr($text, 0, $limit) . '
                ...
                <span class="a-link lined read-full-text-btn">read full</span>
            </div>
            <div class="rdfull-full" style="display: none;">
                ' . nl2br($text) . '
            </div>
        </div>';
    } else {
        return '<div>' . nl2br($text) . '</div>';
    }
}
function ReadFullText($text, $limit = 150)
{
    echo getReadFullText($text, $limit);
}
function StickMenu($o)
{
    global $pix;
    $options = func_get_args();

    /* 
        icon,
        label,
        id,
        hidden,
        link,
        target
    */
?>
    <div class="pix-sticky-btn">
        <div class="btn-obj">
            <span class="material-symbols-outlined">
                more_vert
            </span>
        </div>
        <div class="btn-menu">
            <?php
            foreach ($options as $opn) {
                $attrs = (isset($opn[2]) ? 'id="' . $opn[2] . '" ' : '') .
                    (isset($opn[3]) && $opn[3] == true ? 'style="display:none;" ' : '');
                $tag = 'span';
                if (
                    isset($opn[4]) &&
                    $opn[4]
                ) {
                    $tag = 'a';
                    $attrs .= 'href="' . (preg_match('/^http/i', $opn[4]) ?
                        $opn[4] :
                        $pix->adminURL . $opn[4]
                    ) . '" ';

                    if (
                        isset($opn[5]) &&
                        $opn[5]
                    ) {
                        $attrs .= ' target="' . $opn[5] . '" ';
                    }
                }
                echo '<' . $tag . ' 
                    class="btn-menu-item"
                    ' . $attrs . '
                >
                    <span class="material-symbols-outlined">
                        ', $opn[0], '
                    </span>
                    ', $opn[1], '
                </' . $tag . ' >';
            }
            ?>
        </div>
    </div>
<?php
}
function ButtonMenu($btnLabel, $items)
{
    $menuItems = '';

    foreach ($items as $itm) {
        $tag = 'span';
        $class = 'menu-btn';
        $attrs = '';

        if (isset($itm[1])) {
            $link = isset($itm[1]['link']) ? $itm[1]['link'] : false;
            if ($link) {
                $link = ADMINURL . $link;
                $tag = 'a';
                $attrs .= ' href="' . $link . '"';
            }
            if (isset($itm[1]['class'])) {
                $class .= ' ' . $itm[1]['class'];
            }
            if (($itmId = $itm[1]['id'] ?? '')) {
                $attrs .= ' id="' . $itmId . '"';
            }
            if (isset($itm[1]['data'])) {
                foreach ($itm[1]['data'] as $dk => $dv) {
                    $attrs .= ' data-' . $dk . '="' . $dv . '"';
                }
            }
        }
        $attrs .= 'class="' . $class . '"';
        $menuItems .= '<' . $tag . ' ' . $attrs . '>' . $itm[0] . '</' . $tag . '>';
    }

    echo '<div class="pix-btn-menu">
        <div class="btn-obj">
            <span class="btn-label">', $btnLabel, '</span>
            <span class="material-symbols-outlined">
                arrow_drop_down
            </span>
        </div>
        <div class="btn-menu">
            ', $menuItems, '
        </div>
    </div>';
}
function CheckBox(
    $label,
    $name = '',
    $value = '',
    $checked = false,
    $id = null,
    $class = ''
) {
    echo '<label class="pix-check ', ($class ? $class . '-lbl' : ''), '">
        <input 
            type="checkbox" 
            autocomplete="off" 
            value="', $value, '"
            ', ($name ? 'name="' . $name . '[]"' : ''), '
            ', ($checked ? 'checked' : ''), '
            ', ($class ? 'class="' . $class . '-chk"' : ''), '
            ', ($id ? 'id="' . $id . '"' : ''), '
        />
        <span class="pix-check-tik material-symbols-outlined">
            check
        </span>
        <span class="chk-txt">
            ', $label, '
        </span>
    </label>';
}
function Radio(
    $label,
    $name = '',
    $value = '',
    $checked = false,
    $id = null,
    $class = '',
    $otherAttrs = ''
) {
    echo '<label class="pix-radio">
        <input 
            type="radio" 
            autocomplete="off" 
            value="', $value, '"
            ', ($name ? 'name="' . $name . '"' : ''), '
            ', ($checked ? 'checked' : ''), '
            ', ($class ? 'class="' . $class . '-rdo"' : ''), '
            ', ($id ? 'id="' . $id . '"' : ''), '
            ', $otherAttrs, '
        />
        <span class="rddot"></span>
        <span class="rdtxt">' . $label . '</span>
    </label>';
}
function StickyButton($link = '', $icon = 'add', $id = '', $attrs = array())
{
    $attrs = (object)$attrs;
    $attrId = $id ? ' id="' . $id . '"' : '';
    $hidden = isset($attrs->hidden) && $attrs->hidden == true ? ' style="display:none;"' : '';
    echo
    $link ? '<a class="pix-spl-sticky-btn" href="' . $link . '" ' . $attrId . $hidden . '>' :
        '<span class="pix-spl-sticky-btn" ' . $attrId . $hidden . '>',
    '<span class="material-symbols-outlined">
            ' . $icon . '
        </span>',
    $link ? '</a>' : '</span>';
}
function NoResult(
    $heading = 'No Results',
    $txt = 'Sorry. No results found.',
    $btn = false
) {
?>
    <div class="mdl-no-result">
        <span class="material-symbols-outlined">
            view_comfy_alt
        </span>
        <div class="nrs-heading">
            <?php echo $heading; ?>
        </div>
        <div class="nrs-text">
            <?php echo $txt; ?>
        </div>
        <?php
        if ($btn) {
        ?>
            <div class="nrs-btn-box">
                <a href="<?php echo $btn[1] ?? '#'; ?>" class="pix-btn <?php echo $btn[2] ?? 'site rounded'; ?>" id="<?php echo $btn[3] ?? 'nrsBtnObj'; ?>">
                    <?php echo $btn[0] ?? ''; ?>
                </a>
            </div>
        <?php
        }
        ?>
    </div>
<?php
}

function TabMenu($items, $tbKey = 'tab')
{
    $gtv = $_GET;
    $actTab = false;
    foreach ($items as $arg) {
        if (isset($_GET[$tbKey])) {
            $actTab = $_GET[$tbKey];
        } elseif (!$actTab) {
            $actTab = $arg[0];
        }
        $gtv[$tbKey] = $arg[0];

        echo '<a href="', ADMINURL, 'account/?', http_build_query($gtv), '" class="tab-item ', $actTab == $arg[0] ? 'active' : '', '">
            ', $arg[1], '
        </a>';
    }
}


function ToggleBtn($props = array())
{
    /* 

    id          id for the element. "Chk" suffix will be included
    class       class for element. "-chk" suffix will be included
    name        input name
    value       input value
    checked     input check
    
    */
    $props = (object)$props;
    echo '<label class="pix-toggle no-highlight">
        <input 
            type="checkbox" 
            id="', have($props->id), 'Chk"
            class="', have($props->class), '-chk"
            name="', have($props->name), '"
            value="', have($props->value), '"
            ', isset($props->checked) && $props->checked == true ? 'checked' : '', '
        />
        <div class="toggle-ind"></div>
    </label>';
}

function TabGroup($items)
{
    echo '<div class="pix-tab-groups">';

    foreach ($items as $item) {
        if ($item) {
            echo '<a href="',
            isset($item[2]) && $item[2] ?
                $item[2] : '',
            '" class="tab-btn ',
            isset($item[3]) && $item[3] ?
                'active' : '',
            '">',
            $item[0] ? '<span class="material-symbols-outlined">
                    ' . $item[0] . '
                </span>' : '',
            '
                <span class="btn-txt">
                    ', $item[1], '
                </span>
            </a>';
        }
    }

    echo '</div>';
}
function DangerZone(
    $zoneTitle = null,
    $textBody = null,
    $btnText = null,
    $btnLink
) {
    echo '<div class="danger-zone">
        <div class="dz-title">
            ', $zoneTitle ?: 'Danger Zone', '
        </div>
        <div class="dz-body">
            ', $textBody ?: 'Do you want to remove this permanantly? This is an irreversible process.', '
        </div>
        <a href="', $btnLink ?: '#', '" class="dz-btn confirm">
            ', $btnText ?: 'Yes. I Understand. Delete Now', '
        </a>
    </div>';
}
function AccessDenied()
{
    echo '<h1 style="color:red;">Access Denied</h1><p style="color:red;">Sorry. You don\'t have permission to access this section. Please contact admin.</p>';
}

$modelsDir = dirname(__FILE__) . '/models/';
if (is_dir($modelsDir)) {
    $models = array_slice(scandir($modelsDir), 2);
    foreach ($models as $mdl) {
        $modfile = $modelsDir . $mdl;
        if (is_file($modfile)) {
            include_once $modfile;
        }
    }
}
