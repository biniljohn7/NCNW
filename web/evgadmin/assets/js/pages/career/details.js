(function () {
    $(document).ready(function () {
        _e('crimg').onchange = function () {
            let image = this.files[0],
                pgBar = _e('pgBar');

            if (image) {
                if ((/\.(jpe*g|png|gif)$/i).test(image.name)) {
                    _e('imgBox').addClass('uploading');
                    pix.startFileUpload(
                        domain + 'ajax/anyadmin/', {
                        data: {
                            method: 'career-image-upload',
                            cid: cData.cid,
                            image: image
                        },
                        error: function () {
                            _e('imgBox').removeClass('uploading');
                            pix.openNotification('Oops. Image uploading failed. Please try again');
                            this.value = '';
                        },
                        progress: function (e) {
                            pgBar.style.width = ((e.loaded / e.total) * 100) + '%';
                        },
                        success: function (e, data) {
                            if (data.status === 'ok') {
                                console.log(data);
                                _e('noImg').style.display = 'none';
                                _e('imgPreview').style.display = '';
                                _e('imgPreview').src = data.image;
                                _e('imgBox').removeClass('uploading');
                                _e('crimg').value = '';
                                _e('pgBar').style.width = 0;
                                pix.openNotification('Image updated', 1);
                            } else {
                                e.args.error();
                            }
                        }
                    }
                    )
                } else {
                    pix.openNotification('Please choose a jpeg, png or gif format file.');
                    this.value = '';
                }
            }
        }
    });
})();