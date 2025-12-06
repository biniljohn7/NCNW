(function () {
    $(document).ready(function () {
        _e('eventImg').onchange = function () {
            let image = this.files[0];
            if ((/\.(jpe*g|png|gif)$/i).test(image.name)) {
                popup.showSpinner();
                _e('eventImgForm').submit();

            } else {
                pix.openNotification('Please choose a jpeg, png or gif format file.');
                this.value = '';
            }
        };
    });
})();