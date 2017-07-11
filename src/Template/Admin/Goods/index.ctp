<!-- 主页模板 -->
<?= $this->element('content_header'); ?>



<div class="content-body clearfix">
    <div id="maindiv-alert" class="content-list-page"></div>
    <div class="content-operate clearfix">

        <div class="pull-left">
            <a type="button" href="<?php echo $this->Url->build(array('controller' => 'Goods','action'=>'add')); ?>" class="btn btn-addition pull-left"><i class="icon-plus"></i>&nbsp;&nbsp;新建</a>

            <div class="dropdown pull-left" style="margin-left:30px;">
                <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                    分类
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                     <li>
                        <a href="<?php echo $this->Url->build(array('controller' => 'Goods','action'=>'index')); ?>">
                            全部
                        </a>
                    </li>
                    <?php if(!empty($de)){?>
                    <?php foreach ($de as $key => $value){?>
                    <li>
                        <a href="<?php echo $this->Url->build(array('controller' => 'Goods','action'=>'index')); ?>/<?php echo 'index';?>/<?php echo $key;?>">
                            <?php echo $value;?>
                        </a>
                    </li>
                    <?php }?>
                    <?php }?>
                </ul>
            </div>
            <div class="pull-left" style="margin-left:30px;margin-top: 6px">
                <span>当前分类：</span><span id="category_name"><?= $cat_name?></span>
            </div>
        </div>
        <div class="input-group content-search pull-right">
            <input type="text" class="form-control" id="searchtext" placeholder="搜索商品名称...">
            <span class="input-group-btn">
                <button class="btn btn-primary" id="search" type="button">搜索</button>
            </span>
        </div>
    </div>
    <table class="table table-striped"  data-toggle="table">
        <thead>
            <tr>
                <th>商品id</th>
                <th>商品名称</th>
                <th>商品序列号</th>
                <!-- <th>产品描述</th> -->
                <th>价格</th>
                <th>商品类型</th>
                <!-- <th>商品私有属性</th> -->
                <!-- <th>商品详细描述</th> -->
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php if(isset($data)){
                foreach($data as $value){
                    ?>
                    <tr>
                        <td><?php echo $value['id']; ?></td>
                        <td><?php echo $value['name']; ?></td>
                        <td><?php echo $value['sn']; ?></td>
                        <!-- <td><?php /*echo $value['description']*/; ?></td> -->
                        <td><?php echo $value['price']; ?></td>
                        <td><?php echo $value['goods_category']['name']; ?></td>
                        <!-- <td><?php echo $value['private_attrs']; ?></td> -->
                        <td>
                           <!--  <a  href="<?php echo $this->Url->build(array('controller' => 'GoodsSpec','action'=>'index')); ?>/<?php echo 'index';?>/<?php if(isset($value['id'])){ echo $value['id'];} ?>">修改规格</a> | -->
                           <a  href="<?php echo $this->Url->build(array('controller' => 'Goods','action'=>'upexcel')); ?>/<?php if(isset($value['id'])){ echo $value['id'];} ?>">上传规格</a> |
                           <!-- <a  href="<?php echo $this->Url->build(array('controller' => 'Goods','action'=>'goodspec')); ?>/<?php if(isset($value['id'])){ echo $value['id'];} ?>">商品规格</a> | -->
                           <a  href="<?php echo $this->Url->build(array('controller' => 'Goods','action'=>'edit')); ?>/<?php if(isset($value['id'])){ echo $value['id'];} ?>">修改</a> |
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

<div class="modal fade" id="modal-delete" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">提示</h5>
            </div>
            <div class="modal-body">
                <i class="icon-question-sign text-primary"></i>&nbsp;&nbsp;确认要删除该商品么？<span class="text-primary" id="sure"></span>？
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
        var category_id = <?= $category_id?>;
        var name = $('#searchtext').val();
        location.href = "<?php echo $this->Url->build(array('controller'=>'Goods','action'=>'index'));?>/index/"+category_id+"/"+name;
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
                url: '<?php echo $this->Url->build(array('controller' => 'Goods','action'=>'delete')); ?>',
                dataType: "json",
                data: {'id': id},
                success: function (data) {
                    if (data.code == 0) {
                        tentionHide(data.msg, 0);
                        location.href = '<?php echo $this->Url->build(array('controller'=>'Goods','action'=>'index'));?>';
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