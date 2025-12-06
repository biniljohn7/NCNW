(function () {
    $(document).ready(function () {
        $('#adminForm').formchecker();

        $('#changePass').change(function () {
            let inp = _e('passInp');
            inp.disabled = !this.checked;
            _e('passwdBox')[this.checked ? 'show' : 'hide']();
            if (this.checked) {
                inp.focus();
            }
        });
    });
})();