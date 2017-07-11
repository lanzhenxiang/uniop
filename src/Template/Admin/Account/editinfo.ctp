<style>
    .point-host-startup{
        margin-right: 150px;
    }
</style>

<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div id="maindiv-alert"></div>
    <form class="form-horizontal" id="account-form"
          action="<?php echo $this->Url->build(array('controller' => 'Account','action'=>'postedit')); ?>"
          method="post">
        <div>
            <div class="content-operate clearfix">
                <div class="pull-left">
                    <span style="font-size: 20px;">修改个人信息</span>
                </div>

            </div>
            <hr>
            <!--添加内容-->
            <input type="hidden" name="id" value="<?=$account_info['id'];?>">
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="home">
                    <!--<div class="form-group">-->
                        <!--<label for="inputEmail3" class="col-sm-2 control-label">登录名:</label>-->
                        <!--<div class="col-sm-6">-->
                            <!--<input type="text" class="form-control" name="loginname" id="loginname" value="<?=$account_info['loginname'];?>">-->
                        <!--</div>-->
                    <!--</div>-->

                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">姓名:</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="username" id="username" value="<?=$account_info['username'];?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">手机:</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="mobile" id="mobile" value="<?=$account_info['mobile'];?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">邮箱:</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="email" id="email" value="<?=$account_info['email'];?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">地址:</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="address" id="address" value="<?=$account_info['address'];?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="type" class="col-sm-2 control-label">租户:</label>
                        <div class="col-sm-6">
                            <select class="form-control" id="department" name="department" onchange="changeType()">
                                <option value="" data-name="请选择"><sapn>请选择</sapn></option>
                                <?php if(isset($info)&&!empty($info)){
                                foreach($info as $key => $value){?>
                                <option value="<?php echo $value['id'];?>" <?php if($value['id']==$account_info['department_id']){echo 'selected';}?>><span><?php echo $value['name'];?></span></option>
                                <?php }?>
                                <?php }?>
                            </select>
                        </div>
                    </div>
                    <!--租户选择提示-->
                    <div class="form-group" id="depar_div">
                        <label for="type_note" class="col-sm-2 control-label"></label>
                        <div class="col-sm-6">
                            <label class="control-label text-danger"><i class="icon-exclamation-sign"  id="depart_note">保存信息前，请选择租户</i></label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="type" class="col-sm-2 control-label">有效期:</label>
                        <div class="col-sm-6" id="date-mode-control">
                            <input type="radio" name="expire" checked="true" value="-1" <?php if($account_info['expire']==-1){echo 'checked';}?>/> 永久
                            <input type="radio" name="expire" value="0" <?php if($account_info['expire']!=-1){echo 'checked';}?>/> 临时
                        </div>
                    </div>
                    <div class="form-group" id="date-mode" style="display:<?php if($account_info['expire']==-1){echo 'none';}?>;padding-top:5px;">
                        <label for="expire" class="col-sm-2 control-label">截止日期</label>
                        <div class="col-sm-6">
                            <div class="input-append date" id="datetimepicker"  data-date-format="yyyy-mm-dd">
                                <input size="16" type="text" name="time" id="time"  readonly value="<?php if( $account_info['expire']!=-1){echo  $account_info['expire_new'];}?>">
                                <span class="add-on"><i class="icon-th"></i></span>
                            </div>
                        </div>
                    </div>

                    <!--<div class="form-group">-->
                    <!--<label for="inputEmail3" class="col-sm-2 control-label">备注</label>-->
                    <!--<div class="col-sm-6">-->
                    <!--<textarea type="text" class="form-control" rows="6" name="note" id="note"></textarea>-->
                    <!--</div>-->
                    <!--</div>-->
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" id="submit_add" class="btn btn-primary">保存</button>
                            <!--<a type="button" href="<?php echo $this->Url->build(array('controller' => 'Account','action'=>'index')); ?>" class="btn btn-danger">返回</a>-->
                            <a type="button" id="back" class="btn btn-danger" onclick="window.history.go(-1)">返回</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<?=$this->Html->script(['adminjs.js','bootstrap-datetimepicker.js','validator.bootstrap.js','jquery.cookie.js']); ?>
<?= $this->start('script_last'); ?>
<script type="text/javascript">
//$('#back').on('click',function(){
//    console.log(window.history.go(-1));
//});



    //切换有效期
    var myDate = new Date();
    var year = myDate.getFullYear();
    var month =myDate.getMonth()+1;
    var day =  myDate.getDate();
    var time =year+'-'+month+'-'+day;
    $('#datetimepicker').datetimepicker({
        autoclose:true,
        minView:2,
        startDate:time
    });
    $('#date-mode-control').on('change','input[type="radio"]',function(){
        if($(this).val()=="0"){
            $('#date-mode').css('display','block');
        }else{
            $('#date-mode').css('display','none');
        }
    });
    //    选择租户
    function changeType(){
        var department=$('#department').val();
        if(department==''){
            $('#depar_div').show();
        }else{
            $('#depar_div').hide();
        }
    }


    //提交
    $('#account-form').bootstrapValidator({
        submitButtons: 'button[type="submit"]',
        submitHandler: function(validator, form, submitButton){
            var type = $("input:radio[name='date-mode']:checked").val();
            if(type==-1){
                var time='';
            }else if(type==0){
                var time=$("#time").val();
            }
            $.post(form.attr('action'), form.serialize(), function(data){
                var data = eval('(' + data + ')');
                if (data.code == 0) {
                    tentionHide(data.msg,0);
                    setTimeout(function () {
//                        location.href = "<?php echo $this->Url->build(array('controller'=>'Account','action'=>'index'));?>";
                        window.location.reload();
                    }, 500);

                } else {
                    tentionHide(data.msg,1);
                }
            });
        },
        fields : {
//            loginname: {
//                group: '.col-sm-6',
//                validators: {
//                    notEmpty: {
//                        message: '登录名不能为空'
//                    },
//                    stringLength: {
//                        min: 2,
//                        max: 16,
//                        message: '请保持在2-16位'
//                    },
//                    regexp: {
//                        regexp: /^[^\u4e00-\u9fa5\s]{0,}$/,
//                        message: '登录名中不能有空格和中文'
//                    }
//                }
//            },
            username: {
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '姓名不能为空'
                    },
                    stringLength: {
                        min: 2,
                        max: 16,
                        message: '请保持在2-16位'
                    }
                }
            },
            password: {
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '密码不能为空'
                    },
                    stringLength: {
                        min: 6,
                        max: 16,
                        message: '请保持在6-16位'
                    },
                    regexp: {
                        regexp: /^\S*$/,
                        message: '密码不能有空格'
                    }
                }
            },
            repassword: {
                trigger: 'submit',
                validators: {
                    notEmpty: {
                        message: '确认密码不能为空'
                    },
                    identical: {
                        field: 'password',
                        message: '请输入相同的密码'
                    }
                }
            },
            mobile: {
                group: '.col-sm-6',
                validators: {
//                    notEmpty: {
//                        message: '手机号不能为空'
//                    },
                    regexp: {
                        regexp: /^(0|86|17951)?(13[0-9]|15[012356789]|17[678]|18[0-9]|14[57])[0-9]{8}$/,
                        message: '请输入正确的手机号'
                    }
                }
            },
            email: {
                group: '.col-sm-6',
                validators: {
//                    notEmpty: {
//                        message: '邮箱不能为空'
//                    },
                    emailAddress: {
                        message: '邮箱格式不对'
                    }
                }
            },
            department: {
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '请选择所属租户'
                    }
                }
            }
        }
    });

</script>
<?= $this->end() ?>