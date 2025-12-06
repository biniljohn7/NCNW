(function () {
  $(document).ready(function () {
    $(".sectionDates").datepicker({
        minDate: 0,
        dateFormat: "dd-mm-yy",
    });
    $("#chapterSave").formchecker({
      onValid: function () {
        $(":focus").blur();
        popup.showSpinner();
      },
    });
    var regionSel = _e("regionSel"),
      stateSel = _e("stateSel");
    _e("nationSel").onchange = function () {
      let nation = this.value;
      if (nation) {
        $.ajax(domain + "ajax/anyadmin/", {
          method: "post",
          data: {
            method: "region-dep-dropdown",
            nation: nation,
          },
          error: function () {
            regionSel.innerHTML = "";
            regionSel.innerHTML = '<option value="">Choose Region</option>';
          },
          success: function (data) {
            if (data.status == "ok") {
              var rDatas = data.rDatas;
              regionSel.innerHTML = "";
              regionSel.innerHTML = '<option value="">Choose Region</option>';
              rDatas.forEach((region) => {
                regionSel.innerHTML +=
                  '<option value="' +
                  region.regId +
                  '">' +
                  region.regName +
                  "</option>";
              });
            } else {
              this.error();
            }
          },
        });
      } else {
        regionSel.innerHTML = '<option value="">Choose Region</option>';
      }
    };
    _e("regionSel").onchange = function () {
      let region = this.value;
      if (region) {
        $.ajax(domain + "ajax/anyadmin/", {
          method: "post",
          data: {
            method: "state-dep-dropdown",
            region: region,
          },
          error: function () {
            stateSel.innerHTML = "";
            stateSel.innerHTML = '<option value="">Choose State</option>';
          },
          success: function (data) {
            if (data.status == "ok") {
              console.log(data);
              var sDatas = data.sDatas;
              stateSel.innerHTML = "";
              stateSel.innerHTML = '<option value="">Choose State</option>';
              sDatas.forEach((state) => {
                stateSel.innerHTML +=
                  '<option value="' +
                  state.stId +
                  '">' +
                  state.stName +
                  "</option>";
              });
            } else {
              this.error();
            }
          },
        });
      } else {
        stateSel.innerHTML = '<option value="">Choose State</option>';
      }
    };
  });
})();
