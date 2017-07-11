<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div id="maindiv-alert"></div>
    <form class="form-horizontal">
        <div class="form-group">
            <label for="name" class="col-sm-2 control-label">机器名称</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="name"  id="name" placeholder="机器名称" value="<?php if(isset($department_data['name'])){ echo $department_data['name'];}  ?>">
                <?php if(isset($department_data)){ ?>
                <input type="hidden" class="form-control" name="id"  id="id" value="<?php if(isset($department_data)){ echo $department_data['id']; } ?>">
                <?php } ?>
            </div>
            <label class="control-label text-danger"><i class="icon-asterisk" id="mingcheng">2-16位，不能有空格</i></label>
        </div>
        <div class="form-group">
            <label for="title" class="col-sm-2 control-label">机器title</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="title"  id="title" placeholder="机器title"  value="<?php if(isset($department_data['title'])){ echo $department_data['title'];}  ?>">
            </div>
            <label class="control-label text-danger"><i class="icon-asterisk" id="daima">与底层机器Code保持一致</i></label>
        </div>
        <div class="form-group">
            <label for="code" class="col-sm-2 control-label">机器类型</label>
            <div class="col-sm-6">
                <select name="type" id="type" class="form-control">
                    <option value="hosts">主机</option>
                    <option value="desktop">云桌面</option>
                </select>
            </div>
            <label class="control-label text-danger"><i class="icon-asterisk" id="daima">与底层的镜像Code保持一致</i></label>
        </div>
        <div class="form-group">
            <label for="code" class="col-sm-2 control-label">所属部门</label>
            <div class="col-sm-6">
                <select name="type" id="type" class="form-control">
                    <?php foreach ($data['department_info'] as $key => $department) {?>
                        <option value="<?= $department['id']?>"><?= $department['name']?></option>
                    <?php }?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="code" class="col-sm-2 control-label">所属部门</label>
            <div class="col-sm-6">
                <select name="location_name" id="location_name" class="form-control">
                    <?php foreach ($data['agent_info'] as $agent) {?>
                        <option code="<?= $agent['class_code']?>" value="<?= $agent['display_name']?>" ><?= $agent['display_name']?></option>
                    <?php }?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="vpc" class="col-sm-2 control-label">所属VPCCode</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="vpc"  id="vpc" placeholder="镜像Code"  value="<?php if(isset($department_data['vpc'])){ echo $department_data['vpc'];}  ?>">
            </div>
            <label class="control-label text-danger"><i class="icon-asterisk" id="daima">与底层机器Code保持一致</i></label>
        </div>
        <div class="form-group">
            <label for="router" class="col-sm-2 control-label">所属路由Code</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="router"  id="router" placeholder="镜像Code"  value="<?php if(isset($department_data['router'])){ echo $department_data['router'];}  ?>">
            </div>
            <label class="control-label text-danger"><i class="icon-asterisk" id="daima">与底层机器Code保持一致</i></label>
        </div>
        <div class="form-group">
            <label for="subnet" class="col-sm-2 control-label">所属子网Code</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="subnet"  id="subnet" placeholder="镜像Code"  value="<?php if(isset($department_data['subnet'])){ echo $department_data['subnet'];}  ?>">
            </div>
            <label class="control-label text-danger"><i class="icon-asterisk" id="daima">与底层机器Code保持一致</i></label>
        </div>
        <div class="form-group">
            <label for="type" class="col-sm-2 control-label">硬件套餐</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="type"  id="type" placeholder="硬件套餐"  value="<?php if(isset($department_data['type'])){ echo $department_data['type'];}  ?>">
            </div>
            <label class="control-label text-danger"><i class="icon-asterisk" id="daima">与底层机器Code保持一致</i></label>
        </div>
         <div class="form-group">
            <label for="image_code" class="col-sm-2 control-label">镜像Code</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="image_code"  id="image_code" placeholder="镜像Code"  value="<?php if(isset($department_data['image_code'])){ echo $department_data['image_code'];}  ?>">
            </div>
            <label class="control-label text-danger"><i class="icon-asterisk" id="daima">与底层机器Code保持一致</i></label>
        </div>
        <!-- <div class="form-group">
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
        </div> -->
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
            <label class="control-label text-danger"><i class="icon-asterisk" id="paixu">1-1000的整数</i></label>
        </div>
        <div class="form-group">
            <label for="plat_form" class="col-sm-2 control-label">镜像分类（业务）</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="plat_form"  id="plat_form" placeholder="镜像分类（业务）"  value="<?php if(isset($department_data['plat_form'])){ echo $department_data['plat_form'];}  ?>">
            </div>
            <label class="control-label text-danger"><i class="icon-asterisk" id="jxfl">2-16位，不能有空格</i></label>
        </div>
        <div class="form-group">
            <label for="os_family" class="col-sm-2 control-label">操作系统</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="os_family"  id="os_family" placeholder="操作系统"  value="<?php if(isset($department_data['os_family'])){ echo $department_data['os_family'];}  ?>">
            </div>
            <label class="control-label text-danger"><i class="icon-asterisk" id="zhonglei">2-16位，不能有空格</i></label>
        </div>
        <div class="form-group">
            <label for="image_type" class="col-sm-2 control-label">镜像的应用范围</label>
            <div class="col-sm-6">
                <select class="form-control" id="image_type" name="image_type">
                    <option value="1"<?php if(isset($department_data) && $department_data['image_type']==1){ echo 'selected';}  ?>><span>云主机</span></option>
                    <option value="2"<?php if(isset($department_data) && $department_data['image_type']==2){ echo 'selected';}  ?>><span>云桌面</span></option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <a type="submit" id="ds" class="btn btn-primary">保存</a>
                <a type="submit" href="<?php echo $this->Url->build(array('controller' => 'Imagelist','action'=>'index')); ?>" class="btn btn-danger">返回</a>
            </div>
        </div>
    </form>
</div>


<?=$this->Html->script(['adminjs.js']); ?>
<?= $this->start('script_last'); ?>
<script type="text/javascript">
    $("#ds").click(function(){
        $('#gys').html('');
        $('#mingcheng').html('');
        $('#daima').html('');
        $('#czxt').html('');
        $('#jxfl').html('');
        $('#paixu').html('');
        $('#zhonglei').html('');
        var validate=true;
        var name=$("#image_name").val();
        var code=$("#image_code").val();
        var plat=$("#plat_form").val();
        var os=$("#os_family").val();
        var sort=$("#sort_order").val();
        var classCode=$('#class_code').val();
        var ostype=$('#ostype').val();
        
        if (name) {
            if (name.length < 2 || name.length > 16) {
                $('#mingcheng').html('镜像名称应在2到16位之间');
                validate = false;
            } else {
                $('#mingcheng').html('');
            }
        } else {
            $('#mingcheng').html('请输入镜像名称');
            validate = false;
        }
        if(!code){
            $('#daima').html('请输入镜像代码');
            var validate =false;
        }
        if(!sort){
            $('#paixu').html('请输入排序');
            var validate =false;
        }
        if (plat) {
            if (plat.length < 2 || plat.length > 16) {
                $('#jxfl').html('镜像分类应在2到16位之间');
                validate = false;
            } else {
                $('#jxfl').html('');
            }
        } else {
            $('#jxfl').html('请输入镜像分类（业务）');
            validate = false;
        }
        if (os) {
            if (os.length < 2 || os.length > 16) {
                $('#zhonglei').html('操作系统种类应在2到16位之间');
                validate = false;
            } else {
                $('#zhonglei').html('');
            }
        } else {
            $('#zhonglei').html('请输入操作系统种类');
            validate = false;
        }
        /* if(!ostype){
            $('#czxt').html('请输入操作系统类型');
            var validate =false;
        } */
        if(validate){
            $.ajax({
                type: 'post',
                url: '<?php echo $this->Url->build(array('controller' => 'Imagelist','action'=>'addedit')); ?>',
                data: $("form").serialize(),
                success: function(data) {
                    var data = eval('(' + data + ')');
                    if(data.code==0){
                        tentionHide(data.msg,0);
                        location.href='<?php echo $this->Url->build(array('controller'=>'Imagelist','action'=>'index'));?>';
                    }else{
                        tentionHide(data.msg,1);
                    }
                }
            });
        }
    }
    );
</script>
<?= $this->end() ?>