<script type="text/javascript">

    (function($) {

        //var opts = $.extend({}, galleryDefaults, options);
        //var csrfParams = opts.csrfToken ? '&' + opts.csrfTokenName + '=' + opts.csrfToken : '';


        var defaults = {
            csrfToken: null,
            csrfTokenName: null,
            uploadUrl: '',
            deleteUrl: '',
            arrangeUrl: '',
            photos: []
        };

        function imageUploader(el, options) {
            var settings = $.extend({}, defaults, options);
            var $main = $(el);
            var $identifier = $('input[name="identifier"]', $main).val();
            var $images = $('.images-container');


            function addPhoto(resp) {
                var photo = $('<a><span class="remove-img"><i class="icon-remove"></i></span></a>');
                photo.data('id', resp.data.name);
                photo.data('storage', 'tmp');
                $(photo).css({'background-image': 'url(' + resp.data.file + ')'});
                $images.append(photo);
                return photo;
            }

            function deletePhoto(el) {
                var photo = $(el).parent();
                var id = photo.data('id');
                var pk = photo.data('pk');
                var storage = photo.data('storage');
                var model = $('input[name="model"]').val();
                //'csrf':csrfParams
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: settings.deleteUrl,
                    data: {'id': id, 'storage': storage, 'identifier': $identifier, 'model': model,},
                    success: function(data) {
                        if (data.response.status == 'success') {
                            photo.remove();
                            $('input[name="sort[' + (pk ? pk : id) + ']"]', '.sorted-container').remove();
                        } else {
                            alert(data.response.status);
                        }
                    }
                });
            }

            function update() {
                $('.sorted-container').empty();

                $('.images-container a').each(function(i, val) {
                    var id = $(val).data('id');
                    var pk = $(val).data('pk');
                    $('.sorted-container').append('<input type="hidden" name="sort[' + (pk ? pk : id) + ']" value="' + i + '">');
                });
            }


            if (window.FormData !== undefined) { // if XHR2 available
                console.log('XHR2 avalible');
                var uploadFileName = $('.file', $main).attr('name');

                function multiUpload(files) {
//                $progressOverlay.show();
//                $uploadProgress.css('width', '5%');
                    var filesCount = files.length;
                    var uploadedCount = 0;
                    var ids = [];
                    for (var i = 0; i < filesCount; i++) {
                        var fd = new FormData();

                        fd.append(uploadFileName, files[i]);
                        fd.append('identifier', $identifier);

                        if (settings.csrfToken) {
                            fd.append(settings.csrfTokenName, settings.csrfToken);
                        }
                        var xhr = new XMLHttpRequest();
                        xhr.open('POST', settings.uploadUrl, true);
                        xhr.onload = function() {
                            uploadedCount++;
                            if (this.status == 200) {
                                console.log('download foto');
                                var resp = JSON.parse(this.response);
                                if (resp.response.status == 'success') {
                                    addPhoto(resp.response);
                                    update();
                                } else {
                                    alert('Error');
                                }

                                //ids.push(resp['id']);
                            } else {
                                // exception !!!
                            }

                            //**///////////////////
                            //var image = document.createElement('img');
//      image.src = resp.dataUrl;
//      document.body.appendChild(image);
                            //
                            //$uploadProgress.css('width', '' + (5 + 95 * uploadedCount / filesCount) + '%');
                            console.log(uploadedCount);
                            /*if (uploadedCount === filesCount) {
                             $uploadProgress.css('width', '100%');
                             $progressOverlay.hide();
                             if (opts.hasName || opts.hasDesc) editPhotos(ids);
                             }*/
                        };
                        xhr.send(fd);
                    }
                }

//            (function () { // add drag and drop
//                var el = $main[0];
//                var isOver = false;
//                var lastIsOver = false;
//
//                setInterval(function () {
//                    if (isOver != lastIsOver) {
//                        if (isOver) el.classList.add('over');
//                        else el.classList.remove('over');
//                        lastIsOver = isOver
//                    }
//                }, 30);
//
//                function handleDragOver(e) {
//                    e.preventDefault();
//                    isOver = true;
//                    return false;
//                }
//
//                function handleDragLeave() {
//                    isOver = false;
//                    return false;
//                }
//
//                function handleDrop(e) {
//                    e.preventDefault();
//                    e.stopPropagation();
//
//
//                    var files = e.dataTransfer.files;
//                    multiUpload(files);
//
//                    isOver = false;
//                    return false;
//                }
//
//                function handleDragEnd() {
//                    isOver = false;
//                }
//
//
//                el.addEventListener('dragover', handleDragOver, false);
//                el.addEventListener('dragleave', handleDragLeave, false);
//                el.addEventListener('drop', handleDrop, false);
//                el.addEventListener('dragend', handleDragEnd, false);
//            })();



                $('.file', $main).attr('multiple', 'true').on('change', function(e) {
                    console.log('file change');
                    e.preventDefault();
                    multiUpload(this.files);
                });

            } else {
                console.log('not');
                $('.afile', $main).on('change', function(e) {
                    e.preventDefault();
                    var ids = [];
                    $progressOverlay.show();
                    $uploadProgress.css('width', '5%');

                    var data = {};
                    if (opts.csrfToken)
                        data[opts.csrfTokenName] = opts.csrfToken;
                    $.ajax({
                        type: 'POST',
                        url: opts.uploadUrl,
                        data: data,
                        files: $(this),
                        iframe: true,
                        processData: false,
                        dataType: "json"
                    }).done(function(resp) {
                        addPhoto(resp['id'], resp['preview'], resp['name'], resp['description'], resp['rank']);
                        ids.push(resp['id']);
                        $uploadProgress.css('width', '100%');
                        $progressOverlay.hide();
                        if (opts.hasName || opts.hasDesc)
                            editPhotos(ids);
                    });
                });
            }


            $('.images-container').sortable({
                stop: function() {
                    update();
                }
            }).disableSelection();

            $('a span.remove-img', '.images-container').live('click', function() {
                if (confirm('Вы уверены, что хотите удалить изображения?')) {
                    deletePhoto($(this));

                }
            });

        }//end function 
//   // The actual plugin
        $.fn.imageUploader = function(options) {
            if (this.length) {
                this.each(function() {
                    imageUploader(this, options);
                });
            }
        };
    })(jQuery);


    $(document).ready(function() {
        $('#image-upload-block').imageUploader({
            'uploadUrl': '/upload/upload',
            'deleteUrl': '/upload/delete'
        });

    });

</script>
<style type="text/css">
    #image-upload-block{
        border: 1px solid #DDDDDD;
        border-radius: 4px 4px 4px 4px;
        box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075);
        position: relative;
    }
    #image-upload-block .row-form{
        margin: 0;
    }
    #image-upload-block hr{
        margin: 0 4px;   
    } 
    #image-upload-block .fileinput-button {
        position: relative;
        overflow: hidden;
        margin-left: 8px;
        margin-top: 4px;
        margin-bottom: 4px;
    }

    #image-upload-block .fileinput-button input {
        position: absolute;
        top: 0;
        right: 0;
        margin: 0;
        border: solid transparent;
        border-width: 0 0 100px 200px;
        opacity: 0;
        filter: alpha(opacity = 0);
        -moz-transform: translate(-300px, 0) scale(4);
        direction: ltr;
        cursor: pointer;
    }
    #image-upload-block .images-container a{
        width:120px;
        height:120px;
        cursor: move;
        display:inline-block;
        border:7px solid #303030;
        box-shadow:0 1px 3px rgba(0,0,0,0.5);
        border-radius:4px;
        margin:6px 6px 40px;
        position:relative;
        text-decoration:none;
        background-position:center center;
        background-repeat:no-repeat;
        background-size:cover;
        -moz-background-size:cover;
        -webkit-background-size:cover}
    .images-container a .remove-img {
        position: absolute;
        top:-7px;
        right:-7px;
        border-radius:4px;
        cursor: pointer;
        width: 20px;
        height: 20px;
        display: block;
        background: #303030;
    }
    .images-container a .remove-img .icon-remove {
        padding: 3px 0 0 4px;
        color: #fff;
    }


    /*#thumbs a:after{background-color:#303030;border-radius:7px;bottom:-136px;box-shadow:0 1px 2px rgba(0,0,0,0.3);color:#FFFFFF;content:attr(title);display:inline-block;font-size:10px;max-width:90px;overflow:hidden;padding:2px 10px;position:relative;text-align:center;white-space:nowrap}
    */
</style>

<div id="image-upload-block">
    <div class="btn-toolbar row-form">
        <input type="hidden" name="identifier" value="<?php echo md5(microtime()); ?>"/>      
        <input type="hidden" name="model" value="<?php echo get_class($model); ?>"/>
        <input type="hidden" name="id" value="<?php echo $model->getPrimaryKey(); ?>"/>
        <span class="btn btn-green fileinput-button">
            <i class="icon-plus icon-white"></i>
            Добавить…            
            <input type="file" name="image" class="file" accept="image/*" multiple="multiple"/>
        </span>
    </div>
    <hr>
    <div class="images-container">
        <?php
        if (!$model->isNewRecord) {

            $images = Yii::app()->db->createCommand()
                    ->select('*')
                    ->from('images')
                    ->where('item_id=:item_id', array(':item_id' => $model->getPrimaryKey()))
                    ->order('sort')
                    ->queryAll();

            foreach ($images as $img) {
//                $path = str_replace('admin7.','lab7.', Yii::app()->request->getHostInfo());
                $path = str_replace('admin.', '', Yii::app()->request->getHostInfo());
                $file = $path . '/i/' . $img['image'];
                echo '<a data-id="' . $img['image'] . '" data-storage="locale" style="background-image: url(' . $file . ');"><span class="remove-img"><i class="icon-remove"></i></span></a>';
            }
        }
        ?>

    </div>
    <div class="sorted-container"></div>
</div>