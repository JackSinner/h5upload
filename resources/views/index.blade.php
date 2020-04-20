<div class="form-group {!! !$errors->has($label)?: 'has_error' !!}">
    <label for="{{$id}}" class="col-sm-2 control-label">{{$label}}</label>
    <div class="col-sm-6">
        @include('admin::form.error')
        <div class="input-group">
            <button type="button" onclick="file_click()" class="btn btn-default" id="upload_button">{{ old($column, $value)??'请选择文件上传' }}</button>
            <input onchange="upload(this)" id="file" {{$attributes}} style="opacity: 0;" type="file"/>
            <input id="file_name" name="{{$name}}" style="display: none;" value="{{ old($column, $value) }}" />
        </div>
        <div class="schedule-container">
            <div class="skills schedule" style="width: 0px;">0%</div>
        </div>
        @include('admin::form.help-block')
    </div>
</div>
