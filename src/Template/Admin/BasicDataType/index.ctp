<!-- 主页模板 -->
<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div class="content-operate clearfix">
        <div class="pull-left">
            <a  href="<?php echo $this->Url->build(array('controller' => 'BasicDataType','action'=>'addedit')); ?>" class="btn btn-default">新增 +</a>
        </div>

        <div class="input-group content-search pull-right">
            <input type="text" class="form-control" placeholder="搜索...">
		  <span class="input-group-btn">
			<button class="btn btn-primary" type="button">搜索</button>
		  </span>
        </div>
    </div>
    <table class="table table-striped">
        <thead>
        <tr>
            <th>类型id</th>
            <th>类型名称</th>
            <th>类型说明</th>
            <th>排序</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        <?php if(isset($data)){
            foreach($data as $value){
                ?>
                <tr>
                    <td><?php if(isset($value['type_id'])){ echo $value['type_id'];} ?></td>
                    <td><?php if(isset($value['type_name'])){ echo $value['type_name'];} ?></td>
                    <td><?php if(isset($value['type_note'])){ echo $value['type_note'];} ?></td>
                    <td><?php if(isset($value['sort_order'])){ echo $value['sort_order'];} ?></td>
                    <td><a  href="<?php echo $this->Url->build(array('controller' => 'BasicDataType','action'=>'addedit')); ?>/<?php if(isset($value['type_id'])){ echo $value['type_id'];} ?>">修改</a> |
                        <a id="delete" href="#" onclick="deletes(<?php echo $value['type_id']; ?>)" >删除</a></td>
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

<?= $this->start('script_last'); ?>
<script type="text/javascript">
    function deletes(id){
        $.ajax({
            type: 'post',
            url: '<?php echo $this->Url->build(array('controller' => 'BasicDataType','action'=>'delete')); ?>',
            data: {id:id},
            success: function(data) {
                var data = eval('(' + data + ')');
                if(data.code==0){
                    alert(data.msg);
                    location.href='<?php echo $this->Url->build(array('controller'=>'BasicDataType','action'=>'index'));?>';
                }else{
                    alert(data.msg);
                }
            }
        });

    }

</script>
<?= $this->end() ?>