(function () {
    $(document).ready(function () {
        $('#advocacySave').formchecker({
            onValid: function () {
                $(':focus').blur();
                popup.showSpinner();
            }
        });
        var dependScope = _e('dependScope');
        _e('scopeSel').onchange = function () {
            let scope = this.value;
            if (scope) {
                $.ajax(
                    domain + 'ajax/anyadmin/', {
                    method: 'post',
                    data: {
                        method: 'under-scope',
                        scope: scope
                    },
                    error: function () {
                        dependScope.innerHTML = '';
                    },
                    success: function (data) {
                        if (data.status == 'ok') {
                            console.log(data);
                            var spDatas = data.spDatas;
                            dependScope.innerHTML = '';
                            dependScope.innerHTML = `<div class="fld-label">
                                ${data.scope}
                            </div>`;
                            list = document.createElement('div');
                            list.className = 'fld-inp ctry-sec';
                            spDatas.forEach(scope => {
                                list.innerHTML += pix.models.CheckBox({
                                    name: 'ctry',
                                    value: scope.spId,
                                    label: scope.spName,
                                    className: 'chkbox'
                                });
                            });
                            dependScope.appendChild(list);
                            dependScope.addClass('show');
                        } else {
                            this.error();
                        }
                    }
                }
                );
            } else {
                dependScope.innerHTML = '';
                dependScope.removeClass('show');
            }
        }

        tinymce.init({
            selector: '#pdf',
            plugins: 'preview importcss searchreplace autolink directionality code visualblocks visualchars fullscreen image link media codesample table charmap pagebreak nonbreaking anchor insertdatetime advlist lists wordcount help charmap quickbars emoticons accordion',
            editimage_cors_hosts: ['picsum.photos'],
            menubar: 'file edit view insert format tools table help',
            toolbar: "undo redo | accordion accordionremove | blocks fontfamily fontsize | bold italic underline strikethrough | align numlist bullist | link image | table media | lineheight outdent indent| forecolor backcolor removeformat | charmap emoticons | code fullscreen preview | pagebreak anchor codesample | ltr rtl",
            image_advtab: true,
            importcss_append: true,
            height: 600,
            image_caption: true,
            quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
            noneditable_class: 'mceNonEditable',
            toolbar_mode: 'sliding',
            contextmenu: 'link image table',
            skin: 'oxide',
            content_css: 'default',
            content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:16px }',
            setup: function (editor) {
                editor.on('submit', function (event) {
                    _e('hdPDFInp').value = encodeURIComponent(editor.getContent())
                });
            }
        });
    });
})();