/**
 * 模拟点击
 * @returns {boolean}
 */
function file_click(dom) {
    $(dom).next().click();
}

var uploaded_flag = false;//正在上传标识
var file_resource;//文件资源
var is_multiple = false;//多文件上传
var save_file = [];//上传后,需要保存的file文件名列表
function upload(_this) {
    if (uploaded_flag) {
        alert('文件正在上传中');
        return false;
    }
    is_multiple = $(_this).attr('multiple') == 'multiple' ? true : false;//是否是多文件上传
    var formData = new FormData();
    for (var i = 0; i < $(_this)[0].files.length; i++) {
        formData.append('files[' + i + ']', $(_this)[0].files[i]);
        //添加到缩略图
        $(".thumbs").append(`<div class="item"><img onerror="javascript:this.src='/vendor/laravel-admin-ext/h5upload/img/file.png';" src="${getObjectURL($(_this)[0].files[i])}"></div>`);
    }
    formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
    //先上传文件到本地服务器
    uploaded_flag = true;
    $.ajax({
        url: '/admin/h5upload/location_upload',
        dataType: "json",
        type: "post",
        async: true,
        data: formData,
        processData: false,
        contentType: false,
        xhr: () => {
            let myXhr = $.ajaxSettings.xhr();
            if (myXhr.upload) {
                myXhr.upload.addEventListener("progress", (e) => {
                    let percent = e.loaded / e.total;
                    $(_this).parent("div").next().next().find(".schedule").css('width', Math.floor(percent * 100) + '%').html(Math.floor(percent * 100) + '%');
                }, false);
            }
            return myXhr;
        },
        success: (res) => {
            if (res.code === 200) {
                uploaded_flag = false;
                for (let fileKey in res.data) {
                    $.ajax({
                        url: '/admin/saved',
                        type: 'post',
                        datatype: 'json',
                        data: {
                            key: res.data[fileKey]['save_name'],
                            size: res.data[fileKey]['file_size'],
                            path: window.location.pathname,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: (ret) => {
                            if (ret.code === 200) {
                                $(_this).next().val(get_save_file(ret.data['resource_id']));//保存的数据input
                            }
                            if (ret.code !== 200) {
                                console.warn(ret.msg);
                            }
                        },
                        error: function () {
                            console.warn('保存文件失败了');
                        }
                    });
                }
            } else {
                alert(res.msg);
            }
        },
        error: (err) => {
            console.warn(err);
            alert('上传失败,请稍后再试!');
        }
    });
}

/**
 * 获取本地浏览图链接
 * @param file
 * @returns {null}
 */
function getObjectURL(file) {
    var url = null;
    if (window.createObjectURL != undefined) { // basic
        url = window.createObjectURL(file);
    } else if (window.URL != undefined) { // mozilla(firefox)
        url = window.URL.createObjectURL(file);
    } else if (window.webkitURL != undefined) { // webkit or chrome
        url = window.webkitURL.createObjectURL(file);
    }
    return url;
}

/**
 * 获取要保存的文件名列表
 * @param rus_file_name
 * @returns {*}
 */
function get_save_file(rus_file_name) {
    if (is_multiple) {
        if (save_file.length > 0) {
            save_file = JSON.parse(save_file);
        }
        save_file.push(rus_file_name);
    } else {
        save_file = [rus_file_name];
    }
    save_file = JSON.stringify(save_file);
    console.log('要保存的文件名列表', save_file, '数据类型', typeof save_file);
    return save_file;
}
