<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <form class="form-horizontal">
        <div class="form-group">
            <label for="agent_id" class="col-sm-2 control-label">所在供应商</label>
            <div class="col-sm-6">
                <select class="form-control" id="agent_id" name="agent_id">
                    <option value=""><span>请选择供应商</span></option>
                    <?php foreach ($query as $key => $value) {   ?>
                    <option value="<?php echo $key;?>" <?php if(isset($department_data) && $department_data['agent_id']==$key){ echo 'selected';}  ?>>
                        <span><?php echo $value;?></span>
                    </option>
                    <?php }   ?>
                </select>
            </div>
            <label class="control-label text-danger"><i class="icon-asterisk" id="gys"></i></label>
        </div>
        <div class="form-group">
            <label for="image_name" class="col-sm-2 control-label">镜像名称</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="image_name"  id="image_name" placeholder="镜像名称" value="<?php if(isset($department_data['image_name'])){ echo $department_data['image_name'];}  ?>">
                <?php if(isset($department_data)){ ?>
                <input type="hidden" class="form-control" name="id"  id="id" value="<?php if(isset($department_data)){ echo $department_data['id']; } ?>">
                <?php } ?>
            </div>
            <label class="control-label text-danger"><i class="icon-asterisk" id="mingcheng"></i></label>
        </div>
        <div class="form-group">
            <label for="image_code" class="col-sm-2 control-label">镜像代码</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="image_code"  id="image_code" placeholder="镜像代码"  value="<?php if(isset($department_data['image_code'])){ echo $department_data['image_code'];}  ?>">
            </div>
            <label class="control-label text-danger"><i class="icon-asterisk" id="daima"></i></label>
        </div>
        <div class="form-group">
            <label for="ostype" class="col-sm-2 control-label">操作系统类型</label>
            <div class="col-sm-6">
                <select class="form-control" id="ostype" name="ostype">
                    <option value=""><span>请选择系统类型</span></option>
                    <option value="1"<?php if(isset($department_data) && $department_data['ostype']==1){ echo 'selected';}  ?>><span>Linux</span></option>
                    <option value="2"<?php if(isset($department_data) && $department_data['ostype']==2){ echo 'selected';}  ?>><span>Windows Server</span></option>
                    <option value="3"<?php if(isset($department_data) && $department_data['ostype']==3){ echo 'selected';}  ?>><span>Windows Desktop</span></option>
                    
                </select>
            </div>
            <label class="control-label text-danger"><i class="icon-asterisk" id="czxt"></i></label>
        </div>
        <div class="form-group">
            <label for="image_note" class="col-sm-2 control-label">镜像说明</label>
            <div class="col-sm-6">
                <textarea type="text" class="form-control" rows="4" name="image_note" id="image_note" placeholder="镜像说明 "><?php if(isset($department_data)){ echo $department_data['image_note'];}?></textarea>
            </div>
            <!-- <label id="miaoshu" style="color:#ac2925;margin-top: 5px;">* 必填项</label> -->
        </div>
        <div class="form-group">
            <label for="sort_order" class="col-sm-2 control-label">排序</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="sort_order"  id="sort_order" placeholder="排序"  value="<?php if(isset($department_data['sort_order'])){ echo $department_data['sort_order'];}  ?>">
            </div>
            <label class="control-label text-danger"><i class="icon-asterisk" id="paixu"></i></label>
        </div>
        <div class="form-group">
            <label for="plat_form" class="col-sm-2 control-label">镜像分类（业务）</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="plat_form"  id="plat_form" placeholder="镜像分类（业务）"  value="<?php if(isset($department_data['plat_form'])){ echo $department_data['plat_form'];}  ?>">
            </div>
            <label class="control-label text-danger"><i class="icon-asterisk" id="czxt"></i></label>
        </div>
        <div class="form-group">
            <label for="os_family" class="col-sm-2 control-label">操作系统</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="os_family"  id="os_family" placeholder="操作系统"  value="<?php if(isset($department_data['os_family'])){ echo $department_data['os_family'];}  ?>">
            </div>
            <label class="control-label text-danger"><i class="icon-asterisk" id="zhonglei"></i></label>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <a type="submit" id="ds" class="btn btn-primary">保存</a>
                <a type="submit" class="btn btn-danger">返回</a>
            </div>
        </div>
    </form>
</div>
<?= $this->start('script_last'); ?>
<script type="text/javascript">
    $("#ds").click(function(){
        $('#gys').html('');
        $('#mingcheng').html('');
        $('#daima').html('');
        $('#czxt').html('');
        $('#paixu').html('');
        $('#zhonglei').html('');
        var validate=true;
        var agent=$("#agent_id").val();
        var name=$("#image_name").val();
        var code=$("#image_code").val();
        var plat=$("#plat_form").val();
        var os=$("#os_family").val();
        var sort=$("#sort_order").val();
        var classCode=$('#class_code').val();
        if(sort> 256){
            $('#paixu').html('请输入小于255的数');
            var validate =false;
        }
        if(!(/^[1-9]\d?$/i.test(sort)))
        {
            $('#paixu').html('请输入正确的排序');
            var validate =false;
        }
        if(!name){
            $('#mingcheng').html('请输入镜像名称');
            var validate =false;
        }
        if(!code){
            $('#daima').html('请输入镜像代码');
            var validate =false;
        }
        if(!sort){
            $('#paixu').html('请输入排序');
            var validate =false;
        }
        if(!agent){
            $('#gys').html('请选择供应商');
            var validate =false;
        }
        if(!plat){
            $('#czxt').html('请选择操作系统类型');
            var validate =false;
        }
        if(!os){
            $('#zhonglei').html('请输入操作系统种类');
            var validate =false;
        }
        if(validate){
            $.ajax({
                type: 'post',
                url: '<?php echo $this->Url->build(array('controller' => 'AgentImagelist','action'=>'addedit')); ?>',
                data: $("form").serialize(),
                success: function(data) {
                    var data = eval('(' + data + ')');
                    if(data.code==0){
                        alert(data.msg);
                        location.href='<?php echo $this->Url->build(array('controller'=>'AgentImagelist','action'=>'index'));?>';
                    }else{
                        alert(data.msg);
                    }
                }
            });
        }
    }
    );
</script>
<?= $this->end() ?>