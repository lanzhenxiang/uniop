<!-- 主页模板 -->
<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div id="maindiv-alert" class="content-list-page"></div>
    <div class="content-operate clearfix">
        <div class="pull-left">
            <!-- <a  href="<?php echo $this->Url->build(array('controller' => 'InstanceBasic','action'=>'addedit')); ?>" class="btn btn-addition"><i class="icon-plus"></i>&nbsp;&nbsp;新建</a> -->
        </div>
        <div class="input-group content-search pull-right">
            <input type="text" class="form-control" id="searchtext" placeholder="搜索主机名称或code...">
             <span class="input-group-btn">
                 <button class="btn btn-primary" id="search" type="button">搜索</button>
             </span>
        </div>
    </div>
    <table class="table table-striped">
        <thead>
        <tr>
            <th>id</th>
            <th>主机名称</th>
            <th>Code</th>
            <th>主机类型</th>
            <th>操作系统</th>
            <th>部署区域</th>
            <th>IP地址</th>
            <th>配置</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        <?php if(isset($data)){
            foreach($data as $value){
            ?>
        <tr>
            <td><?php if(isset($value['id'])){ echo $value['id'];} ?></td>
            <td><?php if(isset($value['name'])){ echo $value['name'];} ?></td>
            <td><?php if(isset($value['code'])){ echo $value['code'];} ?></td>
            <td><?php if(isset($value['type'])){ switch ($value['type']) {
                case 'hosts':
                    echo "主机";
                    break;
                case 'desktop':
                    echo "云桌面";
                    break;
                default:
                    echo "未知";
                    break;
            }} ?></td>

            <td><?php if(isset($value['host_extend']['os_family'])){echo $value['host_extend']['os_family'];} ?></td>
            <td><?php if(isset($value['location_name'])){echo $value['location_name']; } ?></td>
            <td><?php if(isset($value['host_extend'])){echo $value['host_extend']['ip']; } ?></td>
            <td><?php if(isset($value['host_extend'])){ echo $value['host_extend']['cpu'].'核*'.$value['host_extend']['memory'].'G';} ?></td>
            <td>
                <a  href="<?php echo $this->Url->build(array('controller' => 'InstanceBasic','action'=>'editip')); ?>/<?php if(isset($value['id'])){ echo $value['id'];} ?>">修改</a> |
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
                <i class="icon-question-sign text-primary"></i>&nbsp;&nbsp;确认要删除该主机么？<span class="text-primary" id="sure"></span>？
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
        location.href = "<?php echo $this->Url->build(array('controller'=>'InstanceBasic','action'=>'index'));?>/index/"+name;
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
                url: '<?php echo $this->Url->build(array('controller' => 'InstanceBasic','action'=>'dele')); ?>',
                data: {id: id},
                success: function (data) {
                    var data = eval('(' + data + ')');
                    if (data.code == 0) {
                        tentionHide(data.msg, 0);
                        location.href = '<?php echo $this->Url->build(array('controller'=>'InstanceBasic','action'=>'index'));?>';
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