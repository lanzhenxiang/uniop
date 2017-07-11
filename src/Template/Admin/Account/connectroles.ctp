<style>
    .point-host-startup{
        margin-right: 150px;
    }
</style>
<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div id="maindiv-alert"></div>
    <form class="form-horizontal" id="role-form" action="<?php echo $this->Url->build(array('controller' => 'Account','action'=>'postconnect')); ?>" method="post">
        <div>
            <div class="content-operate clearfix">
                <div class="pull-left">
                    <span style="font-size: 20px;">关联角色</span>
                </div>

            </div>
            <hr>
            <!--添加内容-->
            <input type="hidden" name="id" id='account_id' value="<?=$data['id']?>">
            <input type="hidden" name="department_id" id="department_id" value="<?=$depart_id?>">
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="home">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">登录名:</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="loginname" id="loginname"
                                   value="<?=$data['loginname']?>" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">姓名:</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="username" id="username"
                                   value="<?=$data['username']?>" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">关联角色:</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="deptname" placeholder="搜索角色名"
                                   style="float:left;" value="<?=$search?>">
                                <span class="input-group-btn" style="float:left;margin-left:1%">
                                    <button class="btn btn-primary" id="search_btn" type="button">搜索</button>
                                 </span>
                            <div style="clear: both"></div>
                        </div>
                    </div>
                    <div class="form-group" id="depar_div">
                        <label for="type_note" class="col-sm-2 control-label"></label>
                        <div class="col-sm-6">
                            <label class="control-label text-danger"><i class="icon-exclamation-sign"  id="depart_note">保存选定的关联角色后，之前设定的角色关联关系会被冲掉</i></label>
                        </div>
                    </div>
                    <!--table-->
                    <div class="bot">
                        <div>
                            <!--<table class="table table-striped" id="role_table" data-toggle="table" data-pagination="true"-->
                                   <!--data-side-pagination="server"-->
                                   <!--data-page-list="[20,30]" data-page-size="20"-->
                                   <!--data-locale="zh-CN" data-click-to-select="true"-->
                                   <!--data-url="<?= $this->Url->build(['controller'=>'Account','action'=>'rolelist']); ?>"-->
                                   <!--data-unique-id="id">-->
                                <!--<thead>-->
                                <!--<tr>-->
                                    <!--<th data-checkbox="true"></th>-->
                                    <!--<th data-field="name">角色名称</th>-->
                                    <!--<th data-field="note">角色说明</th>-->

                                <!--</tr>-->
                                <!--</thead>-->
                            <!--</table>-->
                            <div role="tabpanel" class="tab-pane" id="profile">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th><input  id="all-roles" type="checkbox"></th>
                                    <th>角色名称</th>
                                    <th>角色说明</th>
                                </tr>
                                </thead>
                                <tbody id="roles-content">
                                <?php if(isset($roles)){
                            foreach($roles['roles']['data'] as $value){
                                ?>
                                <tr>
                                    <td><input name="roles" onclick='check(this)' value="<?php if(isset($value['id'])){ echo $value['id'];} ?>" type="checkbox"/></td>
                                    <td><?php echo $value['name']; ?></td>
                                    <td><?php echo $value['note']; ?></td>
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
                        </div>

                        <div class="col-sm-offset-5">
                            <button type="submit" id="submit_connect" class="btn btn-primary">保存</button>
                            <!--<a type="button" href="<?php echo $this->Url->build(array('controller' => 'Account','action'=>'index')); ?>" class="btn btn-danger">返回</a>-->
                            <a type="button" id="back" class="btn btn-danger" onclick="window.history.go(-1)">返回</a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </form>
</div>
<!--确认关联-->
<div class="modal fade" id="modal-connect" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">提示</h5>
            </div>
            <div class="modal-body">
                <i class="icon-question-sign text-primary"></i>&nbsp;&nbsp;确认要关联选中角色么？<span class="text-primary" id="sure"></span>？
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="yes_connect">确认</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">取消</button>
            </div>
        </div>
    </div>
</div>

<?=$this->Html->script(['adminjs.js','bootstrap-datetimepicker.js','validator.bootstrap.js','jquery.cookie.js']); ?>
<?= $this->start('script_last'); ?>
<script type="text/javascript">

//    搜索

$('#search_btn').on('click', function (page) {
    var search = $('#deptname').val();
    var id=$('#account_id').val();
    location.href="<?= $this->Url->build(['controller'=>'Account','action'=>'connectroles']);?>?id="+id+"&search="+encodeURI(search);

});





//分页
function paging(page){
    var search =encodeURI($('#deptname').val());
    var department_id=$('#department_id').val();
    $.ajax({
        type: "GET",
        data:{search:search,department_id:department_id},
        url: "<?php echo $this->Url->build(array('controller' => 'Account','action'=>'getroles')); ?>/"+page,
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
                        type+='<tr><td><input name="roles" onclick="check(this)" value="'+n.id+'"  type="checkbox"/></td><td>'+n.name+'</td><td>'+n.note+'</td></tr>';
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

$('#role-form').bootstrapValidator({
    submitButtons: 'button[type="submit"]',
    submitHandler: function(validator, form, submitButton){
        $.post(form.attr('action'), form.serialize(), function(data){
            var data = eval('(' + data + ')');
            if (data.code == 0) {
                tentionHide(data.msg,0);
                setTimeout(function () {
//                location.href = "<?php echo $this->Url->build(array('controller'=>'Account','action'=>'index'));?>"
                    window.location.reload();
                }, 500);
            } else {
                tentionHide(data.msg,1);
            }
        });
    },

});

</script>
<?= $this->end() ?>