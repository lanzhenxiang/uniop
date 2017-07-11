<?= $this->element('desktop/lists/left',['active_action'=>'group']); ?>
<script src="/js/layer2/layer.js"></script>
<div class="wrap-nav-right">
    <div class="wrap-manage">
        <div class="top">
            <span class="title">添加分组</span>
            <div id="maindiv-alert"></div>
        </div>

        <div class="center clearfix">
           
        <form class="form-horizontal" id="goods-category-form" action="" onsubmit="ajaxform();return false;" method="post">
        <div class="form-group">
            <input type="hidden" name="department_id" value="<?php if(isset($department_id)&&!empty($department_id)){echo $department_id;}else{echo 0;}?>">
            <label for="name" class="col-sm-2 control-label">分组名</label>
            <div class="col-sm-6">
                <input class="form-control" name="software_name" id="software_name" value="<?php if(isset($group)){echo $group->software_name;}?>" type="text">
            </div>
        </div>

        <div class="form-group">
            <label for="name" class="col-sm-2 control-label">分组排序</label>
            <div class="col-sm-6">
                <input class="form-control" name="sort_order" id="sort_order" value="<?php if(isset($group)){echo $group->sort_order;}?>"  type="text">
            </div>
        </div>

        <div class="form-group">
            <label for="description" class="col-sm-2 control-label">备注</label>
            <div class="col-sm-6">
                <textarea type="text" class="form-control" name="note" id="note"><?php if(isset($group)){echo $group->note;}?></textarea>
            </div>
        </div>




        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" id="ds" class="btn btn-primary">保存</button>
                <a type="button" href="/console/desktop/group" class="btn btn-danger">返回</a>
            </div>
        </div>
</form>

            <div style="clear: both;"></div>
        </div>
      
    </div>
</div>


<div class="modal fade" id="modal-msg" tabindex="-1" role="dialog"></div>

<?= $this->start('script_last'); ?>
<script type="text/javascript">
function ajaxform(){
    
    
     layer.msg('数据保存中....', {icon: 4, shade:0.3, time:0});

      $.ajax({
                cache: true,
                type: "POST",
                url:window.location.href,
                data:$('#goods-category-form').serialize(),// 你的formid
                error: function(request) {
                    layer.closeAll()
                     alert("Connection error");
                },
                success: function(data) {
                     layer.closeAll()
                     layer.alert('操作成功', {icon: 6});
                    setTimeout( function(){window.location.href="/console/desktop/group" }, 1 * 1000 );
                }
            });
   }

</script>
<?= $this->end() ?>