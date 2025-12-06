(function () {
    var
        pgn = 0,
        key = '',
        secn = 0,
        txtMsgInp,
        msgList = [],
        currChatPgn = 0,
        txtMsgInp,
        filesSelect = [],
        upFiles = [],
        uplProcessRunning = false;

    $(document).ready(function () {
        txtMsgInp = _e('txtMsgInp');
        loadMembersList({ 'search': false });
        $('#editGroupBtn').click(function (e) {
            popup.show(
                'Edit Group Name',
                `<form method="post" id="msgGrpForm" action="` + domain + `actions/anyadmin/">
                    <input type="hidden" name="method" value="message-group-save" />
                    <input type="hidden" name="id" value="`+ grpId + `" />
                    <input type="text" name="groupName" value="`+ grpName + `" class="group-name-input" id="groupName" data-type="string">
                    <br><br>
                    <button type="submit" class="pix-btn site mr5" id="createBtn">
                        Save
                    </button>
                </form>`,
                {
                    width: 400
                }
            );

            $('#msgGrpForm').formchecker(
                {
                    scroll: 0,
                    onValid: function (e) {
                        $(':focus').blur();
                        popup.showSpinner({ id: 'spnMegGrp' });
                    }
                }
            );
        });

        _e('addGrpMember').onclick = beginAddGrpMember;
        _e('inbSearch').onkeyup = searchFunction;
        $('#chatFm').formchecker({ onValid: sendMsg });
        txtMsgInp.onkeypress = chatFmActnOnKeypress;
        _e('attachFileInput').onchange = uploadFiles;
        loadList();
    });

    function beginAddGrpMember() {
        popup.showPreloader('Loading. Please wait..', 400, 'mbrPreLdr');

        $.ajax(
            domain + 'ajax/anyadmin/', {
            method: 'post',
            data: {
                method: 'get-all-members',
                pgn: pgn,
                key: key,
                chptr: secn
            },
            error: loadMbrErr,
            success: loadMbrSuccess
        });
    }

    function loadMbrErr() {
        popup.hide('mbrPreLdr');
        popup.show(
            'Oops!',
            'An error occurred. Please try again later.',
            { width: 400 }
        );
    }

    function loadMbrSuccess(data) {
        if (data.status == 'ok') {
            popup.hide();

            let htm = '',
                optionStr = '';
            if (data.totalPages) {
                htm = appendMember(data);
            } else {
                htm = '<div class="no-mbr">No members found</div>';
            }

            if (sections.length) {
                for (i = 0; i < sections.length; i++) {
                    optionStr += '<option value="' + sections[i].id + '" ' + (secn == sections[i].id ? 'selected' : '') + '>' + sections[i].name + '</option>';
                }
            }

            popup.show(
                'Members',
                `<div class="mbr-srch">
                        <div class="srch-bar">
                            <form id="mbrSrchForm">
                                <input
                                    type="text"
                                    name="key"
                                    class="key-inp"
                                    id="srchKey"
                                    placeholder="Search by name or email",
                                    value="`+ key + `"
                                />
                                <button
                                    type="submit"
                                    class="srch-btn"
                                >
                                    <span class="material-symbols-outlined">search</span>
                                </button>
                                <select name="sec" id="srchSec" class="srch-sec">
                                    <option value="0">Any Section</option>`+
                optionStr +
                `</select>
                            </form>
                        </div>
                        <div class="mbr-list" id="mbrList">`+ htm + `</div>` +
                (data.totalPages && data.totalPages > data.currentPageNo ?
                    `<div class="show-more" id="showMoreDiv">
                            <span class="pix-btn site sm" id="showMore" data-cp="`+ data.currentPageNo + `">
                                Show more
                            </span>
                        </div>` : '') + `
                    </div>`,
                { width: 500 }
            );

            _e('showMore').onclick = showMoreMember;
            _e('mbrSrchForm').onsubmit = function (e) {
                srchFormSubmit(e);
            }
            _e('srchSec').onchange = function (e) {
                srchFormSubmit(e);
            }
            $('.add-btn').click(setGroupMember);
            $('.popup-close').click(function (e) {
                pgn = 0;
            });

        } else {
            loadMbrErr();
        }
    }

    function srchFormSubmit(e) {
        key = _e('srchKey').value;
        secn = _e('srchSec').value;
        pgn = 0;
        beginAddGrpMember();
        e.preventDefault();
    }

    function appendMember(data) {
        let htm = '', list = data.list;
        for (i = 0; i < list.length; i++) {
            htm += `<div class="each-mbr">
                <div class="avatar-sec">` +
                (list[i].avatar ?
                    `<div class="mbr-img">
                        <img src="` + list[i].avatar + `" alt="" />
                    </div>` :
                    `<div class="no-img">
                        <span class="material-symbols-outlined icn">
                        person
                        </span>
                    </div>`) + `
                </div>
                <div class="nam-sec">` + list[i].name + `</div>
                <div class="actn">
                    <span
                        class="pix-btn site sm add-btn"
                        data-id="` + list[i].id + `"
                        data-name="` + list[i].name + `"
                        data-avatar="` + list[i].avatar + `"
                    >
                        Add
                    </span>
                </div>
            </div>`;
        }
        return htm;
    }

    function showMoreMember() {
        key = _e('srchKey').value;
        pgn = this.d('cp');
        loadMore(key);
    }

    function loadMore(key) {
        popup.showPreloader('Loading. Please wait..', 400, 'mbrPreLdr');

        $.ajax(
            domain + 'ajax/anyadmin/', {
            method: 'post',
            data: {
                method: 'get-all-members',
                pgn: pgn,
                key: key,
                chptr: secn
            },
            error: loadMbrErr,
            success: loadMoreSuccess
        });
    }

    function loadMoreSuccess(data) {
        if (data.status == 'ok') {
            popup.hide('mbrPreLdr');

            let htm = appendMember(data);
            $('#mbrList').append(htm);
            $('#showMore').attr('data-cp', data.currentPageNo);
            if (data.currentPageNo >= data.totalPages) {
                _e('showMoreDiv').hide();
            }
            $('.add-btn')
                .off('click', setGroupMember)
                .on('click', setGroupMember);
        } else {
            loadMbrErr();
        }
    }

    function setGroupMember() {
        let mId = this.d('id');

        popup.showPreloader('Loading. Please wait..', 400, 'addPreLdr');

        $.ajax(
            domain + 'ajax/anyadmin/', {
            method: 'post',
            data: {
                method: 'message-group-mbr-add',
                mbrId: mId,
                grpId: grpId
            },
            error: setMbrError,
            success: setMbrSuccess
        });
    }

    function setMbrError(data) {
        popup.hide('addPreLdr');
        popup.show(
            'Oops!',
            data.msg || 'An error occurred. Please try again later.',
            { width: 400 }
        );
    }

    function setMbrSuccess(data) {
        if (data.status == 'ok') {
            popup.hide('addPreLdr');
            pix.openNotification('Member added.', 1);
            loadMembersList({ 'search': false });
        } else {
            setMbrError(data);
        }
    }
    /////
    function loadMembersList(lstObj) {
        let spinClass = $('#showSpinner');
        spinClass.addClass('active');
        let searchKey = lstObj['search'];
        $.ajax(
            domain + 'ajax/anyadmin/', {
            method: 'get',
            data: {
                method: 'get-group-members-list',
                grpId: grpId,
                search: searchKey
            },
            error: listMbrError,
            success: listMbrSuccess
        });
    }
    //
    function listMbrError(data) {
        let spinClass = $('#showSpinner');
        let listDiv = $('#loadMemberListDiv');
        listDiv.html(
            `<div class="no-member">
                <span class="material-symbols-outlined">person</span>
                <br>
                Error loading members, Try again..
            </div>`
        );
        spinClass.removeClass('active');
    }
    //
    function listMbrSuccess(data) {
        let spinClass = $('#showSpinner');
        let listDiv = $('#loadMemberListDiv');
        if (data && data['list'] && data['status'] == 'ok') {
            let list = JSON.parse(data['list']);
            if (list.length > 0) {
                const html = list.map(userCard).join('');
                listDiv.html(html);
            } else {
                listDiv.html(
                    `<div class="no-member">
                        <span class="material-symbols-outlined">person</span>
                        <br>
                        No members found in this group..
                    </div>`
                );
            }
        } else {
            listDiv.html(
                `<div class="no-member">
                    <span class="material-symbols-outlined">person</span>
                    <br>
                    Error loading members, Try again..
                </div>`
            );
        }
        spinClass.removeClass('active');
    }
    ///
    function getInitials(name = "") {
        return name.trim()
            .split(/\s+/)
            .slice(0, 1)
            .map(w => w[0]?.toUpperCase() ?? "")
            .join("") || "?";
    }
    ///
    function userCard(u) {
        const hasAvatar = !!u.avatar;
        const avatarClass = hasAvatar ? "" : `letter-${getInitials(u.fName).toLowerCase()}`;
        const avatarHTML = hasAvatar ? `<img src="${u.avatar}" alt="${u.fName}">` : getInitials(u.fName);

        return `
            <div class="inb-item">
                <div class="item-link">
                    <span class="usr-thumb">
                        <span class="user-thumb ${avatarClass}">
                            ${avatarHTML}
                        </span>
                    </span>
                    <span class="msg-sender">${u.fName}</span>
                    <a class="unread" href="${domain}actions/anyadmin?method=remove-group-member&group-id=${grpId}&member=${u.id}">
                        <span class="del-icon material-symbols-outlined">person_remove</span>
                    </a>    
                </div>
            </div>
        `;
    }
    //
    function searchFunction(e) {
        let sVal = e.target.value;
        if (sVal.length > 0) {
            loadMembersList({ 'search': sVal });
        } else {
            loadMembersList({ 'search': false });
        }
    }
    function sendMsg() {
        pix.post(
            _e('chatFm'),
            function (e) {
                var
                    msgLst = _e('messageList');

                txtMsgInp.value = '';
                txtMsgInp.focus();

                // add new msg to list
                if (msgLst) {
                    var
                        msgsBox = $('#msgsBox'),
                        nMsg = document.createElement('div');

                    nMsg.className = 'message reply';
                    nMsg.innerHTML += `<div class="box">
                        <div class="delete-msg" data-id="${e.mkey}">
                            <span class="material-symbols-outlined">
                                close
                            </span>
                        </div>
                        <div class="msg-date" title="${e.time}">${e.shortTime}</div>
                        <div class="txt-box">
                            ${e.msg.replace(/\n/g, '<br />')}
                        </div>
                    </div>`;
                    nMsg.getClass('delete-msg').onclick = deleteChatMsg;
                    msgLst.appendChild(nMsg);
                    $(window).scrollTop(
                        $(document).height()
                    );

                    msgsBox.animate({
                        scrollTop: msgsBox.prop("scrollHeight")
                    }, 500);

                    txtMsgInp.style.height = '';
                    $('#cpsLkMntView').removeClass('active');
                }
            },
            {
                role: 'anyadmin',
                successMsg: 'Done! message sent'
            }
        );
        return false;
    }
    function loadList() {
        if (grpId) {

            $('#listLoadingSpn').show();
            $('#listLoadMore').hide();

            pix.post(
                {
                    method: 'messages-load',
                    id: grpId,
                    pgn: currChatPgn
                },
                function (data) {
                    if (currChatPgn > 0) {
                        msgList = data.messages.concat(msgList);
                    } else {
                        msgList = data.messages;
                    }

                    renderMsgList();
                    $('#listLoadingSpn').hide();
                    $('#showMore')[
                        currChatPgn < data.totalPages - 1 ?
                            'show' : 'hide'
                    ]();

                    if (currChatPgn === 0) {
                        $('#msgsBox').scrollTop($('#msgsBox')[0].scrollHeight);
                    }

                    $(window).scrollTop(
                        $(document).height()
                    );
                },
                {
                    role: 'anyadmin',
                    sowNoti: false
                }
            );
        }
    }
    function renderMsgList() {
        let
            list = '',
            mg,
            idx = 0;

        for (mg of msgList) {
            idx++;

            list += `<div class="message ${mg.isSent ? 'reply' : 'self'}">
                    <div class="box">
                        `+ (
                    mg.id && mg.isSent ?
                        `<div class="delete-msg" data-id="${mg.id}">
                            <span class="material-symbols-outlined">
                                close
                            </span>
                        </div>` : ''
                ) + `
                        <div class="msg-date" title="${mg.time}">
                            ${mg.shortTime}
                        </div>` +
                (mg.msgImg ?
                    `<div class="photo-item">
                            <div class="photo-frame">
                                <a href="${mg.msgImg.image}" class="photo-obj" data-fancybox="images">
                                    <img class="photo-vw" src="${mg.msgImg.thumb}" />
                                </a>
                            </div>
                        </div>`:
                    ``
                ) + `
                        <div class="txt-box">
                            ${mg.text.replace(/\n/g, '<br />')}
                        </div>`+
                `</div>
            </div> `;
        }

        _e('messageList').innerHTML = list;
        _e('listLoadMore').show();

        $('.delete-msg').click(deleteChatMsg);
    }
    function deleteChatMsg() {
        if (confirm('Are you sure ?')) {
            var msgBox = this.parentNode, tgt;
            msgBox.addClass('removing');

            $.ajax(domain + 'ajax/anyadmin/', {
                method: 'post',
                data: {
                    method: 'message-delete',
                    groupId: grpId,
                    id: this.d('id')
                },
                box: msgBox,
                error: function () {
                    popup.showError('Unable to remove this message. Please try again.');
                    this.box.removeClass('removing');
                },
                success: function (data) {
                    if (data.status == 'ok') {
                        $(this.box.parentNode).remove();
                    } else {
                        this.error();
                    }
                }
            });
        }
    }
    function chatFmActnOnKeypress(e) {
        if (
            e.keyCode === 13 &&
            !e.shiftKey
        ) {
            if (formchecker.test()) {
                sendMsg();
            }
        } else {
            txtMsgInp.style.height = '1px';
            $(txtMsgInp).height(txtMsgInp.scrollHeight)
        }
    }
    function uploadFiles() {
        var
            inpEl = this,
            files = inpEl.files;

        if (files.length > 0) {
            filesSelect = files;
            prepareUpload();
            inpEl.value = '';
        }
    }
    function prepareUpload() {
        if (filesSelect.length > 0) {
            let
                fKey,
                i;
            for (i = 0; i < filesSelect.length; i++) {
                fKey = makeString();
                upFiles[fKey] = filesSelect[i];
                renderUploadItem({
                    key: fKey,
                    name: filesSelect[i].name
                });
            }
            startFileUpload();
        }
    }
    function renderUploadItem(obj) {
        var
            node,
            parentElmnt = _e('messageList');

        node = document.createElement('div');
        node.className = 'message reply';
        node.setAttribute('id', 'photo_' + obj.key);
        node.innerHTML = `<div class="box">
            <div class="delete-msg" style="display:none;">
                <span class="material-symbols-outlined">
                    close
                </span>
            </div>
            <div class="msg-date" style="display: none;"></div>
            <div class="photo-item">
                <div class="upl-pending incmp-upload">
                    <div class="pix-spinner"></div>
                    <div class="upload-progress">
                        <div class="pg-bar"></div>
                    </div>
                </div>
                <div class="upload-failed" style="display: none;">
                    <div class="failed-icon">
                        <i class="material-symbols-outlined">
                            error
                        </i><br />
                        Upload Failed<br />
                    </div>
                </div>
                <div class="photo-frame" style="display: none;">
                    <a href="javascript:;" class="photo-obj">
                        <img class="photo-vw" />
                    </a>
                </div>
            <div>
        </div>`;

        node.getClass('delete-msg').onclick = deleteChatMsg;
        parentElmnt.appendChild(node);
    }
    function startFileUpload() {
        if (!uplProcessRunning) {
            uplProcessRunning = true;
            setTimeout(processImageUpload, 300);
        }
    }
    function processImageUpload() {
        let fkey;
        for (fkey in upFiles) {
            break;
        }
        if (fkey) {
            const
                pdObj = $('#photo_' + fkey)[0];

            let
                ufile,
                pgBar;

            if (pdObj) {
                ufile = upFiles[fkey];
                pgBar = $(pdObj).find('.pg-bar');
                $(pdObj).find('.upl-pending').addClass('is-loading');

                $('html, body').animate({
                    scrollTop: ($(pdObj).offset().top) - 80
                });

                if ((/\.(jpe*g|png|gif)$/i).test(ufile.name)) {

                    pix.startFileUpload(
                        domain + 'ajax/anyadmin/',
                        {
                            data: {
                                method: 'message-file-upload',
                                groupId: grpId,
                                image: ufile
                            },
                            error: function () {
                                imgUploadFailed(fkey);
                                setTimeout(processImageUpload, 5000);

                            },
                            progress: function (e) {
                                pgBar.width(((e.loaded / e.total) * 100) + '%');
                            },
                            success: function (e, data) {
                                if (data.status === 'ok') {
                                    delete data.status;
                                    setUploadData(fkey, data);

                                    delete upFiles[fkey];
                                    setTimeout(processImageUpload, 1000);

                                } else {
                                    e.args.error();
                                }
                            }
                        }
                    );

                } else {
                    imgUploadFailed(fkey);
                }
            } else {
                setTimeout(processImageUpload, 1000);
            }
        } else {
            uplProcessRunning = false;
        }
    }
    function setUploadData(key, data) {
        var
            contacts = _e('contacts'),
            timeNode,
            activeThumb,
            tgt;

        const pdObj = _e('photo_' + key);

        pdObj.getClass('upl-pending').remove();
        pdObj.getClass('upload-failed').remove();
        pdObj.getClass('photo-frame').show();

        tgt = pdObj.getClass('photo-obj');
        tgt.setAttribute('data-fancybox', 'images');
        tgt.href = data.photo;
        tgt.getClass('photo-vw').src = data.thumb;

        tgt = pdObj.getClass('msg-date');
        tgt.show();
        tgt.setAttribute('time', data.time);
        tgt.innerHTML = data.shortTime;

        tgt = pdObj.getClass('delete-msg');
        tgt.show();
        tgt.setAttribute('data-id', data.mkey);

        txtMsgInp.value = '';
        txtMsgInp.focus();

        // change lastMsg on chat-thumb
        if (contacts) {
            activeThumb = contacts.getClass('active');
            if (activeThumb) {
                $(activeThumb).prependTo($(activeThumb).parent());
                activeThumb.getClass('msg').innerHTML = '<span class="material-symbols-outlined">attachment</span>';
                timeNode = activeThumb.getClass('time');
                timeNode.title = data.time;
                timeNode.innerHTML = data.shortTime;
            }
        }
    }
    function imgUploadFailed(key) {
        const
            pdObj = _e('photo_' + key);

        pdObj.getClass('upl-pending').remove();
        pdObj.getClass('upload-failed').show('flex');

        delete upFiles[key];
        setTimeout(processImageUpload, 1000);
    }
})();