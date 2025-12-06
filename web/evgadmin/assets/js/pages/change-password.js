(function () {
    $(document).ready(function () {
        $('#passwdForm').formchecker();
    });
})();

function checkConfirmPass(o) {
    return o.value.trim() == _e('npass1').value.trim();
}