<!-- 主页模板 -->
<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div class="content-operate clearfix">
        <div class="pull-left">
            <a  href="<?php echo $this->Url->build(array('controller' => 'Orders','action'=>'index')); ?>" class="btn btn-default">返回</a>

        </div>
<!-- 
        <div class="input-group content-search pull-right">
            <input type="text" class="form-control" placeholder="搜索...">
            <span class="input-group-btn">
                <button class="btn btn-primary" type="button">搜索</button>
            </span>
        </div> -->
    </div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>id</th>
                <th>商品名称</th>
                <th>商品编号</th>
                <th>购买数量</th>
                <!-- <th>单价</th> -->
                <th>总价</th>
                <th>持续时间</th>
                <!-- <th>操作</th> -->
            </tr>
        </thead>
        <tbody>
            <?php if(isset($data)){
                foreach($data as $value){
                    ?>
                    <tr>
                        <td><?php if(isset($value['id'])){ echo $value['id'];} ?></td>
                        <td><?php if(isset($value['good_name'])){ echo $value['good_name'];} ?></td>
                        <td><?php if(isset($value['good']['sn'])){ echo $value['good']['sn'];} ?></td>
                        <td><?php if(isset($value['num'])){ echo $value['num'];} ?></td>
                        <td><?php if(isset($value['price_total'])){ echo $value['price_total'];} ?></td>
                        <td><?php if(isset($value['duration'])){ echo $value['duration'].$value['duration_unit'];} ?></td>
                   <!--  <td>
                        <a  href="<?php echo $this->Url->build(array('controller' => 'Orders','action'=>'addedit')); ?>/<?php if(isset($value['id'])){ echo $value['id'];} ?>">修改</a> |
                    </td> -->
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