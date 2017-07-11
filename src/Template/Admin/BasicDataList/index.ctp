<!-- 主页模板 -->
<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div class="content-operate clearfix">
        <div class="pull-left">
            <a type="button" href="<?php echo $this->Url->build(array('controller' => 'BasicDataList','action'=>'addedit')); ?>" class="btn btn-addition pull-left"><i
        class="icon-plus"></i>&nbsp;&nbsp;新建</a>

            <div class="dropdown pull-left" style="margin-left:30px;">
              <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                参数类型
                <span class="caret"></span>
              </button>
              <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                <li>
                    <a href="<?php echo $this->Url->build(array('controller' => 'BasicDataList','action'=>'index')); ?>">全部参数</a>
                </li>
                <?php foreach ($type as $key => $value){?>
                    <li>
                        <a href="<?php echo $this->Url->build(array('controller' => 'BasicDataList','action'=>'index')); ?>/<?php echo 'index';?>/<?php echo $value['type_id'];?>">
                            <?php echo $value['type_name'];?>
                        </a>
                    </li>
                <?php }?>
              </ul>
            </div>
        </div>
        
    </div>
    <table class="table table-striped">
        <thead>
        <tr>
            <th>参数id</th>
            <th>参数类型</th>
            <th>数据代码</th>
            <th>数据值</th>
            <th>数据说明</th>
            <th>排序</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        <?php if(isset($data)){
            foreach($data as $value){
                ?>
                <tr>
                    <td><?php echo $value['id']; ?></td>
                    <td><?php echo $value['basic_data_type']['type_name']; ?></td>
                    <td><?php echo $value['data_code']; ?></td>
                    <td><?php echo $value['data_value']; ?></td>
                    <td><?php echo $value['data_note']; ?></td>
                    <td><?php echo $value['sort_order']; ?></td>
                    <td>
                        <a  href="<?php echo $this->Url->build(array('controller' => 'BasicDataList','action'=>'addedit')); ?>/<?php if(isset($value['id'])){ echo $value['id'];} ?>">修改</a> |
                        <a id="delete" onclick="deletes(<?php echo $value['id']; ?>)" href="#">删除</a>
                    </td>
                </tr>
            <?php
            }
        }
        ?>
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
            url: '<?php echo $this->Url->build(array('controller' => 'BasicDataList','action'=>'delete')); ?>',
            data: {id:id},
            success: function(data) {
                var data = eval('(' + data + ')');
                if(data.code==0){
                    alert(data.msg);
                    location.href='<?php echo $this->Url->build(array('controller' => 'BasicDataList','action'=>'index')); ?>';
                }else{
                    alert(data.msg);
                }
            }
        });

    }
</script>
<?= $this->end() ?>