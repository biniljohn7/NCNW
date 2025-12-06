<?php
$id = esc($_GET['id'] ?? 'new');
$new = $id == 'new';

if (!$new) {
    $dataOk = false;
    if ($id) {
        $evData = $pixdb->getRow('events', ['id' => $id]);
        $dataOk = !!$evData;
    }
    if (!$dataOk) {
        $pix->addmsg('Unknown career');
        $pix->redirect('?page=career');
    }
}

// location scope info
$evLoc = !$new ?
    $pixdb->getRow('event_locations', ['event' => $id]) :
    false;

// active nation
$nations = $pixdb->get(
    'nations',
    ['#SRT' => 'name asc'],
    'id, name'
);

$selNation = $evLoc->nation ?? 1;
$selRegion = $evLoc->region ?? null;
$selState = $evLoc->state ?? null;
$selChapter = $evLoc->chapter ?? null;

// active region
$regions = $pixdb->get(
    'regions',
    [
        'nation' => $selNation,
        '#SRT' => 'name asc'
    ],
    'id, name'
);

// active states
$states = $selRegion ?
    $pixdb->get(
        'states',
        [
            'region' => $selRegion,
            '#SRT' => 'name asc'
        ],
        'id, name'
    )->data : [];

// active chapters
$chapters = $selState ?
    $pixdb->get(
        'chapters',
        [
            'state' => $selState,
            '#SRT' => 'name asc'
        ],
        'id, name'
    )->data : [];


loadStyle('pages/events/mod');
loadScript('pages/events/mod');
?>
<h1>
    <?php
    echo $new ? 'Add an' : 'Modify'
    ?>
    Event
</h1>
<?php
breadcrumbs(
    [
        'Events',
        '?page=events'
    ],
    !$new ? [
        $evData->name,
        "?page=events&sec=details&id=$evData->id"
    ] : null,
    [
        $new ? 'Create an Event' : 'Modify'
    ]
)
?>
<form action="<?php echo ADMINURL, 'actions/anyadmin/'; ?>" method="post" id="eventForm">
    <input type="hidden" name="method" value="event-save" />
    <?php
    if (!$new) {
    ?>
        <input type="hidden" name="id" value="<?php echo $id; ?>" />
    <?php
    }
    ?>
    <div class="fm-field">
        <div class="fld-label">
            Name
        </div>
        <div class="fld-inp">
            <input type="text" size="70" name="name" value="<?php echo $new ? '' : $evData->name; ?>" data-type="string">
        </div>
    </div>

    <div class="fm-field">
        <div class="fld-label">
            Start Date
        </div>
        <div class="fld-inp">
            <input type="text" size="25" name="date" id="evDate" value="<?php echo $new || empty($evData->date) ? '' : date('d / F / Y', strtotime($evData->date)); ?>" data-type="string" autocomplete="off">
        </div>
    </div>

    <div class="fm-field">
        <div class="fld-label">
            End Date
        </div>
        <div class="fld-inp">
            <input type="text" size="25" name="enddate" id="evEndDate" value="<?php echo $new || empty($evData->enddate) ? '' : date('d / F / Y', strtotime($evData->enddate)); ?>" autocomplete="off">
        </div>
    </div>

    <div class="fm-field">
        <div class="fld-label">
            Category
        </div>
        <div class="fld-inp">
            <select name="category" data-type="string">
                <option value="">
                    Choose Category
                </option>
                <?php
                $categories = $pixdb->get(
                    'categories',
                    [
                        '#SRT' => 'ctryName asc',
                        'type' => 'Event'
                    ],
                    'id, ctryName'
                )->data;

                $selCat = $new ? '' : $evData->category;
                foreach ($categories as $ct) {
                    echo '<option ', $selCat == $ct->id ? 'selected' : '', ' value="', $ct->id, '">', $ct->ctryName, '</option>';
                }
                ?>
            </select>
        </div>
    </div>

    <div class="fm-field">
        <div class="fld-label">
            Visibility
        </div>
        <div class="fld-inp">
            <?php
            CheckBox(
                'Publish event',
                'visibility',
                1,
                $new ||
                    (!$new && $evData->enabled == 'Y')
            );
            ?>
        </div>
    </div>

    <div class="fm-field">
        <div class="fld-label">
            Country
        </div>
        <div class="fld-inp">
            <select name="nation" id="nationSel">
                <?php
                foreach ($nations->data as $row) {
                    echo '<option ', $row->id == $selNation ? 'selected' : '', ' value="', $row->id, '">', $row->name, '</option>';
                }
                ?>
            </select>
        </div>
    </div>

    <div class="fm-field">
        <div class="fld-label">
            Region
        </div>
        <div class="fld-inp">
            <div id="regionLdr" class="pt5" style="display: none;">
                loading regions..
            </div>
            <select name="region" id="regionSel">
                <option value="">All Regions</option>
                <?php
                foreach ($regions->data as $row) {
                    echo '<option ', $row->id == $selRegion ? 'selected' : '', ' value="', $row->id, '">', $row->name, '</option>';
                }
                ?>
            </select>
        </div>
    </div>

    <div class="fm-field">
        <div class="fld-label">
            State
        </div>
        <div class="fld-inp">
            <div id="stateLdr" class="pt5" style="display: none;">
                loading states..
            </div>
            <select name="state" id="stateSel">
                <option value="">All States</option>
                <?php
                foreach ($states as $row) {
                    echo '<option ', $row->id == $selState ? 'selected' : '', ' value="', $row->id, '">', $row->name, '</option>';
                }
                ?>
            </select>
        </div>
    </div>

    <div class="fm-field">
        <div class="fld-label">
            Section
        </div>
        <div class="fld-inp">
            <div id="chapterLdr" class="pt5" style="display: none;">
                loading sections..
            </div>
            <select name="chapter" id="chapterSel">
                <option value="">All Sections</option>
                <?php
                foreach ($chapters as $row) {
                    echo '<option ', $row->id == $selChapter ? 'selected' : '', ' value="', $row->id, '">', $row->name, '</option>';
                }
                ?>
            </select>
        </div>
    </div>

    <div class="fm-field">
        <div class="fld-label">
            Location Address
        </div>
        <div class="fld-inp">
            <textarea cols="50" rows="4" name="address" data-type="string"><?php echo $evData->address ?? ''; ?></textarea>
            <br />
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
            Description
        </div>
        <div class="fld-inp">
            <textarea cols="100" rows="15" name="description" data-type="string"><?php echo $evData->descrptn ?? ''; ?></textarea>
            <br />
        </div>
    </div>
    <div class="fm-field">
        <div class="fld-label">
        </div>
        <div class="fld-inp">
            <input type="submit" class="pix-btn lg site bold-500" value="Save Details">
        </div>
    </div>
</form>