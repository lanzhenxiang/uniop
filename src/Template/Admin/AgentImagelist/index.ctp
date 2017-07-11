<!-- 主页模板 -->
<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div class="content-operate clearfix">
        <div class="pull-left">
            <a  href="<?php echo $this->Url->build(array('controller' => 'AgentImagelist','action'=>'addedit')); ?>" class="btn btn-default">新增 +</a>
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
            <th>id</th>
            <th>机房名称</th>
            <th>镜像名称</th>
            <th>镜像代码</th>
            <th>操作系统</th>
            <th>镜像分类（业务）</th>
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
            <td><?php if(isset($value['agent'])){ echo $value['agent']['display_name'];} ?></td>
            <td><?php if(isset($value['image_name'])){ echo $value['image_name'];} ?></td>
            <td><?php if(isset($value['image_code'])){ echo $value['image_code'];} ?></td>
            <td><?php if(isset($value['ostype'])){ 
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
                        # code...
                        break;
                }
                } ?></td>
            <td><?php if(isset($value['plat_form'])){echo $value['plat_form'];} ?></td>
            <td><?php if(isset($value['sort_order'])){ echo $value['sort_order'];} ?></td>
            <td><a  href="<?php echo $this->Url->build(array('controller' => 'AgentImagelist','action'=>'addedit')); ?>/<?php if(isset($value['id'])){ echo $value['id'];} ?>">修改</a> |
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
            url: '<?php echo $this->Url->build(array('controller' => 'AgentImagelist','action'=>'dele')); ?>',
            data: {id:id},
            success: function(data) {
                var data = eval('(' + data + ')');
                if(data.code==0){
                    alert(data.msg);
                    location.href='<?php echo $this->Url->build(array('controller'=>'AgentImagelist','action'=>'index'));?>';
                }else{
                    alert(data.msg);
                }
            }
        });

    }

</script>
<?= $this->end() ?>