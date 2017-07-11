<?= $this->element('desktop/lists/left',['active_action'=>'priorityList','active_group'=>'business_management']); ?>
<style>
    .form-control-blue{
        background-color: #44d2e4;
        cursor: pointer;
        color:white;
    }
    option{
        background-color: white;
        color:#888;
    }
</style>
<div class="wrap-nav-right">
    <div class="wrap-manage">
        <div class="top">
            <span class="title">优先级管理</span>
            <div id="maindiv-alert"></div>
        </div>

        <div class="center clearfix">
            <div class="pull-left">
                <button class="btn btn-default" id="edit_priority">
                    设置优先级
                </button>
            </div>
<!--筛选-->
            <div class="input-group content-search pull-right col-sm-3">
                <input type="text" class="form-control" id="deptname" placeholder="搜索桌面名、CODE">
             <span class="input-group-btn">
                 <button class="btn btn-primary" id="search-btn" type="button">搜索</button>
             </span>
            </div>

            <div class="pull-right">
                <?php if(in_array('ccf_all_select_department',$this->request->session()->read('Auth.User.popedomname'))){?>
                <span>租户:</span>
                <select class="form-control form-control-blue" id="department" name="department" style="width:150px;" onchange="changedepart()">
                    <!--<option value="0">全部租户</option>-->
                    <?php if(isset($department)){?>
                    <?php foreach($department as $key => $value){?>
                    <option value="<?= $value['id'];?>" <?php if($value['id']==$this_depart){echo 'selected';}?>><?= $value['name'];?></option>
                    <?php }?>
                    <?php }?>
                </select>
                <?php }?>

                <span>桌面分组:</span>
                <select class="form-control form-control-blue" id="group" name="group" style="width:150px;" onchange="searchs()">
                    <option value="0">全部分组</option>
                    <?php if(isset($group)){?>
                    <?php foreach($group as $key => $value){?>
                    <option value="<?= $value['id'];?>"><?= $value['software_name'];?></option>
                    <?php }?>
                    <?php }?>
                </select>

                <span>计费方式:</span>
                <select class="form-control form-control-blue" id="charge_mode" name="charge_mode" style="width:150px;" onchange="searchs()">
                    <option value="">全部方式</option>
                    <option value="oneoff">一次性计费</option>
                    <option value="duration">按时长计费</option>
                    <option value="cycle">固定循环周期计费</option>
                    <option value="permanent">永久许可</option>
                </select>

            </div>

            <div style="clear: both;"></div>
        </div>
<!--表格-->
        <div class="bot">
            <div>

                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th><input type="checkbox"  id="total_desktop"></th>
                        <th>所属租户</th>
                        <th>桌面分组</th>
                        <th>桌面CODE</th>
                        <th>桌面名</th>
                        <th>所有模式</th>
                        <th>计费方式</th>
                        <th>单价(元)</th>
                        <th>优先级</th>
                    </tr>
                    </thead>
                    <tbody id="desktop_content">
                    <?php if(isset($desktop_data)&&!empty($desktop_data)){
                                         foreach($desktop_data['data'] as $value){
                                             ?>
                    <tr>
                        <td><input type="checkbox" name="desktop_id" value="<?php echo $value['id']; ?>" onclick="check(this)"></td>
                        <td><?php echo $value['department']['name']; ?></td>
                        <td><?php echo $value['group']; ?></td>
                        <td><?php echo $value['code']; ?></td>
                        <td><?php echo $value['name']; ?></td>
                        <td><?php echo $value['instance_charge']['charge_mode']; ?></td>
                        <td><?php echo $value['charge_mode']; ?></td>
                        <td><?php echo $value['instance_charge']['price']; ?></td>
                        <td><?php echo $value['priority']; ?></td>
                    </tr>
                    <?php
                                         }
                                     }
                                     ?>
                    </tbody>
                </table>
                <input type="hidden" name="desk_id" id="desk_id">
                <div class="content-pagination clearfix">
                    <nav class="pull-right">
                        <ul id='example' attrs="example">
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    </div>
</div>

<!-- 修改优先级 -->
<div class="modal fade" id="modal-priority" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="priority-form"  method="post" action="<?= $this->Url->build(['controller'=>'Desktop','action'=>'editPriority']); ?>">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h5 class="modal-title">
                    <span class="pull-left">设置优先级</span>
                    <div class="pull-right">
                        <button id="priority_submit" type="submit" class="btn btn-primary">保存</button>
                        <button id="priority_cancel" type="button" class="btn btn-danger"data-dismiss="modal">取消</button>
                    </div>
                    <div style="clear:both"></div>
                </h5>
            </div>

                <div class="modal-body">

                    <div class="modal-form-group">
                        <label>优先级:</label>
                        <div class="form-group">
                            <input type="hidden" id="select_id" name="select_id">
                            <input type="text" class="form-control" name="priority_data" id="priority_data" value="">
                        </div>
                    </div>
                    <div>
                        <label>为如下桌面设定优先级：</label>

                    </div>
                    <table class="table table-striped" id="table" data-toggle="table" data-pagination="true"
                    data-side-pagination="server"
                    data-page-list="[20,30]" data-page-size="20"
                    data-locale="zh-CN" data-click-to-select="true"
                    data-url="<?= $this->Url->build(['controller'=>'Desktop','action'=>'pLists']); ?>"
                    data-unique-id="id">
                    <thead>
                    <tr>
                    <th data-field="code">桌面code</th>
                    <th data-field="name">桌面名</th>
                    </tr>
                    </thead>
                    </table>

                </div>


            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-msg" tabindex="-1" role="dialog"></div>
<?= $this->Html->css(['zTreeStyle.css']) ?>
<?= $this->Html->script(['jquery.ztree.core-3.5.js']); ?>
<?= $this->Html->script(['jquery.ztree.excheck-3.5.js']); ?>
<?=$this->Html->script(['adminjs.js','validator.bootstrap.js','bootstrap-datetimepicker.js','jquery.cookie.js','bootstrap-paginator.js']); ?>
<?= $this->start('script_last'); ?>
<script type="text/javascript">
    $('#deptname').on('keyup', function (e) {
        var key = e.which;
        if (key == 13) {
            e.preventDefault();
            searchs();
        }
    });
    $('#search-btn').on('click', function () {
        searchs();
    });
    function changedepart(){
        var html="<option value=\"0\">全部分组</option>";
        $.ajax({
            type:'post',
            data:{department_id:$('#department').val()},
            url:"<?=$this->Url->build(array('controller'=>'Desktop','action'=>'getGroupBydepart'));?>",
            success:function(data){
                data= $.parseJSON(data);
                if(data.length>0){
                    $.each(data,function(i,n){
                        html+="<option value=\""+n.id+"\">" + n.software_name + "</option>"
                    })
                }
                $('#group').html(html);
            }
        });
        searchs();
    }
    function searchs() {
        var search = $('#deptname').val();
        var department_id=$('#department').val();
        var group=$('#group').val();
        var charge_mode=$('#charge_mode').val();

        $.ajax({
            type: "GET",
            data:{search:search,department_id:department_id,group:group,charge_mode:charge_mode},
            url: "<?php echo $this->Url->build(array('controller' => 'Desktop','action'=>'getDesktop')); ?>",
            dataType:"json",
            success: function(msg){
                if(msg.data){
                    var type = '';
                    $.each(msg.data, function(i, n){
                        if (n.id) {
                            if(n.instance_charge==null){
                                type+='<tr><td><input name="desktop_id" onclick="check(this)" value="'+n.id+'"  type="checkbox"/></td><td>'+n.department.name+'</td><td>'+n.group+'</td><td>'+n.code+'</td><td>'+n.name+'</td><td>空</td><td>'+n.charge_mode+'</td><td>空</td><td>'+n.priority+'</td></tr>';
                            }else{
                                type+='<tr><td><input name="desktop_id" onclick="check(this)" value="'+n.id+'"  type="checkbox"/></td><td>'+n.department.name+'</td><td>'+n.group+'</td><td>'+n.code+'</td><td>'+n.name+'</td><td>'+n.instance_charge.charge_mode+'</td><td>'+n.charge_mode+'</td><td>'+n.instance_charge.price+'</td><td>'+n.priority+'</td></tr>';
                            }


                        }
                    });
                    $("#desktop_content").html(type);
                    checkCheck();//已选中的打钩
                }
            }
        });
    }
//设置优先级按钮
    $('#edit_priority').on('click',function(){
        var select_id=$('#desk_id').val();
        if(select_id!='') {
            $('#select_id').val(select_id);
            $('#table').bootstrapTable('refresh', {
                url: "<?= $this->Url->build(['controller'=>'Desktop','action'=>'pLists']);?>?select_id=" + select_id
            });
            $('#modal-priority').modal('show');
        }else{
            made_modal('提示', '请先选择要修改优先级的桌面');
        }
    });


    //分页
        function paging(page){
            var search = $('#deptname').val();
            var department_id=$('#department').val();
            var group=$('#group').val();
            var charge_mode=$('#charge_mode').val();
            $.ajax({
                type: "GET",
                data:{search:search,department_id:department_id,group:group,charge_mode:charge_mode},
                url: "<?php echo $this->Url->build(array('controller' => 'Desktop','action'=>'getDesktop')); ?>/"+page,
                dataType:"json",
                success: function(msg){
                    if(msg.data){
                        var type = '';
                        $.each(msg.data, function(i, n){
                            if (n.id) {
                                type+='<tr><td><input name="desktop_id" onclick="check(this)" value="'+n.id+'"  type="checkbox"/></td><td>'+n.department.name+'</td><td>'+n.group+'</td><td>'+n.code+'</td><td>'+n.name+'</td><td>'+n.instance_charge.charge_mode+'</td><td>'+n.charge_mode+'</td><td>'+n.instance_charge.price+'</td><td>'+n.priority+'</td></tr>';

                            }
                        });
                        $("#desktop_content").html(type);
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
                totalPages:<?=$desktop_data['total']?>,
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
        var selectID = "";
        $.cookie("desktopids",selectID);
        str = $.cookie("desktopids");
        checkCheck();
    });

        //点击桌面是修改cookie
        function check(obj){
            str = $.cookie("desktopids");
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
            $.cookie("desktopids",strs);
            str = $.cookie("desktopids");
            $("#desk_id").val(str);

            checkAll();
        }

        //检查是否全选
        function checkAll(){
            var imagelen = $("input:checkbox[name='desktop_id']:checked").length;
            var imagelens =$("input:checkbox[name='desktop_id']").length;
            if(imagelen == imagelens){
                $('#total_desktop').prop('checked','true');
            }else{
                $('#total_desktop').prop('checked','');
            }
        }

        //根据cookie的值添加check
        function checkCheck(){
            str = $.cookie("desktopids");
            strs=str.split(",");
            $("input:checkbox[name='desktop_id']").each(function(){
                for(var i=0;i<strs.length;i++){
                    if(strs[i]===$(this).val()){
                        $(this).prop('checked','true')
                    }
                }
            });
            checkAll();
            $("#desk_id").val(strs);
        }
        $('#total_desktop').on('click',function(){
            str = $.cookie("desktopids");
            var checked=true;
            strs=str.split(",");

            if($('#total_desktop').is(":checked")){
                $("input:checkbox[name='desktop_id']").prop('checked','true');
            }else{
                $("input:checkbox[name='desktop_id']").prop('checked','');
            }
            $("input:checkbox[name='desktop_id']").each(function(){
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
            $.cookie("desktopids",strs);
            str = $.cookie("desktopids");
            $("#desk_id").val(str);
        })


    $(function(){
        var imagelen = $("input:checkbox[name='desktop_id']:checked").length;
        var imagelens =$("input:checkbox[name='desktop_id']").length;
        if(imagelen == imagelens){
            $('#total_desktop').prop('checked','true');
        }else{
            $('#total_desktop').prop('checked','');
        }
    });


    $('#priority-form').bootstrapValidator({
        submitButtons:'button[type="submit"]',
        submitHandler: function(validator, form, submitButton) {
                $.post(form.attr('action'), form.serialize(), function(data){
                    $('#modal-priority').modal('hide');
                    var data = eval('(' + data + ')');
                    if (data.code == 0) {
                        tentionHide(data.msg,0);
                        setTimeout(function () {
                            window.location.reload();
                        }, 500);
                    } else {
                        tentionHide(data.msg,1);
                        setTimeout(function () {
                            window.location.reload();
                        }, 500);
                    }
                });
        },
        fields:{
            priority_data:{
                validators:{
                    notEmpty: {
                        message: '请输入优先级'
                    },
                    regexp:{
                        regexp:/^[0-9]+$/ ,
                        message: '请输入正整数'
                    },
                    greaterThan:{
                        value : 0,
                        inclusive : false,
                        message: '优先级不能小于0'
                    },
                    lessThan:{
                        value : 9999,
                        inclusive : false,
                        message: '优先级不能大于9999'
                    }
                }
            }
        }
    });
//提示框
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
    //弹框
    function made_modal(name, msg) {
        $("#modal-msg").empty();
        var html = '<div class="modal-dialog" role="document"><div class="modal-content"><div class="modal-header">' +
                '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                '<h5 class="modal-title">' + name + '</h5></div><div class="modal-body">' +
                '<i class="icon-question-sign text-primary"></i>&nbsp;&nbsp;' + msg + '<span class="text-primary" ></span>' +
                '</div><div class="modal-footer"><button type="button" class="btn btn-danger" data-dismiss="modal">确认</button></div> </div> </div>';

        $('#modal-msg').append(html);
        $('#modal-msg').modal('show');
    }
</script>
<?= $this->end() ?>