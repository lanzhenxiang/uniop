<!-- 主页模板 -->
<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div id="maindiv-alert" class="content-list-page"></div>
    <div class="content-operate clearfix">
        <div class="pull-left">
            <a  href="javascript:void(0);"  onclick="window.location.reload()" class="btn btn-addition"><i
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
    <table class="table table-striped">
        <thead>
        <tr>
            <th><input id="selectAll" type="checkbox" name="checkall" /></th>
            <th>id</th>
            <th>镜像名称</th>
            <th>镜像CODE</th>
            <th>操作系统</th>
            <th>业务分类</th>
            <th>适用范围</th>
            <th>排序</th>
            <th>日单价</th>
            <th>月单价</th>
            <th>年单价</th>
            <th>操作</th>
            <th>创建人</th>
            <th>创建时间</th>
        </tr>
        </thead>
        <tbody>
        <?php if(isset($data)){
            foreach($data as $value){
            ?>
        <tr>
            <td><input type="checkbox" name="id" value="<?=$value['id']?>" /></td>
            <td><?php if(isset($value['id'])){ echo $value['id'];} ?></td>
            <td><?php if(isset($value['image_name'])){ echo $value['image_name'];} ?></td>
            <td><?php if(isset($value['image_code'])){ echo $value['image_code'];} ?></td>
            <td><?php if(isset($value['os_family'])){ echo $value['os_family'];} ?></td>
            <!-- <td><?php if(isset($value['ostype'])){
                switch ($value['ostype']) {
                    case 1:
                        echo 'Linux';
                        break;
                    case 2:
                        echo 'Windows Server';
                        break;
                    case 3:
                        echo 'Windows Desktop';
                        break;

                    default:
                        echo '未知';
                        break;
                }
                } ?></td> -->
            <td><?php if(isset($value['plat_form'])){echo $value['plat_form'];} ?></td>
            <td><?php if(isset($value['image_type'])){
                switch ($value['image_type']) {
                    case 1:
                        echo '云主机';
                        break;
                    case 2:
                        echo '云桌面';
                        break;
                    default:
                        echo '未知';
                        break;
                }
                } ?></td>
            <td><?php if(isset($value['sort_order'])){ echo $value['sort_order'];} ?></td>
            <td>
                <?php if(isset($value['price_day'])){
                echo $this->Number->Currency($value['price_day'],'CNY');
                }else {
                echo "——";
                } ?>
            </td>
            <td><?php if(isset($value['price_month'])){
                echo $this->Number->Currency($value['price_month'],'CNY');
                }else {
                echo "——";
                } ?>
            </td>
            <td><?php if(isset($value['price_year'])){
                echo $this->Number->Currency($value['price_year'],'CNY');
                }else {
                echo "——";
                } ?>
            </td>
            <td style="width:60px;" ><a  href="<?php echo $this->Url->build(array('controller' => 'Imagelist','action'=>'addedit')); ?>/<?php if(isset($value['id'])){ echo $value['id'];} ?>">修改  </a>
            </td>
            <td><?php if(isset($value['creat_by'])){echo $value['account']['username'];} ?></td>
            <td><?php if(isset($value['create_time'])){ echo $this->Time->format($value['create_time'],'yyyy-MM-dd HH:mm:ss',false,'PRC');} ?></td>
        </tr>
        <?php }} ?>
        </tbody>
    </table>
    <div class="content-pagination clearfix">
        <nav class="pull-right">
            <ul class="pagination">
                <?php echo $this->Paginator->first('<<');?>
                <?php echo $this->Paginator->prev(' < '); ?>
                <?php echo $this->Paginator->numbers();?>
                <?php echo $this->Paginator->next(' > '); ?>
                <?php echo $this->Paginator->last('>>');?>
            </ul>
        </nav>
    </div>
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

    $('#search').on('click',function(){
        var name = $('#searchtext').val();
        location.href = "<?php echo $this->Url->build(array('controller'=>'Imagelist','action'=>'index'));?>/index/"+name;
    })
    $(function() {
        var name ="<?php echo $name ?>";
        $('#searchtext').val(name);
    })

    //全选
    $('#selectAll').click(function(){
        var isChecked = $(this).prop("checked");
        $("input[name='id']").prop("checked", isChecked);
        //toggleButton();
    });

    function deleteImages(){
        var isChecked = $("input[name='id']").is(':checked');
        if(isChecked == true){
            showModal('提示',' icon-exclamation-sign','确定删除镜像？','','delAll()');
        }else{
            showModal('提示',' icon-exclamation-sign','请选中你要删除的镜像','','',0);
        }
    }

    function delAll(){

        var ids = '';
        $("input[name='id']").each(function(){
            if($(this).is(":checked")){
                ids += $(this).val()+',';
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
</script>
<?= $this->end() ?>