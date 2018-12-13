/**
 * author:gouguoyin
 * qq:245629560
 * doc:http://www.gouguoyin.cn/js/141.html
 */
(function($){
    $.fn.ajaxImageUpload = function(options){

        var defaults = {
            data: null,
            url: '',
            zoom: true,
            allowType: ["gif", "jpeg", "jpg", "bmp",'png'],
            maxNum: 10,
            hidenInputName: '', // 上传成功后追加的隐藏input名，注意不要带[]，会自动带[]，不写默认和上传按钮的name相同
            maxSize: 2, //设置允许上传图片的最大尺寸，单位M
            success: $.noop, //上传成功时的回调函数
            error: $.noop //上传失败时的回调函数
        };

        var thisObj = $(this);
        var config  = $.extend(defaults, options);
        var inputName = thisObj.attr('name');

        // 设置是否在上传中全局变量
        isUploading  = false;

        thisObj.each(function(i){
            thisObj.change(function(){
                ajaxUpload();
            });
        });

        var ajaxUpload = function () {
            // 获取最新的
            var formData = new FormData();
            var fileData = thisObj[0].files;

            if(fileData){
                // 目前仅支持单图上传
                formData.append(inputName, fileData[0]);
            }

            var postData = config.data;

            if (postData) {
                for (var i in postData) {
                    formData.append(i, postData[i]);
                }

            }

            // ajax提交表单对象
            $.ajax({
                url: config.url,
                type: "post",
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                beforeSend: function () {
                    toastr.info('上传中！');
                    $(".loading").show();
                },
                success:function(json){
                    if(json.code == '1' && !json.data){
                        alert('服务器返回的json数据中必须包含src元素');
                        return false;
                    }

                    // 将上传状态设为非上传中
                    isUploading = false;
                    // 执行成功回调函数
                    var callback = config.success;
                    callback(json);
                },
                error:function(e){
                    // 执行失败回调函数
                    var callback = config.error;
                    callback(e);
                }
            });
        };
    };

})(jQuery);