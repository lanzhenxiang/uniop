<!-- 主页模板 -->
<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div id="maindiv-alert" class="content-list-page"></div>
    <div class="content-operate clearfix">

        <div style="padding-bottom: 31px; border-bottom: 1px solid rgb(157, 157, 157); margin-bottom: 15px;"><span style="font-size: 16px">基础信息</span>
        <div style="margin-top: 15px;"><span>配置名称&nbsp;:&nbsp;&nbsp;</span><span><?php if(isset($vpcdata)){ echo $vpcdata['vpc_name'];} ?></span>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>区域&nbsp;:&nbsp;&nbsp;</span><span><?php if(isset($vpcdata)){ echo $vpcdata['region_name'];} ?></span>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>vpc cidr&nbsp;:&nbsp;&nbsp;</span><span><?php if(isset($vpcdata)){ echo $vpcdata['vpc_cidr'];} ?></span></div>
        </div>
        <div class="pull-left">
            <a  href="<?php echo $this->Url->build(array('controller' => 'GoodsVpc','action'=>'addsubnet',$vpc_id)); ?>" class="btn btn-default">添加子网 +</a>
            <a  href="<?php echo $this->Url->build(array('controller' => 'GoodsVpc','action'=>'addecs',$vpc_id)); ?>" onclick="return isCanCreate()" class="btn btn-default">添加ECS/云桌面/防火墙/负载均衡 +</a>
            <a  href="<?php echo $this->Url->build(array('controller' => 'GoodsVpc','action'=>'store',$vpc_id)); ?>" class="btn btn-default">共享存储 +</a>
        </div>
    </div>
    <table class="table table-striped">
        <thead>
        <tr>
            <th>类型</th>
            <th>名称</th>
            <th>计算性能</th>
            <th>计算能力</th>
            <th>OS版本</th>
            <th>数量（台）</th>
            <th>操作</th>
            <th>所在子网</th>
        </tr>
        </thead>
        <tbody>
        <?php if(isset($vpcdetaildata)){
            foreach($vpcdetaildata as $value){
            ?>
        <tr>
            <td><?= $value['type'] ?></td>
            <td><?php if(isset($value['tagname'])){ echo $value['tagname'];}else{echo '默认';} ?></td>
            <td><?php if(isset($value['cpu_number'])){ echo $value['cpu_number'].'核'.$value['memory_gb'].'G';}else{echo '-';} ?></td>
            <td><?php if(isset($value['instance_name'])){ echo $value['instance_name'];}else{echo '-';} ?></td>
            <td><?php if(isset($value['image_name'])){ echo $value['image_name'];}else{echo '-';} ?></td>
            <td><?php if(isset($value['number'])){ echo $value['number'];}else{echo '默认';} ?></td>
            <td><?php if($value["type"]=="fics"||$value["type"]=="oceanstor9k"){}else{
                if($value['vpc_id'] !=0){ $action = 'addsubnet'; if($value['type']=='ecs' || $value['type']=='desktop' ||$value['type']=='firewall'|| $value['type']=='elb'){ $action = 'addecs'; } ?>
                    <a  href="<?php echo $this->Url->build(array('controller' => 'GoodsVpc','action'=>$action)); ?>/<?php if(isset($value['vpc_id'])){ echo $value['vpc_id'];} ?>/<?php echo $value['id'] ?>">修改</a> |
                <a id="delete" href="#" onclick="deletes(<?php echo $value['id']; ?>,<?php echo $vpc_id; ?>)" >删除</a><?php }}
              ?></td>
            <td><?php if(!empty($value['subnetName'])){ echo $value['subnetName'];}elseif($value['type']=='router' || $value['type']=='firewall'){ echo '默认'; }else{ echo '-'; } ?></td>
        </tr>
        <?php }} ?>
        </tbody>
    </table>
    <!-- <div class="content-pagination clearfix">
        <nav class="pull-right">
            <ul class="pagination">
                <?php echo $this->Paginator->first('<<');?>
                <?php echo $this->Paginator->prev(' < '); ?>
                <?php echo $this->Paginator->numbers();?>
                <?php echo $this->Paginator->next(' > '); ?>
                <?php echo $this->Paginator->last('>>');?>
            </ul>
        </nav>
    </div> -->
</div>

<div class="modal fade" id="modal-delete" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">提示</h5>
            </div>
            <div class="modal-body">
                <i class="icon-question-sign text-primary"></i>&nbsp;&nbsp;确认要删除该vpc配置单么？<span class="text-primary" id="sure"></span>？
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
//        var name = $('#searchtext').val();
//        location.href = "<?php //echo $this->Url->build(array('controller'=>'GoodsVpc','action'=>'index'));?>///index/"+code+'/'+name;
    })
   $(function() {
  /*      var name ="<?php echo $name ?>";
        $('#searchtext').val(name);
        var vpc ="<?php echo $vpc_name; ?>";
        $('#vpc').html(vpc);*/
    })
    function deletes(id,vpc_id){
        $.getJSON("/admin/goods-vpc/subnetfree?id="+id,function(data){
            if(data.code==0){
//                tentionHide('请先删除绑定该子网的主机或桌面', 0);
                alert('请先删除绑定该子网的主机或桌面');
            }else{
                $('#modal-delete').modal("show");
                $('#yes').one('click',function() {
                    $.ajax({
                        type: 'post',
                        url: '<?php echo $this->Url->build(array('controller' => 'GoodsVpc','action'=>'deletevpcdetail')); ?>',
                        data: {id: id},
                        success: function (data) {
                            var data = eval('(' + data + ')');
                            if (data.code == 0) {
                                tentionHide(data.msg, 0);
                                location.href = '<?php echo $this->Url->build(array('controller'=>'GoodsVpc','action'=>'configure'));?>/'+vpc_id;
                            } else {
                                $('#modal-delete').modal("hide");
                                tentionHide(data.msg, 1);
                            }
                        }
                    });
                })
            }
        });

    }

</script>
<?= $this->end() ?>