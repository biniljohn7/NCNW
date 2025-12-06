(function () {
    $(document).ready(function () {
        $('#nationSave').formchecker({
            onValid: function () {
                $(':focus').blur();
                popup.showSpinner();
            }
        });
    });
})();