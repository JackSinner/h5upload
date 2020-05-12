@if(count($resource)>0)
    <table>
        <tr>
            <td>文件名称</td>
            <td>文件大小</td>
            <td>上传时间</td>
            <td>操作</td>
        </tr>
        @foreach($resource as $r)
            <tr>
                <td><a target="_blank" href="{{$url}}/{{$r['key']}}">{{$r['key']}}</a></td>
                <td>{{round($r['size']/1024,2)}}kb</td>
                <td>{{$r['created_at']}}</td>
                <td>
                    <a>删除</a>
                    |
                    <a>下载</a>
                </td>
            </tr>
        @endforeach
    </table>
@else
    <p>该目录无资源</p>
@endif
<style>
    tr {
        text-align: center;
    }

    td {
        text-align: center;
        border-bottom:1px solid black;
    }

    table {
        text-align: center;
        width: 100%;
    }
</style>
