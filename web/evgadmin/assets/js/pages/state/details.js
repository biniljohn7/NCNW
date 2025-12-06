(function () {
    var pgn = 0, key = '';
    $(document).ready(function () {
        _e('addLeader').onclick = addLeaderFun;
    });

    function addLeaderFun() {
        popup.showPreloader('Loading. Please wait..', 400, 'mbrPreLdr');

        $.ajax(
            domain + 'ajax/anyadmin/', {
            method: 'post',
            data: {
                method: 'get-all-members',
                pgn: pgn,
                key: key,
                state: pgData.stId
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

            let htm = '';
            if (data.totalPages) {
                htm = appendMember(data);
            } else {
                htm = '<div class="no-mbr">No members found</div>';
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
                                    value="`+ key + `"
                                />
                                <button
                                    type="submit"
                                    class="srch-btn"
                                >
                                    <span class="material-symbols-outlined">search</span>
                                </button>
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
                key = _e('srchKey').value;
                pgn = 0;
                addLeaderFun();
                e.preventDefault();
            }
            $('.add-btn').click(setMbrAsLeader);
            $('.popup-close').click(function (e) {
                pgn = 0;
            });

        } else {
            loadMbrErr();
        }
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
                state: pgData.stId
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
        } else {
            loadMbrErr();
        }
    }

    function setMbrAsLeader() {
        let mId = this.d('id');

        popup.showPreloader();
        $.ajax(
            domain + 'ajax/anyadmin/', {
            method: 'post',
            data: {
                method: 'set-state-leader',
                mId: mId,
                stId: pgData.stId
            },
            error: setMbrError,
            success: setMbrSuccess
        });
    }

    function setMbrError(data) {
        popup.hide();
        popup.show(
            'Oops!',
            data.msg || 'An error occurred. Please try again later.',
            { width: 400 }
        );
    }

    function setMbrSuccess(data) {
        if (data.status == 'ok') {
            popup.hide();
            pix.openNotification('The state leader is added successfully.', 1);
            window.location.reload();
        } else {
            setMbrError(data);
        }
    }
})();