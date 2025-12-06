(function () {
    $(document).ready(function () {
        $('#providerSave').formchecker({
            onValid: function () {
                $(':focus').blur();
                popup.showSpinner();
            }
        });
        _e('pvdimg').onchange = function () {
            let logo = this.files[0],
                pbBar = _e('pgBar');

            if (logo) {
                if ((/\.(jpe*g|png|gif)$/i).test(logo.name)) {
                    _e('imgBox').addClass('uploading');
                    pix.startFileUpload(
                        domain + 'ajax/anyadmin/', {
                        data: {
                            method: 'provider-logo-upload',
                            pid: pData.pid,
                            logo: logo
                        },
                        error: function () {
                            _e('imgBox').removeClass('uploading');
                            pix.openNotification('Oops. Logo uploading failed. Please try again');
                            this.value = '';
                        },
                        progress: function (e) {
                            pbBar.style.width = ((e.loaded / e.total) * 100) + '%';
                        },
                        success: function (e, data) {
                            if (data.status === 'ok') {
                                _e('noImg').style.display = 'none';
                                _e('imgPreview').style.display = '';
                                _e('imgPreview').src = data.logo;
                                _e('imgBox').removeClass('uploading');
                                _e('pvdimg').value = '';
                                _e('pgBar').style.width = 0;
                                pix.openNotification('Logo updated', 1);
                            } else {
                                e.args.error();
                            }
                        }
                    }
                    );
                } else {
                    pix.openNotification('Please choose a jpeg, png or gif format file.');
                    this.value = '';
                }
            }
        }
    });
})();