// common
function _e(id) { var el = document.getElementById(id); el = el == undefined ? document.createElement("span") : el; return el; };
function _cl(id, classid) { return classid == undefined ? document.getElementsByClassName(id) : document.getElementById(id).getElementsByClassName(classid); };
function isMail(id) { return (/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i).test(id); };
function getnum(num) { num = Number(num.toString().trim()); return !isNaN(num) ? num : 0; };
function formatNum(num) { num = Number(num).toFixed(2); num = num.toString().replace(/\.00$/g, ""); return num; }
function money(nStr) { nStr = formatNum(getnum(nStr)); nStr += ''; var x = nStr.split('.'), x1 = x[0], x2 = x.length > 1 ? '.' + x[1] : '', rgx = /(\d+)(\d{3})/; while (rgx.test(x1)) { x1 = x1.replace(rgx, '$1' + ',' + '$2'); } return x1 + x2; }
Element.prototype.addClass = function (name) { if (!new RegExp(name, "gi").test(this.className)) { this.className = this.className + " " + name; } return this; };
Element.prototype.removeClass = function (name) { this.className = this.className.replace(new RegExp(name, "gi"), '').replace(/ {2,}/g, ' ').replace(/^ | $/gi, ''); return this; };
Element.prototype.switchClass = function (cl, add) { add = add == undefined ? true : add; this.className = (add ? this.className + (!new RegExp(cl).test(this.className) ? " " + cl : "") : this.className.replace(new RegExp("(\ *)" + cl, "g"), "")).trim(); return this; };
Element.prototype.toggleClass = function (c) { var r = new RegExp(c, 'i'); if (r.test(this.className)) { this.className = this.className.replace(r, '') } else { this.className += ' ' + c; } };
Element.prototype.getClass = function (cl) { var el = this.getElementsByClassName(cl); return el[0] != undefined ? el[0] : document.createElement("span"); };
Element.prototype.getTag = function (cl) { var el = this.getElementsByTagName(cl); return el[0] != undefined ? el[0] : document.createElement("span"); };
Element.prototype.parent = function (className) { var obj = this; found = false; while (obj != null && !found) { obj = obj.parentNode; found = obj != null && new RegExp(className).test(obj.className); } return obj != null ? obj : document.createElement("span"); };
function makeString(t, n, e) { var o = "", r = ""; for (n = void 0 == n ? 10 : n, t = void 0 == t ? "uln" : t, o += void 0 != e ? e : "", o += /u/.test(t) ? "ABCDEFGHIJKLMNOPQRSTUVWXYZ" : "", o += /l/.test(t) ? "abcdefgijklmnopqrstuvwxyz" : "", o += /n/.test(t) ? "0123456789" : "", o += /s/.test(t) ? "`~!@#$%^&*()_+-={}|][:;\"'<>,./?" : ""; r.length < n;)r += o.charAt(Math.floor(Math.random() * o.length)); return r };
Element.prototype.d = function (name, val) { return val ? this.setAttribute("data-" + name, val) : this.getAttribute("data-" + name); };
String.prototype.htmlEncode = function (a) { return this.replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/'/g, '&#39;').replace(/</g, '&lt;').replace(/>/g, '&gt;') };
String.prototype.htmlDecode = function () { return this.replace(/\&amp\;/g, '&').replace(/\&quot\;/g, '"').replace(/\&\#39\;/g, '\'').replace(/\&lt\;/g, '<').replace(/\&gt\;/g, '>') };
String.prototype.enQuote = function (a) { return this.replace(/"/g, '&quot;').replace(/'/g, '&#39;') };
String.prototype.deQuote = function (a) { return this.replace(/\&quot\;/g, '"').replace(/\&\#39\;/g, '\'') };
function str2url(name) { return name.trim().toLowerCase().replace(/[^0-9a-zA-Z]/g, '-').replace(/--+/g, '-').replace(/-$|^-/g, '') }
Element.prototype.show = function (prop) { this.style.display = prop || 'block'; return this }
Element.prototype.hide = function (prop) { this.style.display = 'none'; return this }
Element.prototype.enable = function () { this.disabled = false; return this }
Element.prototype.disable = function () { this.disabled = true; return this }
function _c(name) { return document.getElementsByClassName(name); }
HTMLCollection.prototype.enable = function () { var i, length = this.length; for (i = 0; i < length; i++) { this[i].disabled = false; } return this; }
HTMLCollection.prototype.disable = function () { var i, length = this.length; for (i = 0; i < length; i++) { this[i].disabled = true; } return this; }
HTMLCollection.prototype.hide = function () { var i, length = this.length; for (i = 0; i < length; i++) { this[i].style.display = 'none'; } return this; }
HTMLCollection.prototype.show = function (prop) { var i, length = this.length; for (i = 0; i < length; i++) { this[i].style.display = prop || 'block'; } return this; }
$.fn.getFormData = function () { var rData = {}, data; if (this[0]) { data = $('#settingsView').serializeArray(); for (i in data) { rData[data[i].name] = data[i].value; } } return rData; }
function jsonDecode(str, dfl) { var jsnData; try { jsnData = JSON.parse(str); } catch (e) { } return jsnData || dfl || null; }
function swapDisplay(a, b) { _e(a).hide(); _e(b).show(); }
function queryString2Obj(str) { var r = {}; if (str) { str = str.split('&'); for (sv of str) { sve = sv.split('='); sve[0] = decodeURIComponent(sve[0]); sve[1] = decodeURIComponent(sve[1] || '').replace(/\+/g, ' '); if ((/\[\]$/).test(sve[0])) { sve[0] = sve[0].replace(/\[\]$/i, ''); if (!r[sve[0]]) { r[sve[0]] = []; } r[sve[0]].push(sve[1]); } else { r[sve[0]] = sve[1]; } } } return r; }

// global functions
function inr(num, nZero) { var r = '0.00', appNum; num = Number(num); if (!isNaN(num)) { num = num.toFixed(2); if (num.length > 6) { num = num.split('').reverse().join(''); appNum = num.slice(0, 6); for (i = 6; i < num.length; i++) { appNum += (i % 2 == 0 ? ',' : '') + num[i]; } r = appNum.split('').reverse().join(''); } else { r = num; } } nZero === undefined ? nZero = 1 : 0; nZero ? r = r.replace(/\.00$/, '') : 0; return r; }

$.fn.preloader_show = function (attrs) {
	var obj = $(this);
	attrs = attrs == undefined ? {} : attrs;
	attrs.theme = attrs.theme != undefined ? "dark" : "light";
	attrs.z = attrs.z == undefined ? 1500 : attrs.z;
	obj.css({ position: "relative" });
	obj.find(".hover-preloader").remove();
	obj.append('<div class="hover-preloader ' + attrs.theme + '" style="z-index:' + attrs.z + ';">\
		<div class="item-container">\
			<span class="image">\
				<div class="pix-spinner"></div>\
			</span>\
			<span class="label">'+ (attrs.text || 'please wait..') + '</span>\
		</div>\
	</div>');
};
$.fn.preloader_remove = function () {
	var obj = $(this);
	obj.find(".hover-preloader").remove();
}
// JavaScript Document
var popup = {
	show: function (title, content, ops) {
		ops = ops == undefined ? {} : ops;
		var infoMode = ops.mode == "info";
		title = title != undefined ? title : "";
		content = content != undefined ? content : "";
		ops.closebtn = ops.closebtn != undefined ? ops.closebtn : true;
		if (infoMode) {
			title = '<div style="text-align:center;">' + title + '</div>';
			content = '<div style="text-align:center;">' + content + '</div>';
			ops.ok_btn = ops.ok_btn != undefined ? ops.ok_btn : true;
			ops.width = ops.width == undefined ? 500 : ops.width;
		}
		var popObj = document.createElement("div");
		if (ops.id != undefined) popObj.id = ops.id;
		popObj.className = 'popup-frame' + (infoMode ? ' info-mode' : '');
		popObj.innerHTML = `<div class="popbox" style="` +
			(ops.width ? 'width:' + ops.width + 'px; ' : '') +
			(ops.height ? 'height:' + ops.height + 'px; ' : '') +
			`">` +
			(ops.closebtn ? '<span class="popup-close popup-close-x"></span>' : '') + `
			<div class="popup-heading `+ (ops.align ? ' text-' + ops.align : '') + `">` + title + `</div>
			<div class="popup-content">
				<div class="limit-content">
					<div class="`+ (ops.align ? ' text-' + ops.align : '') + `">` +
			content +
			(
				ops.ok_btn == true ? `<div class="text-center" style="padding-top:15px;">
								<a href="#" class="ok_btn pix-btn site" style="min-width:100px;">OK</a>
							</div>`:
					``
			) +
			`</div>
				</div>						
			</div>
		</div>`;
		if (ops.closebtn) {
			$(popObj).find(".popup-close").click(function (e) {
				popup.hide($(this).parents(".popup-frame"));
				return false;
			});
		}
		if (ops.ok_btn == true) {
			$(popObj).find(".ok_btn").click(function (e) {
				popup.hide($(this).parents(".popup-frame"));
				return false;
			});
		}
		if (infoMode && !window.infoModeKeyEvtBound) {
			window.infoModeKeyEvtBound = 1;
			document.addEventListener('keypress', popup.onInfoModeCloseEvtTrack);
		}
		if (!popup.popupLastIx) {
			popup.popupLastIx = 7499;
		}
		popup.popupLastIx++;
		popObj.style.zIndex = popup.popupLastIx;
		$("body").prepend(popObj);
		if (ops.callback != undefined) {
			ops.callback();
		}
		$("html").css({ overflow: "hidden" });
	},
	hide: function (obj, ops) {
		ops = ops == undefined ? {} : ops;
		if (obj && obj.indexOf) {
			obj = $('#' + obj);
		}
		obj = obj == undefined ? $(".popup-frame") : obj;
		obj[0] ? $(window).unbind("resize", obj[0].resizeEvent) : 0;
		obj.remove();
		ops.callback ? ops.callback() : 0;
		if ($(".popup-frame").length == 0) {
			$("html").css({ overflow: "" });
		}
	},
	close: function (e) {
		let ppObj = null;
		if (typeof e == 'string') {
			ppObj = $('#' + e);
		}
		if (!ppObj) {
			ppObj = $(this).parents(".popup-frame");
		}
		popup.hide(ppObj);
	},
	showPreloader: function (msg, width, id) {
		this.show('', '<div style="text-align:center;padding-right:22px;">' +
			'<img src="' + domain + 'assets/images/w8.gif" style="vertical-align:-3px;margin-right:10px" />' +
			(msg || 'Loading. Please wait..') +
			'</div>', {
			width: width || 400, closebtn: 0, id: id
		});
	},
	onInfoModeCloseEvtTrack: function (e) {
		if (e.keyCode == 13) {
			var lastInfo = $('.popup-frame.info-mode');
			if (lastInfo[0]) {
				popup.hide(lastInfo[0]);
			}
			if (lastInfo.length < 2) {
				delete window.infoModeKeyEvtBound;
				document.removeEventListener('keypress', popup.onInfoModeCloseEvtTrack);
			}
		}
	},
	showSuccess: function (msg, props) {
		props = props || {};
		popup.showOutput({
			icon: 'check_circle_outline',
			iconColor: '#5cab02',
			title: 'Done !',
			body: msg,
			width: props.width || 400,
			button: 'success lg'
		});
	},
	showError: function (msg, props) {
		props = props || {};
		popup.showOutput({
			icon: 'highlight_off',
			iconColor: '#a00',
			title: 'Oops !',
			body: msg || 'Sorry. We are unable to complete your request. Please try again.',
			width: props.width || 400,
			button: 'danger lg'
		});
	},
	showOutput: function (props) {
		props = props || {};

		var ppStr = `<div class="ppo-icon" style="color:` + (props.iconColor) + `">
			<span class="material-icons material-icons-outlined">`+ (props.icon || 'check_circle') + `</span>
		</div>
		<div class="ppo-title">`+ (props.title || 'Okay !') + `</div>
		<div class="ppo-body">`+ (props.body || 'Everything is ok.') + `</div>
		<div class="ppo-button">
			<span class="popup-close pix-btn `+ (props.button || '') + `">Okay</span>
		</div>`;

		popup.show('', `<div class="popup-output">` + ppStr + `</div>`, {
			width: props.width
		});
	},
	showSpinner: function (ops) {
		ops = ops || {};
		popup.show('', `<div style="padding:10px 0;">
			<div class="pix-spinner" style="margin:0 auto;"></div>
		</div>`, { width: 100, closebtn: false, id: ops.id });
	}
}
$(document).ready(function (e) {
	$(".confirm").click(function (e) {
		return confirm(this.dataset.message != undefined ? this.dataset.message : "Are you sure ?");
	});
});
function closeUserNoti() {
	setTimeout(function () { $(".user-notification").remove(); }, 7000);
}
var pix = {
	openNotification: function (message, ok) {
		message = message || 'Oops. An error occurred. Please try again.';
		ok = ok || '';
		var msgCont = document.getElementById('notificationMsgLoader'), msgItem = document.createElement('div');
		if (!msgCont) {
			msgCont = document.createElement('div');
			msgCont.id = 'notificationMsgLoader';
			document.body.appendChild(msgCont);
		}
		msgItem.className = 'notification-item' + (ok ? ' success' : '');
		msgItem.innerHTML = message + '<em class="fa fa-times noti-close-btn"></em>';
		msgItem.getClass('noti-close-btn').onclick = pix.winNotificationHide;
		$(msgItem).fadeIn(250);
		msgCont.appendChild(msgItem);
		$(msgItem).delay(15000).fadeOut(250, pix.winNotificationFadeOutCb);
	},
	winNotificationHide: function () {
		$(this.parentNode).clearQueue().fadeOut(250, pix.winNotificationFadeOutCb);
	},
	winNotificationFadeOutCb: function () {
		$(this).remove();
	},
	startFileUpload: function (url, args) {
		if (url) {
			var pData = new FormData(),
				req = new XMLHttpRequest();

			if (args.data) {
				for (pKey in args.data) {
					pData.append(pKey, args.data[pKey]);
				}
			}
			if (args.progress) {
				req.upload.addEventListener('progress', args.progress, false);
			}
			if (args.success || args.error) {
				req.args = args;
				req.onreadystatechange = pix.fileUploadStatChange;
			}
			req.open('POST', url);
			req.send(pData);
		}
	},
	fileUploadStatChange: function () {
		if (this.readyState == 4) {
			if (this.status == 200) {
				var jsonObj;
				try {
					jsonObj = JSON.parse(this.response);
				} catch (e) {
					jsonObj = null;
				}
				this.args.success ? this.args.success(this, jsonObj) : 0;
			} else {
				this.args.error ? this.args.error(this) : 0;
			}
		}
	},
	scrollAnim: function (pos) {
		if (pos.getElementsByTagName !== undefined) {
			pos = $(pos).offset().top;
		}
		$('html, body').animate({
			scrollTop: pos
		});
	},
	post: function (data, done, props) {
		if (data.getElementsByTagName !== undefined) {
			data = $(data).serialize();
		}
		if (!props) {
			props = {};
		}

		$(':focus').blur();
		popup.showSpinner({ id: 'pixpostspn' });

		$.ajax(
			domain + 'ajax/',
			{
				method: 'post',
				data: data,
				error: function () {
					popup.close('pixpostspn');
					popup.showError();
				},
				success: function (data) {
					if (data.status == 'ok') {
						popup.close('pixpostspn');
						pix.openNotification(props.successMsg || 'Action completed', true);
						if (done) {
							done(data);
						}

					} else {
						this.error();
					}
				}
			}
		);
	}
};
