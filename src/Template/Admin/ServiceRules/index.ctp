<!-- 主页模板 -->
<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div id="maindiv-alert" class="content-list-page"></div>
    <div class="content-operate clearfix">
        <div class="pull-left">
            <a  href="<?php echo $this->Url->build(array('controller' => 'ServiceRules','action'=>'addedit')); ?>" class="btn btn-default"><i class="icon-plus"></i>&nbsp;&nbsp;新建</a>
        </div>
        <div class="input-group content-search pull-right">
            <input type="text" class="form-control" id="searchtext" placeholder="搜索服务名...">
             <span class="input-group-btn">
                 <button class="btn btn-primary" id="search" type="button">搜索</button>
             </span>
        </div>
    </div>
    <table class="table table-striped">
        <thead>
        <tr>
            <th>id</th>
            <th>服务名称</th>
            <th>弹性类型</th>
            <th>计算规则</th>
            <th>权重</th>
            <th>规则说明</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        <?php if(isset($data)){
            foreach($data as $value){
            ?>
        <tr>
            <td><?php if(isset($value['id'])){ echo $value['id'];} ?></td>
            <td><?php if(isset($value['service_type']['service_name'])){ echo $value['service_type']['service_name'];} ?></td>
            <td><?php if(isset($value['action_type'])){ 
                switch ($value['action_type']) {
                    case '1':
                        echo '关闭现有服务';
                        break;
                    case '2':
                        echo '开启新服务';
                        break;
                }
                } ?></td>
            <td><?php if(isset($value['rule_expression'])){ echo $value['rule_expression'];} ?></td>
            <td><?php if(isset($value['rule_weight'])){ echo $value['rule_weight'];} ?></td>
            <td><?php if(isset($value['rule_note'])){ echo $value['rule_note'];} ?></td>
            <td><a  href="<?php echo $this->Url->build(array('controller' => 'ServiceRules','action'=>'addedit')); ?>/<?php if(isset($value['id'])){ echo $value['id'];} ?>">修改</a> |
                <a id="delete" href="#" onclick="deletes(<?= $value['id']?>)" >删除</a></td>
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
                <i class="icon-question-sign text-primary"></i>&nbsp;&nbsp;确认要删除该弹性规则么？<span class="text-primary" id="sure"></span>？
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
        location.href = "<?php echo $this->Url->build(array('controller'=>'ServiceRules','action'=>'index'));?>/index/"+name;
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
                url: '<?php echo $this->Url->build(array('controller' => 'ServiceRules','action'=>'delete')); ?>',
                data: {id: id},
                success: function (data) {
                    var data = eval('(' + data + ')');
                    if (data.code == 0) {
                        tentionHide(data.msg, 0);
                        location.href = '<?php echo $this->Url->build(array('controller'=>'ServiceRules','action'=>'index'));?>';
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