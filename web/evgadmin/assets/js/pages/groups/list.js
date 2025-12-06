(function () {
    $(document).ready(function () {
        $('#newGroupAddBtn').click(function (e) {
            popup.show(
                'Create New Group',
                `<form method="post" id="msgGrpForm" action="` + domain + `actions/anyadmin/">
                    <input type="hidden" name="method" value="message-group-save" />
                    <input type="text" name="groupName" value="" class="group-name-input" id="groupName" placeholder="New Group Name" data-type="string">
                    <br><br>
                    <button type="submit" class="pix-btn site mr5" id="createBtn">
                        Create
                    </button>
                </form>`,
                {
                    width: 400
                }
            );

            $('#msgGrpForm').formchecker(
                {
                    scroll: 0,
                    onValid: function (e) {
                        $(':focus').blur();
                        popup.showSpinner({ id: 'spnMegGrp' });
                    }
                }
            );
        });
    });
})();