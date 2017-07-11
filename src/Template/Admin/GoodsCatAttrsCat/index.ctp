<!-- 主页模板 -->
<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div class="content-operate clearfix">
        <div class="pull-left">
            <a  href="<?php echo $this->Url->build(array('controller' => 'GoodsCatAttrsCat','action'=>'addedit')); ?>" class="btn btn-addition"><i
        class="icon-plus"></i>&nbsp;&nbsp;新建</a>
        </div>
    </div>
    <table class="table table-striped">
        <thead>
        <tr>
            <th>属性id</th>
            <th>分类属性名称</th>
            <th>属性标签</th>
            <th>所属分类</th>
            <th>排序</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        <?php if(isset($data)){
            foreach($data as $value){
            ?>
        <tr>
            <td><?php if(isset($value['id'])){ echo $value['id'];} ?></td>
            <td><?php if(isset($value['name'])){ echo $value['name'];} ?></td>
            <td><?php if(isset($value['label'])){ echo $value['label'];} ?></td>
            <td><?php if(isset($value['goods_category'])){ echo $value['goods_category']['name'];} ?></td>
            <td><?php if(isset($value['sort_order'])){ echo $value['sort_order'];} ?></td>
            <td><a  href="<?php echo $this->Url->build(array('controller' => 'GoodsCatAttrsCat','action'=>'addedit')); ?>/<?php if(isset($value['id'])){ echo $value['id'];} ?>">修改</a> |
                <a id="delete" href="#" onclick="deletes(<?php echo $value['id']; ?>)" >删除</a></td>
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
            url: '<?php echo $this->Url->build(array('controller' => 'GoodsCatAttrsCat','action'=>'delete')); ?>',
            data: {id:id},
            success: function(data) {
                var data = eval('(' + data + ')');
                if(data.code==0){
                    alert(data.msg);
                    location.href='<?php echo $this->Url->build(array('controller'=>'GoodsCatAttrsCat','action'=>'index'));?>';
                }else{
                    alert(data.msg);
                }
            }
        });

    }

</script>
<?= $this->end() ?>