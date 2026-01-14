(function () {
    $(document).ready(function () {
        $('#passwdForm').formchecker();
        $('.show-pwd').click(pwdTypChnage);
    });

    function pwdTypChnage() {
        let pwdFld = $(this).closest('div').find("input");
        let type = pwdFld.attr("type") === "password" ? "text" : "password";
        let chk = type === "password";
        pwdFld.attr("type", type);
        $(this).text(chk ? 'visibility' : 'visibility_off');
    }
})();

function checkConfirmPass(o) {
    return o.value.trim() == _e('npass1').value.trim();
}