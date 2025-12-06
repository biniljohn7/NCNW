function FmComponent() {
    var onFieldPick,
        closeOnSelect,
        parentElementId = '';

    function openFieldCreateFm(e, data = null) {
        popup.show(
            "Create New Field",
            `<form action="" method="post" id="fieldCreateForm">
                <input type="hidden" name="method" value="form-component-mod" />
                <div class="bold-500 mb10">Field Label</div>
                <div class="mb30">
                    <input type="text" id="fieldLabel" size="50" name="label" data-type="string" data-label="label" value="${data && data.label ? data.label : ''}" />
                </div>
                <div class="bold-500 mb10">Field Type:</div>
                <div class="mb30">
                    <div class="mb10">
                        ${pix.models.Radio({
                name: 'type',
                label: 'Text',
                value: 'text',
                checked: (!data || (data && data.type == 'text')),
                className: 'field-type'
            })}
                    </div>
                    <div>
                        ${pix.models.Radio({
                name: 'type',
                label: 'Dropdown',
                value: 'select',
                checked: data && data.type == 'select',
                className: 'field-type'
            })}
                    </div>
                </div>
                <div id="dropdownOptions" class="mb10" ${data && data.type == 'select' ? '' : 'style="display:none;"'}>
                    <div class="note mb10">Enter options separated by comma</div>
                    <textarea name="options" rows="5" cols="50">${data && data.options ? data.options : ''}</textarea>
                </div>
                <div>
                    <button type="submit" class="pix-btn site">Create Field</button>
                </div>
            </form>`,
            {
                width: 400,
                callback: function () {
                    $('.field-type-rdo').on('change', loadFieldTypeOptions);
                }
            }
        );

        _e("fieldLabel").focus();

        $("#fieldCreateForm").formchecker({
            scroll: 0,
            onValid: function () {
                $(":focus").blur();
                popup.showSpinner({ id: "createFieldSpn" });

                $.ajax(domain + "ajax/anyadmin/", {
                    method: "post",
                    data: $("#fieldCreateForm").serialize(),
                    error: function () {
                        popup.hide("createFieldSpn");
                        popup.showError("Unable to create field. Please try again.");
                    },
                    success: function (res) {
                        if (res.status == "ok") {
                            popup.hide("createFieldSpn");
                            let data = res.data;
                            pickComponent({
                                label: data.label,
                                type: data.type,
                                options: data.options
                            });
                        } else {
                            this.error();
                        }
                    },
                });
                return false;
            },
        });
    }

    function pickComponent(data) {
        if (onFieldPick) {
            onFieldPick(data);
        }

        if (parentElementId) {
            parentElement = parentElementId ? _e(parentElementId) : null;

            if (parentElement) {
                var duplicateField = parentElement.querySelector(`[data-label="${data.label}"][data-fltype="${data.type}"]`);

                if (!duplicateField) {
                    var formField = document.createElement('div'),
                        fldHtml = '',
                        optionsHtml = '',
                        inpName = makeString('ln', 10);

                    fldHtml += `<input type="hidden" name="cuztm_${inpName}_field" value='${JSON.stringify(data)}' />`;
                    if (data.type == 'text') {
                        fldHtml += `<input type="text" name="cuztm_${inpName}_value" />`;
                    } else if (data.type == 'select') {
                        if (data.options && data.options.length > 0) {
                            optionsHtml += data.options
                                .map(opt => `<option value="${opt.trim()}">${opt.trim()}</option>`)
                                .join('');
                        }
                        fldHtml += `<select name="cuztm_${inpName}_value">${optionsHtml}</select>`;
                    }

                    formField.className = 'fm-field';
                    formField.setAttribute('data-label', data.label);
                    formField.setAttribute('data-fltype', data.type);
                    formField.innerHTML = `
                        <div class="fld-label">${data.label}</div>
                        <div class="fld-inp">${fldHtml}</div>
                        <div class="field-actions">
                            <span class="field-delete">
                                <span class="material-symbols-outlined icn">delete</span>
                            </span>
                        </div>`;
                    formField.getClass('field-delete').onclick = function () {
                        formField.remove();
                    }
                    parentElement.appendChild(formField);
                    formField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                } else {
                    pix.openNotification(`"${data.label}" already exists in the form.`);
                }
            }
        }

        if (closeOnSelect) {
            popup.hide();
        }
    }

    function loadFieldTypeOptions() {
        let fieldType = this.value;
        _e('dropdownOptions')[fieldType == 'select' ? 'show' : 'hide']();
    }

    return {
        openSelector: function (ops) {
            parentElementId = ops.parentElementId || null;

            if (ops.onSelect) {
                onFieldPick = ops.onSelect;
            }

            closeOnSelect = ops.closeOnSelect !== undefined ? ops.closeOnSelect : true;

            popup.showSpinner({ id: 'spnFieldPicker' });

            $.ajax(domain + 'ajax/anyadmin/', {
                method: 'get',
                data: { method: 'form-components-list' },
                error: function () {
                    popup.hide('spnFieldPicker');
                    popup.showError('Unable to load templates. Please try again.');
                },
                success: function (data) {
                    if (data.status == 'ok') {
                        popup.hide('spnFieldPicker');
                        popup.show(
                            'Choose A Component',
                            `<div class="field-picker">
                                <div class="label-srh">
                                    <input type="text" id="srhKey" placeholder="Search by label" />
                                    <span class="material-symbols-outlined">search</span>
                                </div>
                                <div class="component-lst" id="componentLst"></div>
                                <div class="component-add">
                                    <span class="pix-btn site" id="fieldMod">Add New Field</span>
                                </div>
                            </div>`,
                            {
                                width: 450,
                                callback: function () {
                                    var fieldObj, fieldHtml = '', optionsHtml = '', field, itm;

                                    for (field of data.list) {
                                        fieldHtml = '';
                                        if (field.type == 'text') {
                                            fieldHtml = `<input type="text" />`;
                                        } else if (field.type == 'select') {
                                            optionsHtml = '';
                                            for (itm of field.options) {
                                                optionsHtml += `<option value="${itm}">${itm}</option>`;
                                            }
                                            fieldHtml = `<select>${optionsHtml}</select>`;
                                        }

                                        fieldObj = document.createElement('div');
                                        fieldObj.className = 'field-obj';
                                        fieldObj.fieldData = field;
                                        fieldObj.innerHTML = `<div class="field-type">
                                                <div class="field-label">${field.label}</div>
                                                <div class="field-actions">
                                                    <span class="actn-btn comp-choose" class="mr5">
                                                        <span class="material-symbols-outlined icn">add</span>
                                                    </span>
                                                    <span class="actn-btn comp-edit" class="mr5">
                                                        <span class="material-symbols-outlined icn">edit</span>
                                                    </span>
                                                    <span class="actn-btn comp-delete" class="confirm">
                                                        <span class="material-symbols-outlined icn">delete</span>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="field-inp">${fieldHtml}</div>`;

                                        _e('componentLst').appendChild(fieldObj);
                                    }
                                }
                            }
                        );

                        $('.comp-choose').click(onSelectButtonClick);
                        $('.comp-edit').click(onFieldEdit);
                        $('.comp-delete').click(deleteField);
                        _e("fieldMod").onclick = openFieldCreateFm;

                        var listObjs = $('.field-obj'),
                            loLen = listObjs.length;

                        function onSelectButtonClick() {
                            const fieldObj = this.closest('.field-obj');
                            const data = fieldObj.fieldData;
                            if (data) {
                                pickComponent(data);
                            } else {
                                pix.openNotification("Field data not found.");
                            }
                        }

                        function srhComponent() {
                            var key = this.value.trim();
                            if (key) {
                                key = new RegExp(key, 'i');
                                for (let i = 0; i < loLen; i++) {
                                    listObjs[i].style.display =
                                        key.test(listObjs[i].getClass('field-label').innerText) ? '' : 'none';
                                }
                            } else {
                                $('.field-obj').css('display', '');
                            }
                        }
                        function onFieldEdit() {
                            const fieldObj = this.closest('.field-obj');
                            const data = fieldObj.fieldData;
                            if (data) {
                                openFieldCreateFm(null, data);
                            } else {
                                pix.openNotification("Field data not found.");
                            }
                        }
                        function deleteField() {
                            const fieldObj = this.closest('.field-obj');
                            const data = fieldObj.fieldData;
                            if (data) {
                                pix.post(
                                    {
                                        method: 'form-component-delete',
                                        label: data.label,
                                        type: data.type
                                    },
                                    function () {
                                        fieldObj.remove();
                                    },
                                    {
                                        role: 'anyadmin',
                                        successMsg: 'Field deleted successfully.',
                                    }
                                );
                            } else {
                                pix.openNotification("Field data not found.");
                            }
                        }

                        _e('srhKey').onkeyup = srhComponent;
                        _e('srhKey').onblur = srhComponent;
                    } else {
                        this.error();
                    }
                }
            });
        }
    };
}
