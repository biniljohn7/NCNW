(function () {
	let stickySide;

	$(document).ready(function () {
		stickySide = $("#stickySide");

		$(window).resize(onWinResize);
		$(".dz-btn").click(deleteUser);

		_e("membStatusChk").onchange = setMembStatus;
		_e("pinStatus").onclick = setPinbStatus;
        _e('resetPwd').onclick = setNewPassword;

		onWinResize();
	});

    function setNewPassword() {
        popup.show(
            'Reset Password',
            `<form action="` + domain + `actions/anyadmin/" method="post" id="savePwd">
                <input type="hidden" name="method" value="reset-member-password" />
                <input type="hidden" name="id" value="`+ pgData.memberId + `" />
                <div class="fm-field">
                    <div class="fld-label mb10">
                        Current Password
                    </div>
                    <div class="fld-inp">
                        <input type="password" size="30" name="resetpass" data-type="string" data-label="password">
                    </div>
                </div>
                <div class="fm-field">
                    <div class="fld-label">
                    </div>
                    <div class="fld-inp">
                        <input type="submit" class="pix-btn site bold-500" value="Submit">
                    </div>
                </div>
            </form>`,
            {
                width: 400,
                id: 'resetPwdForm'
            }
        );

        $('#savePwd').formchecker({
            scroll: 0,
            onValid: function (e) {
                $(':focus').blur();
                popup.showSpinner({ id: 'spnMegGrp' });
            }
        });
    }

	function setMembStatus() {
		popup.showSpinner();
		$.ajax(domain + "ajax/anyadmin/", {
			method: "post",
			data: {
				method: "member-status",
				membId: pgData.memberId,
				newSts: this.checked ? "Y" : "N",
			},
			error: function () {
				popup.hide();
				popup.showError("Unable to perform this action. Please try again.");
			},
			success: function (data) {
				if (data.status == "ok") {
					popup.hide();
					popup.showSuccess("Member status changed");
				} else {
					this.error();
				}
			},
		});
	}

	function onWinResize() {
		stickySide[0].style.top =
			Math.min(60, $(window).height() - stickySide.height() - 20) + "px";
	}
	function deleteUser() {
		popup.show(
			"Confirm Delete",
			`Please confirm if you want to permanently remove this member and all associated data. This action is irreversible. Type <strong>YES</strong> if you are certain.
            <form class="delete-usr-form" method="post" action="${domain}actions/anyadmin/">
                <input type="hidden" name="method" value="member-delete" />
                <input type="hidden" name="id" value="${pgData.memberId}" />
                <input type="text" name="cfm" placeholder="Type yes to delete" />
                <button type="submit" class="pix-btn site">
                    Confirm
                </button>
            </form>`,
			{
				width: 400,
			}
		);
	}

	function setPinbStatus() {
		popup.show(
			"Mark as Shipped",
			`Please seclect the date of shipment.<br><br>
			<input type="text" name="shpdDate" id="shpdDate"><br>
			<small class="mkshp"></small><br>
            <span class="pix-btn site" id="markShipped" placeholder="Click to select date">
                Mark as Shipped
            </span>`,
			{
				width: 450
			}
		);
		$('#shpdDate').datetimepicker({
			format: 'd-M-Y H:i',
			formatTime: 'h:i A',
			maxDate: 0,
			step: 30,
			value: new Date()
		});
		_e("markShipped").onclick = markShippedFun;

	}
	//
	function markShippedFun(e) {
		e.preventDefault();
		let spdDate = $("#shpdDate").val().trim();
		let pinDisb = $('#pinDisb');
		let isValidDate = /^\d{2}-(Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec)-\d{4}\s([01]\d|2[0-3]):[0-5]\d$/.test(spdDate);
		if (isValidDate) {
			popup.hide();
			$(".mkshp").html("");
			popup.showSpinner();

			$.ajax(domain + "ajax/anyadmin/", {
				method: "post",
				data: {
					method: "set-pin-status",
					shpdDate: spdDate,
					membId: pgData.memberId,
				},
				error: function (data) {
					popup.hide();
					popup.showError(data.msg || "Unable to perform this action. Please try again.");
				},
				success: function (data) {
					if (data.status == "ok") {
						popup.hide();
						popup.showSuccess(data.msg || "Pin status changed");

						pinDisb.addClass('atr-green');
						pinDisb.text('Pin Distribution on : ' + data.shpdOn);
						$(".pin-status-drop").css('display', 'none');
					} else {
						this.error(data);
					}
				},
			});
		} else {
			$(".mkshp").html("Select a date");
			return;
		}
	}
})();
