<!-- 主页模板 -->
<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div id="maindiv-alert"  class="content-list-page"></div>
    <div class="content-operate clearfix">
        <div class="pull-left">
            <a  href="<?php echo $this->Url->build(array('controller' => 'Roles','action'=>'addedit')); ?>" class="btn btn-addition"><i
        class="icon-plus"></i>&nbsp;&nbsp;新建</a>
        </div>
         <div class="dropdown pull-left" style="margin-left:30px;" id="selects">
                <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                    租户
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                <?php if(in_array('cmop_global_sys_admin',$this->request->session()->read('Auth.User.popedomname'))){ ?>
                    <li>
                        <a href="javascript:;" onclick="local(-1)">全部</a>
                    </li>
                    <li>
                        <a href="javascript:;" onclick="local(-2)">系统公用</a>
                    </li>
                    <?php } ?>
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
        <div class="input-group content-search pull-right">
            <input type="text" class="form-control" id="searchtext" placeholder="搜索角色名称...">
             <span class="input-group-btn">
                 <button class="btn btn-primary" id="search" type="button">搜索</button>
             </span>
        </div>
    </div>

    <table class="table table-striped">
        <thead>
        <tr>
            <th>序列号</th>
            <th>角色名称</th>
            <th>角色说明</th>
            <th>角色模式</th>
            <th>创建时间</th>
            <th>修改时间</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        <?php if(isset($data)){
            /* if ($this->request->session()->read("Auth.User.popedomname")) { */
            $popedomname = $this->request->session()->read("Auth.User.popedomname");
                foreach($data as $value){
                   /*  if($value['department_id'] ==0 || $value['department_id'] == $this->request->session()->read("Auth.User.department_id")){ */
            ?>
        <tr>
            <td><?php if(isset($value['id'])){ echo $value['id'];} ?></td>
            <td><?php if(isset($value['name'])){ echo $value['name'];} ?></td>
            <td><?php if(isset($value['note'])){ echo $value['note'];} ?></td>
            <td><?php if(isset($value['department_id'])){ if($value['department_id']==0){ echo '系统公用'; }else{ echo '租户专用';}} ?></td>
            <td><?php if(isset($value['created'])){ echo $value['created'];} ?></td>
            <td><?php if(isset($value['modified'])){ echo $value['modified'];} ?></td>
            <td>       
                <a  href="<?php echo $this->Url->build(array('controller' => 'Roles','action'=>'addedit')); ?>/<?php if(isset($value['id'])){ echo $value['id'];} ?>">修改</a> |
                <a  href="<?php echo $this->Url->build(array('controller' => 'Roles','action'=>'addpopedom')); ?>/<?php if(isset($value['id'])){ echo $value['id'];} ?>">修改权限</a> |
                <a id="delete" href="#" onclick="deletes(<?php echo $value['id']; ?>)" >删除</a></td>
        </tr>
        <?php } }?>
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
                <i class="icon-question-sign text-primary"></i>&nbsp;&nbsp;确认要删除该角色么？<span class="text-primary" id="sure"></span>？
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
        location.href = "<?php echo $this->Url->build(array('controller'=>'Roles','action'=>'index'));?>/index/-1/"+name;
    })
    $(function() {
        var name ="<?php echo $name ?>";
        $('#searchtext').val(name);
        var depart_name ="<?php echo $department_name; ?>";
        $('#depart_name').html(depart_name);
    })
    function deletes(id){
        /* alert(window.location.pathname); */
        $('#modal-delete').modal("show");
        $('#yes').one('click',function() {
            $.ajax({
                type: 'post',
                url: '<?php echo $this->Url->build(array('controller' => 'Roles','action'=>'delete')); ?>',
                data: {id: id,url:window.location.pathname},
                success: function (data) {
                    var data = eval('(' + data + ')');
                    if (data.code == 0) {
                        tentionHide(data.msg, 0);
                        location.href = data.url;
                    } else {
                        $('#modal-delete').modal("hide");
                        tentionHide(data.msg, 1);
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
        location.href = "<?php echo $this->Url->build(array('controller' => 'Roles','action'=>'index')); ?>/<?php echo 'index';?>/"+id+"/"+name;
    }

</script>
<?= $this->end() ?>