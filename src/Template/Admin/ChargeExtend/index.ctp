<!-- 主页模板 -->
<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div id="maindiv-alert" class="content-list-page"></div>
    <div class="content-operate clearfix">
        <div class="pull-left">
            <a  href="<?php echo $this->Url->build(array('controller' => 'ChargeExtend','action'=>'addedit')); ?>" class="btn btn-addition pull-left"><i class="icon-plus"></i>&nbsp;&nbsp;新建</a>

        </div>
        
        <div class="dropdown pull-right" style="margin-right:100px;">
                厂商:
            <a href="#" class="dropdown-toggle btn btn-addition text-right" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
            <span class="pull-left" id="agent" val="<?=$agent_id?>"><?=$agent_name?></span>
            <span class="caret"></span>
            </a>
            <ul class="dropdown-menu">
                <li><a href="<?php echo $this->Url->build(array('controller' => 'ChargeExtend','action'=>'index',0,$charge_object)); ?>" >全部</a></li>
                <?php foreach($agents as $agent): ?>
                <li><a href="<?php echo $this->Url->build(array('controller' => 'ChargeExtend','action'=>'index',$agent->id,$charge_object)); ?>" ><?=$agent->agent_name?></a></li>
                <?php endforeach;?>
            </ul>
        </div>
        <div class="dropdown pull-right" style="margin-right:50px;">
                资源类型:
            <a href="#" class="dropdown-toggle btn btn-addition text-right" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
            <span class="pull-left" id="agent" val="<?=$charge_object?>"><?=$charge_object_txt?></span>
            <span class="caret"></span>
            </a>
            <ul class="dropdown-menu">
                <li><a href="<?php echo $this->Url->build(array('controller' => 'ChargeExtend','action'=>'index',$agent_id,'')); ?>" >全部</a></li>
                <?php foreach($charge_object_arr as $key=>$val): ?>
                    <li><a href="<?php echo $this->Url->build(array('controller' => 'ChargeExtend','action'=>'index',$agent_id,$key)); ?>" ><?=$val?></a></li>
                <?php endforeach;?>
            </ul>
        </div>
    </div>
    <table class="table table-striped">
        <thead>
        <tr>
            <th>id</th>
            <th>计费对象</th>
            <th>云厂商</th>
            <th>按天计费单价（元/天）</th>
            <th>按月计费单价 (元/月)</th>
            <th>按年计费单价 (元/年)</th>
            <th>计费说明</th>
            <th>创建人</th>
            <th>创建时间</th>
            <!--<th>计费说明</th>-->
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        <?php if(isset($data)){
                foreach($data as $value){
                    ?>
        <tr>

            <td><?php if(isset($value['id'])){ echo $value['id'];} ?></td>
            <td><?php if(isset($value['charge_object'])){ echo $value['charge_object'];} ?></td>
            <td><?php if(isset($value['agent_name'])){ echo $value['agent_name'];} ?></td>
            <td><?php if(isset($value['daily_price'])){ 
            echo $this->Number->format($value['daily_price'], ['places' => 4,'before' => '¥ ']);} ?></td>
            <td><?php if(isset($value['monthly_price'])){ 
            echo $this->Number->format($value['monthly_price'], ['places' => 4,'before' => '¥ ']);} ?></td>
            <td><?php if(isset($value['yearly_price'])){ 
            echo $this->Number->format($value['yearly_price'], ['places' => 4,'before' => '¥ ']);} ?></td>
            <td><?php if(isset($value['charge_note'])){ echo $value['charge_note'];} ?></td>
            <!--<td><?php if(isset($value['charge_note'])){ echo $value['charge_note'];} ?></td>-->
            <td><?php if(isset($value['create_name'])){ echo $value['create_name'];} ?></td>
            <td><?php if(isset($value['create_time'])){ echo $value['create_time'];} ?></td>
            <td>
                <a  href="<?php echo $this->Url->build(array('controller' => 'ChargeExtend','action'=>'addedit')); ?>/<?php if(isset($value['id'])){ echo $value['id'];} ?>">修改</a> |
                <a id="delete" href="#" onclick="deletes(<?php echo $value['id']; ?>)" >删除</a>
            </td>
        </tr>
        <?php
                }
            } ?>
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
                <i class="icon-question-sign text-primary"></i>&nbsp;&nbsp;确认要删除该计费规则么？<span class="text-primary" id="sure"></span>？
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
    function deletes(id){
        $('#modal-delete').modal("show");
        $('#yes').one('click',function() {
            $.ajax({
                type: 'post',
                url: '<?php echo $this->Url->build(array('controller' => 'ChargeExtend','action'=>'deletes')); ?>',
                data: {id: id},
                success: function (data) {
                    var data = eval('(' + data + ')');
                    if (data.code == 0) {
                        tentionHide(data.message,0);
                        location.href = '<?php echo $this->Url->build(array('controller'=>'ChargeExtend','action'=>'index'));?>';
                    } else {
                        $('#modal-delete').modal("hide");
                        tentionHide(data.message, 1);
                    }
                }
            });
        })
    }
</script>
<?= $this->end() ?>