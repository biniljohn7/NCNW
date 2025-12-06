(function () {
    $(document).ready(function () {
        $('#careerSave').formchecker({
            onValid: function () {
                $(':focus').blur();
                popup.showSpinner();
            }
        });
    });
})();