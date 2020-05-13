@if(count($resource)>0)
    @foreach($resource as $r)
        <div class="thumb">
            <img title="{{$r['key']}}" src="{{$url}}/{{$r['key']}}"
                 onerror='this.src="/vendor/laravel-admin-ext/h5upload/img/file.png"'>
            <p>{{$r['key']}}</p>
            <div class="menu">
                <ul>
                    <li><a href="{{$url}}/{{$r['key']}}" target="_blank">打开</a></li>
                    <li>删除</li>
                </ul>
            </div>
        </div>
    @endforeach
@else
    <div class="notnull">
        <p>该目录无资源</p>
    </div>
@endif
<style>
    .thumb {
        width: 6rem;
        height: 6rem;
        display: inline-block;
        margin: 1rem;
        text-align: center;
        position: relative;
    }

    .thumb img {
        width: 80%;
        height: 80%;
    }

    .thumb p {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        font-size: .7rem;
    }

    .notnull {
        width: 100%;
        text-align: center;
    }

    .menu {
        position: absolute;
        top: 1rem;
        background-color: white;
        text-align: center;
        z-index: 999;
        border: 1px solid rgba(0, 0, 0, 0.11);
        width: 3rem;
        display: none;
    }

    .menu li {
        list-style: none;
        border-top: 1px solid rgba(0, 0, 0, 0.11);
        font-size: .7rem;
    }

    .menu ul {
        text-align: center;
        padding: 0;
    }

    a {
        text-decoration: none;
        color: black;
    }
</style>

<script type="text/javascript">
    var dom = document.getElementsByClassName("thumb");
    for (let index in dom) {
        dom[index].onclick = function (e) {
            //先关闭其他dom的菜单
            for (let i = 0; i < dom.length; i++) {
                dom[i].children[2].style.display = 'none';
            }
            console.log(e);
            dom[index].children[2].style.display = 'block';
            dom[index].children[2].style.top = e.offsetY + 'px';
            dom[index].children[2].style.left = e.offsetX + 'px';
        }
    }
</script>
