(function () {
    $(document).ready(function () {
        $('#startSyncBtn').click(function () {

            startSync();
        });
        $('.ignore-btn').click(ignoreQuery);
    });

    function startSync() {
        var qry = $('.qr-item:not(.qr-ok)')[0];
        if (qry) {
            var statusObj = $(qry).find('.status');
            $('html, body').animate({
                scrollTop: $(qry).offset().top - 10
            });
            statusObj.html(` &nbsp;&nbsp;&nbsp; <span style="color:#f90;"> - Running</span>`);

            $.post(
                'ajax/',
                {
                    method: 'query-sync',
                    sql: $(qry).data().sql
                },
                function (data) {
                    if (data.status == 'ok') {
                        statusObj.html(` &nbsp;&nbsp;&nbsp; <span style="color:#0b0;"> - Query executed !</span>`);
                        $(qry).addClass('qr-ok');
                        setTimeout(startSync, 500);

                    } else {
                        statusObj.html(` &nbsp;&nbsp;&nbsp; <span style="color:#f00;"> - ` + (data.error || 'query running failed') + `</span>`);
                    }
                }
            );

        } else {
            alert('Sync completed');
        }
    }

    function ignoreQuery() {
        const
            qri = $(this).parents('.qr-item'),
            statusObj = qri.find('.status');

        qri.css('pointer-events', 'none');
        qri.fadeTo(300, .3);

        $.post(
            'ajax/',
            {
                method: 'query-ignore',
                sql: qri.data().sql
            },
            function (data) {
                if (data.status == 'ok') {
                    qri.slideUp();

                } else {
                    qri.css('pointer-events', '');
                    qri.fadeTo(200, 1);
                    statusObj.html(` &nbsp;&nbsp;&nbsp; <span style="color:#f00;"> - ` + (data.error || 'unable to ignore') + `</span>`);
                }
            }
        );
    }
})();