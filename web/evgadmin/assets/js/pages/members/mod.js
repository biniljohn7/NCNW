(function () {
    let addField, edcFlds;

    $(document).ready(function () {
        addField = _e("addField");
        edcFlds = $("#edcFlds");
        _e('addNewField').onclick = addNewField;
        _e("chooseAff").onclick = chooseAffiliates;

        $(".delete-edc").click(deleteEducation);

        $("#yearOfIni").datepicker({
            maxDate: 0,
            dateFormat: "d / MM / yy",
        });
        $("#dtOfBirth").datepicker({
            maxDate: 0,
            dateFormat: "dd-mm-yy",
        });
        $("#memberSave").formchecker({
            onValid: function () {
                $(":focus").blur();
                popup.showSpinner();
            }
        });

        $("#isMinor").change(function() {
            $('.gp-fields').toggleClass('show', $(this).prop("checked"));
        });

        function chooseAffiliates() {                           
            popup.showSpinner({
                id: 'spnAffPicker'
            });

            $.ajax(domain + 'ajax/anyadmin/', {
                method: 'get',
                data: {
                    method: 'affiliates-list',
                },
                error: function () {
                    popup.hide('spnAffPicker');
                    popup.showError('Unable to load affiliate list. Please try again.');
                },
                success: function (data) {
                    if (data.status == 'ok') {
                        var listHtml = '',
                        listObjs,
                        loLen,
                        aff,
                        add,
                        i;

                        for (aff of data.data) {
                            add = mb.selAffInfo.some(a => a.id === aff.affiliateId) ? 'add' : '';

                            listHtml += `<li
                                class="aff-wrap ${add}"
                                data-id="${aff.affiliateId}"
                                data-name="${aff.affiliateName}"
                            >
                                <div class="wrap-lf">
                                    <span class="btns select-aff">
                                        <span class="material-symbols-outlined icn">
                                            check
                                        </span>
                                    </span>
                                </div>
                                <div class="wrap-rg">
                                    ${aff.affiliateName}
                                </div>
                            </li>`;
                        }
                        popup.hide("spnAffPicker");
                        popup.show(
                            'Choose Affiliates',
                            `<div class="affiliate-picker">
                                <div class="aff-search">
                                    <input type="text" id="affSearchInp">
                                    <span class="material-symbols-outlined search-icn">
                                        search
                                    </span>
                                </div>
                                <ul class="aff-list">
                                    ${listHtml}
                                </ul>
                                <div class="aff-add-btn">
                                    <span class="pix-btn site sm btn" id="addAff">
                                        CHOOSE AFFILIATES
                                    </span>
                                </div>
                            </div>`,
                            {
                                width: 450,
                                id: 'affList'
                            }
                        );

                        listObjs = $('.aff-wrap');
                        loLen = listObjs.length;
                        function searchAffiliate() {
                            var key = this.value.trim();
                            if(key) {
                                key = new RegExp(key, 'i')
                                for(i = 0; i < loLen; i++) {
                                    listObjs[i].style.display = 
                                    key.test(listObjs[i].getClass('wrap-rg').innerText) ? '' : 'none'
                                }
                            } else {
                                $('.aff-wrap').css('display', '');
                            }
                        }
                        _e('affSearchInp').onkeyup = searchAffiliate;
                        _e('affSearchInp').onblur = searchAffiliate;

                        $('.aff-wrap').click(function () {
                            let id = $(this).data('id'),
                                name = $(this).data('name');

                            if ($(this).hasClass('add')) {
                                mb.selAffInfo = mb.selAffInfo.filter(x => x.id !== id);
                                $(this).removeClass('add');
                            } else {
                                mb.selAffInfo.push({ id: id, name: name });
                                $(this).addClass('add');
                            }
                        });

                        _e('addAff').onclick = addAffiliations;
                    } else {
                        this.error();
                    }
                }
            });
        }

        function addAffiliations() {
            $('input[name="affilateOrgzn[]"]').remove();
            $('.aff-name').remove();

            mb.selAffInfo.forEach(aff => {
                const affHidden = $('<input>', {
                    type: 'hidden',
                    name: 'affilateOrgzn[]',
                    value: aff.id
                });

                const affText = $('<p>', {
                    text: aff.name,
                    class: 'aff-name'
                });

                $('#affHidden').append(affHidden, affText);
            });

            popup.hide('affList');
        }

        var stateSel = _e("stateSel");
        _e("countrySel").onchange = function () {
            let nation = this.value;
            $(":focus").blur();
            if (nation) {
                $.ajax(domain + "ajax/anyadmin/", {
                    method: "post",
                    data: {
                        method: "member-state-dropdown",
                        nation: nation,
                    },
                    error: function () {
                        stateSel.innerHTML = "";
                        stateSel.innerHTML =
                            '<option value="" disabled selected>Choose State</option>';
                    },
                    success: function (data) {
                        if (data.status == "ok") {
                            var stData = data.stData;
                            stateSel.innerHTML = "";
                            stateSel.innerHTML =
                                '<option value="" disabled selected>Choose State</option>';
                            stData.forEach((state) => {
                                stateSel.innerHTML +=
                                    '<option value="' +
                                    state.stateId +
                                    '">' +
                                    state.stateName +
                                    "</option>";
                            });
                        } else {
                            this.error();
                        }
                    },
                });
            } else {
                stateSel.innerHTML =
                    '<option value="" disabled selected>Choose State</option>';
            }
        };

        var regionSel = _e("regionSel"),
            orgStateId = _e("orgStateId"),
            sectionSel = $(".selected-sec");

        _e("nationSel").onchange = function () {
            let nation = this.value;
            if (nation) {
                $.ajax(domain + "ajax/anyadmin/", {
                    method: "post",
                    data: {
                        method: "region-dep-dropdown",
                        nation: nation,
                    },
                    error: function () {
                        regionSel.innerHTML = "";
                        regionSel.innerHTML =
                            '<option value="" disabled selected>Choose Region</option>';
                    },
                    success: function (data) {
                        if (data.status == "ok") {
                            var rDatas = data.rDatas;
                            regionSel.innerHTML = "";
                            regionSel.innerHTML =
                                '<option value="" disabled selected>Choose Region</option>';
                            rDatas.forEach((region) => {
                                regionSel.innerHTML +=
                                    '<option value="' +
                                    region.regId +
                                    '">' +
                                    region.regName +
                                    "</option>";
                            });
                        } else {
                            this.error();
                        }
                    },
                });
            } else {
                regionSel.innerHTML =
                    '<option value="" disabled selected>Choose Region</option>';
            }
        };

        _e("regionSel").onchange = function () {
            let region = this.value;
            if (region) {
                $.ajax(domain + "ajax/anyadmin/", {
                    method: "post",
                    data: {
                        method: "state-dep-dropdown",
                        region: region,
                    },
                    error: function () {
                        orgStateId.innerHTML = "";
                        orgStateId.innerHTML = '<option value="">Choose State</option>';
                    },
                    success: function (data) {
                        if (data.status == "ok") {
                            
                            var sDatas = data.sDatas;
                            orgStateId.innerHTML = "";
                            orgStateId.innerHTML = '<option value="">Choose State</option>';
                            sDatas.forEach((state) => {
                                orgStateId.innerHTML +=
                                    '<option value="' +
                                    state.stId +
                                    '">' +
                                    state.stName +
                                    "</option>";
                            });
                        } else {
                            this.error();
                        }
                    },
                });
            } else {
                orgStateId.innerHTML = '<option value="">Choose State</option>';
            }
        };

        $("#orgStateId").on("change", function () {
            let state = $(this).val();

            if (state) {
                $.ajax(domain + "ajax/anyadmin/", {
                    method: "post",
                    data: {
                        method: "section-dep-dropdown",
                        state: state,
                    },
                    error: function () {
                        sectionSel.each(function () {
                            $(this).html(
                                '<option value="" disabled selected>Choose Section</option>'
                            );
                        });
                    },
                    success: function (data) {
                        if (data.status === "ok") {
                            let sDatas = data.sDatas;

                            sectionSel.each(function () {
                                let $select = $(this);
                                $select.html(
                                    '<option value="" disabled selected>Choose Section</option>'
                                );
                                $.each(sDatas, function (i, section) {
                                    $select.append(
                                        $("<option>", {
                                            value: section.scId,
                                            text: section.scName,
                                        })
                                    );
                                });
                            });
                        } else {
                            this.error();
                        }
                    },
                });
            } else {
                sectionSel.each(function () {
                    $(this).html(
                        '<option value="" disabled selected>Choose Section</option>'
                    );
                });
            }
        });

        addField.onclick = function () {
            edcFlds.append(addEducationField());
            edcFlds.className = "mb20";
            _e("eduValid").value = true;
        };
        $('.field-delete').click(function () {
            let fieldObj = $(this).parents('.fm-field');
            fieldObj.remove();
        });
    });

    function addEducationField() {
        let elem = document.createElement("div"),
            university = "",
            degree = "",
            unv,
            deg;

        for (unv in education.university) {
            const unvData = education.university[unv];
            university += `<option value="${unvData.id}">
                        ${unvData.name}
                    </option>`;
        }

        for (deg in education.degree) {
            const degData = education.degree[deg];
            degree += `<option value="${degData.id}">
                        ${degData.name}
                    </option>`;
        }

        elem.className = "edc-fld mb15";
        elem.innerHTML = `
                <div class="fld-lf">
                    <div class="edc-tp">
                        <select name="university[]" data-type="string" data-label="university">
                            <option value="">
                                Choose University
                            </option>
                            ${university}
                        </select>           
                    </div>
                    <div class="">
                        <select name="degree[]" data-type="string" data-label="degree">
                            <option value="">
                                Choose Degree
                            </option>
                            ${degree}
                        </select>
                    </div>
                </div>
                <div class="fld-rg">
                    <span class="delete-edc">
                        <span class="material-symbols-outlined">
                            delete
                        </span>
                        Remove 
                    </span>
                </div>`;

        $(elem).find(".delete-edc").click(deleteEducation);

        return elem;
    }

    function deleteEducation() {
        const tgtElemn = $(this).closest(".edc-fld");
        tgtElemn.remove();

        if (edcFlds.children(".edc-fld").length == 0) {
            _e("eduValid").value = "";
        }
    }
    function addNewField() {
        new FmComponent().openSelector({
            parentElementId: 'customFields',
        });
    }
})();
function validateCheckboxes() {
    var itm,
        checkboxes = document.getElementsByName("expertise[]");

    for (itm of checkboxes) {
        if (itm.checked) {
            return true;
        }
    }
    return false;
}
function validateBusEmail() {
    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
        mailTxt = document.querySelector('[name="bEmail"]').value;

    if(mailTxt && !emailRegex.test(mailTxt)) {
        return false
    }

    return true;
}

function validateGEmail() {
    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
        gMailTxt = document.querySelector('[name="gpEmail"]').value;

    if(gMailTxt && !emailRegex.test(gMailTxt)) {
        return false
    }

    return true;
}

function validateGPhone() {
    var phoneRegex = /^[+]?[(]?[0-9]{3}[)]?[-\s.]?[0-9]{3}[-\s.]?[0-9]{4,6}$/,
        gPhoneTxt = document.querySelector('[name="gpPhone"]').value;

    if(gPhoneTxt && !phoneRegex.test(gPhoneTxt)) {
        return false
    }

    return true;
}
