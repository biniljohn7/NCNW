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
                            let importCancelled = false;
                            let finalWord = 'Importing Finished';

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
                                            ${finalWord}
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

                            let totRec = records.length;
                            function continuePop() {
                                popup.show(
                                    'Importing',
                                    `<div style="padding-bottom:20px">
                                    Importing
                                    <span id="upRowCount">
                                        0
                                    </span>
                                    <span class="bold-600">
                                        / ${totRec}
                                    </span>
                                </div>
                                <div style="padding:10px 0; text-align:left">
                                    <span class="pix-btn mb20" id="cancelImport">Cancel</span>
                                </div>`, {
                                    width: 400,
                                    align: 'center',
                                    closebtn: false
                                }
                                );
                            }
                            continuePop();

                            uploadRow();

                            function sendRow(data) {
                                if (importCancelled) return;
                                $.ajax(
                                    domain + 'ajax/anyadmin/', {
                                    method: 'post',
                                    data: {
                                        method: 'members-import',
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

                                        } else if (data.status == 'exist') {
                                            console.log(data);
                                            popup.hide();
                                            popup.show(
                                                'Duplicate Found',
                                                `<div style="padding-bottom:20px">
                                                    <p>One duplicate member record has been identified due to a matching ${data.duplicate}. 
                                                    This member already exists in the database and does not need to be added again.</p>
                                                    <p>
                                                    <table>
                                                        <tr>
                                                            <td><strong>Name</strong></td>
                                                            <td>:</td>
                                                            <td>${data.member.fname} ${data.member.lname}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>MemberID</strong></td>
                                                            <td>:</td>
                                                            <td>${data.member.memberID}</td>
                                                        </tr>
                                                        <tr>
                                                            <td><strong>Email</strong></td>
                                                            <td>:</td>
                                                            <td>${data.member.email}</td>
                                                        </tr>
                                                    </table>
                                                    </p>
                                                </div>
                                                <div style="padding:10px 0; text-align:left">
                                                    <span class="pix-btn mb10" id="skipImport">Skip</span>
                                                </div>`, { width: 500, closebtn: false }
                                            );
                                            // $('#acceptImport').on("click", function () {
                                            //     popup.hide();
                                            //     continuePop();
                                            //     setTimeout(uploadRow, 100);
                                            // });
                                            $('#skipImport').on("click", function () {
                                                popup.hide();
                                                errCount++;
                                                incCount();
                                                continuePop();
                                                setTimeout(uploadRow, 100);
                                            });
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

                            $('#cancelImport').on("click", function () {
                                records.length = 0;
                                importCancelled = true;
                                finalWord = 'Import cancelled by user.';
                            });
                            //


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