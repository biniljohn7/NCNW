(function () {
    var
        filesSelect = [],
        upFiles = [],
        fileList,
        requestId,
        rqId;

    $(document).ready(function () {
        fileList = _e('fileList');
        rqId = _e('reqId');

        $('#saveRequest').formchecker({
            scroll: 0,
            onValid: saveRequest
        });
        $("#reqDate").datepicker({
            dateFormat: 'dd M yy',
            minDate: 0
        });

        $('.file-dlt').click(deleteReqFile);
    });

    function saveRequest() {
        $(':focus').blur();
        ShowStatus('Saving request data..');

        $.ajax(
            domain + 'ajax/anyadmin/', {
            method: 'post',
            data: $('#saveRequest').serialize(),
            error: function (data) {
                console.log(data);
                popup.hide();
                popup.showError(
                    'Unable to perform this action. ' +
                    (data.errorMsg || 'Please try again.')
                );
            },
            success: function (data) {
                //console.log(data);
                if (data.status == 'ok') {
                    let
                        inpEl = _e('attachFilles'),
                        files = inpEl.files,
                        file,
                        i,
                        textXp = /\.(png|gif|jpe*g|pdf|txt|docx|doc|xls|xlsx|rtf|ppt|pptx|ai|psd|ttf|eps)$/i;

                    //.jpeg,.jpg,.png,.gif,.pdf,.txt,.docx,.doc,.xls,.xlsx,.rtf,.ppt,.pptx,.ai,.psd,.ttf,.eps

                    requestId = data.id;

                    if (files.length < 1) {
                        redirectDetailPage(requestId);
                    }

                    reqData.reqId = data.id;
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
                        inpEl.value = '';
                    }
                } else {
                    this.error(data);
                }
            }
        }
        );

        return false;
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

                fileList.appendChild(fileObj);
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
                        request: reqData.reqId,
                        files: uf.file
                    },
                    error: function () {
                        fileUploadFailed();
                    },
                    success: function (e, data) {
                        if (data.status == 'ok') {
                            delete data.status;
                            setFileData(uf.key, data);

                            upFiles.shift();
                            processFileUpload();

                            if (
                                (
                                    rqId.value === '' ||
                                    rqId.value !== ''
                                ) &&
                                upFiles.length === 0
                            ) {
                                redirectDetailPage(reqData.reqId);
                            }
                        } else {
                            e.args.error();
                        }
                    }
                }
                );
            } else {
                fileUploadFailed();
            }
        } else {
            filesSelect = [];
            popup.hide();
        }
    }

    function fileUploadFailed() {
        upFiles.shift();
    }

    function setFileData(key, data) {
        let fileObj = _e('file_' + key);
        fileObj.innerHTML = `<a class="file-link" href="${data.file}">
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
                    request: reqData.reqId,
                    fileId: this.d('id')
                },
                error: function () {
                    popup.showError('Unable to perform this action. Please try again.');
                },
                success: function (data) {
                    if (data.status == 'ok') {
                        popup.hide();
                        pNode.remove();
                        pix.openNotification('File removed', true);
                    } else {
                        this.error();
                    }
                }
            }
            );
        }
    }

    function redirectDetailPage(id) {
        window.location.href = domain + '?page=requests&sec=details&id=' + id;
    }

    function ShowStatus(text) {
        popup.hide();
        popup.show(
            '',
            `<div style="padding:25px 0;">
                ${text}
            </div>`, {
            width: 350,
            align: 'center',
            closebtn: 0
        }
        );
    }
})();

function fileCheck() {
    let attachFiles = _e('attachFilles').files,
        textExp = /\.(png|gif|jpe*g|pdf|txt|docx|doc|xls|xlsx|rtf|ppt|pptx|ai|psd|ttf|eps)$/i,
        i;

    if (attachFiles.length > 0) {
        for (i = 0; i < attachFiles.length; i++) {
            const file = attachFiles[i];

            if (!textExp.test(file.name)) {
                return false;
            }
        }
        return true;
    } else {
        return true;
    }
}