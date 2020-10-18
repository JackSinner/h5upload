function bindMove() {
    var sortable = Sortable.create(document.getElementById("h5upload-thumbs"), {
        onUpdate: function (/**Event*/evt) {
            let els = $(evt.from).find("li");
            let values = [];
            for (let i = 0; i < els.length; i++) {
                let dom = $(els[i]);
                let resource_id = dom.attr('data-resource-id');
                values.push(resource_id);
            }
            values = JSON.stringify(values);
            $("#h5upload-thumbs").prev().find("input").next().val(values);
        }
    });
}

function removeImage(index, _this) {
    let arr = $("#file_name").val();
    arr = JSON.parse(arr);
    arr.splice(arr.indexOf(index), 1)
    $("#file_name").val(JSON.stringify(arr));
    $(_this).parent('li').remove();
}
