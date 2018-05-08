$.fn.upload = function() {
    this.each(function(options) {
        var self = this;
        options = options

        var defaults = {
            texts: {
                error: "An error occured.",
                maxSizeRegex: "Max size is not valid.",
                missingAttributes: "Missing 'image' attributes.",
                tooManyAttributes: "Too many 'image' attributes.",
                noFile : "No file selected.",
                sizeTooBig : "Your file is too big.",
                imgTooBig : "Your image is too big.",
                imgTooSmall : "Your image is too small.",
                imgRatioTooBig : "The ratio of your image is too big.",
                imgRatioTooSmall : "The ratio of your image is too small.",
                mimeTypeError : "Your file is not valid.",
                success: "Cancel and upload an another one ?",
                label: "<strong>Choose a file</strong> or drag it here",
                uploading: "Uploading..."
            },
            onUpload: function(loaded, total) {
                $(self).find(".upload-box-input").hide();
                $(self).find(".upload-box-success").hide();
                $(self).find(".upload-box-uploading").show();
                $(self).find(".upload-box-error").hide();
                var valueNow = Math.ceil(loaded / total) * 100;
                $(self).find(".upload-box-progress").css("width", valueNow + "%");
            },
            onSuccess: function(response) {
                $(self).find(".upload-box-uploading").hide();
                $(self).find(".upload-box-error").hide();
                $(self).find(".upload-box-input").hide();
                $(self).find(".upload-box-success").show();
                var $input = $("#"+$(self).data("rel"));
                $input.val(response.file);
                $(self).find(".upload-box-success").html('<strong>Done!</strong> <a href="#" class="upload-box-cancel">'+defaults.texts.success+'</a>')
            },
            onError: function(response) {
                $(self).find(".upload-box-success").hide();
                $(self).find(".upload-box-uploading").hide();
                $(self).find(".upload-box-input").show();
                $(self).find(".upload-box-error").show();
                $(self).find(".upload-box-error").text(defaults.texts[response.message]);
            }
        };

        defaults = Object.assign(defaults, options);

        var $input = $("#"+$(self).data("rel"));
        var maxSize = -1;
        var sizeRegExp = new RegExp(/^(\d+)([O|K|M]{1})$/);
        var mimeTypes = $input.data("mime-types");
        var image = $input.data("image");

        $(".upload-box-uploading-text").html(defaults.texts.uploading);

        $(".upload-box-label").html(defaults.texts.label);

        if($input.data("max-size") != "" && sizeRegExp.test($input.data("max-size"))) {
            var matches = sizeRegExp.exec($input.data("max-size"))
            var multiplier = matches[2] == "O" ? 1 : (matches[2] == "K" ? 1024 : 1024*1024);
            maxSize = parseInt(matches[1]) * multiplier;
        }

        $(this).on("click", ".upload-box-cancel", function() {
            $(self).find(".upload-box-uploading").hide();
            $(self).find(".upload-box-error").hide();
            $(self).find(".upload-box-input").show();
            $(self).find(".upload-box-success").hide();
            $("#"+$(self).data("rel")).val("");
            $(".upload-box-file").val("");
        })

        $(this)
            .on("drag dragstart dragend dragover dragenter dragleave drop", function(e) {
                e.preventDefault();
                e.stopPropagation();
            })
            .on('dragenter dragover', function() {
                $(this).addClass('is-dragover');
            })
            .on('dragleave', function(e) {
                $(this).removeClass('is-dragover');
            })
            .on("drop", function(e) {
                if(e.originalEvent.dataTransfer){
                    if(e.originalEvent.dataTransfer.files.length) {
                        e.preventDefault();
                        e.stopPropagation();
                        upload(e.originalEvent.dataTransfer.files);
                    }
                }
                $(this).removeClass('is-dragover');
                return false;
            });

        $(this).on("change", ".upload-box-file", function() {
            upload(this.files);
        });

        var next = function (file) {
            var formData = new FormData();
            formData.append("file", file);
            formData.append("attr", JSON.stringify($input.data()));

            $.ajax({
                url: '/t_boileau_upload/upload',
                type: 'post',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                xhr: function() {
                    var myXhr = $.ajaxSettings.xhr();
                    if (myXhr.upload) {
                        myXhr.upload.addEventListener('progress', function (e) {
                            if (e.lengthComputable) {
                                defaults.onUpload(e.loaded, e.total);
                            }
                        }, false);
                    }
                    return myXhr;
                },
                success: function(response) {
                    if(response.error) {
                        defaults.onError(response);
                    }else{
                        defaults.onSuccess(response);
                    }
                }
            })
        }

        function upload(files) {
            var file = files[0];

            if (mimeTypes.length > 0 && mimeTypes.indexOf(file.type) == -1) {
                defaults.onError({
                    message: defaults.texts.mimeTypeError
                });
                return;
            }

            if (maxSize >= 0 && file.size > maxSize) {
                defaults.onError({
                    message: defaults.texts.sizeTooBig
                });
                return;
            }

            var isImage = file.type == "image/gif" || file.type == "image/jpeg" || file.type == "image/jpg" || file.type == "image/png";

            if(isImage && (image.min_ratio || image.max_ratio || image.min_height || image.max_height || image.min_width || image.max_width)) {
                var fr = new FileReader;
                fr.onload = function() { // file is loaded
                    var img = new Image;
                    img.onload = function() {
                        var ratio = img.width/img.height;
                        if(image.min_ratio && image.min_ratio > ratio) {
                            defaults.onError({
                                message: defaults.texts.imgRatioTooSmall
                            });
                        }else if(image.max_ratio && image.max_ratio < ratio) {
                            defaults.onError({
                                message: defaults.texts.imgRatioTooBig
                            });
                        }else if(image.min_height && image.min_height > img.height) {
                            defaults.onError({
                                message: defaults.texts.imgTooSmall
                            });
                        }else if(image.max_height && image.max_height < img.height) {
                            defaults.onError({
                                message: defaults.texts.imgTooBig
                            });
                        }else if(image.min_width && image.min_width > img.width) {
                            defaults.onError({
                                message: defaults.texts.imgTooSmall
                            });
                        }else if(image.max_width && image.max_width < img.width) {
                            defaults.onError({
                                message: defaults.texts.imgTooBig
                            });
                        }else{
                            next(file);
                        }
                    };
                    img.src = fr.result; // is the data URL because called with readAsDataURL
                };
                fr.readAsDataURL(file);
            }else{
                next(file);
            }

        }
    })
}

$(".upload-box").upload();