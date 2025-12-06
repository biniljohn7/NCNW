(function () {
  $(document).ready(function () {
    $("#membershipForm").formchecker({
      onValid: function () {
        $(":focus").blur();
        popup.showSpinner();
      },
    });
  });
})();
