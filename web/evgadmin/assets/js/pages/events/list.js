(function () {
    $(document).ready(function () {
        _e('searchForm').onsubmit = function () {
            popup.showSpinner();
        };
        _e('searchScope').onchange = function () {
            popup.showSpinner();
            _e('searchForm').submit();
        }
    });
})();