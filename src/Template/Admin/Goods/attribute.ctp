<!-- 主页模板 -->
<?= $this->element('content_header'); ?>

<style type="text/css">
    .dropdown-menu{
        right: 0;
        left: auto;
    }
</style>
<div class="content-body clearfix">
    <div id="maindiv-alert" class="content-list-page"></div>
    <div class="content-operate clearfix">
        <div class="pull-left">
            <a type="button" href="javascript:;" onclick="refreshTable();" class="btn btn-addition pull-left"><i class="icon-refresh"></i>&nbsp;&nbsp;刷新</a>
            <div class="dropdown pull-left" style="margin-left:10px;">
        <a type="button" href="<?php echo $this->Url->build(array('controller' => 'Goods','action'=>'addattribute')); ?>" class="btn btn-addition pull-left"><i class="icon-plus"></i>&nbsp;&nbsp;新建</a>
    </div>
    <div class="dropdown pull-left" style="margin-left:10px;">
        <a type="button" href="javascript:void(0);" onclick="copyAttribute()" class="btn btn-addition pull-left"><i class="icon-copy"></i>&nbsp;&nbsp;克隆</a>
    </div>
    <div class="dropdown pull-left" style="margin-left:10px;">
        <a type="button" onclick="delattribute();" href="javascript:void(0);" class="btn btn-addition pull-left"><i class="icon-remove"></i>&nbsp;&nbsp;删除</a>
    </div>
            <div class="dropdown pull-left" style="margin-left:200px;">
                厂商
                        <a href="#" class="dropdown-toggle btn btn-addition text-right" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            <span class="pull-left" id="agent" val="">全部</span>
                        <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="javascript:;" onclick="local(0,'','全部')">全部</a></li>
                        <?php if(isset($agent)){
                            foreach($agent as $value) {
                                if ($value['parentid'] == 0) {
                        ?>
                             <li><a href="#" onclick="local(<?php echo $value['id'] ?>,'<?php echo $value['class_code'] ?>','<?php echo $value['agent_name'] ?>')"><?php echo $value['agent_name'] ?></a></li>
                        <?php }}} ?>
                    </ul>
            </div>
            <div class="dropdown pull-left" style="margin-left:30px;">
               地域
                    <a  href="javascript:;" class="dropdown-toggle btn btn-addition text-right" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                        <span class="pull-left" id="agent_t" val="">全部</span>
                        <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu" id="agent_two"></ul>
            </div>
            <!-- <div class="pull-left" style="margin-left:30px;margin-top: 6px">
                <span>当前分类：</span><span id="category_name"><?= $cat_name?></span>
            </div> -->
        </div>
        <div class="input-group content-search pull-right">
            <input type="text" class="form-control" id="txtsearch" placeholder="搜索名称...">
            <span class="input-group-btn">
                <button class="btn btn-primary" id="search" type="button">搜索</button>
            </span>
        </div>
    </div>
    <table class="table table-striped" id="table" data-toggle="table"
      data-pagination="true"
      data-side-pagination="server"
      data-locale="zh-CN"
      data-click-to-select="true"
      data-url="<?= $this->Url->build(['controller'=>'Goods','action'=>'attributelist']); ?>"
      data-unique-id="id">
      <thead>
        <tr>
          <th data-checkbox="true"></th>
          <th data-field="id" data-sortable="true">Id</th>
          <th data-field="attribute_name">名称</th>
          <th data-field="attribute_className">厂商区域</th>
          <th data-field="id" data-formatter="toDoAttribute">操作</th>
          <th data-field="create_time" data-formatter=timestrap2date>创建时间</th>
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
                <i class="icon-question-sign text-primary"></i>&nbsp;&nbsp;<span id="words_d">确认要删除该商品么？</span><span class="text-primary" id="sure"></span>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="yes">确认</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">取消</button>
            </div>
        </div>
    </div>
</div>
<!--复制版本信息 -->
<div class="modal fade" id="modal-copy" tabindex="-1" role="dialog">
       <div class="modal-dialog" role="document">
        <div class="modal-content">
         <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"
          aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h5 class="modal-title">克隆版本信息</h5>
      </div>
        <form id="model-image-from" action="" method="post">
          <div class="modal-body">
            <div class="modal-form-group">
              <label>旧版本名称:</label>
              <div>
                <input id="oldname" name="oldname" type="text" readonly="true" />
              </div>
            </div>
            <div class="modal-form-group">
              <label>新版本名称:</label>
              <div>
                <input id="attribute_name" name="attribute_name" type="text" placeholder="请输入新版本名称"/>
              </div>
            </div>
          </div>
          <div class="modal-footer">
           <input id="attr_id" name="attr_id" type="hidden"  />
           <button id="copySubmit" type="button" class="btn btn-primary">确认</button>
           <button id="" type="button" class="btn btn-danger" data-dismiss="modal">取消</button>
         </div>
       </form>
    </div>
    </div>
    </div>
<div id="maindiv"></div>
<?=$this->Html->script(['adminjs.js']); ?>
<?= $this->start('script_last'); ?>
<script type="text/javascript">
function delattribute(){
    var tot = $('#table').bootstrapTable('getSelections');
    if(tot.length!=0){



        $.ajax({
                type: 'post',
                url: '<?php echo $this->Url->build(array('controller' => 'Goods','action'=>'delattribute')); ?>',
                dataType: "json",
                data: {table: tot},
                success: function (data) {
                    if(data.if==1){
//                        alert('有商品引用该版本,无法删除');
                        $('.icon-question-sign').hide();
                    $('#words_d').html('有商品引用该版本,无法删除');
                        $('#yes').hide();
                        $('#modal-delete').modal("show");
                    }else{
                        if (data.code == 0) {
                            refreshTable();
                        } else {
                            alert("删除失败");
                        }
                    }


                }
            });
    }
}

/**
 * [copyAttribute 复制版本信息]
 * @return {[type]} [description]
 */
function copyAttribute(){
    var id = 0;
    var name ='';
    $("input[name='btSelectItem']:checkbox").each(function() {
            if ($(this)[0].checked == true) {
                //alert($(this).val());
                id = $(this).parent().parent().attr('data-uniqueid');
                var row = $('#table').bootstrapTable('getRowByUniqueId', id);
                name = row.attribute_name;
            }
    });
    if(id == 0){
        showModal('提示', 'icon-exclamation-sign', '请选中一条版本信息', '', '', 0);
    }else{
        $('#oldname').val(name);
        $("#attr_id").val(id);
        $('#modal-copy').modal("show");
    }
}
/**
 * [复制操作]
 */
$('#copySubmit').on('click', function() {
    var attribute_name = $('#attribute_name').val();
    var id = $('#attr_id').val();
    $.ajax({
        type: 'post',
        url:'/admin/Goods/copyAttribute',
        dataType: "json",
        data: {
            attribute_name: attribute_name,
            id:id
        },
        success: function(data) {
            $('#modal-copy').modal("hide");
            refreshTable();
           // showModal('提示', 'icon-exclamation-sign', data.msg, '', , 0);
        }
    });
});

//动态创建modal

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

        html +='</div>';
        html += '<div class="modal-footer"><button id="btnModel_ok" onclick="' + method + '" type="button" class="btn btn-primary">确认</button><button type="button" class="btn btn-danger" id="btnEsc" data-dismiss="modal">取消</button></div></div></div></div>';
        $("#maindiv").append(html);
        if (type == 0) {
            $("#btnModel_ok").remove();
        }
        $('#modal').modal("show");
    }


 function refreshTable() {
        var search = $("#txtsearch").val();
        var class_code = $("#agent").attr('val');
        var class_code2 = $("#agent_t").attr('val');
        $('#table').bootstrapTable('refresh', {
            url :　"<?= $this->Url->build(['controller'=>'Goods','action'=>'attributelist']); ?>?search=" + search + '&class_code=' + class_code + '&class_code2=' + class_code2
        });
    }

function toDoAttribute(value,row,index){
    if(value!=null&&value!=""){
        return '<a href="/admin/goods/editattribute/' + row.id + '">修改</a>';
    }else{
        return "-";
    }
}
//时间戳转换日期格式
  function timestrap2date(value){
    var now = new Date(parseInt(value) * 1000);
    return now.toLocaleString().replace(/年|月/g, "-").replace(/日/g, " ");
  }

    //地域查询

    function local(id, class_code, agent_name) {
        if (agent_name) {
            $('#agent_t').html('全部');
            $('#agent').html(agent_name);
            $('#agent').attr('val', class_code);
            var search = $("#txtsearch").val();
            $('#table').bootstrapTable('refresh', {
                url: "<?= $this->Url->build(['controller'=>'Goods','action'=>'attributelist']); ?>",
                query: {
                    class_code: class_code,
                    search: search,
                    department_id:$("#txtdeparmetId").val()
                }
            });
            var url = "<?= $this->Url->build(['controller'=>'Goods','action'=>'attributelist']); ?>?search=" + search + '&class_code=' + class_code +'&department_id='+$("#txtdeparmetId").val();
            $('#table').attr('data-url', url);
            var jsondata = <?php echo json_encode($agent); ?>;

            if (id != 0) {
                var data = '<li><a href="javascript:;" onclick="local_two(\'\',\'全部\',\'' + class_code + '\')">全部</a></li>';
                $.each(jsondata, function(i, n) {
                    if (n.parentid == id) {
                        data += '<li><a href="#" onclick="local_two(\'' + n.class_code + '\',\'' + n.agent_name + '\',\'' + class_code + '\')">' + n.agent_name + '</a></li>';
                    }
                });
                $('#agent_two').html(data);
            } else {
                data = '';
                $('#agent_t').attr('val', data);
                $('#agent_two').html(data);
            }
        }
    }

    function local_two(class_code2, agent_name, class_code) {
        var search = $("#txtsearch").val();
        $('#agent_t').html(agent_name);
        $('#agent_t').attr('val', class_code2);
        $('#table').bootstrapTable('refresh', {
            url: "<?= $this->Url->build(['controller'=>'Goods','action'=>'attributelist']); ?>",
            query: {
                class_code2: class_code2,
                class_code: class_code,
                search: search,
                department_id:$("#txtdeparmetId").val()
            }
        });
        var url = "<?= $this->Url->build(['controller'=>'Goods','action'=>'attributelist']); ?>?search=" + search + '&class_code=' + class_code + '&class_code2=' + class_code2+'&department_id='+$("#txtdeparmetId").val();
        $('#table').attr('data-url', url);
    }
</script>
<?= $this->end() ?>