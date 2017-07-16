(function ($) {
    var defaults = {
        csrfToken: null,
        csrfTokenName: null,
        uploadUrl: '',
        deleteUrl: '',
        arrangeUrl: '',
        mainPhotoText: '',
        additionalPhotoText: '',
        photos: []
    };

    function imageUploader(el, options) {
        var settings = $.extend({}, defaults, options);
        var $main = $(el);
        var $identifier = $('input[name="identifier"]', $main).val();
        var $images = $('.image-container');
        var csrfParams = settings.csrfToken ? '&' + settings.csrfTokenName + '=' + settings.csrfToken : '';
        var $loader = $('.loader-progresbar-wrp', $main);

        function updateAction() {
            size = $('.image-container .image-item').size();
            if (size > 1) {
                $('#image-upload-block .add-photo-top').html('<p>'+ settings.mainPhotoText +'</p><p>'+ settings.additionalPhotoText +'</p>');
            } else {
                $('#image-upload-block .add-photo-top').html('<p>'+ settings.mainPhotoText +'</p>');
            }
        }

        function addPhoto(resp) {
            var data = {
                'img': {'src': resp.data.file},
                'id': resp.data.name,
                'storage': 'tmp',
                'size': resp.data.size
            };
            var $item = $('#item-image').tmpl(data);

            $images.append($item);
            updateAction();

            return  $item;
        }

        function deletePhoto(el) {
            var photo = $(el).parents('.image-item');
            var id = photo.data('id');
            var pk = photo.data('pk');
            var storage = photo.data('storage');
            var model = $('input[name="model"]').val();

            var data = {
                'id': id,
                'pk': pk,
                'storage': storage,
                'model': model,
                'identifier': $identifier
            };
            if (settings.csrfToken)
                data[settings.csrfTokenName] = settings.csrfToken;

            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: settings.deleteUrl,
                data: data,
                success: function (data) {
                    if (data.response.status == 'success') {
                        photo.remove();
                        $('input[name="sort[' + (pk ? pk : id) + ']"]', '.sorted-container').remove();
                    } else {
                        /*alert(data.response.status);*/
                    }
                }
            });
            updateAction();
        }

        function update() {
            $('.sorted-container').empty();
            $($images).find('.image-item').each(function (i, val) {
                var id = $(val).data('id');
                var pk = $(val).data('pk');
                $('.sorted-container').append('<input type="hidden" name="sort[' + (pk ? pk : id) + ']" value="' + i + '">');
            });
        }

        function multiUploadIE() {

            $($loader).find('.loader-progresbar').css({'width': '0%'});
            $loader.show();
            $($loader).find('.loader-progresbar').css({'width': '50%'});

            var ids = [];

            $.ajax({
                type: 'POST',
                url: settings.uploadUrl,
                data: $(':input', '#form-create-lot').add(':not(:file)').serializeArray(),
                files: $(":file", '#form-create-lot'),
                iframe: true,
                processData: false,
                dataType: "json"
            }).complete(function (resp) {
                    if (resp.status == 200) {
                        var resp = JSON.parse(resp.responseText);
                        if (resp.response.status == 'success') {
                            addPhoto(resp.response);
                            update();
                        } else {
                            $.each(resp.response.data.file, function (i, e) {
                                alert(e);
                            });
                        }

                        $($loader).find('.loader-progresbar').css({'width': '100%'});
                    } else {
                        alert("Error");
                    }
                    $loader.hide();
                });
            return false;
        }

        function multiUpload(files) {

            var uploadFileName = $('.file', $main).attr('name');

            var filesCount = files.length;
            var uploadedCount = 0;
            var ids = [];
            for (var i = 0; i < filesCount; i++) {

                $($loader).find('.loader-progresbar').css({'width': '1%'});
                $loader.show();

                var fd = new FormData();
                fd.append(uploadFileName, files[i]);
                fd.append('identifier', $identifier);

                if (settings.csrfToken) {
                    fd.append(settings.csrfTokenName, settings.csrfToken);
                }
                var xhr = new XMLHttpRequest();
                xhr.open('POST', settings.uploadUrl, true);
                xhr.onload = function () {
                    uploadedCount++;
                    if (this.status == 200) {
                        var resp = JSON.parse(this.response);
                        if (resp.response.status == 'success') {
                            addPhoto(resp.response);
                            update();
                        } else {
                            $.each(resp.response.data.file, function (i, e) {
                                alert(e);
                            });
                        }
                    } else {
                        alert("Error");
                    }
                    $loader.hide();

                };

                xhr.upload.onprogress = function (e) {
                    if (e.lengthComputable) {
                        $($loader).find('.loader-progresbar').css({'width': (e.loaded / e.total) * 100});
                    }
                };

                xhr.send(fd);
            }
            return false;
        }

            $('.file', $main).attr('multiple', 'true').on('change', function (e) {


            if (!window.FormData) {
                multiUploadIE();
            } else {
              multiUpload(this.files);
            }

        });

        $($images).sortable({
            stop: function () {
                update();
            },
            cancel: '.add-photo',
            forcePlaceholderSize: true,
            placeholder: 'lot-plaseholder'
        }).disableSelection();

        $('#image-upload-block').on('click', 'a.btn-remove', function () {

                deletePhoto($(this));
            return false;
        });

    }

    $.fn.imageUploader = function (options) {
        if (this.length) {
            this.each(function () {
                imageUploader(this, options);
            });
        }
    };

})(jQuery);


