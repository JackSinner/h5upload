<?php
function test($t, $l = 0)
{
    foreach ($t as $item) {
        $str = str_pad($item['title'], strlen($item['title']) + $l, '+', STR_PAD_LEFT);
        echo "<p class='item' data-id='{$item['id']}'>{$str}</p>";
        if (isset($item['son'])) {
            $newl = $l + 1;
            test($item['son'], $newl);
        }
    }
}

?>
<style>
    .tree {
        overflow-y: auto;
        height: 51rem;
        width: 16%;
        background-color: white;
        padding: 1rem 1rem;
        display: inline-block;
    }

    .tree .item {
        font-weight: bold;
        cursor: copy;
    }

    .iframe {
        display: inline-block;
        width: 82%;
        height: 51rem;
        overflow-y: auto;
        background-color: white;
    }

    iframe {
        border: none;
        width: 100%;
        height: 100%;
    }
</style>
<div class="tree">
    <?php
    test($tree, 0);
    ?>
</div>
<div class="iframe">
    <iframe src="/admin/treeinfo?id={{count($tree)>0?array_shift($tree)['id']:0}}"></iframe>
</div>

<script type="text/javascript">
    $(function () {
        $(".tree .item").click(function (dom) {
            $("iframe").attr('src', "/admin/treeinfo?id=" + $(this).attr('data-id'));
        });
    });
</script>
