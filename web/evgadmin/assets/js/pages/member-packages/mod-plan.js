(function () {
    $(document).ready(function () {
        $('#planForm').formchecker();

        $('.amt-input').on(
            'keyup blur change',
            updateAmount
        );

        updateAmount();
    });

    function updateAmount() {
        let
            totals = {
                nat: 0,
                loc: 0
            };

        function collectsNums(selector, key) {
            $(selector).each((n, obj) => {
                let numVal = Number(obj.value.trim());
                isNaN(numVal) ? numVal = 0 : 0;
                totals[key] += numVal;
            });
        }

        collectsNums('.nat-amt', 'nat');
        collectsNums('.loc-amt', 'loc');

        _e('ttlLocAmt').innerText = toDollar(totals.loc);
        _e('ttlNatAmt').innerText = toDollar(totals.nat);
        _e('ttlPlanAmt').innerText = toDollar(totals.loc + totals.nat);
    }
})();