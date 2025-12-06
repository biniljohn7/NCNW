(function () {
    $(document).ready(function () {
        $('.category').formchecker({
            onValid: function () {
                $(':focus').blur();
                popup.showSpinner();
            }
        });
    });
})();