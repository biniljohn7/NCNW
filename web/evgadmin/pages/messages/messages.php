<?php
loadStyle('pages/messages/messages');
loadScript('pages/messages/messages');
?>
<div class="page-head">
    <div class="head-col">
        <h1>All Messages</h1>
        <?php
        breadcrumbs(
            ['Messages']
        );
        ?>
    </div>
    <div class="sh-col">
        &nbsp;
    </div>
</div>
<div class="message-container">
    <div class="message-flex">
        <div class="message-list">
            <div class="inb-box">
                <div class="inb-head">
                    INBOX
                </div>
                <?php /*
                <div class="inb-search">
                    <input type="text" name="inbSearch" id="inbSearch" class="search-text" placeholder="Search for sender">
                </div>
                */ ?>
                <div class="inb-list">
                    <div class="inb-item">
                        <a href="#" class="item-link">
                            <span class="usr-thumb">
                                <span class="user-thumb letter-h">H</span>
                            </span>
                            <span class="msg-sender">Harisankar MH</span>
                            <span class="unread">&nbsp;</span>
                        </a>
                    </div>
                    <div class="inb-item">
                        <a href="#" class="item-link">
                            <span class="usr-thumb">
                                <span class="user-thumb letter-h">H</span>
                            </span>
                            <span class="msg-sender">
                                Harisankar MH
                            </span>
                            <span class="unread">&nbsp;</span>
                        </a>
                    </div>
                    <div class="inb-item">
                        <a href="#" class="item-link">
                            <span class="usr-thumb">
                                <span class="user-thumb letter-h">H</span>
                            </span>
                            <span class="msg-sender">Harisankar MH</span>

                        </a>
                    </div>
                    <div class="inb-item">
                        <a href="#" class="item-link">
                            <span class="usr-thumb">
                                <span class="user-thumb letter-h">H</span>
                            </span>
                            <span class="msg-sender">Harisankar MH</span>
                        </a>
                    </div>
                    <div class="inb-item">
                        <a href="#" class="item-link">
                            <span class="usr-thumb">
                                <span class="user-thumb letter-h">H</span>
                            </span>
                            <span class="msg-sender">Harisankar MH</span>
                            <span class="unread">&nbsp;</span>
                        </a>
                    </div>
                    <div class="inb-item">
                        <a href="#" class="item-link">
                            <span class="usr-thumb">
                                <span class="user-thumb letter-h">H</span>
                            </span>
                            <span class="msg-sender">Harisankar MH</span>
                        </a>
                    </div>
                    <div class="inb-item">
                        <a href="#" class="item-link">
                            <span class="usr-thumb">
                                <span class="user-thumb letter-h">H</span>
                            </span>
                            <span class="msg-sender">Harisankar MH</span>
                        </a>
                    </div>
                    <div class="inb-item">
                        <a href="#" class="item-link">
                            <span class="usr-thumb">
                                <span class="user-thumb letter-h">H</span>
                            </span>
                            <span class="msg-sender">Harisankar MH</span>
                        </a>
                    </div>
                    <div class="inb-item">
                        <a href="#" class="item-link">
                            <span class="usr-thumb">
                                <span class="user-thumb letter-h">H</span>
                            </span>
                            <span class="msg-sender">Harisankar MH</span>
                        </a>
                    </div>
                </div>
                <?php /*
                <div class="inb-new">
                    <span class="new-chat-btn">New Chat</span>
                </div>
                */ ?>
            </div>

        </div>
        <div class="message-display">
            <div class="display-area">
                <div class="display-head">
                    Sender Name
                    <span class="del-icon material-symbols-outlined">
                        delete
                    </span>
                </div>
                <div class="display-body">
                    <span class="from-msg">
                        In this article, we'll explore different types of CSS background effects
                    </span>
                    <span class="to-msg">
                        In this article, we'll explore different types of CSS background effects, from gradient-based designs to dynamic, motion-driven backgrounds that
                    </span>
                    <span class="from-msg">
                        In this article, we'll explore different types of CSS background effects,
                    </span>
                    <span class="from-msg">
                        from gradient-based designs to dynamic, motion-driven backgrounds that
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
                </div>
                <div class="message-field-box">
                    <div class="type-msg">
                        <div class="textarea">
                            <textarea name="message" id="chatMessage" class="chat-msg" rows="2"></textarea>
                            <span class="material-symbols-outlined attachfile">
                                attach_file_add
                            </span>
                        </div>
                        <div class="btn-box">
                            <button type="button" class="send-btn">
                                <span class="icon material-symbols-outlined">
                                    arrow_outward
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>