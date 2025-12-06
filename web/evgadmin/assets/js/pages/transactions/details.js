(function () {
    $(document).ready(function () {
        _e('trcnMarkPaid').onclick = trcnMarkPaid;
    });

    function trcnMarkPaid() {
        popup.show(
            'Mark as Transaction Success',
            `<form action="` + domain + `actions/anyadmin/" id="trcnMarkForm" method="post">
                <input type="hidden" name="method" value="transaction-mark-success">
                <input type="hidden" name="trnId" value="` + this.d('id') + `">

                <div class="bold-700 mb10">
                    Reference Number
                </div>
                <div class="mb20">
                    <input type="text" size="100" name="refnumber" data-type="string" data-label="reference Number" />
                </div>

                <div class="pt20">
                    <button class="pix-btn site">
                        Done
                    </button>
                </div>
            </form>`, {
            width: 400
        }
        );

        $('#trcnMarkForm').formchecker({
            scroll: 0,
            onValid: function () {
                $(':focus').blur();
            }
        });
    }
})();