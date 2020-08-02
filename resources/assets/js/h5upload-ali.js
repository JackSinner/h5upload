var file_resource;//文件资源

var oss_info = false;//oss信息

var uploaded_flag = false;//正在上传标识

var upload_success = false;//是否上传完成

var file_ext;//文件后缀

var is_multiple = false;//多文件上传

var save_file = [];//上传后,需要保存的file文件名列表

/**
 * 模拟点击
 * @returns {boolean}
 */
function file_click(dom) {
    $(dom).next().click();
}

/**
 * 验证文件扩展名
 * @returns {boolean}
 */
function check_file() {
    let file_tag = file_resource.name.split('.');
    file_ext = file_tag.pop().toLowerCase(); // 修复文件名包含.导致的文件类型获取错误
    // if (file_tag === 'undefined' || !['mp4', 'avi'].includes(file_ext)) {
    //   alert('文件格式不正确');
    //   return false;
    // }
    return true;
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
 * 上传文件
 * @param _this
 * @returns {boolean}
 */
function upload(_this) {
    if (uploaded_flag) {
        alert('文件正在上传中');
        return false;
    }
    is_multiple = $(_this).attr('multiple') == 'multiple' ? true : false;//是否是多文件上传
    for (let i = 0; i < $(_this)[0].files.length; i++) {
        if ($(_this)[0].files[i] !== 'undefined') file_resource = $(_this)[0].files[i];
        if (!file_resource) {
            return false;
        }
        if (!check_file()) {
            return false;
        }
        $(".thumbs").append(`<div class="item"><img onerror="javascript:this.src='/vendor/laravel-admin-ext/h5upload/img/file.png';" src="${getObjectURL($(_this)[0].files[i])}"></div>`);
        $(_this).prev().html(file_resource.name);
        uploaded_flag = true;
        if (!get_oss_info()) {
            alert('获取上传凭证失败');
            upload_success = true;
            uploaded_flag = false;
            return false;
        }
        var client = new OSS({
            accessKeyId: oss_info.sts.AccessKeyId,
            accessKeySecret: oss_info.sts.AccessKeySecret,
            stsToken: oss_info.sts.SecurityToken,
            endpoint: oss_info.oss.endpoint,
            bucket: oss_info.oss.bucket
        });
        var oss_file_name = generate_upload_name();
        client.multipartUpload(oss_file_name, file_resource, {
            progress: (percentage, checkpoint, res) => {
                if (percentage > 0) {
                    $(_this).parent("div").next().next().find(".schedule").css('width', Math.floor(percentage * 100) + '%').html(Math.floor(percentage * 100) + '%');
                }
            }
        }).then((result) => {
            upload_success = true;
            uploaded_flag = false;
            //上传完毕,添加数据到数据库
            $.ajax({
                url: '/admin/saved',
                type: 'post',
                datatype: 'json',
                data: {
                    key: oss_file_name,
                    size: file_resource.size,
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
        }).catch(function (err) {
            $(_this).parent("div").next().next().find(".schedule").css('width', '0%').html('0%');
            alert('上传到云储存失败,请重试!');
            upload_success = true;
            uploaded_flag = false;
            console.log(err);
        });
    }
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

/**
 * 获取oss sts临时凭证
 * @returns {boolean}
 */
function get_oss_info() {
    $.ajax({
        url: "/admin/h5upload_info",
        type: "post",
        datatype: "json",
        async: false,
        data: {
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function (ret) {
            if (ret.code === 200) {
                oss_info = ret.data;
            }
        },
        error: function (ret) {
            console.log(ret);
        }
    });
    return oss_info;
}

/**
 * 获取上传到oss的文件名
 * @returns {string}
 */
function generate_upload_name() {
    let month = new Date().getMonth();
    month++;
    if (month < 10) {
        month = '0' + month;
    }
    return file_ext + '/' + new Date().getFullYear() + '/' + month + '/' + new Date().getTime() + '.' + file_ext;
}
