(function () {
    $(document).ready(function () {
        $('#benefitSave').formchecker({
            onValid: function () {
                $(':focus').blur();
                popup.showSpinner();
            }
        });
    });
})();