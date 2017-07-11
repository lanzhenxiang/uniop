<!-- 主页模板 -->
<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div id="maindiv-alert" class="content-list-page"></div>
    <div class="content-operate clearfix">
        <div class="pull-left">
            <a  href="<?php echo $this->Url->build(array('controller' => 'ServiceType','action'=>'addedit','0')); ?>" class="btn btn-addition pull-left"><i class="icon-plus"></i>&nbsp;&nbsp;新建</a>

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
                <th>媒体服务名称</th>
                <!--<th>媒体服务代码</th>-->
                <th>租户</th>
                <th>计费模板</th>
                <th>忙碌实例数量</th>
                <th>空闲实例数量</th>
                <th>最后更新时间</th>
                <th>排队任务数</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php if(isset($data)){
                foreach($data as $value){
                    ?>
                    <tr>

                        <td><?php if(isset($value['type_id'])){ echo $value['type_id'];} ?></td>
                        <td><?php if(isset($value['service_name'])){ echo $value['service_name'];} ?></td>
                        <!--<td><?php /*if(isset($value['service_code'])){ echo $value['service_code'];} */?></td>-->
                        <td><?php if(isset($value['department']['name'])){ echo $value['department']['name'];} ?></td>
                        <td><?php if(isset($value['charge_template']['template_name'])){ echo $value['charge_template']['template_name'];} ?></td>
                        <td><?php if(isset($value['busy_instance'])){ echo $value['busy_instance'];} ?></td>
                        <td><?php if(isset($value['free_instance'])){ echo $value['free_instance'];} ?></td>
                        <td><?php if(isset($value['check_time'])){ echo date('Y-m-d H:i:s',$value['check_time']);} ?></td>
                        <td><?php if(isset($value['wait_job'])){ echo $value['wait_job'];} ?></td>
                        <td>
                            <a  href="<?php echo $this->Url->build(array('controller' => 'ServiceType','action'=>'check')); ?>/<?php if(isset($value['type_id'])){ echo $value['type_id'];} ?>">详细信息</a> |
                            <a  href="<?php echo $this->Url->build(array('controller' => 'ServiceType','action'=>'addedit')); ?>/<?php if(isset($value['type_id'])){ echo $value['type_id'];} ?>">修改</a> |
                            <a  href="<?php echo $this->Url->build(array('controller' => 'ServiceType','action'=>'editdevice')); ?>/<?php if(isset($value['type_id'])){ echo $value['type_id'];} ?>">关联主机</a> |
                            <?php if($isctvit!=0){ ?>
                            	 <a  href="<?php echo $this->Url->build(array('controller' => 'ServiceType','action'=>'introducehost')); ?>/<?php if(isset($value['type_id'])){ echo $value['type_id'];} ?>">引入主机</a> |
                            <?php } ?>
                            <a id="delete" href="#" onclick="deletes(<?php echo $value['type_id']; ?>)" >删除</a>
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
                <i class="icon-question-sign text-primary"></i>&nbsp;&nbsp;确认要删除该服务类型及该服务与主机的关联关系么？<span class="text-primary" id="sure"></span>？
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
        location.href = "<?php echo $this->Url->build(array('controller'=>'ServiceType','action'=>'index'));?>/index/0/"+name;
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
                url: '<?php echo $this->Url->build(array('controller' => 'ServiceType','action'=>'deletes')); ?>',
                data: {id: id},
                success: function (data) {
                    var data = eval('(' + data + ')');
                    if (data.code == 0) {
                        tentionHide(data.message,0);
                        location.href = '<?php echo $this->Url->build(array('controller'=>'ServiceType','action'=>'index'));?>';
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
        location.href = "<?php echo $this->Url->build(array('controller' => 'ServiceType','action'=>'index')); ?>/<?php echo 'index';?>/"+id+"/"+name;
    }
</script>
<?= $this->end() ?>