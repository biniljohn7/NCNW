<div class="lt-tp">
    <div class="tp lf">
        <div class="req-id itm">
            <?php echo '#' . $rData->id; ?>
        </div>
        <div class="sts itm">
            <span class="material-symbols-outlined icn">
                <?php echo $iconsReq[$rData->status] ?? ''; ?>
            </span>
            <span class="attch-lbl">
                <?php echo $HelpDesk->getStatusName($rData->status); ?>
            </span>
        </div>
    </div>
    <div class="tp rt">
        <div class="attach itm">
            <a href="#attachments">
                <span class="material-symbols-outlined attach icn">
                    attach_file
                </span>
                <span class="bold-600 attach-count">
                    <?php echo $rData->attachments; ?>
                </span>
                <span class="attch-lbl">
                    Attachment<?php echo $rData->attachments > 1 ? 's' : ''; ?>
                </span>
            </a>
        </div>
        <div class="cmnts itm">
            <a href="#comments">
                <span class="material-symbols-outlined cmnt icn">
                    chat_bubble
                </span>
                <span class="bold-600" id="ttlComments">
                    <?php echo $rData->ttlComments; ?>
                </span>
                <span class="attch-lbl">
                    Comment<?php echo $rData->ttlComments > 1 ? 's' : ''; ?>
                </span>
            </a>
        </div>
    </div>
</div>

<div class="req-dtls">
    <?php
    if (isset($rData->summary)) {
        echo ReadFullText($rData->summary, 350);
    } else {
        echo '<i>not specified</i>';
    }
    ?>
</div>

<div class="req-dtls">
    <div class="dtls-icn">
        <span class="expand-dtl">
            <span class="material-symbols-outlined icn">
                keyboard_arrow_right
            </span>
            <span class="cnt-lbl">
                Request Details
            </span>
        </span>
    </div>
    <div class="dtls-cnt">
        <div class="cnt-vals">
            <?php
            if (isset($rData->desc)) {
                echo $rData->desc;
            } else {
                echo '<i>not specified</i>';
            }
            ?>
        </div>
    </div>
</div>
<!-- new -->
<div class="req-dtls">
    <div class="dtls-icn">
        <span class="expand-dtl">
            <span class="material-symbols-outlined icn">
                keyboard_arrow_right
            </span>
            <span class="cnt-lbl">
                Referance URl
            </span>
        </span>
    </div>
    <div class="dtls-cnt">
        <div class="cnt-vals">
            <?php
            if (isset($rData->refUrl)) {
                echo '<a href="' . $rData->refUrl . '" target="_blank">Click Here</a>';
            } else {
                echo '<i>not specified</i>';
            }
            ?>
        </div>
    </div>
</div>
<!-- New -->
<a name="attachments"></a>
<div class="req-dtls">
    <div class="dtls-icn">
        <span class="expand-dtl">
            <span class="material-symbols-outlined icn">
                keyboard_arrow_right
            </span>
            <span class="cnt-lbl">
                Impact on Business Operations
            </span>
        </span>
    </div>
    <div class="dtls-cnt">
        <div class="cnt-vals">
            <?php
            if (isset($rData->impact)) {
                echo $rData->impact;
            } else {
                echo '<i>not specified</i>';
            }
            ?>
        </div>
    </div>
</div>

<div class="req-dtls">
    <div class="dtls-icn">
        <span class="expand-dtl">
            <span class="material-symbols-outlined icn">
                keyboard_arrow_right
            </span>
            <span class="cnt-lbl">
                Attachments
                (<span id="attachCount" class="attach-count">
                    <?php echo $rData->attachments; ?>
                </span>)
            </span>
        </span>
    </div>
    <div class="dtls-cnt">
        <div class="cnt-vals file-list" id="fileList">
            <?php
            foreach ($reqFiles->data as $file) {
            ?>
                <div class="file-name">
                    <a href="<?php echo $pix->uploadPath, 'request-images/', $file->file; ?>" class="file-link" download="<?php echo $file->name; ?>">
                        <span class="material-symbols-outlined">
                            attach_file
                        </span>
                        <?php echo $file->name; ?>
                    </a>
                    <span class="file-ttl">
                        <span class="material-symbols-outlined file-dlt" data-id="<?php echo $file->id; ?>">
                            close
                        </span>
                    </span>
                </div>
            <?php
            }
            ?>
            <br>
            <div class="add-attch">
                <span class="add-files upload">
                    <span class="txt-lbl">
                        Add Attachments
                    </span>
                    <input type="file" name="attachFiles[]" id="attachFilles" class="attach-files" data-type="files" data-label="files" data-optional="1" multiple>
                </span>
            </div>
        </div>
    </div>
</div>

<div class="req-dtls">
    <div class="dtls-icn">
        <span class="expand-dtl">
            <span class="material-symbols-outlined icn">
                keyboard_arrow_right
            </span>
            <span class="cnt-lbl">
                Additional Comments
            </span>
        </span>
    </div>
    <div class="dtls-cnt">
        <div class="cnt-vals">
            <?php
            if (isset($rData->comments)) {
                echo $rData->comments;
            } else {
                echo '<i>not specified</i>';
            }
            ?>
        </div>
        <a name="comments"></a>
    </div>
</div>