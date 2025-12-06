(function () {
    $(document).ready(function () {
        $('.dt-rng-input').datepicker({
            minDate: 0,
            dateFormat: 'D, dd M yy',
            onSelect: function () {
                const
                    isStart = (/rng-start/).test(this.className);
                $(this.parentNode.parentNode).find(
                    '.rng-' + (isStart ? 'end' : 'start')
                ).datepicker(
                    'option',
                    isStart ? 'minDate' : 'maxDate',
                    new Date(this.value)
                );
            }
        });
    });
})();