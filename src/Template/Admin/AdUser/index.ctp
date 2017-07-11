<!-- 主页模板 -->
<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div id="maindiv-alert" class="content-list-page"></div>
    <div class="content-operate clearfix">
    <div class="dropdown pull-left" style="margin-left:30px;" id="selects">
         <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                    vpc
         <span class="caret"></span>
         </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                    <li>
                        <a href="javascript:;" onclick="local('all')">全部</a>
                    </li>
                    <?php foreach ($vpc_data as $key => $value){?>
                        <li>
                            <a href="javascript:;" onclick="local('<?= $value['code']?>')">
                                <?php echo $value['name'];?>
                            </a>
                        </li>
                    <?php }?>
                </ul>
            </div>

            <div class="pull-left" style="margin-left:30px;margin-top: 6px">
                <span>当前vpc：</span><span id="vpc"></span>
               
            </div>
         
        <div class="input-group content-search pull-right">
            <input type="text" class="form-control" id="searchtext" placeholder="搜索用户名...">
             <span class="input-group-btn">
                 <button class="btn btn-primary" id="search" type="button">搜索</button>
             </span>
        </div>
          
    </div>
    <table class="table table-striped">
        <thead>
        <tr>
            <th>id</th>
            <th>登录账号</th>
            <th>登录密码</th>
            <th>vpcCode</th>
            <th>创建人</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        <?php if(isset($data)){
            foreach($data as $value){
            ?>
        <tr>
            <td><?php if(isset($value['id'])){ echo $value['id'];} ?></td>
            <td><?php if(isset($value['loginName'])){ echo $value['loginName'];} ?></td>
            <td><?php if(isset($value['loginPassword'])){ echo $value['loginPassword'];} ?></td>
            <td><?php if(isset($value['vpcCode'])){ echo $value['vpcCode'];} ?></td>
            <td><?php if(isset($value['account']['username'])){ echo $value['account']['username'];} ?></td>
            <td><a  href="<?php echo $this->Url->build(array('controller' => 'AdUser','action'=>'addedit')); ?>/<?php if(isset($value['id'])){ echo $value['id'];} ?>">修改</a> |
                <a id="delete" href="#" onclick="deletes(<?php echo $value['uid']; ?>,'<?php echo $value['vpcCode'] ?>','<?php echo $value['loginName'] ?>')" >删除</a></td>
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
                <i class="icon-question-sign text-primary"></i>&nbsp;&nbsp;确认要删除该AD账号么？<span class="text-primary" id="sure"></span>？
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
        var code ="<?php echo $code; ?>";
        location.href = "<?php echo $this->Url->build(array('controller'=>'AdUser','action'=>'index'));?>/index/"+code+'/'+name;
    })
   $(function() {
        var name ="<?php echo $name ?>";
        $('#searchtext').val(name);
        var vpc ="<?php echo $vpc_name; ?>";
        $('#vpc').html(vpc);
    })
    function deletes(uid,vpcCode,loginName){
        $('#modal-delete').modal("show");
        $('#yes').one('click',function() {
            $.ajax({
                type: 'post',
                url: '<?php echo $this->Url->build(array('controller' => 'AdUser','action'=>'delete')); ?>',
                data: {uid: uid, vpcCode: vpcCode, loginName: loginName},
                success: function (data) {
                    var data = eval('(' + data + ')');
                    if (data.code == 0) {
                        tentionHide(data.msg, 0);
                        location.href = '<?php echo $this->Url->build(array('controller'=>'AdUser','action'=>'index'));?>';
                    } else {
                        $('#modal-delete').modal("hide");
                        tentionHide(data.msg, 1);
                    }
                }
            });
        })
    }

    function local(code){
        var name = $('#searchtext').val();
        if(!name){
            name='';
        }
        location.href = "<?php echo $this->Url->build(array('controller' => 'AdUser','action'=>'index')); ?>/<?php echo 'index';?>/"+code+"/"+name;
    }
</script>
<?= $this->end() ?>