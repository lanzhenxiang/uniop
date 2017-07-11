<!-- 主页模板 -->
<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div id="maindiv-alert" class="content-list-page"></div>
    <div class="content-operate clearfix">
        <div class="pull-left">
            <div class="dropdown pull-left" style="margin-left:30px;">
                <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">流程类型
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                    <li>
                        <a href="<?php echo $this->Url->build(array('controller' => 'Orders','action'=>'index',0,0,$name)); ?>">全部</a>
                    </li>
                    <?php foreach ($template_data as $template
                          ) { ?>
                    <li>
                        <a href="<?php echo $this->Url->build(array('controller' => 'Orders','action'=>'index',0,$template['flow_id'],$name)); ?>"><?= $template['flow_name']?></a>
                    </li>
                    <?php }?>
                </ul>
            </div>
            <div class="dropdown pull-left" style="margin-left:30px;">
                <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">订单状态
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                    <li>
                        <a href="<?php echo $this->Url->build(array('controller' => 'Orders','action'=>'index',0,$flow['flow_id'],$name)); ?>">全部</a>
                    </li>
                    <?php if(isset($detail_data)){?>
                    <?php foreach ($detail_data as $detail) {?>
                    <li>
                        <a href="<?php echo $this->Url->build(array('controller' => 'Orders','action'=>'index',$detail['lft'],$flow['flow_id'],$name)); ?>"><?= $detail['step_name']?></a>
                    </li>
                    <?php }}?>
                </ul>
            </div>
            <div class="pull-left" style="margin-left:30px;margin-top: 6px">
                <span>当前流程类型：</span>
                <span><?= $flow['flow_name']?> </span>
            </div>
            <div class="pull-left" style="margin-left:30px;margin-top: 6px">
                <span>当前订单状态：</span>
                <span id="order_state"><?= $step['step_name']?>
                </span>
            </div>
        </div>
        <div class="input-group content-search pull-right">
            <input type="text" class="form-control" id="searchtext" placeholder="搜索订单号或下单人...">
            <span class="input-group-btn">
                <button class="btn btn-primary" id="search" type="button">搜索</button>
            </span>
        </div>
    </div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>id</th>
                <th>订单号</th>
                <th>总价</th>
                <!-- <th>购买租户</th>-->
                <th>下单人</th>
                <th>下单时间</th>
                <th>订单状态</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody><?php if(isset($data)){
            foreach($data as $value){?>
            <tr>
                <td><?php if(isset($value['id'])){ echo $value['id'];} ?></td>
                <td><?php if(isset($value['number'])){ echo $value['number'];} ?></td>
                <td><?php if(isset($value['price_total'])){ echo $value['price_total'];} ?></td>
                <!-- <td><?php /*if(isset($value['department'])){ echo $value['department']['name'];} */?></td>-->
                <td><?php if(isset($value['account'])){ echo $value['account']['username'];} ?></td>
                <td><?php if(isset($value['create_time'])){ echo date('Y-m-d H:i:s',$value['create_time']);} ?></td>
                <td><?php if(isset($value['workflow_detail']['step_name'])){ echo $value['workflow_detail']['step_name']; }?></td>
                <td><a  href="<?php echo $this->Url->build(array('controller' => 'OrdersGoods','action'=>'index')); ?>/<?php echo 'index';?>/<?php if(isset($value['id'])){ echo $value['id'];} ?>">查看订单详情</a> |
                    <a  href="#" class = "auto-proce">自动处理</a> |
                    <a  href="<?php echo $this->Url->build(array('controller' => 'Orders','action'=>'addedit')); ?>/<?php if(isset($value['id'])){ echo $value['id'];} ?>">修改</a> |
                    <a id="delete" href="#" onclick="deletes(<?php echo $value['id']; ?>)" >删除</a>
                </td>
            </tr>
            <?php }} ?>
        </tbody>
    </table>
    <div class="content-pagination clearfix">
        <nav class="pull-right">
            <ul class="pagination">
                <?php echo $this->Paginator->first('<<');?>
                <?php echo $this->Paginator->numbers();?>
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
                <i class="icon-question-sign text-primary"></i>&nbsp;&nbsp;确认要删除该订单么？<span class="text-primary" id="sure"></span>？
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
        var status = <?= $status?>;
        var name = $('#searchtext').val();
        location.href = "<?php echo $this->Url->build(array('controller'=>'Orders','action'=>'index'));?>/index/"+status+"/"+name;
    })
    $(function() {
        var name ="<?php echo $name ?>";
        $('#searchtext').val(name);
    })

    function deletes(id) {
        $('#modal-delete').modal("show");
        $('#yes').one('click',function() {
            $.ajax({
                type: 'post',
                url: '<?php echo $this->Url->build(array('controller' => 'Orders','action'=>'delete')); ?>',
                data: {id: id},
                success: function (data) {
                    var data = eval('(' + data + ')');
                    if (data.code == 0) {
                        tentionHide(data.msg, 0);
                        location.href = '<?php echo $this->Url->build(array('controller'=>'Orders','action'=>'index'));?>';
                    } else {
                        $('#modal-delete').modal("hide");
                        tentionHide(data.msg, 1);
                    }
                }
            });
        })
    }

    $('.auto-proce').bind('click',function(){
        alert("进入下一阶段");
    })
</script>
<?= $this->end() ?>