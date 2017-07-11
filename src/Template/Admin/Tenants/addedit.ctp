<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <form class="form-horizontal">
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">租户名称</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="name"  id="name" placeholder="租户名称" value="<?php if(isset($data['name'])){ echo $data['name'];}  ?>">
               <?php if(isset($data)){ ?>
                <input type="hidden" class="form-control" name="id"  id="id" value="<?php if(isset($data)){ echo $data['id']; } ?>">
                <?php } ?>
            </div>
            <label style="color: #ac2925;margin-top: 5px;" id="names">* 必填项</label>
        </div>

        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">密码</label>
            <div class="col-sm-6">
                <?php if(isset($data)){ ?>
                <input type="password" class="form-control" name="password" id="password" placeholder="留空即保持原密码">
            </div>
                <?php }else{ ?>
                    <input type="password" class="form-control" name="password" id="password" placeholder="密码">

            </div>
            <label style="color: #ac2925;margin-top: 5px;" id="passwords">* 必填项</label>
            <?php } ?>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">确认密码</label>
            <div class="col-sm-6">
                <input type="password" class="form-control" id="re_password" placeholder="确认密码" value="">
            </div>
            <label style="color: #ac2925;margin-top: 5px;" id="re_passwords"></label>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">电话号码</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="phone"  id="phone" placeholder="电话号码" value="<?php if(isset($data['phone'])){ echo $data['phone'];}  ?>">
            </div>
            <label style="color: #ac2925;margin-top: 5px;" id="phones"></label>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">电子邮箱</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="email"  id="email" placeholder="电子邮箱" value="<?php if(isset($data['email'])){ echo $data['email'];}  ?>">
            </div>
            <label style="color: #ac2925;margin-top: 5px;" id="emails"></label>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <a type="submit" id="ds" class="btn btn-primary">保存</a>
                <a type="submit" href="<?php echo $this->Url->build(array('controller' => 'Tenants','action'=>'index')); ?>" class="btn btn-danger">返回</a>
            </div>
        </div>
    </form>
</div>
<?= $this->start('script_last'); ?>
<script type="text/javascript">


$("#ds").click(function(){
        var validate = true;
        var name = $('#name').val();
        var password = $('#password').val();
        var re_password = $('#re_password').val();
        var phone = $('#phone').val();
        var email = $('#email').val();
        if(name){
            if(name.length<2 || name.length>16){
                $('#names').html('名称应在2到16位之间');
                validate =false;
            }else{
                $('#names').html('');
            }

        }else{
            $('#names').html('请输入租户名称');
            validate =false;
        }
        if(password){
            if(password.length<6 || password.length>16){
                $('#passwords').html('密码长度应在6到16位之间');
                validate =false;
            }else{
                $('#passwords').html('');
            }
        }else{
            var id = $("#id").val();
            if(id==''){
                $('#passwords').html('请输入密码');
                validate =false;
            }
        }
        if(re_password){
            if(password !== re_password){
                $('#re_passwords').html('两次密码输入的不同');
                validate =false;
            }else{
                $('#re_passwords').html('');
            }
        }
        if(phone != '' && !(/^(0|86|17951)?(13[0-9]|15[012356789]|17[678]|18[0-9]|14[57])[0-9]{8}$/.test(phone))){
            $('#phones').html('电话号码格式有误');
            validate =false;
        }else{
            $('#phones').html('')
        }
        if(email != '' && !(/\w+[@]{1}\w+[.]\w+/.test(email))){
            $('#emails').html('邮箱格式有误');
            validate =false;
        }else{
            $('#emails').html('')
        }
        if(validate){
            $.ajax({
                type: 'post',
                url: '<?php echo $this->Url->build(array('controller' => 'Tenants','action'=>'addedit')); ?>',
                data: $("form").serialize(),
                success: function(data) {
                    var data = eval('(' + data + ')');
                    if(data.code==0){
                        alert(data.msg);
                        location.href='<?php echo $this->Url->build(array('controller'=>'Tenants','action'=>'index'));?>';
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

