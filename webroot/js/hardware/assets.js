/**
 * 格式化列表状态
 * @param value
 */
function formatter_status(value){
    if(value == 1){
        return '上线';
    }else{
        return '下架';
    }
}

/**
 * 删除列表数据
 */
$("#btnDel").click(function () {
    var rows = $('#table').bootstrapTable('getSelections');
    var selected = $.isEmptyObject(rows);
    if(selected){
        layer.alert("请选择删除项");
    }else {
        var ids = "";
        $.each(rows,function (index,value) {
            ids += value.id + ','
        })
        $.ajax({
            type: "post",
            url: $("#delUrl").val(),
            async: true,
            timeout: 9999,
            data: {
                ids: ids,
            },
            dataType:'json',
            success: function(data) {
                if (data.code != "0") {
                    layer.alert(data.msg);
                }
                refreshTable();
            }
        });
    }
});

//input 存在一个被选中状态
$("#table").on('all.bs.table', function(e, row, $element) {
    if ($("tbody input:checked").length >= 1) {
        $(".center .btn-default").attr('disabled', false);
    } else {
        $(".center .btn-default").attr('disabled', true);
    }
});