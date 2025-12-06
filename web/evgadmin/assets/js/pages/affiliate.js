(function () {
  $(document).ready(function () {
    $("#affiliateSave").formchecker({
      onValid: function () {
        $(":focus").blur();
        popup.showSpinner();
      },
    });
  });
})();
