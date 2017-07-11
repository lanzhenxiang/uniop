<?= $this->element('content_header'); ?>
<style>
    .bold{
        font-weight: bold;
    }
     .point-host-startup{
         margin-right: 150px;
     }
</style>
<div class="content-body clearfix">
    <div id="maindiv-alert"></div>
    <form class="form-horizontal" id="software-list-form" action="<?php echo $this->Url->build(array('controller' => 'Software','action'=>'postconnect')); ?>" method="post">
    <div>
        <div class="content-operate clearfix">
            <div class="pull-left">
                <span style="font-size: 20px;">关联云桌面</span>
            </div>

        </div>
        <hr>
        <div class=" pull-left">
            <div class="now-role"><span class="bold">工具分类:&#160;</span><span><?php if(isset($softname)){ echo $softname;} ?></span></div>

            <div class="now-role"><span class="bold">勾选关联</span></div>
        </div>



        <div class="input-group content-search pull-right">

            <input type="text" class="form-control" id="deptname" placeholder="搜索桌面名、桌面CODE">
             <span class="input-group-btn">
                 <button class="btn btn-primary" id="search-button" type="button">搜索</button>
             </span>

        </div>


        <div class="pull-right form-group" style="margin-right:10px">
            <span>租户:</span>
            <select class="form-control" id="department" name="department" style="width:150px;" onchange="changedepart()">
                <option value="0" >全部租户</option>
                <?php foreach($depart as $key => $value){?>
                <option value="<?= $value['id'];?>"><?= $value['name'];?></option>
                <?php }?>
            </select>
        </div>
        <div style="clear: both;"></div>
        <hr>
        <!--<?php var_dump($data);?>-->
        <!--添加内容-->

        <div role="tabpanel" class="tab-pane" id="profile">
            <div>

                    <table class="table table-striped">
                        <input type="hidden" name="software_id" value="<?=$id;?>">
                        <input name="check-desktop" type="hidden" id="check-desktop">
                        <input name="checkDesktop" type="hidden" id="checkDesktop">
                        <thead>
                        <tr>
                            <th><input type="checkbox"  id="all-desktop"></th>
                            <th>桌面名</th>
                            <th>桌面code</th>
                            <th>所属租户</th>
                            <th>部署区位</th>
                            <th>VPC</th>
                            <th>子网</th>
                        </tr>
                        </thead>
                        <tbody id="desktop-content">
                        <?php if(isset($query)){
                                         foreach($query['DesktopExtend']['data'] as $value){
                                             ?>
                        <tr>
                            <td><input name="desktop" onclick='check(this)' value="<?php if(isset($value['id'])){ echo $value['id'];} ?>" <?php if(isset($department_data['host_id'])){ if (in_array($value['id'],$department_data['host_id'])){echo 'checked';}}?> type="checkbox"/></td>
                            <td><?php if(isset($value['name'])){ echo $value['name'];}?></td>
                            <td><?php if(isset($value['code'])){ echo $value['code'];}?></td>
                            <td><?php if(isset($value['department_name'])){ echo $value['department_name'];} ?></td>
                            <td><?php if(isset($value['location_name'])){ echo $value['location_name'];} ?></td>
                            <td><?php if(isset($value['vpc'])){ echo $value['vpc'];} ?></td>
                            <td><?php if(isset($value['subnet'])){ echo $value['subnet'];} ?></td>

                        </tr>
                        <?php
                                         }
                                     }?>
                        </tbody>
                    </table>
                <div class="content-pagination clearfix">
                    <nav class="pull-right">
                        <ul id='example'>
                        </ul>
                    </nav>
                </div>

            </div>
        </div>

        <div class="col-sm-offset-5">
            <button type="submit" id="con_submit" class="btn btn-primary">保存</button>
            <!--<a type="button" href="<?php echo $this->Url->build(array('controller' => 'Software','action'=>'index')); ?>" class="btn btn-danger">返回</a>-->
            <a type="button" onclick="window.history.go(-1)" class="btn btn-danger">返回</a>
        </div>
    </div>
    </form>
</div>
<?php $desktop_id='';if(isset($department_data['host_id'])){ $desktop_id=implode(",",$department_data['host_id']);}?>
<?= $this->Html->css(['zTreeStyle.css']) ?>
<?= $this->Html->script(['jquery.ztree.core-3.5.js']); ?>
<?= $this->Html->script(['jquery.ztree.excheck-3.5.js']); ?>
<?=$this->Html->script(['adminjs.js','validator.bootstrap.js']); ?>
<?= $this->start('script_last'); ?>
<script type="text/javascript">
    //分页
    function paging(page){
        var department_id = $('#department').val();
        var search = $("#deptname").val();
        $.ajax({
            type: "GET",
            url: "<?php echo $this->Url->build(array('controller' => 'Software','action'=>'check_desktop')); ?>/"+page+"/"+department_id+"/"+search,
            dataType:"json",
            success: function(msg){
                if(msg.data){
                    var type = '';
                    $.each(msg.data, function(i, n){
                        if (n.id) {

                            type+='<tr><td><input name="desktop" onclick="check(this)" value="'+n.id+'"  type="checkbox"/></td><td>'+n.name+'</td><td>'+n.code+'</td><td>'+n.department_name+'</td><td>'+n.location_name+'</td><td>'+ n.vpc+'</td><td>'+ n.subnet+'</td></tr>';
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



    //添加cookie
    $(function(){
        var hostId = "<?php echo $desktop_id;?>";
        $.cookie("checkDesktop",hostId);
        str = $.cookie("checkDesktop");
    });

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



    //保存用户
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
//                    location.href="<?php echo $this->Url->build(array('controller'=>'Software','action'=>'index'));?>";
                    window.location.reload();
                }else{
                    tentionHide(data.msg,1);
                }
            });
        }

    });


    //选择租户
    function changedepart() {
        var search = $('#deptname').val();
        var department_id = $('#department').val();
        $.ajax({
            url: "<?php echo $this->Url->build(array('controller' => 'Software','action'=>'checkDesktop')); ?>/1/"+department_id+"/"+search,
            success: function(data) {
                datas = $.parseJSON(data);
                if(datas){
                    var type = '';
                    $.each(datas.data, function(i, n){
                        if (n.id) {
                            type+='<tr><td><input name="desktop" onclick="check(this)" value="'+n.id+'"  type="checkbox"/></td><td>'+n.name+'</td><td>'+n.code+'</td><td>'+n.department_name+'</td><td>'+n.location_name+'</td><td>'+ n.vpc+'</td><td>'+ n.subnet+'</td></tr>';
                        }
                    });
                }
                $("#desktop-content").html(type);
                checkCheck();//已选中的打钩

            }
        });

    }


    $("#search-button").on('click',function(){
        var department_id = $('#department').val();
        var search = $("#deptname").val();
        $.ajax({
            url: "<?php echo $this->Url->build(array('controller' => 'Software','action'=>'checkDesktop')); ?>/1/"+department_id+"/"+search,
            success: function(data) {
                datas = $.parseJSON(data);
                if(datas){
                    var type = '';
                    $.each(datas.data, function(i, n){
                        if (n.id) {
                            type+='<tr><td><input name="desktop" onclick="check(this)" value="'+n.id+'"  type="checkbox"/></td><td>'+n.name+'</td><td>'+n.code+'</td><td>'+n.department_name+'</td><td>'+n.location_name+'</td><td>'+ n.vpc+'</td><td>'+ n.subnet+'</td></tr>';
                        }
                    });
                }
                $("#desktop-content").html(type);
                checkCheck();//已选中的打钩

            }
        });
    });


</script>
<?= $this->end() ?>