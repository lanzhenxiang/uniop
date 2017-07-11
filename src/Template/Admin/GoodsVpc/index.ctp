<!-- 主页模板 -->
<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div id="maindiv-alert" class="content-list-page"></div>
    <div class="content-operate clearfix">

        <div class="pull-left">
            <a  href="<?php echo $this->Url->build(array('controller' => 'GoodsVpc','action'=>'addedit')); ?>" class="btn btn-default">新增基本信息 +</a>
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
            <th>创建时间</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        <?php if(isset($data)){
            foreach($data as $value){
            ?>
        <tr>
            <td><?php if(isset($value['vpc_id'])){ echo $value['vpc_id'];} ?></td>
            <td><?php if(isset($value['vpc_name'])){ echo $value['vpc_name'];} ?></td>
            <td><?php if(isset($value['region_name'])){ echo explode('-',$value['region_name'])[0];} ?></td>
            <td><?php if(isset($value['region_name'])){ echo explode('-',$value['region_name'])[1];} ?></td>
            <td><?php if(isset($value['create_time'])){ echo date('Y-m-d H:i:s',$value['create_time']);} ?></td>
            <td><a  href="<?php echo $this->Url->build(array('controller' => 'GoodsVpc','action'=>'addedit')); ?>/<?php if(isset($value['vpc_id'])){ echo $value['vpc_id'];} ?>">修改基本信息</a> |
                <a  href="<?php echo $this->Url->build(array('controller' => 'GoodsVpc','action'=>'configure')); ?>/<?php if(isset($value['vpc_id'])){ echo $value['vpc_id'];} ?>">配置单设置</a> |
                <a  href="javascript:;" onclick="copy_vpc(<?php echo $value['vpc_id']; ?>)" >复制</a> |
                <a id="delete" href="javascript:;" onclick="deletes(<?php echo $value['vpc_id']; ?>)" >删除</a></td>
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
                <i class="icon-question-sign text-primary"></i>&nbsp;&nbsp;确认要删除该vpc配置单么？<span class="text-primary" id="sure"></span>？
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
                <i class="icon-question-sign text-primary"></i>&nbsp;&nbsp;确认要复制该vpc配置单么？<span class="text-primary" id="sure"></span>？
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="copy">确认</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">取消</button>
            </div>
        </div>
    </div>
</div>
<?=$this->Html->script(['adminjs.js']); ?>
<?= $this->start('script_last'); ?>

<script type="text/javascript">

    function deletes(vpc_id){
        $('#modal-delete').modal("show");
        $('#yes').one('click',function() {
            $.ajax({
                type: 'post',
                url: '<?php echo $this->Url->build(array('controller' => 'GoodsVpc','action'=>'deletevpc')); ?>',
                data: {vpc_id: vpc_id},
                success: function (data) {
                    var data = eval('(' + data + ')');
                    if (data.code == 0) {
                        tentionHide(data.msg, 0);
                        location.href = '<?php echo $this->Url->build(array('controller'=>'GoodsVpc','action'=>'index'));?>';
                    } else {
                        $('#modal-delete').modal("hide");
                        tentionHide(data.msg, 1);
                    }
                }
            });
        })
    }


    function copy_vpc(vpc_id){
        $('#modal-copy').modal("show");
        $('#copy').one('click',function() {
            $.ajax({
                type: 'post',
                url: '<?php echo $this->Url->build(array('controller' => 'GoodsVpc','action'=>'copyvpc')); ?>',
                data: {vpc_id: vpc_id},
                success: function (data) {
                    var data = eval('(' + data + ')');
                    if (data.code == 0) {
                        tentionHide(data.msg, 0);
                        location.href = '<?php echo $this->Url->build(array('controller'=>'GoodsVpc','action'=>'index'));?>';
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