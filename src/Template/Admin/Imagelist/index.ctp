<!-- 主页模板 -->
<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div id="maindiv-alert" class="content-list-page"></div>
    <div class="content-operate clearfix">
        <div class="pull-left">
            <a  href="javascript:void(0);"  onclick="refreshTable()" class="btn btn-addition"><i
                    class="icon-refresh"></i>&nbsp;&nbsp;刷新</a>
            <a  href="<?php echo $this->Url->build(array('controller' => 'Imagelist','action'=>'addedit')); ?>" class="btn btn-addition"><i
                    class="icon-plus"></i>&nbsp;&nbsp;新建</a>
            <a  href="javascript:void(0);" id="delete-btn" onclick="deleteImages()" class="btn btn-addition"><i
                    class="icon-remove"></i>&nbsp;&nbsp;删除</a>
        </div>
        <div class="input-group content-search pull-right">
            <input type="text" class="form-control" id="searchtext" placeholder="搜索镜像名称或代码...">
             <span class="input-group-btn">
                 <button class="btn btn-primary" id="search" type="button">搜索</button>
             </span>
        </div>
    </div>


    <table id="table" class="table table-striped " style="text-align: center" data-toggle="table"
           data-pagination="true" data-sortable="false"
           data-side-pagination="server"
           data-locale="zh-CN"
           data-click-to-select="true"

           data-unique-id="id">
        <thead>
        <tr>
            <th data-checkbox="true"></th>
            <th data-field="id" data-sortable="true">Id</th>
            <th data-field="image_name" data-sortable="true" >镜像名称</th>
            <th data-field="image_code">镜像CODE</th>
            <th data-field="os_family">操作系统</th>
            <th data-field="plat_form">业务分类</th>
            <th data-field="image_type" data-formatter="formatter_type">适用范围</th>
            <th data-field="sort_order" >排序</th>
            <th data-field="price_day" data-formatter="formatter_price">日单价</th>
            <th data-field="price_month" data-formatter="formatter_price">月单价</th>
            <th data-field="price_year" data-formatter="formatter_price">年单价</th>
            <th data-field="id" data-formatter="formatter_operate">操作</th>
            <th data-field="account" data-formatter="formatter_account">创建人</th>
            <th data-field="create_time" data-formatter='timestrap2date'>创建时间</th>
        </tr>
        </thead>
    </table>




</div>

<div class="modal fade" id="modal-delete" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">提示</h5>
            </div>
            <div class="modal-body">
                <i class="icon-question-sign text-primary"></i>&nbsp;&nbsp;确认要删除该镜像么？<span class="text-primary" id="sure"></span>？
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="yes">确认</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">取消</button>
            </div>
        </div>
    </div>
</div>
<div id="maindiv"></div>
<?=$this->Html->script(['adminjs.js']); ?>
<?= $this->start('script_last'); ?>
<script type="text/javascript">

//    $('#search').on('click',function(){
//        var name = $('#searchtext').val();
//        location.href = "<?php echo $this->Url->build(array('controller'=>'Imagelist','action'=>'index'));?>/index/"+name;
//    })
//    $(function() {
//        var name ="<?php echo $name ?>";
//        $('#searchtext').val(name);
//    })
//    //刷新
//    function refreshTable(){
//        var searchtext = $("#searchtext").val();
//        var list_url = "/admin/Imagelist/lists/" + searchtext ;
//        $('#table').bootstrapTable('refresh', {
//            url :list_url
//        });
//    }
$(function(){
    searchs();
});
function searchs(){
    var name = $('#searchtext').val();
    var list_url = "/admin/Imagelist/lists/" + name ;
    $('#table').bootstrapTable('refresh', {
        url :list_url
    });
}

//搜索
$('#search').on('click',function(){
 searchs();
});
    //全选
    $('#selectAll').click(function(){
        var isChecked = $(this).prop("checked");
        $("input[name='id']").prop("checked", isChecked);
        //toggleButton();
    });

    function deleteImages(){
        var isChecked = $("input[name='btSelectItem']").is(':checked');
        if(isChecked == true){
            //是否关联厂商地域
            var ids = '';
            $("input[name='btSelectItem']:checkbox").each(function(){
                if($(this)[0].checked == true){
                    var id = $(this).parent().parent().attr('data-uniqueid');
                    ids += id+',';
                }
            });
            $.getJSON("/admin/Imagelist/getAgent?ids="+ids,function(data){
                if(data.code==0){
                    showModal('提示',' icon-exclamation-sign','确定删除镜像？','','delAll()');
                }else{
                    showModal('提示',' icon-exclamation-sign',data.msg+'<br>&#160;&#160;&#160;&#160;&#160;确定删除镜像？','','delAll()');
                }
            });


        }else{
            showModal('提示',' icon-exclamation-sign','请选中你要删除的镜像','','',0);
        }
    }

    function delAll(){

        var ids = '';
        $("input[name='btSelectItem']:checkbox").each(function(){
            if($(this)[0].checked == true){
                var id = $(this).parent().parent().attr('data-uniqueid');
                ids += id+',';
            }
        });
        $.ajax({
            type: 'post',
            url: '<?php echo $this->Url->build(array('controller' => 'Imagelist','action'=>'deleAll')); ?>',
            data: {ids: ids},
            success: function (data) {
                $('.modal').modal('hide');
                var data = eval('(' + data + ')');
                if (data.code == 0) {
                    tentionHide(data.msg, 0);
                    location.href = '<?php echo $this->Url->build(array('controller'=>'Imagelist','action'=>'index'));?>';
                } else {
                    $('#modal-delete').modal("hide");
                    tentionHide(data.msg, 1);
                }
            }
        });
    }

    function deletes(id){
        $('#modal-delete').modal("show");
        $('#yes').one('click',function() {
            $.ajax({
                type: 'post',
                url: '<?php echo $this->Url->build(array('controller' => 'Imagelist','action'=>'dele')); ?>',
                data: {id: id},
                success: function (data) {
                    var data = eval('(' + data + ')');
                    if (data.code == 0) {
                        tentionHide(data.msg, 0);
                        location.href = '<?php echo $this->Url->build(array('controller'=>'Imagelist','action'=>'index'));?>';
                    } else {
                        $('#modal-delete').modal("hide");
                        tentionHide(data.msg, 1);
                    }
                }
            });
        })
    }


    function showModal(title, icon, content, content1, method, type, delete_info) {
        $("#maindiv").empty();

        var html = "";
        html += '<div class="modal fade" id="modal" tabindex="-1" role="dialog">';
        html += '<div class="modal-dialog" role="document">';
        html += '<div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
        html += '<h5 class="modal-title">' + title + '</h5>';
        html += '</div><div class="modal-body"><i class="'+icon+' icon-color-gray text-primary"></i>&nbsp;&nbsp;' + content + '<span class="text-primary batch-warning">' + content1 + '</span>';
        if(delete_info == 1){
            html +='<br /><i class="icon-exclamation-sign icon-color-gray text-primary"></i>&nbsp;&nbsp;<span > 可在回收站找回已删除的主机</span><span class=" text-primary batch-warning" id="modal-dele-name"></span>';
        }
        var cancelLabel = '取消';
        if(type == 0){
            cancelLabel = '关闭';
        }
        html +='</div>';
        html += '<div class="modal-footer"><button id="btnModel_ok" onclick="' + method + '" type="button" class="btn btn-primary">确认</button><button type="button" class="btn btn-danger" id="btnEsc" data-dismiss="modal">'+ cancelLabel +'</button></div></div></div></div>';
        $("#maindiv").append(html);
        if (type == 0) {
            $("#btnModel_ok").remove();
        }
        $('#modal').modal("show");
    }




    function formatter_type(value){
        if(value==1){
            return '云主机';
        }else if(value==2){
            return '云桌面';
        }else if(value==4){
         return '云主机与云桌面';
        }else{
            return '未知';
        }

    }
    function formatter_price(value){
        if(value!=''){
            return '￥'+Number(value).toFixed(2);
        }else{
            return '￥0.00';
        }
    }
    function formatter_operate(value){
        var url = '<a class="btn" href="/admin/Imagelist/addedit/'+value+'">修 改</a>';
        return url;
    }
    function formatter_account(value){
        if(value !== null && typeof(value) == 'object'){
            return value.username;
        }else{
            return '--';
        }
    }
    function timestrap2date(value){
        if(value === null){
            return '--';
        }
        var now = new Date(parseInt(value) * 1000);
        return now.toLocaleString().replace(/年|月/g, "-").replace(/日/g, " ");
    }


</script>
<?= $this->end() ?>