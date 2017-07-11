<?= $this->element('desktop/lists/left',['active_action'=>'group']); ?>
<script src="/js/layer2/layer.js"></script>
<div class="wrap-nav-right">
    <div class="wrap-manage">
        <div class="top">
            <span class="title">弹性伸缩管理</span>
            <div id="maindiv-alert"></div>
        </div>

        <div class="clearfix" style="padding-top:30px;">
           
        <form class="form-horizontal" id="goods-category-form" action="" onsubmit="ajaxform();return false;" method="post">
        <div class="form-group">
            <label for="name" class="col-sm-2 control-label">伸缩策略名</label>
            <div class="col-sm-6">
                <input class="form-control" name="name" id="name" value="<?php if(isset($groupPolicy)){echo $groupPolicy->name;}?>" type="text">
            </div>
        </div>

        <div class="form-group">
            <label for="name" class="col-sm-2 control-label"></label>
            <div class="col-sm-6">
                <label class="radio-inline"><input style="padding:0px"  <?php if(isset($groupPolicy) && $groupPolicy->status == 1 ){echo 'checked="checked"';}?> name="status" id="status" value="1" type="radio"> 启用策略</label> 
                 <label class="radio-inline"><input style="padding:0px" <?php if(!isset($groupPolicy) || $groupPolicy->status !=1 ){echo 'checked="checked"';}?>  name="status" id="status" value="0" type="radio"> 停用策略</label> 
            </div>
        </div>


        <div class="form-group">
            <label for="name" class="col-sm-2 control-label">最低保有量</label>
            <div class="col-sm-6">
                <input class="form-control" name="min" id="min" value="<?php if(isset($groupPolicy)){echo $groupPolicy->min;}?>" type="text">
                <p>空闲桌面的最低数，整数，最小为0，最大值不能超过加入分组中的桌面数</p>
                <p>当前分组的桌面站点数为：( <?=$count?>)</p>
            </div>
        </div>




        <div class="form-group">
            <label for="name" class="col-sm-2 control-label">优先级模式</label>
            <div class="col-sm-6">
                <label class="radio-inline"><input style="padding:0px" name="priority" <?php if(!isset($groupPolicy) || $groupPolicy->priority !=0 ){echo 'checked="checked"';}?> id="priority" value="1" type="radio"> 启用</label> 
                 <label class="radio-inline"><input style="padding:0px" <?php if(isset($groupPolicy) && $groupPolicy->priority ==0 ){echo 'checked="checked"';}?>  name="priority" id="priority" value="0" type="radio"> 不启用</label> 
                  <p>弹性伸缩启用优先级模式后</p>
                  <p>开机时，按优先级从高到低顺序执行</p>
                  <p>关机时，按优先级从低到高顺序执行</p>
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
     ex = /^\d+$/;
     layer.msg('数据保存中....', {icon: 4, shade:0.3, time:0});
     console.log($('#goods-category-form').serialize());

      if($("#min").val()<0 || !ex.test($("#min").val()) ){
        layer.alert('最低保有量必须是正整数', {icon: 2});return false;
     }

     if(  $("#min").val() > <?=$count?>){
         layer.alert('最低保有量不能大于分组中的桌面数量', {icon: 2});return false;
     }


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
                     setTimeout( function(){location.reload()}, 1 * 1000 );
                }
            });
   }

</script>
<?= $this->end() ?>