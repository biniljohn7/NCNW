<?php
$msgGrp = false;
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    if ($id) {
        $msgGrp = $pixdb->getRow(
            'message_groups',
            ['id' => $id]
        );
    }
}
if (!$msgGrp) {
    $pix->addmsg('Invalid message group.');
    $pix->redirect('?page=groups');
}

$sections = $pixdb->get(
    'chapters',
    ['#SRT' => 'name asc'],
    'id, name'
);
$sections = $sections->data;

echo '<script type="text/javascript">
    var grpId = ' . $msgGrp->id . ', grpName = "' . $msgGrp->title . '", sections = ' . json_encode($sections) . ';
</script>';


loadStyle('pages/messages/messages');
loadStyle('pages/groups/group-view');
loadScript('pages/groups/group-view');
?>
<div class="page-head">
    <div class="head-col">
        <h1><?php echo $msgGrp->title ?? 'Untitled Group'; ?></h1>
        <?php
        breadcrumbs(
            [
                'Message Groups',
                '?page=groups'
            ],
            [
                $msgGrp->title ?? 'Untitled Group'
            ]
        );
        ?>
    </div>
    <div class="sh-col">
        <span
            class="pix-btn site rounded mr5" id="editGroupBtn">
            <span class="material-symbols-outlined fltr">
                edit
            </span>
            Edit <?php echo $msgGrp->title ?? 'Untitled Group'; ?>
        </span>
    </div>
</div>
<?php
$allMembers = $pixdb->custom_query(
    'SELECT 
    id,
    concat(firstName, " ", lastName) as fName,
    avatar
    FROM members
    WHERE members.id IN(
        SELECT member 
        FROM message_group_members
        WHERE `groupId` = ' . $id . '
    )
    ORDER BY `firstName` ASC'
)->data;
?>
<div class="message-container">
    <div class="message-flex">
        <div class="message-list">
            <div class="inb-box">
                <div class="inb-head">
                    Members
                </div>
                <div class="inb-search">
                    <input type="text" name="inbSearch" id="inbSearch" class="search-text" placeholder="Search member">
                </div>
                <div class="inb-list">
                    <div class="spin-container" id="showSpinner">
                        <div class="pix-spinner"></div>
                    </div>
                    <div id="loadMemberListDiv"></div>
                </div>
                <div class="inb-new">
                    <span class="new-chat-btn" id="addGrpMember">Add Member</span>
                </div>
            </div>

        </div>
        <div class="message-display">
            <div class="display-area">
                <div class="display-head">
                    <?php echo $msgGrp->title ?? 'Untitled Group'; ?>
                    <!-- <span class="del-icon material-symbols-outlined">
                        delete
                    </span> -->
                </div>
                <!-- <div class="display-body">
                    <span class="to-msg">
                        In this article, we'll explore different types of CSS background effects, from gradient-based designs to dynamic, motion-driven backgrounds that
                    </span>

                    <span class="to-msg">
                        In this article, we'll explore different types of CSS background effects, from gradient-based designs to dynamic,
                    </span>
                    <span class="to-msg">
                        In this article, we'll explore different types of CSS background effects, from gradient-based designs to dynamic, motion-driven backgrounds that
                    </span>

                    <span class="to-msg">
                        In this article, we'll explore different types of CSS background effects, from gradient-based designs to dynamic,
                    </span>
                    <span class="to-msg">
                        In this article, we'll explore
                    </span>
                    <span class="to-msg">
                        Hi
                    </span>
                </div> -->


                <div class="chat-box display-body" id="msgsBox">
                    <div class="pt20 mb20" id='listLoadingSpn' style="display: none;">
                        <div class="simple-spinner"></div>
                    </div>

                    <div class='chat-view-btn' id='listLoadMore'>
                        <span id="showMore" style="display: none;">
                            Show Old Messages
                            <i class="material-symbols-outlined">
                                expand_less
                            </i>
                        </span>
                        <div id="messageList">

                        </div>
                    </div>
                </div>
                <div class="message-field-box">
                    <form class="chat-form" id="chatFm">
                        <input type="hidden" name="method" value="message-send" />
                        <input class="hidden" name="group" value="<?php echo $msgGrp->id; ?>" />
                        <div class="type-msg">
                            <div class="textarea">
                                <textarea name="message" id="txtMsgInp" class="chat-msg" rows="2"></textarea>
                                <span class="material-symbols-outlined attachfile">
                                    <input type="file" class="img-input" id="attachFileInput" accept=".jpeg,.jpg,.png,.gif" multiple />
                                    attach_file_add
                                </span>
                            </div>
                            <div class="btn-box">
                                <button class="send-btn">
                                    <span class="icon material-symbols-outlined">
                                        arrow_outward
                                    </span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>