<!-- 主页模板 -->
<div class="content-body clearfix"  >
    <div class="content-operate clearfix">
        <div class="pull-left">
            <a type="button" href="<?php echo $this->Url->build(array('controller' => 'GoodsSpec','action'=>'addedit'));if(isset($data)){ echo '/add/'.$data[0]['good']['id'];} ?>" class="btn btn-default">新增 +</a>
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
                <th>规格id</th>
                <th>所属商品</th>
                <th>规格名称</th>
                <th>规格代码</th>
                <th>规格描述</th>
                <th>排序</th>
                <th>是否展示</th>
                <th>是否使用接口</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php if(isset($data)){
                foreach($data as $value){
                    ?>
                    <tr>
                        <td><?php echo $value['id']; ?></td>
                        <td><?php echo $value['good']['name']; ?></td>
                        <td><?php echo $value['spec_name']; ?></td>
                        <td><?php echo $value['spec_code']; ?></td>
                        <td><?php echo $value['spec_value']; ?></td>
                        <td><?php echo $value['sort_order']; ?></td>
                        <td><?php $i=$value['is_display']==1? '是':'否'; echo $i;?></td>
                        <td><?php $i=$value['is_need']==1? '是':'否'; echo $i;?></td>
                        <td>
                            <a  href="<?php echo $this->Url->build(array('controller' => 'GoodsSpec','action'=>'addedit')); ?>/<?php if(isset($value['id'])){ echo 'edit/'.$value['id'];} ?>">修改</a> |
                            <a value="<?php echo $value['id']; ?>" href="#" onclick="deletes(<?php echo $value['id']; ?>)">删除</a>
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
            type: "POST",
            url: '<?php echo $this->Url->build(array('controller'=>'GoodsSpec','action'=>'dele'));?>',
            dataType: "json",
            data: {'id':id},
            success: function(data){
                if(data.code==0){
                    alert(data.msg);
                    location.href='<?php echo $this->Url->build(array('controller'=>'GoodsSpec','action'=>'index'));?>';
                }else{
                    alert(data.msg);
                }
            },
            error: function(){
                alert(2);
            }
        });
    }
</script>
<?= $this->end() ?>