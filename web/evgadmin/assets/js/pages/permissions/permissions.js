(function () {
    $(document).ready(function () {
        $('#permForm').formchecker({
            onValid: function () {
                $(":focus").blur();
                popup.showSpinner();
            },
        });
        $('.perm-inp-chk').change(function () {
            let
                chks = $('.perm-inp-chk:checked'),
                permsList = [],
                i;

            for(i = 0; i < chks.length; i++) {
                permsList.push(chks[i].value);
            }

            if(permsList.includes('members')) {
                $('.members-mod').addClass('active');
            } else {
                _e('membersModAccessChk').checked = false;
                $('.members-mod').removeClass('active');
            }
        });

        $('#type').change(function () {
            let type = $(this).val();
            window.location.href = domain + "?page=permissions" + (type ? "&type=" + type : "");
        });
    });
})();