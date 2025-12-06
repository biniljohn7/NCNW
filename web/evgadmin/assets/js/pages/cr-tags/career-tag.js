(function () {
    $(document).ready(function () {
        $('#tagSave').formchecker({
            onValid: function () {
                $(':focus').blur();
                popup.showSpinner();
            }
        });
    });
})();