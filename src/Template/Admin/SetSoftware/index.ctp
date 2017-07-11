<!-- 主页模板 -->
<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div id="maindiv-alert" class="content-list-page"></div>
    <div class="content-operate clearfix">
        <div class="pull-left">
            <a  href="<?php echo $this->Url->build(array('controller' => 'SetSoftware','action'=>'addedit')); ?>" class="btn btn-addition"><i class="icon-plus"></i>&nbsp;&nbsp;新建</a>
        </div>
        <div class="input-group content-search pull-right">
            <input type="text" class="form-control" id="searchtext" placeholder="搜索非编名称或代码...">
            <span class="input-group-btn">
                <button class="btn btn-primary" id="search" type="button">搜索</button>
            </span>
        </div>
    </div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>id</th>
                <th>非编规格名称</th>
                <th>非编代码</th>
                <th>硬件套餐名称</th>
                <th>镜像名称</th>
                <th>品牌</th>
                <!-- <th>软件说明</th> -->
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php if(!empty($query)){
                foreach($query as $value){?>
                <tr>
                    <td><?php if(isset($value['set_id'])){ echo $value['set_id'];} ?></td>
                    <td><?php if(isset($value['set_name'])){ echo $value['set_name'];} ?></td>
                    <td><?php if(isset($value['set_code'])){ echo $value['set_code'];} ?></td>
                    <td><?php if(isset($value['hard'])){ echo $value['hard'];} ?></td>
                    <td><?php if(isset($value['image'])){ echo $value['image'];} ?></td>
                    <td><?php if(isset($value['provider'])){ echo $value['provider'];} ?></td>
                    <!-- <td><?php if(isset($value['set_type'])){ echo $value['set_type'];} ?></td> -->
                    <td>
                        <a  href="<?php echo $this->Url->build(array('controller' => 'SetSoftware','action'=>'addedit')); ?>/<?php if(isset($value['set_id'])){ echo $value['set_id'];} ?>">修改</a> |
                        <a id="delete" href="javascript:;" onclick="deletes(<?php if(isset($value['set_id'])){ echo $value['set_id'];} ?>)" >删除</a>
                    </td>
                </tr>
                <?php }
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
                <i class="icon-question-sign text-primary"></i>&nbsp;&nbsp;确认要删除该非编套餐么？<span class="text-primary" id="sure"></span>？
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
                url: '<?php echo $this->Url->build(array('controller' => 'SetSoftware','action'=>'dele')); ?>',
                data: {id: id},
                success: function (data) {
                    var data = eval('(' + data + ')');
                    if (data.code == 0) {
                        tentionHide(data.msg, 0);
                        location.href = '<?php echo $this->Url->build(array('controller'=>'SetSoftware','action'=>'index'));?>';
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
        location.href = "<?php echo $this->Url->build(array('controller'=>'SetSoftware','action'=>'index'));?>/index/"+name;
    })
    $(function() {
        var name ="<?php echo $name ?>";
        $('#searchtext').val(name);
    })
</script>
<?= $this->end() ?>