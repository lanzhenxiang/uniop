<!-- 主页模板 -->
<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div id="maindiv-alert" class="content-list-page"></div>
    <div class="content-operate clearfix">

        <div style="padding-bottom: 31px; border-bottom: 1px solid rgb(157, 157, 157); margin-bottom: 15px;"><span style="font-size: 16px">编排资源清单</span>
        <div style="margin-top: 15px;"><span>业务模板名称&nbsp;:&nbsp;&nbsp;</span><span><?php if(isset($vpcdata)){ echo $vpcdata['biz_temp_name'];} ?></span>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>区域&nbsp;:&nbsp;&nbsp;</span><span><?php if(isset($vpcdata)){ echo $vpcdata['region_name'];} ?></span>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
        </div>
        <div class="pull-left">
            <a  href="<?php echo $this->Url->build(array('controller' => 'BusinessTemplate','action'=>'addecs',$biz_tid)); ?>" class="btn btn-default">添加ECS +</a>
            <a  href="<?php echo $this->Url->build(array('controller' => 'BusinessTemplate','action'=>'index')); ?>" class="btn btn-default">返回列表</a>
        </div>
    </div>
    <table class="table table-striped">
        <thead>
        <tr>
            <th>配置项ID</th>
            <th>配置项</th>
            <th>计算性能</th>
            <th>OS版本</th>
            <th>数量（台）</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        <?php if(isset($vpcdetaildata)){
            foreach($vpcdetaildata as $value){
            ?>
        <tr>
            <td><?php if(isset($value['id'])){ echo $value['id'];} ?></td>
            <td><?php if(isset($value['tagname'])){ echo $value['tagname'];}else{echo '系统预留';} ?></td>
            <td><?php if(isset($value['cpu_number'])){ echo $value['cpu_number'].'核'.$value['memory_gb'].'G';}else{echo '系统预留';} ?></td>
            <td><?php if(isset($value['image_name'])){ echo $value['image_name'];}else{echo '系统预留';} ?></td>
            <td><?php if(isset($value['number'])){ echo $value['number'];}else{echo '系统预留';} ?></td>
            <td><?php if($value['biz_tid'] !=0){ $action = 'addsubnet'; if($value['type']=='ecs' || $value['type']=='desktop' ||$value['type']=='firewall'){ $action = 'addecs'; } ?>
                    <a  href="<?php echo $this->Url->build(array('controller' => 'BusinessTemplate','action'=>$action)); ?>/<?php if(isset($value['biz_tid'])){ echo $value['biz_tid'];} ?>/<?php echo $value['id'] ?>">修改</a> |
                <a id="delete" href="#" onclick="deletes(<?php echo $value['id']; ?>,<?php echo $biz_tid; ?>)" >删除</a><?php } ?></td>
        </tr>
        <?php }} ?>
        </tbody>
    </table>
    <!-- <div class="content-pagination clearfix">
        <nav class="pull-right">
            <ul class="pagination">
                <?php echo $this->Paginator->first('<<');?>
                <?php echo $this->Paginator->prev(' < '); ?>
                <?php echo $this->Paginator->numbers();?>
                <?php echo $this->Paginator->next(' > '); ?>
                <?php echo $this->Paginator->last('>>');?>
            </ul>
        </nav>
    </div> -->
</div>

<div class="modal fade" id="modal-delete" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">提示</h5>
            </div>
            <div class="modal-body">
                <i class="icon-question-sign text-primary"></i>&nbsp;&nbsp;确认要删除该资源清单么？<span class="text-primary" id="sure"></span>？
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="yes">确认</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">取消</button>
            </div>
        </div>
    </div>
</div>
<?=$this->Html->script(['adminjs.js']); ?>
<?= $this->start('script_last'); ?>

<script type="text/javascript">
    $('#search').on('click',function(){
//        var name = $('#searchtext').val();
//        location.href = "<?php //echo $this->Url->build(array('controller'=>'BusinessTemplate','action'=>'index'));?>///index/"+code+'/'+name;
    })
   $(function() {
  /*      var name ="<?php echo $name ?>";
        $('#searchtext').val(name);
        var vpc ="<?php echo $biz_temp_name; ?>";
        $('#vpc').html(vpc);*/
    })
    function deletes(id,biz_tid){
        $('#modal-delete').modal("show");
        $('#yes').one('click',function() {
            $.ajax({
                type: 'post',
                url: '<?php echo $this->Url->build(array('controller' => 'BusinessTemplate','action'=>'deletevpcdetail')); ?>',
                data: {id: id},
                success: function (data) {
                    var data = eval('(' + data + ')');
                    if (data.code == 0) {
                        tentionHide(data.msg, 0);
                        location.href = '<?php echo $this->Url->build(array('controller'=>'BusinessTemplate','action'=>'configure'));?>/'+biz_tid;
                    } else {
                        $('#modal-delete').modal("hide");
                        tentionHide(data.msg, 1);
                    }
                }
            });
        })
    }


</script>
<?= $this->end() ?>