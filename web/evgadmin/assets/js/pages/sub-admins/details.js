(function () {
    $(document).ready(function () {
        let xhr;

        $('.perm-inp-chk').change(function () {

            let
                chks = $('.perm-inp-chk:checked'),
                permsList = [],
                role = $('.hd-role-selector input[name=hd-role]:checked').val(),
                i;
            for (i = 0; i < chks.length; i++) {
                permsList.push(chks[i].value);
            }
            if (permsList.includes("helpdesk")) {
                $('.hd-role-selector').addClass('active');
            } else {
                $('.hd-role-selector').removeClass('active');
                $('.hd-role-selector input[name=hd-role]').prop('checked', false);
                role = null;
            }
            if (permsList.includes("members")) {
                $('.members-mod').addClass('active');
            } else {
                _e('membersModAccessChk').checked = false;
                $('.members-mod').removeClass('active');
            }
            if (role) {
                permsList.push(role);
            }
            ajax_req(permsList);
        });
        $('.hd-role-selector input[name=hd-role]').change(function () {
            let
                chks = $('.perm-inp-chk:checked'),
                permsList = [],
                role = this.value,
                i;

            for (i = 0; i < chks.length; i++) {
                permsList.push(chks[i].value);
            }
            permsList.push(role);
            ajax_req(permsList);
        });
        //Ajax Request
        function ajax_req(permsList) {
            if (xhr) {
                xhr.abort();
            }
            xhr = $.ajax(
                domain + 'ajax/admin/',
                {
                    method: 'post',
                    data: {
                        method: 'sub-admin-perms-save',
                        perms: permsList,
                        admin: pgData.adminId
                    },
                    error: function (e) {
                        if (e && e.statusText != 'abort') {
                            pix.openNotification('Unable to save permissions !');
                        }
                    },
                    success: function (data) {
                        if (data.status == 'ok') {
                            pix.openNotification('Permissions saved !', 1);
                        } else {
                            this.error();
                        }
                    }
                }
            );
        }
    });
})();