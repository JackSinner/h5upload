<div class="form-group {!! !$errors->has($label)?: 'has_error' !!}">
    <label for="{{$id}}" class="col-sm-2 control-label">{{$label}}</label>
    <div class="col-sm-6">
        @include('admin::form.error')
        <div class="input-group">
            <button type="button" onclick="file_click(this)" class="btn btn-default"
                    id="upload_button">选择文件上传
            </button>
            <input onchange="upload(this)" id="file" {{$attributes}} style="opacity: 0;" type="file"/>
            <input id="file_name" name="{{$name}}" style="display: none;" value="{{ old($column, $value) }}"/>
        </div>
        <!--缩略图开始-->
        <ol id="h5upload-thumbs" class="thumbs">
            @foreach($h5resource as $id=>$resource)
                <li data-resource-id="{{$id}}" title="按住鼠标拖动顺序" class="item">
                    <img onerror="javascript:this.src='/vendor/laravel-admin-ext/h5upload/img/file.png';"
                         src="{{$resource}}"/>
                    <div class="remove" title="点击删除图片" onclick="removeImage('{{$id}}',this)"></div>
                </li>
            @endforeach
        </ol>
        <!--缩略图结束-->
        <!--进度开始-->
        <div class="schedule-container">
            <div class="skills schedule" style="width: 0px;">0%</div>
        </div>
        <!--进度结束-->
        @include('admin::form.help-block')
    </div>
</div>

<script>
    $(function () {
        bindMove();
    });
</script>
