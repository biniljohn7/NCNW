<div class="rcnt-itm">
    <div class="itm-top">
        <div class="itm-hed">
            Recent Inquiries
        </div>
        <div class="itm-view">
            <a href="<?php echo ADMINURL, '?page=enquiries'; ?>">View all</a>
        </div>
    </div>
    <?php
    $enqConds = [
        '#SRT' => 'CASE WHEN `read`="N" THEN 1 ELSE 0 END desc,id desc',
        '__limit' => 10
    ];

    $enquiries = $pixdb->get(
        'enquiries',
        $enqConds
    );
    ?>
    <div class="itm-lists">
        <?php
        foreach ($enquiries->data as $row) {
        ?>
            <div class="lists-itm <?php echo $row->read == 'N' ? 'unread' : ''; ?>">
                <a href="<?php echo ADMINURL, "?page=enquiries&sec=details&id=$row->id"; ?>" class="list-item">
                    <span class="list-top">
                        <span class="top-itms">
                            <span class="material-symbols-outlined icn">
                                label
                            </span>
                            <span class="itm-dtl">
                                <span class="dtl-lb">
                                    Name
                                </span>
                                <span class="dtl-val">
                                    <?php
                                    echo $row->name;
                                    ?>
                                </span>
                            </span>
                        </span>
                        <span class="top-itms">
                            <span class="material-symbols-outlined icn">
                                subject
                            </span>
                            <span class="itm-dtl">
                                <span class="dtl-lb">
                                    Subject
                                </span>
                                <span class="dtl-val">
                                    <?php
                                    echo $row->subject;
                                    ?>
                                </span>
                            </span>
                        </span>
                        <span class="top-itms">
                            <span class="material-symbols-outlined icn">
                                calendar_month
                            </span>
                            <span class="itm-dtl">
                                <span class="dtl-lb">
                                    Date
                                </span>
                                <span class="dtl-val">
                                    <?php
                                    echo date('d F Y h:ia', strtotime($row->date));
                                    ?>
                                </span>
                            </span>
                        </span>
                    </span>
                    <span class="list-btm">
                        <span class="btm-itms">
                            <span class="material-symbols-outlined icn">
                                chat
                            </span>
                            <span class="itm-dtl">
                                <span class="dtl-lb">
                                    Message
                                </span>
                                <span class="dtl-val">
                                    <?php
                                    echo substr($row->description, 0, 50), strlen($row->description) > 50 ? '...' : '';
                                    ?>
                                </span>
                            </span>
                        </span>
                    </span>
                </a>
            </div>
        <?php
        }
        ?>
    </div>
</div>