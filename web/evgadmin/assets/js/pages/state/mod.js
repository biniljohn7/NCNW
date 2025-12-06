(function () {
    $(document).ready(function () {
        $('#stateSave').formchecker({
            onValid: function () {
                $(':focus').blur();
                popup.showSpinner();
            }
        });
        var regionSel = _e('regionSel');
        _e('nationSel').onchange = function () {
            let nation = this.value;
            $(':focus').blur();
            if (nation) {
                $.ajax(
                    domain + 'ajax/anyadmin/', {
                    method: 'post',
                    data: {
                        method: 'region-dep-dropdown',
                        nation: nation
                    },
                    error: function () {
                        regionSel.innerHTML = '';
                        regionSel.innerHTML = '<option value="">Choose Region</option>';
                    },
                    success: function (data) {
                        if (data.status == 'ok') {
                            var rDatas = data.rDatas;
                            regionSel.innerHTML = '';
                            regionSel.innerHTML = '<option value="">Choose Region</option>';
                            rDatas.forEach(region => {
                                regionSel.innerHTML += '<option value="' + region.regId + '">' + region.regName + '</option>';
                            });
                        } else {
                            this.error();
                        }
                    }
                }
                );
            } else {
                regionSel.innerHTML = '<option value="">Choose Region</option>';
            }
        };
    });
})();