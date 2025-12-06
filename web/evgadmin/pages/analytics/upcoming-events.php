<?php
global $date;
$eventList = $pixdb->get(
    'events',
    [
        '#SRT' => 'id desc',
        '__QUERY__' => [
            'date >= "' . $date . '"'
        ],
        '__limit' => 10
    ],
    'id,name,enabled,date,scope,image'
);
?>
<div class="up-event sold-prd">
    <div class="evnt-hed">
        Upcoming Events
    </div>
    <div class="evnt-lists">
        <?php
        foreach ($eventList->data as $row) {
            $dtlLink = $pix->adminURL . '?page=events&sec=details&id=' . $row->id;
        ?>
            <div class="evnt-list">
                <a href="<?php echo $dtlLink; ?>">
                    <span class="evnt-left">
                        <span class="evnt-wrap">
                            <span class="evnt-thumb">
                                <?php
                                if ($row->image) {
                                    $row->image = $pix->uploadPath . 'events/' . $pix->thumb($row->image, '150x150');
                                ?>
                                    <img src="<?php echo $row->image; ?>">
                                <?php
                                } else {
                                ?>
                                    <span class="no-img">
                                        <span class="material-symbols-outlined icon">
                                            hide_image
                                        </span>
                                    </span>
                                <?php
                                }
                                ?>
                            </span>
                            <span class="evnt-info">
                                <span class="evnt-name">
                                    <?php
                                    echo $row->name;
                                    ?>
                                </span>
                                <span class="evnt-date">
                                    <?php
                                    echo date('j F Y', strtotime($row->date));
                                    ?>
                                </span>
                            </span>
                        </span>
                    </span>
                    <span class="evnt-right">
                        <span class="sts">
                            <?php echo ucfirst($row->scope); ?>
                        </span>
                        <span class="sts  <?php echo $row->enabled == 'Y' ? 'active' : 'inactive' ?>">
                            <?php
                            echo $row->enabled == 'Y' ?
                                'Published' :
                                'Un-Published';
                            ?>
                        </span>
                    </span>
                </a>
            </div>
        <?php
        }
        if (!$eventList->pages) {
        ?>
            <div class="no-res">
                No events
            </div>
        <?php
        }
        ?>
    </div>
</div>