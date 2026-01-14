(function () {
    $(document).ready(function () {
        $('#loginForm').formchecker();
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