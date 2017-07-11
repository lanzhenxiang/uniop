
<div class="wrap-nav-right wrap-index-page">
  <div class="index-total section">
    <div class="section-header clearfix relative">
      <h5 class="pull-left">
        账户信息
      </h5>
      <div id="maindiv-alert"></div>
    </div>
    <div class="section-body change-pw clearfix">
      <form id="account-info-form" action="<?php echo $this->Url->build(array('controller' => 'Accountinfo','action'=>'index')); ?>" method="post">
        <div class="form-group">
          <label for="">用户名</label>
          <input name="username" id="username" value="<?php echo isset($data)?$data['username']:''; ?>" type="text">
          <span class="text-danger username"></span>
        </div>
        <div class="form-group">
          <label for="">登录名</label>
          <input name="loginname" disabled="disabled" id="loginname" value="<?php echo isset($data)?$data['loginname']:''; ?>" type="text">
        </div>
        <div class="form-group">
          <label for="">所在地址</label>
          <input name="address" id="address" value="<?php echo isset($data)?$data['address']:''; ?>" type="text">
          <span class="text-danger address"></span>
        </div>
        <div class="form-group">
          <label for="">手机号码</label>
          <input name="mobile" id="mobile" value="<?php echo isset($data)?$data['mobile']:''; ?>" type="text">
          <span class="text-danger mobile"></span>
        </div>
        <div class="form-group">
          <label for="">常用邮箱</label>
          <input name="email" id="loginemail" value="<?php echo isset($data)?$data['email']:''; ?>" type="text">
          <span class="text-danger loginemail"></span>
        </div>

        <div class="form-group">
          <label for="">账户头像</label>
          <div class="image-editor">
            <input type="file" id="file"  class="cropit-image-input">
            <input type="hidden" name="image" id="image" class="cropit-image-input">
            <a  class="btn btn-primary change-images">选择图片</a>
            <div class="cropit-image-preview" style="background-image:url(/<?php if(isset($data)){
              if(!empty($data['image'])){ echo $data['image']; }else{ echo 'images/user-photo.png'; }
            } ?>);"></div>
            <div class="image-size-label">
              调节图片大小
            </div>
            <input type="range" class="cropit-image-zoom-input">
            <button class="export btn btn-primary" type="button">保存图片</button>
            <span class="icon icon-image large-image"></span>
          </div>
        </div>

        <div class="form-group">
          <label for=""></label>
          <button type="submit" id="submit" class="btn btn-primary" >确认修改</button>
        </div>
      </form>
    </div>    

  </div>

</div>

<?= $this->Html->script(['validator.bootstrap.js']); ?>
<script>
  $(function(){
    $('.image-editor').cropit();
    $('.change-images').click(function(){
      $('.cropit-image-input').click();
    });
    $('.export').click(function() {
      var imageData = $('.image-editor').cropit('export');
      var formData = new FormData();
      formData.append('file', imageData);
      var xhr = new XMLHttpRequest();
      xhr.open('POST','<?php echo $this->Url->build(array('controller' => 'Accountinfo','action'=>'images')); ?>',true);
      xhr.onreadystatechange = function() {
        if (xhr.readyState == 4) {
          if (xhr.status == 200) {
            var msg = xhr.responseText;
            $('#image').val(msg);
          }
        }
      }
      xhr.send(formData);
    });


  });

  // $('#submit').on('click',function(){
  //   var valid = true;
  //   var username = $("#username").val();
  //   var loginemail = /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+/;
  //   var email = $("#loginemail").val();
  //   var address = $("#address").val();
  //   var mobile = $("#mobile").val();
  //   var mobileValid = /^(0|86|17951)?(13[0-9]|15[012356789]|17[0678]|18[0-9]|14[57])[0-9]{8}$/;
  //   if(username == ''){
  //     $(".username").html('账户昵称不能为空');
  //     valid = false;
  //   }else{
  //     $(".username").html('');
  //   }
  //   if(loginemail.test(email)){
  //     $(".loginemail").html('');

  //   }else{
  //     $(".loginemail").html('邮箱格式有问题');
  //     valid = false;
  //   }
  //   if(address == ''){
  //     $(".address").html('地址不能为空');
  //     valid = false;
  //   }else{
  //     $(".address").html('');
  //   }
  //   if(mobileValid.test(mobile)){
  //     $(".mobile").html('');
  //   }else{
  //     $(".mobile").html('手机号填写有问题');
  //     valid = false;
  //   }
  //   if(valid){
  //     $.ajax({
  //       method:'post',
  //       url:'<?php echo $this->Url->build(array('controller' => 'Accountinfo','action'=>'index')); ?>',
  //       data:$("form").serialize(),
  //       success:function(data){
  //         data = $.parseJSON(data);
  //         if(data.code=='0000'){
  //           tentionHide(data.msg,0);
  //           window.location.reload();
  //         }else{
  //           tentionHide(data.msg,1);
  //         }
  //       }
  //     })
  //   }

  // })

$('#account-info-form').bootstrapValidator({
  submitButtons: 'button[type="submit"]',
  submitHandler: function(validator, form, submitButton){
    $.post(form.attr('action'), form.serialize(), function(data){
      data = $.parseJSON(data);
      if(data.code=='0000'){
        tentionHide(data.msg,0);
        window.location.reload();
      }else{
        tentionHide(data.msg,1);
      }
    });
  },
  fields : {
    username: {
      // group: '.col-sm-6',
      validators: {
        notEmpty: {
          message: '用户名不能为空'
        },
        stringLength: {
          min: 2,
          max: 16,
          message: '请保持在2-16位'
        },
        regexp: {
          regexp: /^\S*$/,
          message: '用户名中不能有空格'
        }
      }
    },
    mobile: {
      // group: '.col-sm-6',
      validators: {
        notEmpty: {
          message: '手机号不能为空'
        },
        regexp: {
          regexp: /^(0|86|17951)?(13[0-9]|15[012356789]|17[678]|18[0-9]|14[57])[0-9]{8}$/,
          message: '请输入正确的手机号'
        }
      }
    },
    email: {
      // group: '.col-sm-6',
      validators: {
        notEmpty: {
          message: '邮箱不能为空'
        },
        emailAddress: {
          message: '邮箱格式不对'
        }
      }
    },
  }
});

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

</script>