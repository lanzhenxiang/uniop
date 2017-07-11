<!-- 主页模板 -->
<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div class="content-operate clearfix">
        <div class="pull-left">
            <a type="button" href="<?php echo $this->Url->build(array('controller' => 'GoodsCatAttrs','action'=>'addedit')); ?>" class="btn btn-addition pull-left"><i
        class="icon-plus"></i>&nbsp;&nbsp;新建</a>

            <div class="dropdown pull-left" style="margin-left:30px;">
              <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                分类
                <span class="caret"></span>
              </button>
              <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                 <?php foreach ($query as $key => $value){?>
                <li>
                    <a href="<?php echo $this->Url->build(array('controller' => 'GoodsCatAttrs','action'=>'index')); ?>/<?php echo 'index';?>/<?php echo $value['id'];?>">
                        <?php echo $value['name'];?>
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
                <th>属性id</th>
                <th>属性名</th>
                <th>属性标签</th>
                <th>分类属性</th>
                <th>排序</th>
                <th>价格</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php if(isset($data)){
                foreach($data as $value){
                    ?>
                    <tr>
                        <td><?php echo $value['id']; ?></td>
                        <td><?php echo $value['value']; ?></td>
                        <td><?php echo $value['label']; ?></td>
                        <td><?php echo $value['goods_cat_attrs_cat']['name']; ?></td>
                        <td><?php echo $value['sort_order']; ?></td>
                        <td><?php echo $value['price']; ?></td>
                        <td>
                            <a  href="<?php echo $this->Url->build(array('controller' => 'GoodsCatAttrs','action'=>'addedit')); ?>/<?php if(isset($value['id'])){ echo $value['id'];} ?>">修改</a> |
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
                <?php echo $this->Paginator->last('>>');?>?>

          </ul>
      </nav>
  </div>
</div>

<?= $this->start('script_last'); ?>
<script type="text/javascript">
   function deletes(id){
    $.ajax({
        type: "POST",
        url: '<?php echo $this->Url->build(array('controller'=>'GoodsCatAttrs','action'=>'delete'));?>',
        dataType: "json",
        data: {'id':id},
        success: function(data){
            if(data.code==0){
                alert(data.msg);
                location.href='<?php echo $this->Url->build(array('controller'=>'GoodsCatAttrs','action'=>'index'));?>';
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