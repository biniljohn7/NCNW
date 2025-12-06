(function () {
    let
        cmpCount = 0,
        sccCount = 0,
        errCount = 0,
        refError = [];

    $(document).ready(function () {
        $('#importForm').formchecker({
            onValid: function () {
                cmpCount = 0;
                sccCount = 0;
                errCount = 0;

                $(':focus').blur();
                popup.showSpinner();

                const file = _e('csvFile').files[0];

                pix.startFileUpload(
                    domain + 'ajax/anyadmin/', {
                    data: {
                        method: 'csv-read',
                        file: file
                    },
                    error: function () {
                        popup.hide();
                        pix.openNotification('Unable to read CSV data');
                    },
                    success: function (e, data) {
                        if (data.status == 'ok') {
                            let records = data.records;

                            if (records.length === 0) {
                                popup.showError('The file is empty.');
                                return;
                            }

                            records.shift();

                            function uploadRow() {
                                if (records[0]) {
                                    sendRow(records[0]);
                                } else {
                                    _e('importForm').reset();
                                    popup.hide();
                                    popup.showSuccess(
                                        `<div class="bold-600 text-black">
                                            Importing Finished
                                        </div>
                                        <div class="pt10 mb30">
                                            <span class="text-green pr30">
                                                <span class="bold-600">
                                                    Added:
                                                </span>
                                                ${sccCount}
                                            </span>
                                            <span class="text-red">
                                                <span class="bold-600">
                                                    Failed:
                                                </span>
                                                ${errCount}
                                            </span>
                                        </div>`
                                    );
                                }
                            }

                            popup.hide();
                            popup.show(
                                'Importing',
                                `<div style="padding-bottom:20px">
                                    Importing
                                    <span id="upRowCount">
                                        0
                                    </span>
                                    <span class="bold-600">
                                        / ${records.length}
                                    </span>
                                </div>`, {
                                width: 400,
                                align: 'center',
                                closebtn: false
                            }
                            );

                            uploadRow();

                            function sendRow(data) {
                                $.ajax(
                                    domain + 'ajax/admin/', {
                                    method: 'post',
                                    data: {
                                        method: 'sub-admin-import',
                                        records: JSON.stringify({ data: data })
                                    },
                                    error: function () {
                                        errCount++;
                                        incCount();
                                        setTimeout(uploadRow, 2000);
                                    },
                                    success: function (data) {
                                        if (data.status == 'ok') {
                                            sccCount++;
                                            incCount();
                                            setTimeout(uploadRow, 100);
                                        } else {
                                            this.error();
                                        }
                                    }
                                }
                                );
                            }

                            function incCount(data) {
                                cmpCount++;
                                records.shift();
                                _e('upRowCount').innerText = cmpCount;
                            }
                        } else {
                            popup.hide();
                            pix.openNotification('Unable to read CSV data');
                        }
                    }
                }
                );
                return false;
            }
        });
    });
})();