// common
function _e(id) {
    var el = document.getElementById(id);
    el = el == undefined ? document.createElement("span") : el;
    return el;
};

function _cl(id, classid) { return classid == undefined ? document.getElementsByClassName(id) : document.getElementById(id).getElementsByClassName(classid); };

function isMail(id) { return (/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i).test(id); };

function getnum(num) { num = Number(num.toString().trim()); return !isNaN(num) ? num : 0; };

function formatNum(num) {
    num = Number(num).toFixed(2);
    num = num.toString().replace(/\.00$/g, "");
    return num;
}

function money(nStr) {
    nStr = formatNum(getnum(nStr));
    nStr += '';
    var x = nStr.split('.'),
        x1 = x[0],
        x2 = x.length > 1 ? '.' + x[1] : '',
        rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) { x1 = x1.replace(rgx, '$1' + ',' + '$2'); }
    return x1 + x2;
}

function toDollar(num) {
    num = Number(num).toFixed(2).split('.');
    return '$' + Number(num[0]).toLocaleString() + '.' + num[1];
}

function getCurDate() {
    var
        currentdate = new Date(),
        hours = currentdate.getHours(),
        minutes = currentdate.getMinutes(),
        isAMPM = 'AM';

    const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

    if (hours > 12) {
        isAMPM = 'PM';
        hours -= 12;
    }

    if (hours < 10) {
        hours = '0' + hours;
    }

    if (minutes < 10) {
        minutes = '0' + minutes;
    }

    return currentdate.getDate() + ' / ' +
        monthNames[currentdate.getMonth()] + ' / ' +
        currentdate.getFullYear() + ' ' +
        hours + ':' +
        minutes + ' ' +
        isAMPM;
}

Element.prototype.addClass = function (name) { if (!new RegExp(name, "gi").test(this.className)) { this.className = this.className + " " + name; } return this; };
Element.prototype.removeClass = function (name) { this.className = this.className.replace(new RegExp(name, "gi"), '').replace(/ {2,}/g, ' ').replace(/^ | $/gi, ''); return this; };
Element.prototype.switchClass = function (cl, add) {
    add = add == undefined ? true : add;
    this.className = (add ? this.className + (!new RegExp(cl).test(this.className) ? " " + cl : "") : this.className.replace(new RegExp("(\ *)" + cl, "g"), "")).trim();
    return this;
};
Element.prototype.toggleClass = function (c) { var r = new RegExp(c, 'i'); if (r.test(this.className)) { this.className = this.className.replace(r, '') } else { this.className += ' ' + c; } };
Element.prototype.getClass = function (cl) { var el = this.getElementsByClassName(cl); return el[0] != undefined ? el[0] : document.createElement("span"); };
Element.prototype.getTag = function (cl) { var el = this.getElementsByTagName(cl); return el[0] != undefined ? el[0] : document.createElement("span"); };
Element.prototype.parent = function (className, defValue = 'span') {
    var obj = this;
    found = false;
    while (obj != null && !found) {
        obj = obj.parentNode;
        found = obj != null && new RegExp(className).test(obj.className);
    }
    return obj ? obj :
        (
            defValue === 'span' ?
                document.createElement('span') :
                defValue
        );
};

function makeString(t, n, e) {
    var o = "",
        r = "";
    for (n = void 0 == n ? 10 : n, t = void 0 == t ? "uln" : t, o += void 0 != e ? e : "", o += /u/.test(t) ? "ABCDEFGHIJKLMNOPQRSTUVWXYZ" : "", o += /l/.test(t) ? "abcdefgijklmnopqrstuvwxyz" : "", o += /n/.test(t) ? "0123456789" : "", o += /s/.test(t) ? "`~!@#$%^&*()_+-={}|][:;\"'<>,./?" : ""; r.length < n;) r += o.charAt(Math.floor(Math.random() * o.length));
    return r
};
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
Element.prototype.qs = function (selector) { return this.querySelector(selector); }

function _c(name) { return document.getElementsByClassName(name); }
HTMLCollection.prototype.enable = function () { var i, length = this.length; for (i = 0; i < length; i++) { this[i].disabled = false; } return this; }
HTMLCollection.prototype.disable = function () { var i, length = this.length; for (i = 0; i < length; i++) { this[i].disabled = true; } return this; }
HTMLCollection.prototype.hide = function () { var i, length = this.length; for (i = 0; i < length; i++) { this[i].style.display = 'none'; } return this; }
HTMLCollection.prototype.show = function (prop) { var i, length = this.length; for (i = 0; i < length; i++) { this[i].style.display = prop || 'block'; } return this; }
$.fn.getFormData = function () {
    var rData = {},
        data;
    if (this[0]) { data = $('#settingsView').serializeArray(); for (i in data) { rData[data[i].name] = data[i].value; } }
    return rData;
}

function jsonDecode(str, dfl) { var jsnData; try { jsnData = JSON.parse(str); } catch (e) { } return jsnData || dfl || null; }

function swapDisplay(a, b) {
    _e(a).hide();
    _e(b).show();
}

function queryString2Obj(str) {
    var r = {};
    if (str) {
        str = str.split('&');
        for (sv of str) {
            sve = sv.split('=');
            sve[0] = decodeURIComponent(sve[0]);
            sve[1] = decodeURIComponent(sve[1] || '').replace(/\+/g, ' ');
            if ((/\[\]$/).test(sve[0])) {
                sve[0] = sve[0].replace(/\[\]$/i, '');
                if (!r[sve[0]]) { r[sve[0]] = []; }
                r[sve[0]].push(sve[1]);
            } else { r[sve[0]] = sve[1]; }
        }
    }
    return r;
}

// global functions
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
			<span class="label">' + (attrs.text || 'please wait..') + '</span>\
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
            title = title ? '<div style="text-align:center;">' + title + '</div>' : '';
            content = '<div style="text-align:center;">' + content + '</div>';
            ops.ok_btn = ops.ok_btn != undefined ? ops.ok_btn : true;
            ops.width = ops.width == undefined ? 500 : ops.width;
        }
        var popObj = document.createElement("div");
        if (ops.id != undefined) popObj.id = ops.id;
        popObj.className = "popup-frame " + (ops.class || '');
        popObj.innerHTML = '<div class="popup-window" style="' + (ops.width != undefined ? 'width:' + ops.width + 'px; ' : '') +
            (ops.height != undefined ? 'height:' + ops.height + 'px; ' : '') + '">' +
            (
                ops.closebtn ?
                    `<div class="pop-close-dv">
						<div class="popup-close">
							<span class="cl-icon material-symbols-outlined">
								close
							</span>
						</div>
					</div>` :
                    ''
            ) +
            (title ? '<div class="popup-heading ' + (ops.align ? 'text-' + ops.align : '') + '">' + title + '</div>' : '') +
            '<div class="popup-content-fm  ' + (ops.align ? 'text-' + ops.align : '') + '">' +
            content + (ops.ok_btn ? '<div class="text-center pt15"><span class="ok_btn pix-btn success">OK</span></div>' : '') +
            '</div>' +
            '</div>';
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
        var newZ = $(".popup-frame:first-child");
        newZ = newZ.length > 0 ? newZ[0].style.zIndex : 7499;
        popObj.style.zIndex = newZ + 1;
        $("body").prepend(popObj);
        if (ops.callback != undefined) {
            a = {};
            for (ok in ops) {
                !(/^(width|callback|closebtn|close_btn|id|height)$/gi).test(ok) ? a[ok] = ops[ok] : 0;
            }
            ops.callback(a);
        }
        popObj.resizeEvent = function () {
            popup.updateHeight($(popObj));
        };
        $(window).bind("resize", popObj.resizeEvent);
        $("html").css({ overflow: "hidden" });
        popObj.resizeEvent();
    },
    hide: function (obj, ops) {
        ops = ops == undefined ? {} : ops;
        if (obj && obj.indexOf) {
            obj = $('#' + obj);
        }
        obj = obj == undefined ? $(".popup-frame") : obj;
        obj[0] ? $(window).unbind("resize", obj[0].resizeEvent) : 0;
        if (obj && obj.remove) {
            obj.remove();
        }
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
    updateHeight: function (obj) {
        $('.popup-content-fm').css({ maxHeight: $(window).height() - 118 });
    },
    showPreloader: function (msg, width, id) {
        this.show(
            '',
            `<div class="preloader">
                <span class="pix-spinner spinner"></span>` +
            (msg || 'Loading. Please wait..') +
            `</div>`, {
            width: width || 400,
            closebtn: 0,
            id: id
        }
        );
    },
    showSuccess: function (msg, props) {
        props = props || {};
        popup.showOutput({
            icon: 'check_circle_outline',
            iconColor: '#5cab02',
            title: 'Done !',
            body: msg,
            width: props.width || 400,
            button: 'success',
            type: 'success'
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
            button: 'danger',
            type: 'error'
        });
    },
    showOutput: function (props) {
        props = props || {};

        var ppStr = `<div class="ppo-icon" style="color:` + (props.iconColor) + `">
			<span class="alert-icon material-symbols-outlined">
                ` + (props.icon || 'check_circle') + `
            </span>
		</div>
		<div class="ppo-title">
            ` + (props.title || 'Okay !') + `
        </div>
		<div class="ppo-body">
            ` + (props.body || 'Everything is ok.') + `
        </div>
		<div class="ppo-button">
			<span class="popup-close pix-btn ` + (props.button || '') + `">
                Okay
            </span>
		</div>`;

        popup.show(
            '',
            `<div class="popup-output">` + ppStr + `</div>`, {
            width: props.width,
            class: 'out-msg msg-' + (props.type || '')
        }
        );
    },
    showSpinner: function (ops) {
        ops = ops || {};
        popup.show(
            '',
            `<div style="padding:10px 0;">
                <div class="pix-spinner" style="margin:0 auto;"></div>
            </div>`, {
            width: 100,
            closebtn: false,
            id: ops.id
        }
        );
    }
}
$(document).ready(function (e) {
    $('.confirm').click(function (e) {
        return confirm(this.dataset.message || 'Are you sure ?');
    });
    $('#mobileMenuBtn').click(function () {
        $('#siteMobileMenu').addClass('show');
    });
    $('#mobMenuGreyBg').click(function () {
        $('#siteMobileMenu').removeClass('show').addClass('fading');
        setTimeout(function () {
            $('#siteMobileMenu').removeClass('fading')
        }, 300);
    });

    // navigation menu for mobile
    _e('mobMenuBtn').onclick = function () {
        document.body.addClass('mob-menu-active');
    };
    _e('mobMenuNavBg').onclick = closeMobileMenu;
    _e('mobMenuCloseBtn').onclick = closeMobileMenu;

    function closeMobileMenu() {
        document.body.removeClass('mob-menu-active');
    }

    // user menu for mobile
    _e('menuProfile').onclick = function () {
        document.body.addClass('user-menu-active');
    };

    _e('userMenuBgBox').onclick = hideUserMenu;
    _e('userMenuCloseBtn').onclick = hideUserMenu;

    function hideUserMenu() {
        document.body.removeClass('user-menu-active');
    };

    // active side menu auto scroll
    if ($(window).width() >= 1024) {
        let
            sideMenuBody = $('#sideMenuBody'),
            activeMenuItem = $('.menu-item.active');

        if (activeMenuItem[0]) {
            sideMenuBody.scrollTop(
                activeMenuItem.offset().top -
                sideMenuBody.offset().top -
                (sideMenuBody.height() / 2) +
                activeMenuItem.height() +
                sideMenuBody.scrollTop()
            )
        }
    }
});

function closeUserNoti() {
    setTimeout(function () { $(".user-notification").remove(); }, 7000);
}
var pix = {
    openNotification: function (message, ok) {
        message = message || 'Oops. An error occurred. Please try again.';
        ok = ok || '';
        var msgCont = document.getElementById('notificationMsgLoader'),
            msgItem = document.createElement('div');
        if (!msgCont) {
            msgCont = document.createElement('div');
            msgCont.id = 'notificationMsgLoader';
            document.body.appendChild(msgCont);
        }
        msgItem.className = 'notification-item' + (ok ? ' success' : '');
        msgItem.innerHTML = message + '<em class="material-symbols-outlined noti-close-btn">close</em>';
        msgItem.getClass('noti-close-btn').onclick = pix.winNotificationHide;
        $(msgItem).fadeIn(250);
        msgCont.appendChild(msgItem);
        $(msgItem).delay(3000).fadeOut(250, pix.winNotificationFadeOutCb);
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
        var sowNoti, hideSpin;
        if (data.getElementsByTagName !== undefined) {
            data = $(data).serialize();
        }
        if (!props) {
            props = {};
        }
        sowNoti = typeof props.sowNoti !== "undefined" ? props.sowNoti : true;
        hideSpin = typeof props.hideSpin !== "undefined" ? props.hideSpin : true;
        showSpin = typeof props.showSpin !== "undefined" ? props.showSpin : true;

        $(':focus').blur();
        if (showSpin) {
            popup.showSpinner({ id: 'pixpostspn' });
        }

        $.ajax(
            domain + 'ajax/' + (props.role || 'public') + '/', {
            method: 'post',
            data: data,
            error: function (data) {
                popup.close('pixpostspn');
                popup.showError(data.errorMsg || null);
            },
            success: function (data) {
                if (data.status == 'ok') {
                    if (hideSpin) {
                        popup.close('pixpostspn');
                    }
                    if (sowNoti) {
                        pix.openNotification(props.successMsg || 'Action completed', true);
                    }
                    if (done) {
                        done(data);
                    }

                } else {
                    this.error(data);
                }
            }
        }
        );
    },
    models: {
        CheckBox: function (props) {
            /* 
            instructions
                params
                    className
                        addition classnames
                        string value

                    value
                        checkbox value
                        string value

                    checked
                        checkbox ticked or not
                        boolean

                    id
                        id of check box and element
                        string value

                    label
                        label for the checkbox
                        string value
            */
            if (!props) {
                props = {};
            }
            return `<label class="pix-check ` + (props.className ? props.className + '-lbl' : '') + `">
				<input 
					type="checkbox" 
					autocomplete="off" 
					value="${props.value || '1'}"
					` + (props.name ? `name="${props.name}[]" ` : '') + `
					` + (props.checked ? 'checked' : '') + `
					` + (props.className ? `class="${props.className}-chk" ` : '') + `
					` + (props.id ? `id="${props.id}" ` : '') + `
				/>
				<span class="pix-check-tik material-symbols-outlined">
					check
				</span>
				<span class="chk-txt">
					${props.label || ''}
				</span>
			</label>`;
        },
        Radio: function (props) {
            /* 
             instructions
                 params
                     className
                         addition classnames
                         string value
 
                     value
                         checkbox value
                         string value
 
                     checked
                         checkbox ticked or not
                         boolean
 
                     id
                         id of check box and element
                         string value
 
                     label
                         label for the checkbox
                         string value
             */
            if (!props) {
                props = {};
            }
            return `<label class="pix-radio ` + (props.className ? props.className + '-lbl' : '') + `">
                <input 
                    type="radio" 
                    autocomplete="off" 
                    value="${props.value || '1'}"
                    ` + (props.name ? `name="${props.name}" ` : '') + `
                    ` + (props.checked ? 'checked' : '') + `
                    ` + (props.className ? `class="${props.className}-rdo" ` : '') + `
                    ` + (props.id ? `id="${props.id}" ` : '') + `
                />
                <span class="rddot"></span>
                <span class="rdtxt">
                    ${props.label || ''}
                </span>
            </label>`;
        },
        ReadFullText: function (text, limit) {
            limit = limit || 150;
            if (text.length > limit) {
                return `<div>
                    <div class="rdfull-short">
                        ${text.slice(0, limit)}
                        ...
                        <span class="a-link lined read-full-text-btn">read full</span>
                    </div>
                    <div class="rdfull-full" style="display: none;">
                        ${text.replace(/\n/g, '<br />')}
                    </div>
                </div>`;

            } else {
                return `<div>
                    ${text.replace(/\n/g, '<br />')}
                </div>`;
            }
        },
    }
};

const statesList = {
    'AL': 'Alabama',
    'AK': 'Alaska',
    'AZ': 'Arizona',
    'AR': 'Arkansas',
    'CA': 'California',
    'CO': 'Colorado',
    'CT': 'Connecticut',
    'DE': 'Delaware',
    'FL': 'Florida',
    'GA': 'Georgia',
    'HI': 'Hawaii',
    'ID': 'Idaho',
    'IL': 'Illinois',
    'IN': 'Indiana',
    'IA': 'Iowa',
    'KS': 'Kansas',
    'KY': 'Kentucky',
    'LA': 'Louisiana',
    'ME': 'Maine',
    'MD': 'Maryland',
    'MA': 'Massachusetts',
    'MI': 'Michigan',
    'MN': 'Minnesota',
    'MS': 'Mississippi',
    'MO': 'Missouri',
    'MT': 'Montana',
    'NE': 'Nebraska',
    'NV': 'Nevada',
    'NH': 'New Hampshire',
    'NJ': 'New Jersey',
    'NM': 'New Mexico',
    'NY': 'New York',
    'NC': 'North Carolina',
    'ND': 'North Dakota',
    'OH': 'Ohio',
    'OK': 'Oklahoma',
    'OR': 'Oregon',
    'PA': 'Pennsylvania',
    'RI': 'Rhode Island',
    'SC': 'South Carolina',
    'SD': 'South Dakota',
    'TN': 'Tennessee',
    'TX': 'Texas',
    'UT': 'Utah',
    'VT': 'Vermont',
    'VA': 'Virginia',
    'WA': 'Washington',
    'WV': 'West Virginia',
    'WI': 'Wisconsin',
    'WY': 'Wyoming',
    'DC': 'District of Columbia',
    'AS': 'American Samoa',
    'GU': 'Guam',
    'MP': 'Northern Mariana Islands',
    'PR': 'Puerto Rico',
    'VI': 'U.S. Virgin Islands'
};

function getStateName(code) {
    return statesList[code] || code;
}

// tasteraiser
window.globalFormCheckerScrollAdd = -70;

function ReadFullTextBtnClick() {
    const pn = $(this.parentNode.parentNode);
    pn.find('.rdfull-short').hide();
    pn.find('.rdfull-full').show();
}