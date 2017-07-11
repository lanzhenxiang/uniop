
<div class="wrap-nav-right wrap-index-page ">

    <div class="index-total section">
        <div class="section-header clearfix relative">

            <h5 class="pull-left">
                修改密码
            </h5>
            <div id="maindiv-alert"></div>
        </div>
        <div class="section-body change-pw clearfix">
            <form id="password-form" action="<?= $this->Url->build(['prefix'=>'console','controller'=>'Password','action'=>'index']); ?>" method="post">
                <div class="form-group">
                    <label for="">旧密码</label>
                    <input style="display:none">
                    <input name="old_password" id="old_password" type="password">
                    <span class="text-danger old_password"></span>
                </div>
                <div class="form-group">
                    <label for="">新密码</label>
                    <input style="display:none" autocomplete="off">
                    <input name="password" type="password" id="password">
                    <span class="text-danger password"></span>
                </div>
                <div class="form-group">
                    <label for="">确认密码</label>
                    <input style="display:none" autocomplete="off">
                    <input name="repassword" type="password" id="repassword">
                    <span class="text-danger repassword"></span>
                </div>
                <div class="form-group">
                    <label for=""></label>
                    <button type="submit" id="submit" class="btn btn-primary">确认修改</button>
                </div>
            </form>
        </div>    
        
    </div>

</div>
<?=$this->Html->script(['validator.bootstrap.js']); ?>
<?php  $this->start('script_last'); ?>
<script type="text/javascript">

    function tentionHide(content, state) {
        $("#maindiv-alert").empty();
        var html = "";
        if (state == 0) {
            html += '<div class="point-host-startup "><i></i>' + content + '</div>';
            $("#maindiv-alert").append(html);
            $(".point-host-startup ").slideUp(3000);
        } else {
            html += '<div class="point-host-startup point-host-startdown"><i></i>' + content + '</div>';
            $("#maindiv-alert").append(html);
            $(".point-host-startdown").slideUp(3000);
        }
    }

    
    
//     $('#submit').on('click',function(){
//         var valid = true;
//         var oldPassword = $("#old_password").val();
//         var password = $("#password").val();
//         var repassword = $("#repassword").val();
//         var reg=/\S{6,}/;
//         if( reg.test(oldPassword)){
//          $(".old_password").html("");
//      }else{
//         $(".old_password").html("请输入6-16位的密码");
//         valid = false;
//     }
//     if(oldPassword == password){
//         $(".password").html("旧密码和新密码不能相同");
//         valid = false;
//     }else if(reg.test(password)){
//         $(".password").html("");
//     }else{
//         $(".password").html("请输入6-16位的密码");
//         valid = false;
//     }
//     if(password == repassword){
//         $(".repassword").html('');
//     }else{
//        $(".repassword").html('新密码和确认密码请输入一样的');
//        valid = false;
//    }
//    if(valid){
//     $.ajax({
//         type: 'post',
//         url: '<?= $this->Url->build(['prefix'=>'console','controller'=>'Password','action'=>'index']); ?>',
//         data: $("form").serialize(),
//         success: function(data) {
//             var data = eval('(' + data + ')');
//             if(data.code==0){
//                 tentionHide(data.msg,0);
//                 location.href='<?= $this->Url->build(['prefix'=>'console','controller'=>'Password','action'=>'index']); ?>';
//             }else{
//                 tentionHide(data.msg,1);
//             }
//         }
//     });
// }
// })
$('#password-form').bootstrapValidator({
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
            if(data.code==0){
                tentionHide(data.msg,0);
                location.href='<?= $this->Url->build(['prefix'=>'console','controller'=>'Password','action'=>'index']); ?>';
            }else{
                tentionHide(data.msg,1);
            }
        });
    },
    fields : {
        old_password: {
            validators: {
                notEmpty: {
                    message: '旧密码不能为空'
                },
                stringLength: {
                    min: 6,
                    max: 16,
                    message: '请保持在6-16位'
                },
                regexp: {
                    regexp: /^\S*$/,
                    message: '旧密码不能有空格'
                },different: {
                    field: 'password',
                    message: '旧密码与新密码不能相同'
                }
            }
        },
        
        password: {
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
                },
                different: {
                    field: 'old_password',
                    message: '请输入与旧密码不相同的密码'
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
        
    }
});  
</script>
<?php $this->end(); ?>