(function () {
    $(document).ready(function () {
        $("#collegiateSave").formchecker({
            onValid: function () {
                $(":focus").blur();
                popup.showSpinner();
            },
        });
    });
})();
