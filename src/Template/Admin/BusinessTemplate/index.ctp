<!-- 主页模板 -->
<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div id="maindiv-alert" class="content-list-page"></div>
    <div class="content-operate clearfix">

        <div class="pull-left">
            <a  href="<?php echo $this->Url->build(array('controller' => 'BusinessTemplate','action'=>'addedit')); ?>" class="btn btn-default">新增基本信息 +</a>
        </div>

       <!-- <div class="input-group content-search pull-right">
            <input type="text" class="form-control" id="searchtext" placeholder="搜索用户名...">
             <span class="input-group-btn">
                 <button class="btn btn-primary" id="search" type="button">搜索</button>
             </span>
        </div>-->

    </div>
    <table class="table table-striped">
        <thead>
        <tr>
            <th>id</th>
            <th>名称</th>
            <th>厂商</th>
            <th>区域</th>
            <th>版本</th>
            <th>系统规模</th>
            <th>模板状态</th>
            <th>创建时间</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        <?php if(isset($data)){
            foreach($data as $value){
            ?>
        <tr>
            <td><?php if(isset($value['biz_tid'])){ echo $value['biz_tid'];} ?></td>
            <td><?php if(isset($value['biz_temp_name'])){ echo $value['biz_temp_name'];} ?></td>
            <td><?php if(isset($value['region_name'])){ echo explode('-',$value['region_name'])[0];} ?></td>
            <td><?php if(isset($value['region_name'])){ echo explode('-',$value['region_name'])[1];} ?></td>
            <td><?php if(isset($value['version'])){ echo $value['version'];} ?></td>
            <td><?php if(isset($value['system_level'])){ echo $value['system_level'];} ?></td>
            <td><?php if(isset($value['status'])){ echo $value['status'];} ?></td>
            <td><?php if(isset($value['create_time'])){ echo date('Y-m-d H:i:s',$value['create_time']);} ?></td>
            <td><a  href="<?php echo $this->Url->build(array('controller' => 'BusinessTemplate','action'=>'addedit')); ?>/<?php if(isset($value['biz_tid'])){ echo $value['biz_tid'];} ?>">修改基本信息</a> |
                <a  href="<?php echo $this->Url->build(array('controller' => 'BusinessTemplate','action'=>'configure')); ?>/<?php if(isset($value['biz_tid'])){ echo $value['biz_tid'];} ?>">资源清单设置</a> |
                <a  href="javascript:;" onclick="copy_biz(<?php echo $value['biz_tid']; ?>)" >复制</a> |
                <a id="delete" href="javascript:;" onclick="deletes(<?php echo $value['biz_tid']; ?>)" >删除</a>|
                <a id="push" href="javascript:;" onclick="push(<?php echo $value['biz_tid']; ?>)" >发布</a>|
                <a id="unpush" href="javascript:;" onclick="unpush(<?php echo $value['biz_tid']; ?>)" >取消发布</a>
                </td>
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
                <i class="icon-question-sign text-primary"></i>&nbsp;&nbsp;确认要删除该模板资源清单么？<span class="text-primary" id="sure"></span>？
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="yes">确认</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">取消</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-copy" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">提示</h5>
            </div>
            <div class="modal-body">
                <i class="icon-question-sign text-primary"></i>&nbsp;&nbsp;确认要复制该模板资源清单么？<span class="text-primary" id="sure"></span>？
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="copy">确认</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">取消</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-push" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">提示</h5>
            </div>
            <div class="modal-body">
                <i class="icon-question-sign text-primary"></i>&nbsp;&nbsp;<span id="push-txt">确认要发布该业务模板么？</span><span class="text-primary" id="sure"></span>？
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="commit">确认</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">取消</button>
            </div>
        </div>
    </div>
</div>
<?=$this->Html->script(['adminjs.js']); ?>
<?= $this->start('script_last'); ?>

<script type="text/javascript">

    function deletes(biz_tid){
        $('#modal-delete').modal("show");
        $('#yes').one('click',function() {
            $.ajax({
                type: 'post',
                url: '<?php echo $this->Url->build(array('controller' => 'BusinessTemplate','action'=>'deleteBusinessTemplate')); ?>',
                data: {biz_tid: biz_tid},
                success: function (data) {
                    var data = eval('(' + data + ')');
                    if (data.code == 0) {
                        tentionHide(data.msg, 0);
                        location.href = '<?php echo $this->Url->build(array('controller'=>'BusinessTemplate','action'=>'index'));?>';
                    } else {
                        $('#modal-delete').modal("hide");
                        tentionHide(data.msg, 1);
                    }
                }
            });
        })
    }


    function copy_biz(biz_tid){
        $('#modal-copy').modal("show");
        $('#copy').one('click',function() {
            $.ajax({
                type: 'post',
                url: '<?php echo $this->Url->build(array('controller' => 'BusinessTemplate','action'=>'copyBusinessTemplate')); ?>',
                data: {biz_tid: biz_tid},
                success: function (data) {
                    var data = eval('(' + data + ')');
                    if (data.code == 0) {
                        tentionHide(data.msg, 0);
                        location.href = '<?php echo $this->Url->build(array('controller'=>'BusinessTemplate','action'=>'index'));?>';
                    } else {
                        $('#modal-delete').modal("hide");
                        tentionHide(data.msg, 1);
                    }
                }
            });
        })
    }
    //模板发布
    function push(biz_tid){
        $('#modal-push').modal("show");
        commit(biz_tid,'push');
    }
    //模板取消发布
    function unpush(biz_tid){
        $('#modal-push').modal("show");
        $('#push-txt').html("确认取消发布该业务模板？")
        commit(biz_tid,'unpush');
    }
    function commit(biz_tid,action){
        $('#commit').one('click',function() {
            $.ajax({
                type: 'post',
                url: '<?php echo $this->Url->build(array('controller' => 'BusinessTemplate','action'=>'statusTaggle')); ?>',
                data: {biz_tid: biz_tid,action:action},
                success: function (data) {
                    var data = eval('(' + data + ')');
                    if (data.code == 0) {
                        tentionHide(data.msg, 0);
                        location.href = '<?php echo $this->Url->build(array('controller'=>'BusinessTemplate','action'=>'index'));?>';
                    } else {
                        $('#modal-push').modal("hide");
                        tentionHide(data.msg, 1);
                    }
                }
            });
        })
    }

</script>
<?= $this->end() ?>