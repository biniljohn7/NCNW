(function () {
    const electedMap = {
        officers_titles: () => elected.selTitles,
        states: () => elected.selStates,
        chapters: () => elected.selSections,
        regions: () => elected.selRegions,
        affiliates: () => elected.selAffiliates
    };

    $(document).ready(function () {
        $(".member-elect").on('change', setOfficer);
        $(".choose-items-for-filter").click(openItemsWin);
    });

    function openItemsWin() {
        let table = $(this).data('table'),
            label = $(this).data('label'),
            selName = $(this).data('name'),
            searchlabel = '';
        if (label === 'Sections') {
            searchlabel = 'or ID';
        }
        popup.showSpinner({ id: "spnItemPicker" });

        $.ajax(domain + 'ajax/anyadmin/', {
            method: 'get',
            data: {
                method: 'items-list',
                table: table
            },
            error: function () {
                popup.hide('spnItemPicker');
                popup.showError('Unable to load list. Please try again.');
            },
            success: function (data) {
                if (data.status == 'ok') {
                    var
                        listHtml = '',
                        loLen,
                        i,
                        add,
                        itm,
                        itmObjs;

                    for (itm of data.data) {
                        add = addClassFunc(table, itm.id);

                        listHtml += `<div 
                            class="itm-obj ${add}"
                            data-id="${itm.id}"
                            data-name="${itm.name}"
                        >
                            <span class="obj-txt">${itm.name}</span>
                        </div>`;
                    }

                    popup.hide('spnItemPicker');
                    popup.show(
                        'Choose ' + data.dataLabel,
                        `<div class="item-picker">
                            <div class="itm-search">
                                <input type="text" id="itmSearchInp" placeholder="Search by name ${searchlabel}">
                                <span class="material-symbols-outlined srch-icn">
                                    search
                                </span>
                            </div>
                            <div class="itm-list">
                                ${listHtml}
                            </div>
                            <div class="itm-add-btn">
                                <span class="pix-btn site sm btn" id="addItm">
                                    Choose ${data.dataLabel}
                                </span>
                            </div>
                        </div>`,
                        {
                            width: 450,
                            id: 'itemsPopup'
                        }
                    );

                    itmObjs = $('.itm-obj');
                    loLen = itmObjs.length;
                    function searchItem() {
                        var value = this.value.trim();

                        if (value) {
                            value = new RegExp(value, 'i');
                            for (i = 0; i < loLen; i++) {
                                itmObjs[i].style.display = value.test(itmObjs[i].getClass('obj-txt').innerText) ? '' : 'none';
                            }
                        } else {
                            $('.itm-obj').css('display', '');
                        }

                    }
                    _e('itmSearchInp').onkeyup = searchItem;
                    _e('itmSearchInp').onblur = searchItem;

                    $('.itm-obj').click(function () {
                        let id = $(this).data('id'),
                            name = $(this).data('name');
                        if (table == 'officers_titles') {
                            if ($(this).hasClass('add')) {
                                elected.selTitles = elected.selTitles.filter(x => x.id != id);
                                $(this).removeClass('add');
                            } else {
                                elected.selTitles.push({ id: id, name: name });
                                $(this).addClass('add');
                            }
                        } else if (table == 'states') {
                            if ($(this).hasClass('add')) {
                                elected.selStates = elected.selStates.filter(x => x.id != id);
                                $(this).removeClass('add');
                            } else {
                                elected.selStates.push({ id: id, name: name });
                                $(this).addClass('add');
                            }
                        } else if (table == 'chapters') {
                            if ($(this).hasClass('add')) {
                                elected.selSections = elected.selSections.filter(x => x.id != id);
                                $(this).removeClass('add');
                            } else {
                                elected.selSections.push({ id: id, name: name });
                                $(this).addClass('add');
                            }
                        } else if (table == 'regions') {
                            if ($(this).hasClass('add')) {
                                elected.selRegions = elected.selRegions.filter(x => x.id != id);
                                $(this).removeClass('add');
                            } else {
                                elected.selRegions.push({ id: id, name: name });
                                $(this).addClass('add');
                            }
                        } else if (table == 'affiliates') {
                            if ($(this).hasClass('add')) {
                                elected.selAffiliates = elected.selAffiliates.filter(x => x.id != id);
                                $(this).removeClass('add');
                            } else {
                                elected.selAffiliates.push({ id: id, name: name });
                                $(this).addClass('add');
                            }
                        }
                    });

                    _e('addItm').onclick = function () {
                        addItems(
                            table,
                            label,
                            selName
                        );
                    }
                } else {
                    this.error();
                }
            }
        });
    }

    function addClassFunc(table, itm) {
        let selArr = (electedMap[table] || (() => []))();
        return selArr.some(a => a.id == itm) ? 'add' : '';
    }

    function addItems(
        table,
        label,
        selName
    ) {
        let arr = (electedMap[table] || (() => []))();

        $(`input[name="${selName}[]"]`).remove();
        $(`#chosed${label}ForFltr .sel-name`).remove();

        arr.forEach(itm => {
            const ttlHidden = $('<input>', {
                type: 'hidden',
                name: selName + '[]',
                value: itm.id
            });

            const ttlText = $('<span>', {
                text: itm.name,
                class: 'sel-name'
            });

            $(`#chosed${label}ForFltr`).append(ttlHidden, ttlText);
        });

        popup.hide('itemsPopup');
    }

    function setOfficer() {
        let has = this.options[this.selectedIndex].getAttribute('data-has');
        if (has == 'yes') {
            let confirmed = confirm('Do you want to change the officer title?');
            if (!confirmed) {
                retun;
            }
        }
        popup.showSpinner();
        let offId = this.value;
        let memId = this.options[this.selectedIndex].getAttribute('data-member');
        $.ajax(
            domain + 'ajax/anyadmin/', {
            method: 'post',
            data: {
                method: 'elect-officers',
                uId: memId,
                offId: offId
            },
            error: setoffError,
            success: setOffSuccess
        });
    }

    function setoffError(data) {
        popup.hide();
        popup.showError(
            data.msg || 'An error occurred. Please try again later.',
            { width: 400 }
        );
    }

    function setOffSuccess(data) {
        if (data.status == 'ok') {
            popup.hide();
            popup.showSuccess(data.msg || 'An error occurred. Please try again later.', 1);
            //window.location.reload();
        } else {
            setoffError(data);
        }
    }
})();