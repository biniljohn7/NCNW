(function () {
    $(document).ready(function () {
        $('#pdtForm').formchecker();
        $('.pdt-type-rdo').change(pdtTypeChng);
    });
    function pdtTypeChng() {
        let type = $('.pdt-type-rdo:checked').val();
        if (type == 'fee') {
            _e('feeInfo').style.display = 'block';
            _e('feeAmt').setAttribute('data-type', 'number');
            _e('feeAmt').setAttribute('data-min', '0');
            _e('validity').setAttribute('data-type', 'string');
        } else {
            _e('feeInfo').style.display = 'none';
            _e('feeAmt').removeAttribute('data-type');
            _e('feeAmt').removeAttribute('data-min');
            _e('validity').removeAttribute('data-type');
        }
    }
})();