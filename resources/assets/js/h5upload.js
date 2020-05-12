var file_resource;//文件资源

var oss_info = false;//oss信息

var uploaded_flag = false;//正在上传标识

var upload_success = false;//是否上传完成

var file_ext;//文件后缀


function file_click() {
    $("input[id='file']").click();
}

function check_file() {
    let file_tag = file_resource.name.split('.');
    file_ext = file_tag.pop().toLowerCase(); // 修复文件名包含.导致的文件类型获取错误
    // if (file_tag === 'undefined' || !['mp4', 'avi'].includes(file_ext)) {
    //   alert('文件格式不正确');
    //   return false;
    // }
    return true;
}

function upload(_this) {
    if (uploaded_flag) {
        alert('文件正在上传中');
        return false;
    }
    if ($(_this)[0].files[0] !== 'undefined') file_resource = $(_this)[0].files[0];
    if (!file_resource) {
        return false;
    }
    if (!check_file()) {
        return false;
    }
    $("#upload_button").html(file_resource.name);
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
                $('.schedule').css('width', Math.floor(percentage * 100) + '%').html(Math.floor(percentage * 100) + '%');
            }
        }
    }).then(function (result) {
        $('#file_name').val(result.name);
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
            success: function (ret) {
                if (ret.code !== 200) {
                    console.warn(ret.msg);
                }
            },
            error: function () {
                console.warn('保存文件失败了');
            }
        });
    }).catch(function (err) {
        $('.schedule').css('width', '0%').html('0%');
        alert('上传到云储存失败,请重试!');
        upload_success = true;
        uploaded_flag = false;
    });
}

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

function generate_upload_name() {
    let month = new Date().getMonth();
    month++;
    if (month < 10) {
        month = '0' + month;
    }
    return file_ext + '/' + new Date().getFullYear() + '/' + month + '/' + new Date().getTime() + '.' + file_ext;
}
