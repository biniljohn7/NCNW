(function () {
    $(document).ready(function () {
        function MonthlyGraph(
            id, title, xhr
        ) {
            const
                monthNames = [
                    "January",
                    "February",
                    "March",
                    "April",
                    "May",
                    "June",
                    "July",
                    "August",
                    "September",
                    "October",
                    "November",
                    "December"
                ];
            let
                gpBox = _e(id),
                calDate = new Date(),
                cvs,
                ctx,
                bodyWidth,
                bodyHeight,
                spinner,
                errorView,
                dataXhr;

            gpBox.className = 'grp-box';
            gpBox.innerHTML = `<div class="gp-head">
                    <div class="gp-name">
                        ${title}
                    </div>
                    <div class="gp-month">
                        <div class="gpm-btn left">
                            <span class="material-symbols-outlined">
                                chevron_left
                            </span>
                        </div>
                        <div class="gpm-name">
                            November 2023
                        </div>
                        <div class="gpm-btn right">
                            <span class="material-symbols-outlined">
                                chevron_right
                            </span>
                        </div>
                    </div>
                </div>
                <div class="gp-body">
                    <canvas></canvas>
                    <div class="gp-loading">
                        <div class="pix-spinner"></div>
                    </div>
                    <div class="gp-error">
                        <div class="er-box">
                            <span class="material-symbols-outlined">
                                report_problem
                            </span>
                            <div class="er-txt">
                                Sorry. Unable to load data.
                            </div>
                            <div class="er-action">
                                <span class="pix-btn sm render-retry-btn">
                                    try again
                                </span>
                            </div>
                        </div>
                    </div>
                </div>`;

            spinner = gpBox.getClass('gp-loading');
            errorView = gpBox.getClass('gp-error');
            cvs = gpBox.getTag('canvas');
            ctx = cvs.getContext("2d");

            gpBox.getClass('render-retry-btn').onclick = function () {
                renderGraph();
            };

            bodyWidth = $(cvs.parentNode).width();
            bodyHeight = bodyWidth * .65;

            cvs.width = bodyWidth;
            cvs.height = bodyHeight;

            function renderGraph() {
                gpBox.getClass('gpm-name').innerText = monthNames[
                    calDate.getMonth()
                ] + ' ' + calDate.getFullYear();

                spinner.show('flex');
                errorView.hide();

                if (dataXhr) {
                    dataXhr.abort();
                }

                xhr.data.month = calDate.getFullYear() +
                    '-' +
                    (calDate.getMonth() + 1);

                dataXhr = $.ajax(
                    domain + 'ajax/' + xhr.action, {
                    method: 'get',
                    data: xhr.data,
                    error: function () {
                        spinner.hide();
                        errorView.show('flex');
                    },
                    success: function (data) {
                        if (data.status == 'ok') {
                            spinner.hide();

                            let
                                isBig = bodyWidth >= 450,
                                leftMargin = bodyWidth / 17.17,
                                btmMargin = 30,
                                vertDivs = 8,
                                vertHigh = 0,
                                tmpDate,
                                numDays,
                                dayDiv,
                                leftPos,
                                topPos,
                                vertDiff,
                                vertDiffNum,
                                dotRadius = isBig ? 4 : 3,
                                i,
                                y;

                            // collecting highest value
                            for (i in data.data) {
                                if (data.data[i] > vertHigh) {
                                    vertHigh = 1.1 * data.data[i];
                                }
                            }
                            vertHigh = Math.ceil(
                                vertHigh / vertDivs
                            ) * vertDivs;

                            tmpDate = new Date(calDate);
                            tmpDate.setMonth(tmpDate.getMonth() + 1);
                            tmpDate.setDate(0);
                            numDays = tmpDate.getDate();

                            ctx.clearRect(0, 0, bodyWidth, bodyHeight);
                            ctx.strokeStyle = "#000";
                            ctx.lineWidth = 1;

                            // graph scale base
                            ctx.beginPath();
                            ctx.moveTo(leftMargin, 0);
                            ctx.lineTo(leftMargin, bodyHeight - btmMargin);
                            ctx.lineTo(bodyWidth - 10, bodyHeight - btmMargin);
                            ctx.stroke();

                            // vertical scales
                            dayDiv = (bodyWidth - leftMargin - 10) / (numDays - 1);

                            leftPos = leftMargin + dayDiv;

                            ctx.strokeStyle = "#b184cc";
                            ctx.beginPath();

                            for (i = 1; i <= numDays; i++) {
                                ctx.moveTo(leftPos, 0);
                                ctx.lineTo(leftPos, bodyHeight - btmMargin);
                                leftPos += dayDiv;
                            }
                            ctx.stroke();

                            // scale days
                            ctx.font = Math.round(bodyWidth / 60) + 'px Arial';
                            ctx.fillStyle = '#000';

                            y = bodyHeight - btmMargin + Math.round(bodyWidth / 35.8);
                            leftPos = leftMargin - 5;
                            for (i = 1; i <= numDays; i++) {
                                ctx.fillText(i, leftPos, y);
                                leftPos += dayDiv;
                            }

                            // horizontal scales
                            topPos = 0;
                            vertDiff = (bodyHeight - btmMargin) / vertDivs;

                            ctx.strokeStyle = "#aa84cc";
                            ctx.beginPath();

                            for (i = 0; i < vertDivs; i++) {
                                ctx.moveTo(leftMargin, topPos);
                                ctx.lineTo(bodyWidth - 10, topPos);

                                vertDiffNum = vertHigh - (i * (vertHigh / vertDivs));
                                if (vertDiffNum >= 1000) {
                                    vertDiffNum = (Math.round(vertDiffNum / 100) / 10).toFixed(1) + 'K';
                                }

                                ctx.fillText(vertDiffNum, 0, topPos + 12);

                                topPos += vertDiff;
                            }
                            ctx.stroke();

                            // drawing graph
                            topPos = 0;
                            leftPos = leftMargin;

                            ctx.strokeStyle = "#9969c4";
                            ctx.lineWidth = bodyWidth > 450 ? 2 : 1;
                            ctx.beginPath();

                            for (i in data.data) {
                                topPos =
                                    (bodyHeight - btmMargin) -
                                    (
                                        (bodyHeight - btmMargin) *
                                        (data.data[i] / vertHigh)
                                    );

                                if (i == 1) {
                                    ctx.moveTo(leftMargin, topPos);
                                } else {
                                    ctx.lineTo(leftPos, topPos);
                                }

                                leftPos += dayDiv;
                            }
                            ctx.stroke();

                            ctx.fillStyle = '#8457bd';
                            ctx.strokeStyle = '#6c3b98';
                            ctx.lineWidth = isBig ? 2 : 1;

                            leftPos = leftMargin;

                            for (i in data.data) {
                                topPos =
                                    (bodyHeight - btmMargin) -
                                    (
                                        (bodyHeight - btmMargin) *
                                        (data.data[i] / vertHigh)
                                    );

                                // if (i == 1) {
                                // ctx.moveTo(leftMargin, topPos);
                                // } else {
                                //     ctx.lineTo(leftPos, topPos);
                                // }

                                ctx.beginPath();
                                ctx.arc(leftPos, topPos, dotRadius, 0, 2 * Math.PI);
                                ctx.fill();
                                ctx.stroke();

                                leftPos += dayDiv;
                            }

                        } else {
                            this.error();
                        }
                    }
                }
                );
            }
            renderGraph();

            $(gpBox).find('.gpm-btn').click(function () {
                const isNext = (/right/).test(this.className);
                calDate.setMonth(
                    calDate.getMonth() + (
                        isNext ?
                            1 :
                            -1
                    )
                );
                renderGraph();
            });
        }

        new MonthlyGraph(
            'gpRevenue',
            `<div class="graph-hed">
                Revenue
            </div>`, {
            action: 'anyadmin/',
            data: {
                method: 'dash-graph-data',
                type: 'monthly-revenue'
            }
        }
        );

        new MonthlyGraph(
            'gpMembers',
            `<div class="graph-hed">
                Number of Members
            </div>`, {
            action: 'anyadmin/',
            data: {
                method: 'dash-graph-data',
                type: 'monthly-member-nums'
            }
        }
        );
    });
})();