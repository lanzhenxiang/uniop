<!-- 主页模板 -->
<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div id="maindiv-alert" class="content-list-page"></div>
    <div class="content-operate clearfix">
        <div class="pull-left">
            <a  href="<?php echo $this->Url->build(array('controller' => 'ChargeTemplate','action'=>'addedit','0')); ?>" class="btn btn-addition pull-left"><i class="icon-plus"></i>&nbsp;&nbsp;新建</a>

            <div class="dropdown pull-left" style="margin-left:30px;" id="selects">
                <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                    租户
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                    <li>
                        <a href="javascript:;" onclick="local(0)">全部</a>
                    </li>
                    <?php foreach ($dept_grout as $key => $value){?>
                    <li>
                        <a href="javascript:;" onclick="local(<?= $value['id']?>)">
                            <?php echo $value['name'];?>
                        </a>
                    </li>
                    <?php }?>
                </ul>
            </div>

            <div class="pull-left" style="margin-left:30px;margin-top: 6px">
                <span>当前租户：</span><span id="depart_name"></span>
            </div>
        </div>

        <div class="input-group content-search pull-right">
            <input type="text" class="form-control" id="searchtext" placeholder="搜索服务名称...">
             <span class="input-group-btn">
                 <button class="btn btn-primary" id="search" type="button">搜索</button>
             </span>
        </div>
    </div>
    <table class="table table-striped">
        <thead>
        <tr>
            <th>id</th>
            <th>模板名称</th>
            <th>按流量计费</th>
            <th>按天计费</th>
            <th>按月计费</th>
            <th>按年计费</th>
            <th>所属租户</th>
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
            <td><?php if(isset($value['template_name'])){ echo $value['template_name'];} ?></td>
            <td><?php if(isset($value['charge_expression'])){ echo $value['charge_expression'];} ?></td>
            <td><?php if(isset($value['charge_expression1'])){ echo $value['charge_expression1'];} ?></td>
            <td><?php if(isset($value['charge_expression2'])){ echo $value['charge_expression2'];} ?></td>
            <td><?php if(isset($value['charge_expression4'])){ echo $value['charge_expression4'];} ?></td>
            <td><?php if(isset($value['department']['name'])){ echo $value['department']['name'];} ?></td>
            <!--<td><?php if(isset($value['charge_note'])){ echo $value['charge_note'];} ?></td>-->
            <td>
                <a  href="<?php echo $this->Url->build(array('controller' => 'ChargeTemplate','action'=>'addedit')); ?>/<?php if(isset($value['id'])){ echo $value['id'];} ?>">修改</a> |
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
                <i class="icon-question-sign text-primary"></i>&nbsp;&nbsp;确认要删除该计费模板么？<span class="text-primary" id="sure"></span>？
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
        var name = $('#searchtext').val();
        location.href = "<?php echo $this->Url->build(array('controller'=>'ChargeTemplate','action'=>'index'));?>/index/"+name;
    })
    $(function() {
        var name ="<?php echo $name ?>";
        $('#searchtext').val(name);
        var depart_name ="<?php echo $department_name; ?>";
        $('#depart_name').html(depart_name);
    })
    function deletes(id){
        $('#modal-delete').modal("show");
        $('#yes').one('click',function() {
            $.ajax({
                type: 'post',
                url: '<?php echo $this->Url->build(array('controller' => 'ChargeTemplate','action'=>'deletes')); ?>',
                data: {id: id},
                success: function (data) {
                    var data = eval('(' + data + ')');
                    if (data.code == 0) {
                        tentionHide(data.message,0);
                        location.href = '<?php echo $this->Url->build(array('controller'=>'ChargeTemplate','action'=>'index'));?>';
                    } else {
                        $('#modal-delete').modal("hide");
                        tentionHide(data.message, 1);
                    }
                }
            });
        })
    }

    function local(id){
        var name = $('#searchtext').val();
        if(!name){
            name='';
        }
        location.href = "<?php echo $this->Url->build(array('controller' => 'ChargeTemplate','action'=>'index')); ?>/<?php echo 'index';?>/"+id+"/"+name;
    }
</script>
<?= $this->end() ?>