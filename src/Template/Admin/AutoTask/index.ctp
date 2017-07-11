<!-- 主页模板 -->
<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div id="maindiv-alert" class="content-list-page"></div>
    <div class="content-operate clearfix">
        <div class="pull-left">
            <a  href="<?php echo $this->Url->build(array('controller' => 'AutoTask','action'=>'addedit','0')); ?>" class="btn btn-addition pull-left"><i class="icon-plus"></i>&nbsp;&nbsp;新建</a>
        </div>

        <div class="input-group content-search pull-right">
            <input type="text" class="form-control" id="searchtext" placeholder="搜索任务名称...">
             <span class="input-group-btn">
                 <button class="btn btn-primary" id="search" type="button">搜索</button>
             </span>
        </div>
    </div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>任务名称</th>
               <!-- <th>任务URL</th>-->
                <th>任务类型</th>
                <th>开始日期</th>
                <th>下次执行时间</th>
                <th>间隔时间</th>
                <th>结束日期</th>
                <th>任务状态</th>
                <th>任务说明</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php if(isset($data)){
                foreach($data as $value){
                    ?>
                    <tr>

                        <td><?php if(isset($value['task_name'])){ echo $value['task_name'];} ?></td>
                       <!-- <td><?php /*if(isset($value['task_url'])){ echo $value['task_url'];} */?></td>-->
                        <td><?php if(isset($value['task_type'])){
                            switch($value['task_type']){
                                case 1:
                                    echo '一次性任务';break;
                                case 2:
                                    echo '每天';break;
                                case 3:
                                    echo '每周';break;
                                case 4:
                                    echo '每月';break;
                            }
                         } ?></td>
                        <td><?php if(isset($value['begin_time'])){ echo date('Y-m-d H:i:s',$value['begin_time']);} ?></td>
                        <td><?php if(isset($value['next_begin_time'])){ echo date('Y-m-d H:i:s',$value['next_begin_time']);} ?></td>
                        <td><?php if(isset($value['task_interval'])){ echo $value['task_interval'].'秒';} ?></td>
                        <td><?php if(isset($value['end_time'])){ echo date('Y-m-d H:i:s',$value['end_time']);} ?></td>
                        <td><?php if(isset($value['task_status'])){ if($value['task_status']==0){ echo '挂起';}else{ echo '启用';}} ?></td>
                        <td><?php if(isset($value['end_time'])){ echo $value['task_note'];} ?></td>
                        <td>
                            <a  href="<?php echo $this->Url->build(array('controller' => 'AutoTask','action'=>'check')); ?>/<?php if(isset($value['id'])){ echo $value['id'];} ?>">详细信息</a> |
                            <a  href="<?php echo $this->Url->build(array('controller' => 'AutoTask','action'=>'addedit')); ?>/<?php if(isset($value['id'])){ echo $value['id'];} ?>">修改</a> |
                            <a id="delete" href="#" onclick="deletes(<?php echo $value['id']; ?>)" >删除</a>
                        </td>
                    </tr>
                    <?php
                }
            } ?>
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
                <i class="icon-question-sign text-primary"></i>&nbsp;&nbsp;确认要删除该自动计划任务么？<span class="text-primary" id="sure"></span>？
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
        var name = $('#searchtext').val();
        location.href = "<?php echo $this->Url->build(array('controller'=>'AutoTask','action'=>'index'));?>/index/"+name;
    })
    $(function() {
        var name ="<?php echo $name ?>";
        $('#searchtext').val(name);
    })
    function deletes(id){
        $('#modal-delete').modal("show");
        $('#yes').one('click',function() {
            $.ajax({
                type: 'post',
                url: '<?php echo $this->Url->build(array('controller' => 'AutoTask','action'=>'deletes')); ?>',
                data: {id: id},
                success: function (data) {
                    var data = eval('(' + data + ')');
                    if (data.code == 0) {
                        tentionHide(data.message,0);
                        location.href = '<?php echo $this->Url->build(array('controller'=>'AutoTask','action'=>'index'));?>';
                    } else {
                        $('#modal-delete').modal("hide");
                        tentionHide(data.message, 1);
                    }
                }
            });
        })
    }

</script>
<?= $this->end() ?>