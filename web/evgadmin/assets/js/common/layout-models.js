(function () {
    $(document).ready(function () {
        // details page
        // $('#mdlDetailsImgInput').change(function () {
        //     let inp = this;
        //     if ((/\.(jpe*g|png|gif)$/i).test(inp.value)) {
        //         $(':focus').blur();
        //         popup.showSpinner();

        //         let
        //             upData = {
        //                 image: inp.files[0]
        //             },
        //             upArgs = window.mdlDetailsImgUpload,
        //             ix;

        //         if (upArgs) {
        //             upData.method = upArgs.handle;
        //             if (
        //                 upArgs.params &&
        //                 typeof upArgs.params == 'object'
        //             ) {
        //                 for (ix in upArgs.params) {
        //                     upData[ix] = upArgs.params[ix];
        //                 }
        //             }
        //         }

        //         pix.startFileUpload(
        //             domain + 'ajax/' + (upArgs.role || 'admin') + '/',
        //             {
        //                 data: upData,
        //                 error: function () {
        //                     popup.hide();
        //                     popup.showError('Oops. Image uploading failed. Please try again');
        //                     inp.value = '';
        //                 },
        //                 success: function (e, data) {
        //                     if (
        //                         data &&
        //                         data.status == 'ok'
        //                     ) {
        //                         popup.hide();
        //                         pix.openNotification('Photo changed', 1);
        //                         inp.value = '';

        //                         let
        //                             pn = inp.parentNode.parentNode,
        //                             imgTag = pn.getElementsByTagName('img')[0];

        //                         $(pn).find('.img-empty').remove();
        //                         if (!imgTag) {
        //                             imgTag = document.createElement('img');
        //                             pn.prepend(imgTag)
        //                         }
        //                         imgTag.setAttribute('src', data.thumb);

        //                     } else {
        //                         this.error();
        //                     }
        //                 }
        //             }
        //         );

        //     } else {
        //         inp.value = '';
        //         pix.openNotification('Invalid file format');
        //     }
        // });
        // if ($(window).width() <= 767) {
        //     const
        //         menuBox = $('#mdlDetailsPageLeftMenu'),
        //         actMenuBtn = $('.dl-mi-btn.active');

        //     if (actMenuBtn[0]) {
        //         menuBox.scrollLeft(
        //             actMenuBtn.offset().left +
        //             menuBox.scrollLeft() - 30
        //         );
        //     }
        // }
        // deltails page end here

        // sidebar filter
        $('#listFilterButton').click(function () {
            $('#sidebarFilterModel').addClass('active');
        });
        $('#cancelSidebarFilter').click(function () {
            $('#sidebarFilterModel').removeClass('active');
        });

        // date range filter
        $('.dt-rng-input').datepicker({
            maxDate: 0,
            dateFormat: 'd / M / yy',
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

        // read full text
        $('.read-full-text-btn').click(function () {
            const pn = $(this.parentNode.parentNode);
            pn.find('.rdfull-short').hide();
            pn.find('.rdfull-full').show();
        });

        // stick button menu
        $('.pix-sticky-btn .btn-menu-item').click(function () {
            const pn = this.parentNode.parentNode;
            pn.style.pointerEvents = 'none';
            setTimeout(
                function () {
                    pn.style.pointerEvents = '';
                },
                200
            );
        });
    });
})();