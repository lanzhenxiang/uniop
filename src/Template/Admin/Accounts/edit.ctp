<?= $this->element('content_header'); ?>
<?= $this->Html->css(['bootstrap-datetimepicker.min.css']); ?>
    <style>
        #date-mode-control{
            padding-top:5px;
            padding-bottom:5px;
        }
    </style>
<div class="content-body clearfix">
    <div id="maindiv-alert"></div>
    <form class="form-horizontal" id="account-form" action="<?php echo $this->Url->build(array('controller'=>'Accounts','action'=>'editadd'));?>" method="post">
        <div>
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">基础信息</a></li>
                <li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">角色定义</a></li>
                <!-- <li role="presentation"><a href="#setting" aria-controls="setting" role="tab" data-toggle="tab">参数设置</a></li> -->
            </ul>
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="home">
                    <div class="form-group">
                        <label for="loginname" class="col-sm-2 control-label">登录名</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="loginname" name="loginname" value="<?php echo $acc[0]['loginname']?>" placeholder="登录名">
                        </div>
                    </div>
                <div class="form-group">
                    <input type="hidden" value="<?php echo $acc[0]['id']?>" name="userid" id="userid">
                    <label for="username" class="col-sm-2 control-label">用户名</label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" id="username" name="username" value="<?php echo $acc[0]['username']?>" placeholder="用户名">
                    </div>
                </div>
                <div class="form-group">
                    <label for="password" class="col-sm-2 control-label">密码</label>
                    <div class="col-sm-6">
                        <input style="display:none" autocomplete="off">
                        <input type="password" class="form-control" name="password" id="password" placeholder="需要修改密码时填写" autocomplete="off">
                    </div>
                </div>
                <div class="form-group">
                    <label for="repassword" class="col-sm-2 control-label">确认密码</label>
                    <div class="col-sm-6">
                        <input type="password" class="form-control" name="repassword" id="repassword" placeholder="需要修改密码时填写">
                    </div>
                </div>
                <div class="form-group">
                    <label for="telphone" class="col-sm-2 control-label">手机号码</label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" name="mobile" id="telphone" value="<?php echo $acc[0]['mobile']?>" placeholder="手机号码">
                    </div>
                </div>
                <div class="form-group">
                    <label for="email" class="col-sm-2 control-label">邮箱</label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" id="email" name="email" value="<?php echo $acc[0]['email']?>" placeholder="邮箱">
                    </div>
                </div>
                <div class="form-group">
                    <label for="address" class="col-sm-2 control-label">地址</label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" id="address" name="address" value="<?php echo $acc[0]['address']?>" placeholder="地址">
                    </div>

                </div>
                    <div class="form-group">
                        <label for="expire" class="col-sm-2 control-label">时间模式</label>
                        <div class="col-sm-6" id="date-mode-control">
                            <input type="radio" name="date-mode" <?php if($acc[0]['expire'] == '-1'){ echo "checked='checked'";} ?> value="-1" /> 永久
                            <input type="radio" name="date-mode" <?php if($acc[0]['expire'] != '-1'){ echo "checked='checked'";} ?> value="0" /> 选择时间
                        </div>
                    </div>

                    <div class="form-group" id="date-mode" style="display:<?php if($acc[0]['expire'] == '-1'){ echo 'none';}else{ echo 'display'; }  ?>;padding-top:5px;">
                        <label for="expire" class="col-sm-2 control-label">过期时间</label>
                        <div class="col-sm-6">
                            <div class="input-append date" id="datetimepicker"  data-date-format="yyyy-mm-dd">
                                <input size="16" type="text" name="time" id="time" value="<?php if($acc[0]['expire'] != '-1'){ echo date("Y-m-d",$acc[0]['expire']-86400);} ?>" readonly>
                                <span class="add-on"><i class="icon-th"></i></span>
                            </div>
                        </div>
                    </div>
                <div class="form-group">
                    <label for="department" class="col-sm-2 control-label">租户</label>
                    <div class="col-sm-6">
                        <select id="department" name="department" class="form-control">
                            <?php foreach ($data as $key => $value) {   ?>
                            <option value="<?php echo $value['id'];?>" <?php if(isset($acc[0]['department_id']) && $acc[0]['department_id']==$value['id']){ echo 'selected';}  ?> >
                                <span><?php echo $value['name'];?></span>
                            </option>
                            <?php }   ?>
                        </select>
                    </div>
                </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="profile">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th><input  id="all-roles" type="checkbox"></th>
                            <th>角色id</th>
                            <th>角色名称</th>
                            <th>角色说明</th>
                            <th>角色类型</th>
                        </tr>
                        </thead>
                        <tbody id="roles-content">
                        <?php if(isset($roles)){
                            foreach($roles['roles']['data'] as $value){
                                ?>
                                <tr>
                                    <td><input name="roles" onclick='check(this)' value="<?php if(isset($value['id'])){ echo $value['id'];} ?>" type="checkbox"/></td>
                                    <td><?php echo $value['id']; ?></td>
                                    <td><?php echo $value['name']; ?></td>
                                    <td><?php echo $value['note']; ?></td>
                                    <td><?php if($value['department_id'] !=0){
                                            echo '所属租户专用角色';
                                        }else{
                                            echo '公用角色';
                                        }; ?></td>
                                </tr>
                            <?php
                            }
                        }
                        ?>
                        </tbody>
                    </table>
                    <div class="content-pagination clearfix">
                        <nav class="pull-right">
                            <ul id='example' attrs="example">
                            </ul>
                        </nav>
                    </div>
                    <input type="hidden" name="role_id"  id="role_id">
                </div>
                <div style="display:none;" role="tabpanel" class="tab-pane clearfix" id="setting">
                    <div style="width: 50%;float: left">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th><input type="checkbox"  id="totals"></th>
                                <th>参数代码</th>
                                <th>参数值</th>
                                <th>参数说明</th>
                            </tr>
                            </thead>
                            <?php if(isset($para)){
                                foreach($para as $value){
                                    ?>
                                    <tr>
                                        <td><input type="checkbox"  name="para_id" data-role="<?php echo $value['id']; ?>" <?php if(isset($para_code)){
                                                if(in_array($value['para_code'], $para_code)){echo 'checked';}
                                            } ?>></td>
                                        <td><?php echo $value['para_code']; ?></td>
                                        <td><?php echo $value['para_value']; ?></td>
                                        <td><?php echo $value['para_note']; ?></td>
                                    </tr>
                                <?php
                                }
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="department-add department-add pull-left">
                        <ul>
                            <?php if(isset($user_data)){
                            foreach ($user_data as $key=>$value) { ?>
                            <li class="labelwidth add<?php echo $value['para_code'] ?>"><label><?php echo $value['para_note'] ?></label>
                                <textarea name="<?php echo $value['para_code'] ?>[para_value]"><?php echo $value['para_value'] ?></textarea>
                                <input type="hidden" value="<?php echo $value['para_note'] ?>" name="<?php echo $value['para_code'] ?>[para_note]">
                                <?php } } ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" id="account_submit" class="btn btn-primary">保存</button>
                <a type="button" href="<?php echo $this->Url->build(array('controller' => 'Accounts','action'=>'index')); ?>" class="btn btn-danger">返回</a>
            </div>
        </div>
    </form>
</div>

<?=$this->Html->script(['adminjs.js','bootstrap-datetimepicker.js','validator.bootstrap.js','jquery.cookie.js']); ?>
<?= $this->start('script_last'); ?>
<script type="text/javascript">
    //分页
    function paging(page){
        $.ajax({
            type: "GET",
            url: "<?php echo $this->Url->build(array('controller' => 'Accounts','action'=>'getroles')); ?>/"+page,
            dataType:"json",
            success: function(msg){
                if(msg.data){
                    var type = '';
                    $.each(msg.data, function(i, n){
                        if (n.id) {
                            var de = '公用角色';
                            if(n.department_id >0){
                                de = '所属租户专用角色';
                            }
                            type+='<tr><td><input name="roles" onclick="check(this)" value="'+n.id+'"  type="checkbox"/></td><td>'+n.id+'</td><td>'+n.name+'</td><td>'+n.note+'</td><td>'+de+'</td></tr>';
                        }
                    });
                    $("#roles-content").html(type);
                    checkCheck();//已选中的打钩
                }
            }
        });
    }
    //分页
    var options = {
        alignment:'right',
        bootstrapMajorVersion:10,
        currentPage: <?= $page?>,
        numberOfPages: 8,
        totalPages:<?= $roles['roles']['total']?>,
        itemTexts: function (type, page, current) {
            switch (type) {
                case "first":
                    return "<<";
                case "prev":
                    return "<";
                case "next":
                    return ">";
                case "last":
                    return ">>";
                case "page":
                    return page;
            }
        }
    }

    //更新分页
    function pageing(datas){
        var element = $('#example');//对应下面ul的ID
        var options = {
            alignment:'right',
            bootstrapMajorVersion:10,
            currentPage: datas.page,
            numberOfPages: 8,
            totalPages:datas.total,
            itemTexts: function (type, page, current) {
                switch (type) {
                    case "first":
                        return "<<";
                    case "prev":
                        return "<";
                    case "next":
                        return ">";
                    case "last":
                        return ">>";
                    case "page":
                        return page;
                }
            }
        }
        element.bootstrapPaginator(options);
    }

    $('#example').bootstrapPaginator(options);//填充分页


    //添加cookie
    $(function(){
        var RoleId = "<?php echo $RoleID;?>";
        $.cookie("rolesids",RoleId);
        str = $.cookie("rolesids");
        checkCheck();
    })


    //点击桌面是修改cookie
    function check(obj){
        str = $.cookie("rolesids");
        strs=str.split(",");
        if(obj.checked){
            strs[strs.length]=obj.value;
        }else{
            for(var i=0;i<strs.length;i++){
                if(strs[i]===obj.value){
                    strs.splice(i,1);
                    i--;
                }
            }
        }
        $.cookie("rolesids",strs);
        str = $.cookie("rolesids");
        $("#role_id").val(str);

        checkAll();
    }

    //检查是否全选
    function checkAll(){
        var imagelen = $("input:checkbox[name='roles']:checked").length;
        var imagelens =$("input:checkbox[name='roles']").length;
        if(imagelen == imagelens){
            $('#all-roles').prop('checked','true');
        }else{
            $('#all-roles').prop('checked','');
        }
    }

    //更具cookie的值添加check
    function checkCheck(){
        str = $.cookie("rolesids");
        strs=str.split(",");
        $("input:checkbox[name='roles']").each(function(){
            for(var i=0;i<strs.length;i++){
                if(strs[i]===$(this).val()){
                    $(this).prop('checked','true')
                }
            }
        });
        checkAll();
        $("#role_id").val(strs);
    }

    $('#all-roles').on('click',function(){
        str = $.cookie("rolesids");
        var checked=true;
        strs=str.split(",");
        if($('#all-roles').is(":checked")){
            $("input:checkbox[name='roles']").prop('checked','true');
        }else{
            $("input:checkbox[name='roles']").prop('checked','');
        }
        $("input:checkbox[name='roles']").each(function(){
            if($(this).prop("checked")==true){
                checked = jQuery.inArray($(this).val(), strs);
                if(checked<0){
                    strs[strs.length]=$(this).val();
                }
            }else{
                for(var i=0;i<strs.length;i++){
                    if(strs[i]==$(this).val()){
                        strs.splice(i,1);
                        i--;
                    }
                }
            }
        });
        $.cookie("rolesids",strs);
        str = $.cookie("rolesids");
        $("#role_id").val(str);
    })

    $(function(){
        var imagelen = $("input:checkbox[name='roles']:checked").length;
        var imagelens =$("input:checkbox[name='roles']").length;
        if(imagelen == imagelens){
            $('#all-roles').prop('checked','true');
        }else{
            $('#all-roles').prop('checked','');
        }
    })






    var myDate = new Date();
    var year = myDate.getFullYear();
    var month =myDate.getMonth()+1;
    var day =  myDate.getDate();
    var time =year+'-'+month+'-'+day;
    $('#datetimepicker').datetimepicker({
        autoclose:true,
        minView:2,
        startDate:time
        }
    );

    $('#date-mode-control').on('change','input[type="radio"]',function(){
        if($(this).val()=="0"){
            $('#date-mode').css('display','block');
        }else{
            $('#date-mode').css('display','none');
        }
    })

    //参数
    $('#totals').on('click',function(){
        if($('#totals').is(":checked")){
            $("input:checkbox[name='para_id']").prop('checked','true');
            $('.department-add ul').empty();
            $("input:checkbox[name='para_id']").each(
                function(){
                    var index = $(this).parent().siblings().eq(0).html();
                    //var content = '<li class="labelwidth add' + index + '"><label>' + $(this).parent().siblings().eq(2).html() + '</label><input type="text" name="' + $(this).parent().siblings().eq(0).html() + '[para_value]" value="' + $(this).parent().siblings().eq(1).html() + '" /><input type="hidden" name="' + $(this).parent().siblings().eq(0).html() + '[para_note]" value="' + $(this).parent().siblings().eq(2).html() + '" /></li>';
                    var content = '<li class="labelwidth add' + index + '"><label>' + $(this).parent().siblings().eq(2).html() + '</label><textarea name="' + $(this).parent().siblings().eq(0).html() + '[para_value]">' + $(this).parent().siblings().eq(1).html() +'</textarea><input type="hidden" name="' + $(this).parent().siblings().eq(0).html() + '[para_note]" value="' + $(this).parent().siblings().eq(2).html() + '" /></li>';
                    $('.department-add ul').append(content);
                }
            );
        }else{
            $("input:checkbox[name='para_id']").prop('checked','');
            $("input:checkbox[name='para_id']").each(
                function(){
                    var index = ".add" +  $(this).parent().siblings().eq(0).html();
                    $('.department-add li').remove(index);
                }
            );
        }
    })

    $("input:checkbox[name='para_id']").on('click',function(){
        var len = $("input:checkbox[name='para_id']:checked").length;
        var lens =$("input:checkbox[name='para_id']").length;
        if($(this).is(':checked')){
            var index = $(this).parent().siblings().eq(0).html();
            // var content = '<li class="labelwidth add' + index + '"><label>' + $(this).parent().siblings().eq(2).html() + '</label><input type="text" name="' + $(this).parent().siblings().eq(0).html() + '[para_value]" value="' + $(this).parent().siblings().eq(1).html() + '" /><input type="hidden" name="' + $(this).parent().siblings().eq(0).html() + '[para_note]" value="' + $(this).parent().siblings().eq(2).html() + '" /></li>';
            var content = '<li class="labelwidth add' + index + '"><label>' + $(this).parent().siblings().eq(2).html() + '</label><textarea name="' + $(this).parent().siblings().eq(0).html() + '[para_value]">' + $(this).parent().siblings().eq(1).html() +'</textarea><input type="hidden" name="' + $(this).parent().siblings().eq(0).html() + '[para_note]" value="' + $(this).parent().siblings().eq(2).html() + '" /></li>';
            $('.department-add ul').append(content);
        }else{
            var index = ".add" +  $(this).parent().siblings().eq(0).html();
            $('.department-add li').remove(index);
        }
        if(len == lens){
            $('#totals').prop('checked','true');
        }else{
            $('#totals').prop('checked','');
        }
    })

    $(function(){
        var validator = $('#account-form').bootstrapValidator().data('bootstrapValidator');
        $('a[data-toggle="tab"]').on('hide.bs.tab', function (e) {
            validator.validate();
            if (!validator.isValid()) {
                return false;
            }else{
                $('#account_submit').removeAttr('disabled');
            }
        })
    });


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
                location.href = '<?php echo $this->Url->build(array('controller'=>'Accounts','action'=>'index'));?>/index/<?php echo $depart_id; ?>';
            } else {
                tentionHide(data.msg,1);
            }
        });
    },
    fields : {
        loginname: {
            group: '.col-sm-6',
            validators: {
                notEmpty: {
                    message: '登录名不能为空'
                },
                stringLength: {
                    min: 2,
                    max: 16,
                    message: '请保持在2-16位'
                },
                regexp: {
                    regexp: /^[^\u4e00-\u9fa5\s]{0,}$/,
                    message: '登录名中不能有空格和中文'
                }
            }
        },
        username: {
            group: '.col-sm-6',
            validators: {
                notEmpty: {
                    message: '用户名不能为空'
                },
                stringLength: {
                    min: 2,
                    max: 16,
                    message: '请保持在2-16位'
                },
            }
        },
        password: {
            group: '.col-sm-6',
            validators: {
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
                identical: {
                    field: 'password',
                    message: '请输入相同的密码'
                }
            }
        },
        mobile: {
            group: '.col-sm-6',
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
            group: '.col-sm-6',
            validators: {
                notEmpty: {
                    message: '邮箱不能为空'
                },
                emailAddress: {
                    message: '邮箱格式不对'
                }
            }
        }
    }
});
</script>
<?= $this->end() ?>