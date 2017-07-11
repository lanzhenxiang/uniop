<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div id="maindiv-alert"></div>
    <form class="form-horizontal" id="software-list-form" action="<?php echo $this->Url->build(array('controller' => 'SoftwareList','action'=>'addedit')); ?>" method="post">
        <div>
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">基本信息</a></li>
                <li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">应用桌面</a></li>
                <li role="presentation"><a href="#images" aria-controls="images" role="tab" data-toggle="tab">图片上传</a></li>
            </ul>
            <!-- Tab panes -->
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="home">
                    <?php if(isset($department_data['SoftwareList'])){ $softwareinfo=$department_data['SoftwareList'][0];}?>
                    <div class="form-group">
                        <label for="software_name" class="col-sm-2 control-label">软件名称</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="software_name"  id="software_name" placeholder="软件名称" value="<?php if(isset($softwareinfo['software_name'])){ echo $softwareinfo['software_name'];}  ?>">
                            <?php if(isset($softwareinfo)){ ?>
                            <input type="hidden" class="form-control" name="id"  id="id" value="<?php if(isset($softwareinfo)){ echo $softwareinfo['id']; } ?>">
                            <?php } ?>
                            <input type="hidden" class="form-control" name="display_name"  id="display_name" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="software_code" class="col-sm-2 control-label">软件代码</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="software_code"  id="software_code" placeholder="代码"  value="<?php if(isset($softwareinfo['software_code'])){ echo $softwareinfo['software_code'];}  ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="icon_file" class="col-sm-2 control-label">软件图标</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="icon_file"  id="icon_file" placeholder="图标"  value="<?php if(isset($softwareinfo['icon_file'])){ echo $softwareinfo['icon_file'];}  ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="lauch_path" class="col-sm-2 control-label">执行文件名称或者URL</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="lauch_path"  id="lauch_path" placeholder="执行文件名称或者URL"  value="<?php if(isset($softwareinfo['lauch_path'])){ echo $softwareinfo['lauch_path'];}  ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="software_version" class="col-sm-2 control-label">软件版本</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="software_version"  id="software_version" placeholder="软件版本"  value="<?php if(isset($softwareinfo['software_version'])){ echo $softwareinfo['software_version'];}  ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="product_name" class="col-sm-2 control-label">厂商名称</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="product_name"  id="product_name" placeholder="厂商名称"  value="<?php if(isset($softwareinfo['product_name'])){ echo $softwareinfo['product_name'];}  ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="software_note" class="col-sm-2 control-label">软件说明</label>
                        <div class="col-sm-6">
                            <textarea rows="4" type="text" class="form-control" name="software_note"  id="software_note" placeholder="软件说明"  ><?php if(isset($softwareinfo['software_note'])){ echo $softwareinfo['software_note'];}?></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="sort_order" class="col-sm-2 control-label">软件排序</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="sort_order"  id="sort_order" placeholder="排序"  value="<?php if(isset($softwareinfo['sort_order'])){ echo $softwareinfo['sort_order'];}  ?>">
                        </div>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="profile">
                    <div class="input-group content-search pull-right" style="margin-bottom: 10px;">
                        <input type="text" class="form-control" name="search" id="search" placeholder="搜索桌面名称...">
                        <span class="input-group-btn">
                            <button class="btn btn-primary" id="search-button" type="button">搜索</button>
                        </span>
                    </div>
                    <table class="table table-striped">
                        <input name="check-desktop" type="hidden" id="check-desktop">
                        <input name="checkDesktop" type="hidden" id="checkDesktop">
                        <thead>
                            <tr>
                                <th><input id="all-desktop" type="checkbox"/></th>
                                <th>id</th>
                                <th>桌面名称</th>
                                <th>所属租户</th>
                                <th>部署区域</th>
                            </tr>
                        </thead>
                        <tbody id="desktop-content">
                            <?php if(isset($query)){
                                foreach($query['DesktopExtend']['data'] as $value){
                                    ?>
                                    <tr>
                                        <td><input name="desktop" onclick='check(this)' value="<?php if(isset($value['id'])){ echo $value['id'];} ?>" <?php if(isset($department_data['host_id'])){ if (in_array($value['id'],$department_data['host_id'])){echo 'checked';}}?> type="checkbox"/></td>
                                        <td><?php if(isset($value['id'])){ echo $value['id'];} ?></td>
                                        <td><?php if(isset($value['name'])){ echo $value['name'];}?></td>
                                        <td><?php if(isset($value['department_name'])){ echo $value['department_name'];} ?></td>
                                        <td><?php if(isset($value['location_name'])){ echo $value['location_name'];} ?></td>
                                    </tr>
                                    <?php 
                                }
                            } ?>
                        </tbody>
                    </table>
                    <div class="content-pagination clearfix">
                        <nav class="pull-right">
                            <ul id='example'>
                            </ul>
                        </nav>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="images">
                    <div class="form-group">
                        <label for="software_name" class="col-sm-2 control-label">上传图片</label>
                        <div id="up" class="col-sm-6">
                            <!-- <label>上传图片</label> -->
                            <input type="file" accept="image/*" name="upfile[]" multiple id="userfile" class="file"> 
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="submit" id="ds" class="btn btn-primary">保存</button>
                        <a type="button" href="<?php echo $this->Url->build(array('controller' => 'SoftwareList','action'=>'index')); ?>" class="btn btn-danger">返回</a>
                    </div>
                </div>
            </div>

        </div>

    </form>
</div>
<?php $desktop_id='';if(isset($department_data['host_id'])){ $desktop_id=implode(",",$department_data['host_id']);}?>
<?=$this->Html->script(['adminjs.js','jquery.cookie.js','jquery.uploadify.min.js','validator.bootstrap.js']); ?>
<?= $this->start('script_last'); ?>
<script type="text/javascript">
    //分页
    function paging(page){  
        console.log(page);
        var search = $("#search").val();
        $.ajax({  
            type: "GET",  
            url: "<?php echo $this->Url->build(array('controller' => 'SoftwareList','action'=>'check_desktop')); ?>/"+page+"/"+search, 
            dataType:"json",  
            success: function(msg){
                if(msg.data){
                    var type = '';
                    $.each(msg.data, function(i, n){
                        if (n.id) {
                            type+='<tr><td><input name="desktop" onclick="check(this)" value="'+n.id+'"  type="checkbox"/></td><td>'+n.id+'</td><td>'+n.name+'</td><td>'+n.department_name+'</td><td>'+n.location_name+'</td></tr>';
                        }
                    });
                    $("#desktop-content").html(type);
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
        totalPages:<?= $query['DesktopExtend']['total']?>,
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

    $("#userfile").fileinput({
        uploadUrl: "<?php echo $this->Url->build(array('controller'=>'Goods','action'=>'image'));?>", // server upload action
        uploadAsync: false,
        showPreview: true,
        allowedFileExtensions: ['jpg', 'png', 'gif'],
        maxFileCount: 5
    });

    //添加cookie
    $(function(){
        var hostId = "<?php echo $desktop_id;?>";
        $.cookie("checkDesktop",hostId);
        str = $.cookie("checkDesktop");
    })

    //点击桌面是修改cookie
    function check(obj){
        str = $.cookie("checkDesktop"); 
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
        $.cookie("checkDesktop",strs); 
        
        checkAll();
    }

    //检查是否全选
    function checkAll(){
        var imagelen = $("input:checkbox[name='desktop']:checked").length;
        var imagelens =$("input:checkbox[name='desktop']").length;
        if(imagelen == imagelens){
            $('#all-desktop').prop('checked','true');
        }else{
            $('#all-desktop').prop('checked','');
        }
    }

    //更具cookie的值添加check
    function checkCheck(){
        str = $.cookie("checkDesktop");
        strs=str.split(",");
        console.log(strs);
        $("input:checkbox[name='desktop']").each(function(){
            for(var i=0;i<strs.length;i++){
                if(strs[i]===$(this).val()){
                    $(this).prop('checked','true')
                }
            }
        });
        checkAll();
    }

    $('#all-desktop').on('click',function(){
        str = $.cookie("checkDesktop");
        var checked=true;
        strs=str.split(",");
        if($('#all-desktop').is(":checked")){
            $("input:checkbox[name='desktop']").prop('checked','true');
        }else{
            $("input:checkbox[name='desktop']").prop('checked','');
        }
        $("input:checkbox[name='desktop']").each(function(){
            // console.log($(this).prop("checked"));
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
        $.cookie("checkDesktop",strs); 
        str = $.cookie("checkDesktop"); 
        $("#checkDesktop").val(str);
    })

$(function(){
    var imagelen = $("input:checkbox[name='desktop']:checked").length;
    var imagelens =$("input:checkbox[name='desktop']").length;
    if(imagelen == imagelens){
        $('#all-desktop').prop('checked','true');
    }else{
        $('#all-desktop').prop('checked','');
    }
})

    $(function(){
        var validator = $('#software-list-form').bootstrapValidator().data('bootstrapValidator');
        $('a[data-toggle="tab"]').on('hide.bs.tab', function (e) {
            validator.validate();
            if (!validator.isValid()) {
                return false;
            }else{
                $('#ds').removeAttr('disabled');
            }
        })
    });


    $('#software-list-form').bootstrapValidator({
    submitButtons: 'button[type="submit"]',
    submitHandler: function(validator, form, submitButton){
        str = $.cookie("checkDesktop");
        $("#checkDesktop").val(str);
        var desktopID="";
        $("[name='desktop']:checked").each(function(){
            desktopID+=$(this).val()+",";
        })
        $("#check-desktop").val(desktopID);

        $.post(form.attr('action'), form.serialize(), function(data){
            var data = eval('(' + data + ')');
            if(data.code==0){
                tentionHide(data.msg,0);
                location.href='<?php echo $this->Url->build(array('controller'=>'SoftwareList','action'=>'index'));?>';
            }else{
                tentionHide(data.msg,1);
            }
        });
    },
    fields : {
        software_name: {
            group: '.col-sm-6',
            validators: {
                notEmpty: {
                    message: '软件名称不能为空'
                },
                stringLength: {
                    min: 2,
                    max: 16,
                    message: '请保持在2-16位'
                },
                regexp: {
                    regexp: /^\S*$/,
                    message: '软件名称不能有空格'
                }
            }
        },
        software_code: {
            group: '.col-sm-6',
            validators: {
                notEmpty: {
                    message: '软件代码不能为空'
                },
                stringLength: {
                    min: 2,
                    max: 30,
                    message: '请保持在2-30位'
                },
                regexp: {
                    regexp: /^[^\u4e00-\u9fa5\s]{0,}$/,
                    message: '软件代码中不能有空格和中文'
                }
            }
        },
        icon_file: {
            group: '.col-sm-6',
            validators: {
                notEmpty: {
                    message: '软件图标不能为空'
                },
                regexp: {
                    regexp: /^\w+\.(jpg|gif|bmp|png)$/,
                    message: '请填写正确的软件图标名称(文件名不支持中文)'
                }
            }
        },
        sort_order: {
            group: '.col-sm-6',
            validators: {
                notEmpty: {
                    message: '排序不能为空'
                },
                between: {
                    min: 0,
                    max: 1000,
                    message: '排序只能在0-1000之间'
                },
                regexp: {
                    regexp: /^([1-9]\d*|0)$/,
                    message: '请输入整数'
                }
            }
        },
    }
}); 

$("#search-button").on('click',function(){
    var search = $("#search").val();
    $.ajax({
        url: '<?php echo $this->Url->build(array('controller' => 'SoftwareList','action'=>'check_desktop')); ?>/1/'+search,
        success: function(data) {
            datas = $.parseJSON(data);
            if(datas){
                var type = '';
                $.each(datas.data, function(i, n){
                    if (n.id) {
                        type+='<tr><td><input name="desktop" onclick="check(this)" value="'+n.id+'"  type="checkbox"/></td><td>'+n.id+'</td><td>'+n.name+'</td><td>'+n.department_name+'</td><td>'+n.location_name+'</td></tr>';
                    }
                });
            }
            $("#desktop-content").html(type);
            checkCheck();//已选中的打钩
            pageing(datas);   
        }
    });
})
</script>
<?= $this->end() ?>