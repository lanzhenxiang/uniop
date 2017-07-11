<!-- 主页模板 -->
<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div class="content-operate clearfix">
        <div class="pull-left">
            <a  href="<?php echo $this->Url->build(array('controller' => 'AgentSet','action'=>'addedit')); ?>" class="btn btn-default">新增 +</a>
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
            <th>供应商或地域名称</th>
            <th>套餐名称</th>
            <th>套餐代码</th>
            <th>CPU数</th>
            <th>内存大小</th>
            <th>机器类型</th>
            <th>操作</th>
        </tr>
    </thead>
    <tbody>
        <?php if(isset($data)){ ?>
           <?php foreach($data as $value){ ?>
                <tr>
                    <td><?php if(isset($value['set_id'])){ echo $value['set_id'];} ?></td>
                    <td><?php if(isset($value['agent'])){ echo $value['agent']['agent_name'];} ?></td>
                    <td><?php if(isset($value['set_name'])){ echo $value['set_name'];} ?></td>
                    <td><?php if(isset($value['set_type_code'])){ echo $value['set_type_code'];} ?></td>
                    <td><?php if(isset($value['cpu_number'])){ echo $value['cpu_number'];} ?></td>
                    <td><?php if(isset($value['memory_gb'])){ echo $value['memory_gb'];} ?></td>
                    <td><?php if(isset($value['set_type'])){ echo $value['set_type'];} ?></td>
                    <td>
                        <a  href="<?php echo $this->Url->build(array('controller' => 'AgentSet','action'=>'addedit')); ?>/<?php if(isset($value['set_id'])){ echo $value['set_id'];} ?>">修改</a> |
                        <a id="delete" href="#" onclick="deletes(<?php if(isset($value['set_id'])){ echo $value['set_id']; }?>)" >删除</a>
                    </td>
                </tr>
                <?php } ?>
                <?php } ?>
            </tbody>
        </table>
        <div class="content-pagination clearfix">
            <nav class="pull-right">
                <ul class="pagination">

                </ul>
            </nav>
        </div>
    </div>

<?php $this->start('script_last'); ?>
<script type="text/javascript">
    function deletes(id){
        $.ajax({
            type: 'post',
            url: '<?php echo $this->Url->build(array('controller' => 'AgentSet','action'=>'dele')); ?>',
            data: {id:id},
            success: function(data) {
                var data = eval('(' + data + ')');
                if(data.code==0){
                    alert(data.msg);
                    location.href='<?php echo $this->Url->build(array('controller'=>'AgentSet','action'=>'index'));?>';
                }else{
                    alert(data.msg);
                }
            }
        });
    }
</script>
<?php $this -> end(); ?>