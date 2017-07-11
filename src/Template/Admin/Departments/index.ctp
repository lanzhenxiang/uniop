<!-- 主页模板 -->
<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div id="maindiv-alert"  class="content-list-page"></div>
    <?= $this->Html->css(['zTreeStyle.css']) ?>
    <?= $this->Html->script(['jquery.ztree.core-3.5.js']); ?>
    <div class="content-operate clearfix">
        <div class="pull-left">
            <?php if(in_array('cmop_global_sys_admin',$this->request->session()->read('Auth.User.popedomname'))){ ?>
            <a type="button" id="add"  href="<?php echo $this->Url->build(array('controller'=>'Departments','action'=>'add')); ?>/0" class="btn btn-addition pull-left"><i class="icon-plus"></i>&nbsp;&nbsp;新建</a>
        <?php }else{ ?>
                <a type="button" disabled="disabled" class="btn btn-addition pull-left"><i class="icon-plus"></i>&nbsp;&nbsp;新建</a>
            <?php } ?>
        </div>
        <div class="input-group content-search pull-right">
            <input type="text" class="form-control" id="searchtext" placeholder="搜索名称或code...">
             <span class="input-group-btn">
                 <button class="btn btn-primary" id="search" type="button">搜索</button>
             </span>
        </div>
    </div>
    <table class="table table-striped">
        <thead>
        <tr>
            <th>租户名称</th>
            <th>租户code</th>
            <th>租户类型</th>
            <th>租户底层标识符</th>
            <th>accessKey</th>
            <th>email</th>
            <th>创建人</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        <?php if(!empty($data)){
            foreach($data as $value){
                ?>
                <tr>
                    <td><?php if(isset($value['name'])){ echo $value['name'];} ?></td>
                    <td><?php if(isset($value['dept_code'])){ echo $value['dept_code'];} ?></td>
                    <td><?php if(isset($value['type'])){ switch ($value['type']) {
                        case 'normal':
                            echo "普通租户";
                            break;
                        case 'platform':
                            echo "平台租户";
                            break;
                    }} ?></td>
                    <td><?php if(isset($value['identifier'])){ echo $value['identifier'];} ?></td>
                    <td><?php if(isset($value['access_key'])){ echo $value['access_key'];} ?></td>
                    <td><?php if(isset($value['email'])){ echo $value['email'];} ?></td>
                    <td><?php if(isset($value['account']['username'])){ echo $value['account']['username'];} ?></td>
                    <td>
                        <?php  if(in_array('cmop_global_sys_admin',$this->request->session()->read('Auth.User.popedomname'))){ ?>
                            <a  href="<?php echo $this->Url->build(array('controller' => 'Departments','action'=>'edit')); ?>/<?php if(isset($value['id'])){ echo $value['id'];} ?>">修改</a> |
                            <a id="delete" href="#" onclick="deletes(<?php echo $value['id']; ?>)" >删除</a>
                    <?php }else{ ?>
                            <a  href="javascript:;" style="color: #777777">修改</a> |
                            <a ihref="javascript:;" style="color: #777777">删除</a>
                    <?php } ?>
                    </td>
                </tr>
            <?php }} ?>
        </tbody>
    </table>
    <?php if(!empty($data)){ ?>
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
    <?php } ?>
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
                <i class="icon-question-sign text-primary"></i>&nbsp;&nbsp;确认要删除该租户么？<span class="text-primary" id="sure"></span>？
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
        location.href = "<?php echo $this->Url->build(array('controller'=>'Departments','action'=>'index'));?>/index/"+name;
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
               url: '<?php echo $this->Url->build(array('controller' => 'Departments','action'=>'delete')); ?>',
               data: {id: id},
               success: function (data) {
                   var data = eval('(' + data + ')');
                   if (data.code == 0) {
                       tentionHide(data.msg, 0);
                       location.href = '<?php echo $this->Url->build(array('controller'=>'Departments','action'=>'index'));?>';
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