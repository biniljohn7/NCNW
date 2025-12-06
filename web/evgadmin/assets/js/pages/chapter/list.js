(function () {
    $(document).ready(function () {
        var shRegion = _e('shRegion'),
            shState = _e('shState');
        _e('shNation').onchange = function () {
            let nation = this.value;
            if (nation) {
                $.ajax(
                    domain + 'ajax/anyadmin/', {
                    method: 'post',
                    data: {
                        method: 'region-dropdown',
                        nation: nation
                    },
                    error: function () {
                        shRegion.innerHTML = '';
                        shRegion.innerHTML = '<option value="">Any Region</option>';
                    },
                    success: function (data) {
                        if (data.status == 'ok') {
                            var rDatas = data.rDatas;
                            shRegion.innerHTML = '';
                            shRegion.innerHTML = '<option value="">Any Region</option>';
                            rDatas.forEach(region => {
                                shRegion.innerHTML += '<option value="' + region.regId + '">' + region.regName + '</option>';
                            });
                        } else {
                            this.error();
                        }
                    }
                }
                );
            } else {
                shRegion.innerHTML = '<option value="">Any Region</option>';
                shState.innerHTML = '<option value="">Any State</option>';
            }
        }
        _e('shRegion').onchange = function () {
            let region = this.value;
            if (region) {
                $.ajax(
                    domain + 'ajax/anyadmin/', {
                    method: 'post',
                    data: {
                        method: 'state-dropdown',
                        region: region
                    },
                    error: function () {
                        shState.innerHTML = '';
                        shState.innerHTML = '<option value="">Any State</option>';
                    },
                    success: function (data) {
                        if (data.status == 'ok') {
                            console.log(data);
                            var sDatas = data.sDatas;
                            shState.innerHTML = '';
                            shState.innerHTML = '<option value="">Any State</option>';
                            sDatas.forEach(state => {
                                shState.innerHTML += '<option value="' + state.stId + '">' + state.stName + '</option>';
                            });
                        } else {
                            this.error();
                        }
                    }
                }
                );
            } else {
                shState.innerHTML = '<option value="">Any State</option>';
            }
        }
    });
})();