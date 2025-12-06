(function () {
    $(document).ready(function () {
        $('.dlt-btn').on('click', function () {
            return confirm('Are you sure?');
        });
        $('#quesSave').formchecker({
            onValid: function () {
                $(':focus').blur();
                popup.showSpinner();
            }
        });
    });
})();