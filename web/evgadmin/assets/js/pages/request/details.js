(function () {
    var filesSelect = [],
        upFiles = [],
        fileList,
        attachCount,
        ttlComments,
        mnCmntBox;

    $(document).ready(function () {
        mnCmntBox = _e('mnCmntBox');
        fileList = _e('fileList');
        ttlComments = _e('ttlComments');
        attachCount = document.querySelectorAll('.attach-count');

        _e('attachFilles').onchange = uploadFiles;
        $('.file-dlt').click(deleteReqFile);

        if ($('#reqCommentsList')[0]) {
            req.listBox = _e('reqCommentsList');
            req.showMoreBtn = _e('showMoreBtn');
            req.reviewSpinner = _e('reviewSpinner');
            req.reviewError = _e('reviewError');

            _e('reviewErrorRetryBtn').onclick = function () {
                req.loadComments();
            }
            req.showMoreBtn.onclick = function () {
                req.pgn++;
                req.loadComments();
            }
            $('.post-comment-btn').click(req.postComment);

            req.loadComments();
        }

        var icons = document.querySelectorAll('.expand-dtl'),
            x;

        for (x = 0; x < icons.length; x++) {
            icons[x].onclick = showDetails;
        }

        $('.estimate-btn').click(function () {
            openEstimateForm();
        });
        $('.feedback-deployment-btn').click(openDplFeedbackForm);
        $('.revise-req-resp-btn').click(openReviseReqRespForm);
        _e('modifyEstimateBtn').onclick = function () {
            openEstimateForm(pgData.estimate);
        };
    });

    function showDetails() {
        var cntVals = this.parentNode.nextElementSibling.qs('.cnt-vals'),
            icn = this.qs('.icn'),
            label = this.qs('.cnt-lbl'),
            show;

        function toggleIcon(show) {
            icn.style.transition = 'transform 0.5s ease-in-out, color 0.5s ease-in-out, fontWeight 0.5s ease-in-out';
            icn.style.transform = show ? 'rotate(90deg)' : 'rotate(0deg)';
            icn.style.color = show ? '#6e3b79' : '#000';
            icn.style.fontWeight = show ? '600' : '500';
            icn.classList.toggle('show', show);
        }

        function toggleContent(show) {
            cntVals.style.transition = 'max-height 0.5s ease-in-out, opacity 0.5s ease-in-out, fontWeight 0.5s ease-in-out';
            cntVals.style.maxHeight = show ? `${cntVals.scrollHeight}px` : '0';
            cntVals.style.opacity = show ? '1' : '0';
            cntVals.style.overflow = show ? 'visible' : 'hidden';
            cntVals.classList.toggle('show', show);
        }

        function labelContent(show) {
            label.style.transition = 'color 0.5s ease-in-out';
            label.style.color = show ? '#6e3b79' : '#000';
            label.style.fontWeight = show ? '600' : '500';
            label.classList.toggle('show', show);
        }

        show = !icn.classList.contains('show');
        toggleIcon(show);
        toggleContent(show);
        labelContent(show);
    }


    function uploadFiles() {
        let files = this.files,
            file,
            i,
            textXp = /\.(jpe*g|png|gif|pdf|txt|docx|doc|xls|xlsx|rtf|ppt|pptx|ai|psd|ttf|eps)$/i;

        processFileUpload();
        if (files.length > 0) {
            for (i = 0; i < files.length; i++) {
                file = files[i];
                if (textXp.test(file.name)) {
                    filesSelect.push(file);
                }
            }
            if (filesSelect.length > 0) {
                prepareUpload();
            }
            this.value = '';
        }
    }

    function prepareUpload() {
        popup.showSpinner();

        if (filesSelect.length > 0) {
            let fileObj,
                fKey,
                j;

            for (j = 0; j < filesSelect.length; j++) {
                fKey = makeString('ln', 20);
                upFiles.push({
                    key: fKey,
                    file: filesSelect[j]
                });

                fileObj = document.createElement('div');
                fileObj.className = 'file-name';
                fileObj.id = 'file_' + fKey;

                fileList.prepend(fileObj);
                fileList.style.maxHeight = '100%';
            }
            filesSelect = [];
            processFileUpload();
        }
    }

    function processFileUpload() {
        let uf;
        if (upFiles[0]) {
            uf = upFiles[0];
            if ((/\.(jpe*g|png|gif|pdf|txt|docx|doc|xls|xlsx|rtf|ppt|pptx|ai|psd|ttf|eps)$/i).test(uf.file.name)) {

                pix.startFileUpload(
                    domain + 'ajax/anyadmin/', {
                    data: {
                        method: 'set-request-files',
                        request: reqId,
                        files: uf.file
                    },
                    error: function (data) {
                        fileUploadFailed();
                        popup.showError(
                            'Unable to perform this action. ' +
                            (data.errorMsg || 'Please try again.')
                        );
                    },
                    success: function (e, data) {
                        if (data.status == 'ok') {
                            delete data.status;
                            setFileData(uf.key, data);

                            upFiles.shift();
                            processFileUpload();

                            for (let i = 0; i < attachCount.length; i++) {
                                attachCount[i].innerHTML = `<span class="">
                                                                    ${data.count}
                                                                  </span>`;
                            }
                        } else {
                            e.args.error(data);
                        }
                    }
                });
            } else {
                fileUploadFailed();
            }
        } else {
            filesSelect = [];
            popup.hide();
            pix.openNotification('File upload complete', true);
        }
    }

    function fileUploadFailed() {
        upFiles.shift();
    }

    function setFileData(key, data) {
        let fileObj = _e('file_' + key);
        fileObj.innerHTML = `<a href="${data.file}" class="file-link" download="${data.name}">
                <span class="material-symbols-outlined">
                    attach_file
                </span>
            </a>
            <span class="file-ttl">
                ${data.name}
                 <span class="material-symbols-outlined file-dlt" data-id="${data.rid}">
                    close
                </span>
            </span>`;

        $(fileObj).find('.file-dlt').click(deleteReqFile);
    }

    function deleteReqFile() {
        if (confirm('Are you sure?')) {
            var pNode = $(this.parentNode.parentNode);
            popup.showSpinner();

            $.ajax(
                domain + 'ajax/anyadmin/', {
                method: 'post',
                data: {
                    method: 'delete-request-files',
                    request: reqId,
                    fileId: this.d('id')
                },
                error: function () {
                    popup.showError('Unable to perform this action. Please try again.');
                },
                success: function (data) {
                    if (data.status == 'ok') {
                        popup.hide();
                        pNode.remove();
                        for (let i = 0; i < attachCount.length; i++) {
                            attachCount[i].innerHTML = `<span class="">
                                                                ${data.count}
                                                              </span>`;
                        }
                        pix.openNotification('File removed', true);
                    } else {
                        this.error();
                    }
                }
            });
        }
    }

    function openEstimateForm(data) {
        let
            estTime = data ? data.time.split(':') : [];

        popup.show(
            (data ? 'Modify' : 'Submit') + ' Your Estimate',
            `<form action="${domain}actions/anyadmin/" method="post" id="estimateForm">
                <input type="hidden" name="method" value="helpdesk/${data ? 'modify-estimate' : 'set-status'}" />
                <input type="hidden" name="task" value="${reqId}" />
                <input type="hidden" name="status" value="estimated" />

                <div class="bold-600 mb15">
                    Submit your estimated time for the completion of this task
                </div>

                <div class="bold-600 mb5">
                    Mark Issue As:
                </div>
                <div class="mb30">
                    <div class="mb10">
                        ${pix.models.Radio(
                {
                    name: 'esttype',
                    label: 'Bug',
                    value: 'bug',
                    checked: (
                        !data || (
                            data &&
                            data.type == 'bug'
                        )
                    )
                }
            )}
                    </div>
                    <div class="">
                        ${pix.models.Radio(
                {
                    name: 'esttype',
                    label: 'New Feature',
                    value: 'new-feature',
                    checked: data && data.type == 'new-feature'
                }
            )}
                    </div>
                </div>

                <div class="bold-600 mb5">
                    Estimated Time
                </div>
                <div class="mb30">
                    <div class="">
                        <input type="text" name="estdays" id="estDaysInp" size="2" value="${estTime[0] || ''}">
                        Days
                        &nbsp;&nbsp;&nbsp;
                        <input type="text" name="esthrs" id="estHrsInp" size="2" value="${estTime[1] || ''}">
                        Hours
                    </div>
                    <input 
                        type="hidden" 
                        data-type="func" 
                        data-func="validateEtimateTime" 
                        data-errormsg="estimated time is invalid" 
                    />
                </div>

                <div class="">
                    <button type="submit" class="pix-btn site">
                        SUBMIT
                    </button>
                </div>
            </form>`,
            { width: 400 }
        );

        $('#estimateForm').formchecker({
            scroll: 0
        });
    }
    function openDplFeedbackForm() {
        popup.show(
            'Feedback on Deployment',
            `<form action="${domain}actions/anyadmin/" method="post" id="estimateForm">
                <input type="hidden" name="method" value="helpdesk/set-status" />
                <input type="hidden" name="task" value="${reqId}" />

                <div class="bold-600 mb15">
                    Submit your feedback on the deployment of this task.
                </div>

                <div class="mb30">
                    <div class="mb10">
                        ${pix.models.Radio(
                {
                    name: 'status',
                    label: 'Issue Resolved. Close Task.',
                    value: 'resolved',
                    className: 'feedback-status',
                    checked: true
                }
            )}
                    </div>
                    <div class="">
                        ${pix.models.Radio(
                {
                    name: 'status',
                    label: 'Didn\'t meet our expectations. Needs some changes.',
                    value: 'revise-update',
                    className: 'feedback-status'
                }
            )}
                    </div>
                </div>

                <div id="fbkMsgBox">
                    <div class="bold-600 mb5">
                        Explain the reason for your feedback
                    </div>
                    <div class="mb30">
                        <textarea 
                            id="fbkMsg"
                            name="message" 
                            data-type="string"
                            cols="300" 
                            rows="4" 
                            disabled
                        ></textarea>
                    </div>
                </div>

                <div class="">
                    <button type="submit" class="pix-btn site">
                        SUBMIT
                    </button>
                </div>
            </form>`,
            { width: 400 }
        );

        $('.feedback-status-rdo').change(function () {
            let
                msgBox = _e('fbkMsgBox'),
                msgInp = _e('fbkMsg'),
                resolved = $('.feedback-status-rdo:checked').val() == 'resolved';

            msgInp.disabled = resolved;
            msgBox[resolved ? 'hide' : 'show']();
            // 
        }).change();

        $('#estimateForm').formchecker({
            scroll: 0
        });
    }
    function openReviseReqRespForm() {
        popup.show(
            'Respond to Revision Request',
            `<form action="${domain}actions/anyadmin/" method="post" id="estimateForm">
                <input type="hidden" name="method" value="helpdesk/set-status" />
                <input type="hidden" name="task" value="${reqId}" />

                <div class="bold-600 mb15">
                    Submit your response to the revision request for this task.
                </div>

                <div class="mb30">
                    <div class="mb10">
                        ${pix.models.Radio(
                {
                    name: 'status',
                    label: 'Started working on the revision.',
                    value: 'started',
                    className: 'rev-status',
                    checked: true
                }
            )}
                    </div>
                    <div class="">
                        ${pix.models.Radio(
                {
                    name: 'status',
                    label: 'We have an issue with the revision request.',
                    value: 'have-issue',
                    className: 'rev-status'
                }
            )}
                    </div>
                </div>

                <div id="fbkMsgBox">
                    <div class="bold-600 mb5">
                        Explain the issue with the revision request
                    </div>
                    <div class="mb30">
                        <textarea 
                            id="fbkMsg"
                            name="message" 
                            data-type="string"
                            cols="300" 
                            rows="4" 
                            disabled
                        ></textarea>
                    </div>
                </div>

                <div class="">
                    <button type="submit" class="pix-btn site">
                        SUBMIT
                    </button>
                </div>
            </form>`,
            { width: 400 }
        );

        $('.rev-status-rdo').change(function () {
            let
                msgBox = _e('fbkMsgBox'),
                msgInp = _e('fbkMsg'),
                started = $('.rev-status-rdo:checked').val() == 'started';

            msgInp.disabled = started;
            msgBox[started ? 'hide' : 'show']();
            // 
        }).change();

        $('#estimateForm').formchecker({
            scroll: 0,
            onValid: function () {
                if ($('.rev-status-rdo:checked').val() == 'have-issue') {
                    _e('taskCommentInp').value = _e('fbkMsg').value;
                    popup.hide();
                    $('#taskCommentPostBtn').click();
                    return false;
                }
            }
        });
    }
})();

function validateEtimateTime() {
    let
        days = getnum(_e('estDaysInp').value.trim()),
        hrs = getnum(_e('estHrsInp').value.trim());

    return !!days || !!hrs;
}

var req = {
    pgn: 0,
    loadComments: function (row = null) {
        let
            pgNum,
            moreBtn,
            errorBox,
            spinner,
            listBox;

        if (row) {
            if (!row.qs('.reply-info')) {
                let info = document.createElement('div');
                info.className = 'reply-info pt30';
                info.innerHTML = `<div class="replies-list"></div>
                    <div class="text-center">
                        <span class="pix-btn outlined sm more-btn">
                            Show More Replies
                        </span>
                        <div class="pix-spinner iblock md"></div>
                        <div class="error-view">
                            <div class="mb10">
                                Oops. An error occurred with loading comments.
                            </div>
                            <span class="reply-retry-btn pix-btn danger sm outlined rounded">
                                Retry
                            </span>
                        </div>
                    </div>
                    <div class="pt20 comment-form reply-cmnt-box">
                        <div class="bold-600 mb10">
                            Write Your Reply:
                        </div>
                        <div class="mb10">
                            <textarea cols="10" style="width: 100%;" rows="3" class="comment-inp"></textarea>
                        </div>
                        <div>
                            <span class="pix-btn md site post-comment-btn tp-reply">
                                Post Reply
                            </span>
                            <span class="pix-btn md comment-cancel-btn">
                                Cancel
                            </span>
                        </div>
                    </div>`;
                $(info).find('.post-comment-btn').click(req.postComment);
                let btn,
                    rplyBox;
                if (btn = info.qs('.comment-cancel-btn')) {
                    btn.onclick = function () {

                        req.showMainCommentBox(true);

                        let replyBox = this.closest('.reply-cmnt-box');

                        if (replyBox) {
                            replyBox.style.display = 'none';
                        }
                    };
                }
                row.qs('.cmt-col').appendChild(info);
            }

            pgNum = row.pgn;
            moreBtn = row.qs('.more-btn');
            errorBox = row.qs('.error-view');
            spinner = row.qs('.pix-spinner');
            listBox = row.qs('.replies-list');

            row.qs('.reply-retry-btn').onclick = function () {
                req.loadComments(row);
            };
            moreBtn.onclick = function () {
                row.pgn++;
                req.loadComments(row);
            };
        } else {
            pgNum = req.pgn;
            moreBtn = req.showMoreBtn;
            errorBox = req.reviewError;
            spinner = req.reviewSpinner;
            listBox = req.listBox;
        }

        moreBtn.hide();
        errorBox.hide();
        spinner.show('inline-block');

        let params = {
            method: 'get-req-comments',
            request: reqId,
            pgn: pgNum
        };

        if (row) {
            params.comment = row.commentId;
        }

        $.ajax(
            domain + 'ajax/anyadmin/', {
            method: 'get',
            data: params,
            pgn: pgNum,
            error: function () {
                spinner.hide();
                errorBox.show();
            },
            success: function (data) {
                if (data.status == 'ok') {
                    spinner.hide();

                    if (!this.pgn) {
                        listBox.innerHTML = '';
                    }

                    let rowOps = {};
                    if (row) {
                        rowOps.blockReply = true;
                    }

                    if (!row &&
                        pgNum == 0 &&
                        data.list.length == 0
                    ) {
                        req.checkEmptyList();
                    }

                    for (let cm of data.list) {
                        listBox.appendChild(
                            req.createCommentObj(cm, rowOps)
                        );
                    }

                    if (data.totalPages - 1 > this.pgn) {
                        moreBtn.show('inline-block');
                    }

                } else {
                    this.error();
                }
            }
        }
        );
    },
    createCommentObj: function (data, ops = {}) {
        let
            o = document.createElement('div'),
            actionBox = '',
            actionBtns = [],
            btn;

        if (data.type != 'system') {
            if (!ops.blockReply) {
                if (data.replies > 0) {
                    actionBtns.push(
                        `<span class="ops-btn open-rpl-btn">
                            <span class="bold-600">
                                ${data.replies}
                            </span>
                            repl${data.replies > 1 ? 'ies' : 'y'}
                        </span>`
                    );
                }

                actionBtns.push(
                    `<span class="ops-btn post-reply-btn">
                        Post Reply
                    </span>`
                );
            }

            if (data.own) {
                actionBtns.push(
                    `<span class="ops-btn edit-comment-btn">
                        <span class="material-symbols-outlined">
                            edit
                        </span>
                        Edit
                    </span>`
                );
                actionBtns.push(
                    `<span class="ops-btn delete-comment-btn">
                        <span class="material-symbols-outlined">
                            do_not_disturb_on
                        </span>
                        Delete
                    </span>`
                );
            }

            actionBox = `<div class="cmt-actions">
                ${actionBtns.join(' <span class="sep"></span> ')}
            </div>`;
        }

        o.className = 'comment-row';
        o.commentId = data.id;
        o.comment = data.comment;
        o.innerHTML = `<div class="user-thumb">
                ${data.user.avatar ?
                `<img src="${data.user.avatar}">` :
                `<div class="no-thumb">
                        <div class="tp-letter">
                            ${data.user.name.toUpperCase().slice(0, 2)}
                        </div>
                    </div>`
            }
            </div>
            <div class="cmt-col">
                <div class="user-info">
                    <span class="usr-name">
                        ${data.user.name}
                    </span>
                    <span class="cmt-date">
                        ${data.date}
                    </span>
                </div>
                <div class="cmt-text">
                    ${pix.models.ReadFullText(data.comment, 350)}
                </div>
                ${actionBox}
            </div>`;

        if (btn = o.qs('.read-full-text-btn')) {
            btn.onclick = ReadFullTextBtnClick;
        }

        if (btn = o.qs('.open-rpl-btn')) {
            // btn.onclick = req.openReplies;

            btn.onclick = function () {
                req.showMainCommentBox();
                req.openReplies.bind(this)();
                req.showReplyCmntBox.bind(this)();
                req.showActiveReplyBox.bind(this)();
            };
        }

        if (btn = o.qs('.post-reply-btn')) {
            btn.onclick = function () {
                req.showMainCommentBox();
                req.openReplies.bind(this)();
                req.showActiveReplyBox.bind(this)();

                let inp;
                if (inp = o.qs('.comment-inp')) {
                    inp.focus();
                }
                req.showReplyCmntBox.bind(this)();
            };
        }

        if (btn = o.qs('.edit-comment-btn')) {
            btn.onclick = req.editComment;
        }

        if (btn = o.qs('.delete-comment-btn')) {
            btn.onclick = req.deleteComment;
        }

        return o;
    },
    showReplyCmntBox() {
        let cmntBx = this.parentNode.nextElementSibling.qs('.reply-cmnt-box');

        if (cmntBx) {
            cmntBx.style.display = 'block';
        }
    },
    checkEmptyList() {
        if ($('.comment-row').length == 0) {
            _e('reqCommentsList').innerHTML = `<div class="text-center text-11 pt50 mb50" id="noCommentsView">
                    No comments have been posted yet. Be the first to leave one!
                </div>`;
        } else {
            $('#noCommentsView').remove();
        }

        if ($('.reply-cmnt-box:visible').length == 0) {
            req.showMainCommentBox(true);
        }
    },
    showMainCommentBox(
        showMn = false
    ) {
        if (showMn == false) {
            mnCmntBox.hide();
        } else {
            mnCmntBox.show();
        }
    },
    showActiveReplyBox() {
        let actBtn = this.parentNode.nextElementSibling.qs('.reply-cmnt-box'),
            rBtns = document.querySelectorAll('.reply-cmnt-box'),
            i;

        for (i = 0; i < rBtns.length; i++) {
            if (rBtns[i] === actBtn) {
                rBtns[i].style.display = 'block';
            } else {
                rBtns[i].style.display = 'none';
            }
        }
    },
    openReplies: function () {
        let row = this.parent('comment-row');
        row.pgn = 0;
        req.loadComments(row);
    },
    postComment: function () {
        let
            btn = this,
            form = btn.parent('comment-form'),
            row = form.parent('comment-row'),
            inp = form.qs('.comment-inp'),
            comment = inp.value.trim(),
            isReply = btn.className.indexOf('tp-reply') >= 0;

        if (comment) {
            form.addClass('waiting');
            popup.showSpinner();

            let postData = {
                method: 'post-req-comment',
                req: reqId,
                comment: comment
            };
            if (isReply) {
                postData.replyto = row.commentId;
            }
            $.ajax(
                domain + 'ajax/anyadmin/', {
                method: 'post',
                data: postData,
                error: function (msg) {
                    form.removeClass('waiting');
                    popup.showError(
                        'Unable to perform this action. ' +
                        (msg || 'Please try again.')
                    );
                },
                success: function (data) {
                    if (data.status == 'ok') {
                        popup.hide();
                        let
                            co = req.createCommentObj({
                                id: data.id,
                                user: data.user,
                                date: data.date,
                                comment: data.comment,
                                replies: data.replies,
                                own: data.own
                            }, {
                                blockReply: isReply
                            }),
                            listBox = isReply ?
                                form.parentNode.qs('.replies-list') : req.listBox;

                        listBox.prepend(co);
                        req.checkEmptyList();

                        form.removeClass('waiting');
                        inp.value = '';
                        ttlComments.innerText = data.count;

                        _e('reqCommentsList').scrollTo(0, 0);
                        pix.openNotification('Your comment posted!', 1);

                    } else {
                        this.error(data.errorMsg);
                    }
                }
            }
            );
        }
    },
    editComment: function () {
        let
            textView = this.parent('cmt-actions').previousElementSibling,
            row = this.parent('comment-row');

        popup.show(
            'Modify Comment',
            `<form method="post" id="commentModForm">
                        <input type="hidden" name="method" value="modify-comment" />
                        <input type="hidden" name="id" value="${row.commentId}" />
                        <div class="bold-600 mb10">
                            Comment:
                        </div>
                        <div class="mb10">
                            <textarea name="comment" cols="300" rows="4" data-type="string">${row.comment}</textarea>
                        </div>
                        <div class="">
                            <button type="submit" class="pix-btn site">
                                Modify
                            </button>
                        </div>
                    </form>`,
            {
                width: 550
            }
        );
        $('#commentModForm').formchecker(
            {
                scroll: 0,
                onValid: function (e) {
                    $(':focus').blur();
                    popup.showSpinner({ id: 'spnCommentMod' });

                    $.ajax(
                        domain + 'ajax/anyadmin/',
                        {
                            method: 'post',
                            data: $(e.target).serialize(),
                            error: function () {
                                popup.hide('spnCommentMod');
                                popup.showError('Unable to modify comment. Please try again.');
                            },
                            success: function (data) {
                                if (data.status == 'ok') {
                                    textView.innerText = data.comment;
                                    row.comment = data.comment;
                                    popup.hide();

                                } else {
                                    this.error();
                                }
                            }
                        }
                    );

                    return false;
                }
            }
        );
    },
    deleteComment: function () {
        if (confirm('Are you sure?')) {
            let
                row = this.parent('comment-row'),
                col = row.parent('reply-info', false);

            row.addClass('waiting');
            popup.showSpinner();

            $.ajax(
                domain + 'ajax/anyadmin/',
                {
                    method: 'post',
                    data: {
                        method: 'delete-comment',
                        id: row.commentId,
                        req: reqId
                    },
                    error: function () {
                        row.removeClass('waiting');
                        popup.showError('Unable to perform this action. Please try again.');
                    },
                    success: function (data) {
                        if (data.status == 'ok') {
                            popup.hide();
                            $(row).remove();
                            ttlComments.innerText = data.count;
                            req.checkEmptyList();

                            if (col) {
                                let
                                    btn = col.previousElementSibling.qs('.open-rpl-btn'),
                                    num = Number(btn.innerText.replace(/[^0-9]/g, '')) || 1;

                                btn.innerHTML = `<span class="bold-600">
                                        ${num - 1}
                                    </span> repl`+ (num > 1 ? 'ies' : 'y');
                            }
                        } else {
                            this.error();
                        }
                    }
                }
            );
        }
    },
}