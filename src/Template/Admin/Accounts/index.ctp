<!-- 主页模板 -->
<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div id="maindiv-alert" class="content-list-page"></div>
    <div class="content-operate clearfix">
        <div class="pull-left">
            <a type="button" id="add"  val="<?php echo $department_id; ?>" href="<?php echo $this->Url->build(array('controller' => 'Accounts','action'=>'add')); ?>/<?php echo $department_id; ?>" class="btn btn-addition pull-left"><i
        class="icon-plus"></i>&nbsp;&nbsp;新建</a>

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
             <input type="text" class="form-control" id="searchtext" placeholder="搜索登陆名称或用户名称...">
             <span class="input-group-btn">
                 <button class="btn btn-primary" id="search" type="button">搜索</button>
             </span>
         </div>
    </div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>id</th>
                <th>登录名称</th>
                <th>用户名称</th>
                <th>邮箱</th>
                <th>手机号</th>
                <th>地址</th>
                <!--<th>用户状态</th>-->
                <th>租户</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php if(isset($data)){
                foreach($data as $value){
                    ?>
                    <tr>
                        <td><?php echo $value['id']; ?></td>
                        <td><?php echo $value['loginname']; ?></td>
                        <td><?php echo $value['username']; ?></td>
                        <td><?php echo $value['email']; ?></td>
                        <td><?php echo $value['mobile']; ?></td>
                        <td><?php echo $value['address']; ?></td>
                       <!-- <td><?php /*echo $value['active']; */?></td>-->
                        <td><?php echo $value['department']['name']; ?></td>
                        <td><!-- <a  href="<?php echo $this->Url->build(array('controller' => 'Accounts','action'=>'edit')); ?>/<?php if(isset($value['id'])){ echo $value['id'];} ?>">修改密码</a> | -->
                            <a  href="<?php echo $this->Url->build(array('controller' => 'Accounts','action'=>'edit')); ?>/<?php if(isset($value['id'])){ echo $value['id'];} ?>/<?php echo $department_id; ?>">修改</a> |
                            <a value="<?php echo $value['id']; ?>" href="javascript:;" onclick="deletes(<?php echo $value['id']; ?>)">删除</a>
                        </td>
                    </tr>
                    <?php
                }
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


<div class="modal fade" id="modal-delete" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">提示</h5>
            </div>
            <div class="modal-body">
                <i class="icon-question-sign text-primary"></i>&nbsp;&nbsp;确认要删除该用户么？<span class="text-primary" id="sure"></span>？
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="yes">确认</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">取消</button>
            </div>
        </div>
    </div>
</div>

<?=$this->Html->script(['adminjs.js','jquery.cookie.js']); ?>
<?= $this->start('script_last'); ?>
<script type="text/javascript">
    $('#search').on('click',function(){
        var name = $('#searchtext').val();
        var department = $('#add').attr('val');
        location.href = "<?php echo $this->Url->build(array('controller'=>'Accounts','action'=>'index'));?>/index/"+department+"/"+name;
    })

    $(function(){
        var name ="<?php echo $name ?>";
        $('#searchtext').val(name);
        var depart_name ="<?php echo $department_name; ?>";
        var dept_id ="<?php echo $department_id ?>";
        $('#depart_name').html(depart_name);
        var priont = <?php echo isset($priont)?$priont:'3'; ?>;
        if(dept_id){
            if(dept_id==0){
                $('#selects').show();
                $('#add').attr('disabled',true);
            }else{
                if(priont == '3'){
                    $('#add').attr('disabled',true);
                    $('#selects').hide();
                }else if(priont=='1'){
                    $('#add').attr('disabled',false);
                    $('#selects').show();
                }else if(priont =='2'){
                    $('#add').attr('disabled',false);
                    $('#selects').hide();
                }

            }

        }else{
            $('#selects').hide();
            $('#add').attr('disabled',true);

        }

    })


    function deletes(id){
        $('#modal-delete').modal("show");
        $('#yes').one('click',function() {
            $.ajax({
                type: "POST",
                url: '<?php echo $this->Url->build(array('controller'=>'Accounts','action'=>'delete'));?>',
                dataType: "json",
                data: {'id': id},
                success: function (data) {
                    if (data.code == 0) {
                        tentionHide(data.msg,0);
                        location.href = '<?php echo $this->Url->build(array('controller'=>'Accounts','action'=>'index'));?>/index/<?php echo $department_id; ?>';
                    } else {
                        $('#modal-delete').modal("hide");
                        tentionHide(data.msg,1);
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
        location.href = "<?php echo $this->Url->build(array('controller' => 'Accounts','action'=>'index')); ?>/<?php echo 'index';?>/"+id+"/"+name;
    }
/*

    var settings = {
        url: '<?php echo $this->Url->build(array('controller'=>'Accounts','action'=>'department'));?>',
        success: function(data){
           var data = data.replace(/name/g,"text").replace(/children/g,"nodes");
           var obj = $.parseJSON(data);
           var treeArray = new Array();
           $.each(obj,function(i,n){
               if(n.parent_id==0){
                  n.href="javascript:;";
               }
               treeArray.push(n);
           });
           $('#treeview').treeview({
              enableLinks: true,
              selectedColor: "#333",
              selectedBackColor: "#fff",
              data: treeArray
           });
        }
    }
    $.ajax(settings);

    $("#dropdown").click(
        function(){
          if($("#treeview").css("display")=="none"){
            $("#treeview").fadeIn();
          }else{
            $("#treeview").fadeOut();
          }
        }
     );
*/

</script>
<?= $this->end() ?>