(function () {
    let
        cmpCount = 0,
        sccCount = 0,
        errCount = 0,
        duplicates = [],
        duplicateKeys = new Set(),
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
                                    let duplicateHtml = '';

                                    if (duplicates.length > 0) {
                                        duplicateHtml = `
                                        <div class="mt20">
                                            <h4>Duplicate Members (${duplicates.length})</h4>
                                        
                                            <div class="duplicate-list compare-list">
                                        
                                                ${duplicates.map(d => `
                                                <div class="duplicate-compare-card">
                                        
                                                    <div class="dup-header">
                                                        Duplicate By: ${d.type.toUpperCase()}
                                                    </div>
                                        
                                                    <div class="dup-compare-grid">
                                        
                                                        <!-- Uploaded -->
                                                        <div class="dup-column uploaded">
                                                            <h5>Import</h5>
                                        
                                                            <p><b>Name:</b> ${d.uploaded.fname} ${d.uploaded.lname}</p>
                                                            <p><b>Email:</b> ${d.uploaded.email ?? '--'}</p>
                                                            <p><b>Member ID:</b> ${d.uploaded.memberID ?? '--'}</p>
                                                        </div>
                                        
                                                        <!-- Existing -->
                                                        <div class="dup-column existing">
                                                            <h5>Existing</h5>
                                        
                                                            <a href="${d.uploaded.link}"
                                                            target="_blank"
                                                            class="pix-btn xs outlined">
                                                            View Member
                                                            </a>
                                        
                                                            <p><b>Name:</b> ${d.existing.fname} ${d.existing.lname}</p>
                                                            <p><b>Email:</b> ${d.existing.email ?? '--'}</p>
                                                            <p><b>Member ID:</b> ${d.existing.memberID ?? '--'}</p>
                                                        </div>
                                        
                                                    </div>
                                        
                                                </div>
                                                `).join('')}
                                        
                                            </div>
                                        </div>`;
                                    }


                                    popup.showSuccess(`
                                        <div class="bold-600 text-black">
                                            ${finalWord}
                                        </div>
                                        <div class="pt10 mb30">
                                            <span class="text-green pr30">
                                                <strong>Added:</strong> ${sccCount}
                                            </span>
                                            <span class="text-red">
                                                <strong>Failed:</strong> ${errCount}
                                            </span>
                                        </div>
                                        ${duplicateHtml}
                                    `, { width: 800 });
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
                                    width: 600,
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
                                            let key = `${data.member.email || ''}-${data.member.phone || ''}-${data.member.memberID || ''}`;

                                            if (!duplicateKeys.has(key)) {
                                                duplicateKeys.add(key);
                                                duplicates.push({
                                                    type: data.duplicate,
                                                    uploaded: data.uploaded,
                                                    existing: data.member
                                                });

                                            }

                                            errCount++;
                                            incCount();
                                            setTimeout(uploadRow, 100);
                                        } else {
                                            this.error();
                                        }
                                    }
                                });
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