(function () {
    var a, b, c, d;
    $(document).ready(function () {
        _e("addVideoBtn").onclick = function() {
            openVideoForm("new");
        }
        $(".edit-video").click(editVideo);
        $(".delete-video").click(deleteVideo);
        $(".videoPrevw").click(function (){
            videoPrevw(this.parentNode.getAttribute('data-url'));
        });
        $(".video-text").click(function (){
            videoPrevw(this.parentNode.parentNode.getAttribute('data-url'));
        });
    });

    function openVideoForm(id, data) {
        a = id == "new";
        data = data == undefined ? {} : data;
        popup.show(
            '<div class="text-center">' + (a ? 'Add New' : 'Edit') + ' Video</div>',
            '<form id="saveVideo" method="post" action="">' +
                '<input type="hidden" name="method" value="video-save" />' +
                '<input type="hidden" name="id" value="'+ (id) +'" />' +
                '<div class="text-input mb10">' +
                    '<input type="text" id="videoTitle" data-type="string" name="title" class="fullwidth" placeholder="Video Title" value="' + (!a ? data.title : '') + '" />' +
                '</div>' +
                '<div class="mb10">' +
                    '<textarea class="fullwidth" name="embed_c" data-type="string" id="videoInput" cols="40" rows="4" placeholder="Embed code">' + (!a ? '<iframe src="' + data.video +
                        '" width="640" height="360"></iframe>' : '') + 
                    '</textarea>' +
                '</div>' +
                '<div class="vd-fom-ebl">' +
                    '<div>' +
                        'Enable' +
                    '</div>' +
                    '<label class="pix-toggle no-highlight">' +
                        '<input type="checkbox" value="1" class="check-bx" name="enable"' + (a  || (!a && data.enable == 'Y') ? 'checked' : '') + '/>' +
                        '<div class="toggle-ind"></div>' +
                    '</label>' +
                '</div>' +
                '<div class="pt15 text-center vd-fom-actions">' +
                    '<input type="submit" class="pix-btn primary" value="SAVE VIDEO" name="saveVideo" /> ' +
                    '<span class="pix-btn" id="cancelVideo">CANCEL</span>' +
                '</div>' +
            '</form>', 
            {
                width: 500, 
                closebtn: 0,
                id: 'addVdForm',
                callback: function () {
                    _e("cancelVideo").onclick = function () {
                        popup.hide();
                    };
                }
            }
        );

        $('#saveVideo').formchecker({
            scroll: 0,
            onValid: function () {
                $(':focus').blur();
                popup.showPreloader('Please wait..', 400, 'vdSubLdr');
                $.ajax(
                    domain + 'ajax/anyadmin/', {
                    method: 'post',
                    data: $('#saveVideo').serialize(),
                    error: function () {
                        popup.hide('vdSubLdr');
                        popup.showError('Unable to save video. Please try again.');
                    },
                    success: function (data) {
                        if (data.status == 'ok') {
                            popup.hide('vdSubLdr');
                            popup.hide();
                            if(id == "new") {
                                a = document.createElement("li");
                                a.className = "video";
                                a.setAttribute('data-url', data.video);
                                a.innerHTML = '<span class="pnt videoPrevw">' +
                                    '<img src="' + (data.vid != '' ? 'https://img.youtube.com/vi/' + data.vid + '/0.jpg' : 'images/video-thumb.jpg') + '" ' +
                                    'alt="thumb" /></span>' +
                                    '<span class="fetu">' +
                                    '<span class="video-text">' + data.title + '</span>' +
                                    '<span class="editable-sec">' +
                                    '<span class="material-symbols-outlined fe-icn edit-video" data-id="' + data.id + '" data-video="' + data.video + '" data-enable="' + data.enable + '">edit</span>' +
                                    '<span class="material-symbols-outlined fe-icn delete-video" data-id="' + data.id + '">delete</span>' +
                                    '</span>' +
                                    '</span>';
                                _e("videoListMain").appendChild(a);
                                $(a).find(".edit-video").click(editVideo);
                                $(a).find(".delete-video").click(deleteVideo);
                                $(a).find('.videoPrevw').click(function () {
                                    videoPrevw(this.parentNode.getAttribute('data-url'));
                                });
                                $(a).find('.video-text').click(function () {
                                    videoPrevw(this.parentNode.parentNode.getAttribute('data-url'));
                                });
                            } else {
                                let d = document.querySelector('.video .edit-video[data-id="' + data.id + '"]').closest('.video');

                                d.getElementsByClassName("pnt")[0].innerHTML =
                                    '<img src="' + (data.vid !== '' ? 'https://img.youtube.com/vi/' + data.vid + '/0.jpg' :
                                        'images/video-thumb.jpg') + '" alt="' + data.title + '" />';

                                d.getElementsByClassName("video-text")[0].innerHTML = data.title;

                                d.querySelector(".edit-video").setAttribute("data-video", data.video);
                                d.querySelector(".edit-video").setAttribute("data-enable", data.enable);
                                d.setAttribute("data-url", data.video);
                            }
                        } else {
                            this.error();
                        }
                    }
                });
                return false;
            }
        });
    }

    function editVideo() {
        d = this.parentNode.parentNode.parentNode;
        openVideoForm(this.getAttribute("data-id"), {
            title: d.getElementsByClassName("video-text")[0].innerHTML,
            video: this.getAttribute("data-video"),
            enable: this.getAttribute("data-enable")
        });
    }

    function deleteVideo() {
        var id = this.getAttribute('data-id');
        a = $(this).closest('.video');         

        if(confirm("Are are sure?")) {
            if(id) {
                popup.showPreloader('Please wait..', 400, 'vdSubLdr');
                
                $.ajax(
                    domain + 'ajax/anyadmin/', {
                    method: 'post',
                    data: {
                        method: 'video-delete',
                        id: id
                    },
                    error: function () {
                        popup.hide('vdSubLdr');
                        popup.showError('Unable to perform this action. Please try again.');
                    },
                    success: function (data) {
                        if (data.status == 'ok') {
                            popup.hide('vdSubLdr');
                            a.remove();
                        } else {
                            this.error();
                        }
                    }
                }
                );
                return false;
            }
        }
    }

    function videoPrevw(url) {
        popup.show("", '<iframe width="854" height="480" src="' + url + '" frameborder="0" allowfullscreen  id="videoDisplay"></iframe>', { width: 854, id: 'videoPreviewDisp' });
    }
})();