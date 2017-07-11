<!-- 主页模板 -->
<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div class="content-operate clearfix">
        <div class="pull-left">
            <a  href="<?php echo $this->Url->build(array('controller' => 'Tenants','action'=>'addedit')); ?>" class="btn btn-default">新增 +</a>
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
            <th>序列号</th>
            <th>用户名</th>
            <th>手机号</th>
            <th>电子邮箱</th>
            <th>接入密钥</th>
            <th>创建人</th>
            <th>创建时间</th>
            <th>修改时间</th>
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
            <td><?php if(isset($value['phone'])){ echo $value['phone'];} ?></td>
            <td><?php if(isset($value['email'])){ echo $value['email'];} ?></td>
            <td><?php if(isset($value['access_key'])){ echo $value['access_key'];} ?></td>
            <td><?php if(isset($value['account'])){ echo $value['account']['username'];} ?></td>
            <td><?php if(isset($value['create_time'])){ echo date('Y-m-d H:i:s',$value['create_time']);} ?></td>
            <td><?php if(isset($value['modify_time'])){ echo date('Y-m-d H:i:s',$value['modify_time']);} ?></td>
            <td><a  href="<?php echo $this->Url->build(array('controller' => 'Tenants','action'=>'addedit')); ?>/<?php if(isset($value['id'])){ echo $value['id'];} ?>">修改</a> |
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
            url: '<?php echo $this->Url->build(array('controller' => 'Tenants','action'=>'delete')); ?>',
            data: {id:id},
            success: function(data) {
                var data = eval('(' + data + ')');
                if(data.code==0){
                    alert(data.msg);
                    location.href='<?php echo $this->Url->build(array('controller'=>'Tenants','action'=>'index'));?>';
                }else{
                    alert(data.msg);
                }
            }
        });

    }

</script>
<?= $this->end() ?>