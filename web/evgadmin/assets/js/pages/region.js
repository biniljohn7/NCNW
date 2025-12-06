(function () {
    $(document).ready(function () {
        $('#regionSave').formchecker({
            onValid: function () {
                $(':focus').blur();
                popup.showSpinner();
            }
        });
    });
})();