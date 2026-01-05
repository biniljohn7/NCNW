(function () {
    var
        pgn = 0,
        key = '',
        secn = 0,
        selMbr = [],
        posCartArr = [],
        selItm = '';

    $(document).ready(function () {
        _e("addItem").onclick = addItems;
        $(document).on('click', '.remove-mbr', removeMbr);
        $('.pos-submit').click(savePOS);

        function savePOS() {
            let type = this.d('type');
            popup.showPreloader('Saving POS data..', 400, 'svPreLdr');

            $.ajax(
                domain + 'ajax/anyadmin/', {
                method: 'post',
                data: $('#savePOS').serialize() + '&paymentMode=' + type,

                error: function (data) {
                    popup.hide('svPreLdr');
                    popup.showError('Unable to perform this action. ' + (data.errorMsg || 'Please try again.'));
                },
                success: function (data) {
                    if (data.status == 'ok') {
                        popup.hide('svPreLdr');
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        }
                    } else {
                        this.error(data);
                    }
                }
            }
            );

            return false;
        }

        function addItems(e, id, key) {
            let itemList = '',
                mbrLists = '',
                exMbrs,
                selected;

            if (items.length != 0) {
                for (itm in items) {
                    const data = items[itm];
                    if (items[itm].mb_id) {
                        selected = id == 'mb_' + data.mb_id;
                        itemList += `<option value="mb_${items[itm].mb_id}" ${selected ? 'selected' : ''}>
                            ${items[itm].mb_name}
                        </option>`;
                    }

                    if (items[itm].pd_id) {
                        selected = id == 'pd_' + data.pd_id;
                        itemList += `<option value="pd_${items[itm].pd_id}" ${selected ? 'selected' : ''}>
                            ${items[itm].pd_name}
                        </option>`;
                    }
                }
            }

            if (id) {
                exMbrs = posCartArr[key].mbrs || [];
                exMbrs.forEach(mb => {
                    mbrLists += `<div id="mbr-${mb.id}" class="mbr-itm">
                        <div class="usr-thumb">
                            <span class="user-thumb">
                                ${mb.avatar
                            ? `<img src="${mb.avatar}">`
                            : mb.name.charAt(0).toUpperCase()}
                            </span>
                        </div>
                        <div class="usr-info">
                            <div class="mbr-name">
                                <span class="name-hold">${mb.name}</span>
                                <span class="sec-hold">${mb.secName}</span>
                            </div>
                            <div class="mbr-close">
                                <span class="material-symbols-outlined icn remove-mbr" data-id="${mb.id}">
                                    remove
                                </span>
                            </div>
                        </div>
                    </div>`;
                });

                selItm = posCartArr[key].id || '';
                selMbr = [...posCartArr[key].mbrs];
                $('.remove-mbr').off('click').on('click', removeMbr);
            }

            popup.show(
                'ADD ITEM',
                `<form action="" method="post" id="saveItm">
                    <input type="hidden" name="method" value="pos-item-save" />
                    <input type="hidden" name="id" value="" />
                    <div class="mb15">
                        <select name="item" class="itm-fld" id="chItem">
                            <option value="" disabled selected>
                                Choose Item
                            </option>
                            ${itemList}
                        </select>
                    </div>
                    <div class="mb15">
                        <span class="pix-btn sm" id="addMember">Add Member</span>
                        <div id="memberList" class="mbrs-list">
                            ${mbrLists}
                        </div>
                    </div>
                    <div class="text-center btn-sec">
                        <input type="submit" class="pix-btn site" name="saveItm" value="Save">
                        <button class="pix-btn popup-close">Cancel</button>
                    </div>
                </form>`,
                {
                    width: 450,
                    id: 'itmForm'
                }
            );

            _e('addMember').onclick = addMember;
            $('#chItem').on('change', function () {
                selItm = $(this).val();
            });

            $("#saveItm").formchecker({
                scroll: 0,
                onValid: function () {
                    $(':focus').blur();

                    if (!Array.isArray(posCartArr)) {
                        return false;
                    }

                    if (!selItm || selItm.trim() === "") {
                        pix.openNotification('No item selected. Please select an item before saving.');
                        return false;
                    }

                    if (!selMbr || !Array.isArray(selMbr) || selMbr.length === 0) {
                        pix.openNotification('No members selected. Please select at least one member.');
                        return false;
                    }

                    if (id && typeof key !== "undefined" && posCartArr[key]) {
                        posCartArr[key].id = selItm;
                        posCartArr[key].mbrs = selMbr;
                    } else {
                        posCartArr.push({
                            id: selItm,
                            mbrs: selMbr
                        });
                    }

                    renderCart();

                    selItm = "";
                    selMbr = [];

                    popup.hide("itmForm");
                    return false;
                }
            });
        }

        function renderCart() {
            let cartWrap = _e('cartWrap'),
                ttlCharge = _e('ttlCharge'),
                totalAmount = 0;

            cartWrap.innerHTML = '';
            cartWrap.classList.add('show');
            _e('cartNoRes').classList.add('hide');

            posCartArr.forEach((cart, key) => {
                let memberList = '',
                    itmAmnt = 0,
                    itmName = '';

                if (cart.id.includes("mb_")) {
                    itmName = items[cart.id].mb_name;
                    itmAmnt = items[cart.id].mb_amnt;
                } else if (cart.id.includes("pd_")) {
                    itmName = items[cart.id].pd_name;
                    itmAmnt = items[cart.id].pd_amnt;
                }

                let cartAmount = itmAmnt * cart.mbrs.length;
                totalAmount += cartAmount;

                cart.mbrs.forEach(mbr => {
                    let mbrAvatar = mbr.avatar
                        ? `<div class="mbr-img"><img src="${mbr.avatar}" alt="" /></div>`
                        : `<div class="no-img"><span class="material-symbols-outlined icn">person</span></div>`;

                    memberList += `<div class="ech-mbr">
                        <input type="hidden" name="paymentCatgry[${cart.id}][]" value="${mbr.id}" />
                        <div class="info-sec">
                            <div class="person-info">
                                <div class="avatar-sec">
                                    ${mbrAvatar}
                                </div>
                                <div class="mbr-nam">${mbr.name}</div>
                            </div>
                        </div>
                        <div class="info-wrap">
                            <div class="wp-lf">Membership:</div>
                            <div class="wp-rg">${mbr.membership ?? '--'}</div>
                        </div>
                        <div class="info-wrap">
                            <div class="wp-lf">Member ID:</div>
                            <div class="wp-rg">${mbr.memberid ?? '--'}</div>
                        </div>
                        <div class="info-wrap">
                            <div class="wp-lf">Section:</div>
                            <div class="wp-rg">${mbr.secName ?? '--'}</div>
                        </div>
                        <div class="info-wrap">
                            <div class="wp-lf">City:</div>
                            <div class="wp-rg">${mbr.city ?? '--'}</div>
                        </div>
                        <div class="info-wrap">
                            <div class="wp-lf">Zipcode:</div>
                            <div class="wp-rg">${mbr.zipcode ?? '--'}</div>
                        </div>
                    </div>`;
                });

                let elm = document.createElement('div');
                elm.className = 'cart-itm';
                elm.id = `id_${key}`;
                elm.innerHTML = `<div class="cart-hed">${itmName}</div>
                <div class="cart-sub">
                    <div class="cart-mbr">Members</div>
                    <div class="cart-mbrs" id="${'mbrList_' + key}">${memberList}</div>
                    <div class="cart-actn-sec">
                        <div class="sec-lf">
                            <span class="act-btn edt-actn" data-key="${key}" data-id="${cart.id}">EDIT</span>
                            <span class="act-btn dlt-actn" data-key="${key}" data-id="${cart.id}">REMOVE</span>
                        </div>
                        <div class="sec-rg">
                            <div class="sec-label">Total Charge</div>
                            <div class="sec-value amnt">${toDollar(cartAmount)}</div>
                        </div>
                    </div>
                </div>`;

                cartWrap.appendChild(elm);
            });

            ttlCharge.classList.add('show');
            _e('chrgAmnt').innerText = toDollar(totalAmount);

            $(".edt-actn").click(changeItem);
            $(".dlt-actn").click(removeItem);

            if (posCartArr.length === 0) {
                $('#cartNoRes').removeClass('hide');
                $('#ttlCharge').removeClass('show');
                $('#cartWrap').removeClass('show');
            }
        }

        function changeItem() {
            var id = this.d('id'),
                key = this.d('key');

            addItems('', id, key);
        }

        function removeItem() {
            var id = this.d('id'),
                key = this.d('key'),
                tgtElm = _e('id_' + key);

            if (confirm('Do you want to remove this POS?')) {
                popup.showSpinner({ id: 'dltSpinner' });

                if (tgtElm && tgtElm.parentNode) {
                    tgtElm.parentNode.removeChild(tgtElm);
                }

                if (posCartArr[key]) {
                    posCartArr.splice(key, 1);
                }

                renderCart();
                popup.hide('dltSpinner');
            }
        }
    });

    function addMember() {
        popup.showPreloader(
            'Loading. Please wait..', 400, 'mbrPreLdr'
        );

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
            popup.hide('mbrPreLdr');
            popup.hide('membPopup');

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
                                    placeholder="Search by Name/Email/Member ID",
                                    value="`+ key + `"
                                />
                                <button
                                    type="submit"
                                    class="srch-btn"
                                >
                                    <span class="material-symbols-outlined">search</span>
                                </button>
                                <select name="sec" id="srchSec" class="srch-sec">
                                    <option value="0">Any Section</option>`
                + optionStr +
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
                { width: 500, id: 'membPopup' }
            );

            _e('showMore').onclick = showMoreMember;
            _e('mbrSrchForm').onsubmit = function (e) {
                srchFormSubmit(e);
            }
            _e('srchSec').onchange = function (e) {
                srchFormSubmit(e);
            }
            $('.add-btn').click(setItemMember);
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
        addMember();
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
                <div class="nam-sec">
                    <span class="name-hold">` + list[i].name + `</span>
                    <span class="sec-hold">` + list[i].secName + `</span>
                </div>
                <div class="actn">
                    <span
                        class="pix-btn site sm add-btn"
                        data-id="` + list[i].id + `"
                        data-name="` + list[i].name + `"
                        data-avatar="` + list[i].avatar + `"
                        data-membership="` + list[i].membership + `"
                        data-memberid="` + list[i].memberId + `"
                        data-city="` + list[i].city + `"
                        data-zipcode="` + list[i].zipcode + `"
                        data-sec = "${list[i].secName}"
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
                .off('click', setItemMember)
                .on('click', setItemMember);
        } else {
            loadMbrErr();
        }
    }

    function setItemMember() {
        let id = $(this).data('id'),
            name = $(this).data('name'),
            avatar = $(this).data('avatar'),
            membership = $(this).data('membership'),
            memberid = $(this).data('memberid'),
            city = $(this).data('city'),
            zipcode = $(this).data('zipcode'),
            secName = $(this).data('sec');

        let exists = selMbr.some(m => m.id === id);

        if (exists) {
            pix.openNotification('Member already exists!');
        } else {
            let member = { id, name, avatar, membership, memberid, city, zipcode, secName };
            selMbr.push(member);
            pix.openNotification('Member added.', 1);

            $("#memberList").append(`
                <div id="mbr-${id}" class="mbr-itm">
                    <div class="usr-thumb">
                        <span class="user-thumb">
                            ${avatar
                    ? `<img src="${avatar}">`
                    : name.charAt(0).toUpperCase()}
                        </span>
                    </div>
                    <div class="usr-info">
                        <div class="mbr-name">
                                <span class="name-hold">${name}</span>
                                <span class="sec-hold">${secName}</span>
                        </div>
                        
                    
                        <div class="mbr-close">
                            <span class="material-symbols-outlined icn remove-mbr" data-id="${id}">
                                remove
                            </span>
                        </div>
                    </div>
                </div>
            `);
        }
    }

    function removeMbr() {
        if (confirm("Do you want to remove this member?")) {
            let id = $(this).data('id');

            selMbr = selMbr.filter(m => m.id !== id);
            $(`#mbr-${id}`).remove();
            pix.openNotification('Member removed.', 1);
        }
    }
})();
