<!-- 主页模板 -->
<style type="text/css">
    .bootstrap-table .table thead > tr > th{
        padding:8px;
    }
</style>
<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div id="maindiv-alert"  class="content-list-page"></div>
    <?= $this->Html->css(['zTreeStyle.css']) ?>
    <?= $this->Html->script(['jquery.ztree.core-3.5.js']); ?>
    <div class="content-operate clearfix">
        <div class="pull-left">
            <a type="button"   href="<?php echo $this->Url->build(array('controller'=>'agent','action'=>'index')); ?>" class="btn btn-addition pull-left" style="margin-right: 20px;">返回部署区位列表</a>

            <?php if(in_array('cmop_global_sys_admin',$this->request->session()->read('Auth.User.popedomname'))){ ?>
            <a type="button" id="add"  href="<?php echo $this->Url->build(array('controller'=>'agent','action'=>'areaedit')); ?>/<?= $_agentid ?>/0" class="btn btn-addition pull-left"><i class="icon-plus"></i>&nbsp;&nbsp;新建</a>
        <?php }else{ ?>
                <a type="button" disabled="disabled" class="btn btn-addition pull-left"><i class="icon-plus"></i>&nbsp;&nbsp;新建</a>
            <?php } ?>
        </div>
        <div class="input-group content-search pull-right">
            <input type="text" class="form-control" id="searchtext" placeholder="搜索地域名...">
             <span class="input-group-btn">
                 <button class="btn btn-primary" id="search" type="button">搜索</button>
             </span>
        </div>
    </div>
    <div class="bot">
        <div class="bootstrap-table">
            <div class="fixed-table-body">
            <table class="table table-striped">
        <thead>
        <tr>
            <th>ID</th>
            <th>云厂商</th>
            <th>地域</th>
            <th>地域CODE</th>
            <th>虚拟化技术</th>
            <th>开放通用IaaS</th>
            <th>开放云桌面</th>
            <th>排序</th>
            <th>操作</th>
            <th>备注</th>
            <th>创建人</th>
            <th>创建时间</th>
        </tr>
        </thead>
        <tbody>
        <?php
            if(!empty($_data)){
               foreach($_data as $value){ ?>
                <tr>
                    <td><?= $value["id"] ?></td>
                    <td><?= $c->getAgentById($value["parentid"])->agent_name ?></td>
                    <td><?= $value["agent_name"] ?></td>
                    <td><?= $value["region_code"] ?></td>
                    <td><?= $value["virtual_technology"] ?></td>
                    <td><?= $value["is_enabled"]==1?'Y':'N' ?></td>
                    <td><?= $value["is_desktop"]==1?'Y':'N' ?></td>
                    <td><?= $value["sort_order"] ?></td>
                    <td>
                      <a href="<?php echo $this->Url->build(array('controller'=>'agent','action'=>'areaedit')); ?>/<?= $_agentid ?>/<?= $value["id"] ?>">修改</a>
                     |<a href="<?php echo $this->Url->build(array('controller'=>'agent','action'=>'agentset')); ?>/<?= $_agentid ?>/<?= $value["id"] ?>">定义云主机计算能力</a>
                     |<a href="<?php echo $this->Url->build(array('controller'=>'agent','action'=>'imageset')); ?>/<?= $_agentid ?>/<?= $value["id"] ?>">定义云主机系统镜像</a>
                     |<a href="<?php echo $this->Url->build(array('controller'=>'spec','action'=>'desktopset')); ?>/<?= $_agentid ?>/<?= $value["id"] ?>">定义桌面规格</a>
                     |<a href="<?php echo $this->Url->build(array('controller'=>'agent','action'=>'departmentset')); ?>/<?= $_agentid ?>/<?= $value["id"] ?>">定义可见租户</a>
                     |<a href="javascript:;" onclick="deletes(<?= $value['id']; ?>)" >删除</a></td>
                    <td><?= $value["remark"] ?></td>
                    <td><?php if($value['account']){echo $value['account']->username;}  ?></td>
                    <td><?= $value["create_time"] ?></td>
                </tr>
               <?php }
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
        </div>
    </div>
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
                <i class="icon-question-sign text-primary"></i>&nbsp;&nbsp;确认要删除该区域吗？<span class="text-primary" id="sure">删除区域需先删除设备</span>
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
            url: '<?php echo $this->Url->build(array('controller' => 'Agent','action'=>'dele')); ?>',
            data: {id: id},
            success: function (data) {
                var data = eval('(' + data + ')');
                if (data.code == 0) {
                    tentionHide(data.msg, 0);
                    var name = $('#searchtext').val();
                    location.href = "<?php echo $this->Url->build(array('controller'=>'Agent','action'=>'arealist'));?>/<?= $_agentid ?>/"+name;
                } else {
                    $('#modal-delete').modal("hide");
                    tentionHide(data.msg, 1);
                }
            }
        });
    })
}
$('#search').on('click',function(){
        var name = $('#searchtext').val();
        location.href = "<?php echo $this->Url->build(array('controller'=>'Agent','action'=>'arealist'));?>/<?= $_agentid ?>/"+name;
    })
$(function() {
    var name ="<?php echo $name ?>";
    $('#searchtext').val(name);
})
</script>
<?= $this->end() ?>