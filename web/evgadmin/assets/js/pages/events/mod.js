(function () {

    $(document).ready(function () {
        $('#evDate, #evEndDate').datepicker({
            minDate: 0,
            dateFormat: 'd / MM / yy'
        });
        $('#eventForm').formchecker();

        bindAreaSelector(
            _e('nationSel'),
            _e('regionSel'),
            _e('regionLdr'),
            'region-dropdown',
            'nation',
            'rDatas',
            'regId',
            'regName',
            'All Regions',
            [
                _e('stateSel'),
                _e('chapterSel')
            ]
        );

        bindAreaSelector(
            _e('regionSel'),
            _e('stateSel'),
            _e('stateLdr'),
            'state-dropdown',
            'region',
            'sDatas',
            'stId',
            'stName',
            'All States',
            [_e('chapterSel')]
        );

        bindAreaSelector(
            _e('stateSel'),
            _e('chapterSel'),
            _e('chapterLdr'),
            'chapter-dropdown',
            'id',
            'list',
            'id',
            'name',
            'All Sections',
            []
        );
    });

    function bindAreaSelector(
        selector,
        targetOps,
        targetLoader,
        method,
        findKey,
        listKey,
        idKey,
        nameKey,
        allLabel,
        gdChilds
    ) {
        function resetChilds(objs) {
            let o;
            for (o of objs) {
                let label = '';
                if (o.id.indexOf('state') >= 0) {
                    label = 'States';
                } else if (o.id.indexOf('chapter') >= 0) {
                    label = 'Sections';
                }
                o.innerHTML = `<option value="">All ${label}</option>`;
            }
        }
        selector.onchange = function () {
            if (this.value) {
                targetOps.hide();
                targetLoader.show();

                let pData = { method: method };
                pData[findKey] = this.value;

                $.ajax(
                    domain + 'ajax/anyadmin/',
                    {
                        method: 'post',
                        data: pData,
                        success: function (data) {
                            if (data.status == 'ok') {
                                targetOps.show('inline-block');
                                targetLoader.hide();

                                let
                                    ops = `<option value="">${allLabel}</option>`,
                                    row;

                                for (row of data[listKey]) {
                                    ops += `<option value="${row[idKey]}">${row[nameKey]}</option>`;
                                    targetOps.innerHTML = ops;
                                }

                                resetChilds(gdChilds);
                            }
                        }
                    }
                );
            } else {
                resetChilds([...gdChilds, targetOps]);
                // targetOps.innerHTML = `<option value="">${allLabel}</option>`;
            }
        };
    }
})();
