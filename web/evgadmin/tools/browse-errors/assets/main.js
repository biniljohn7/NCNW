$(document).ready(function () {
    // checking errors
    let erBoxes = $('.errorbox');
    if (erBoxes.length > 0) {
        erBoxes.each(function () {
            let
                wp = document.createElement('div');

            wp.className = 'err-wrap';
            wp.setAttribute(
                'data-id',
                this.id.replace('err_', '')
            );
            wp.innerHTML = `<div class="close-error">
                <span class="material-symbols-outlined">
                    close
                </span>
            </div>`;

            $(wp).find('.close-error').click(function () {
                $.post(
                    window.location.href,
                    {
                        id: $(this.parentNode).data('id'),
                        log: $('.nav-link.active').text().trim()
                    }
                );

                $(this.parentNode).fadeOut(300);
            });

            this.parentNode.insertBefore(wp, this);
            wp.appendChild(this);
        });
    }

    // 
    $('.file-del').click(function () {
        return confirm('Are you sure ?');
    });
    document.onkeyup = function (e) {
        if (e.keyCode == 46) {
            if (confirm('Are you sure ?')) {
                window.location.href = $('.nav-link.active + a').prop('href');
            }
        }
    }
});


// Check if the browser supports Web Notifications
if ("Notification" in window) {
    Notification.requestPermission()
        .then(function (permission) {
            if (permission === "granted") {
                checkErrorLoop();
            }
        })
        .catch(function (error) {
            console.error("Error requesting notification permission:", error);
        });
}

function checkErrorLoop() {
    setTimeout(
        checkNewError,
        window.location.host == 'localhost' ?
            3000 :
            15000
    );
}

function checkNewError() {
    $.ajax('', {
        method: 'get',
        data: {
            getnewerror: 1
        },
        success: function (data) {
            if (data.size > errorSize) {
                var notification = new Notification(
                    'Developer Alert', {
                    body: 'Hello developer, Some new code errors detected on NCNW.',
                    icon: window.location.href.slice(
                        0,
                        window.location.href.indexOf('/tools') + 1
                    ) + 'images/logo_icon.png'
                }
                );
                notification.onclick = function () {
                    window.focus();
                    notification.close();
                    window.location.reload();
                };
                errorSize = data.size;
            }
            checkErrorLoop();
        }
    });
}