<table>
    @foreach($resource as $r)
        <tr>
            <td><a target="_blank" href="{{$url}}/{{$r['key']}}">{{$r['key']}}</a></td>
        </tr>
    @endforeach
</table>
