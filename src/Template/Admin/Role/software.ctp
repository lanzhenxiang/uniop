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
    <!--<form class="form-horizontal">-->
    <div>
        <div class="content-operate clearfix">
            <div class="pull-left">
                <span style="font-size: 20px;">关联工具中心权限</span>
            </div>

        </div>
        <hr>
        <div class="now-role"><span class="bold">角色名:&#160;</span><span><?php if(isset($rolename)){ echo $rolename;} ?></span></div>
        <div class="now-role"><span class="bold">说明:&#160;</span><span>权限更新后，用户重新登录CMOP后生效。</span></div>
        <br>
        <div class="now-role"><span class="bold">关联工具分类——勾选关联</span></div>
        <hr>
        <br>
        <div>
            <div class="input-group content-search pull-right">
                <input type="text" class="form-control" id="deptname" placeholder="搜索工具分类、厂商" value="<?=$search;?>">
             <span class="input-group-btn">
                 <button class="btn btn-primary" id="search_btn" type="button">搜索</button>
             </span>
            </div>
        </div>
        <br>
        <!--<?php var_dump($data);?>-->
        <!--添加内容-->
        <div role="tabpanel" class="tab-pane" id="profile">
            <div>
                <form>
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th><input type="checkbox"  id="total_soft"></th>
                            <th>工具分类名</th>
                            <th>厂商</th>
                            <th>备注</th>
                        </tr>
                        </thead>
                        <tbody id="soft-content">
                        <?php if(isset($software)){
                                         foreach($software['software']['data'] as $value){
                                             ?>
                        <tr>
                            <td><input type="checkbox" name="soft_id" value="<?php echo $value['id']; ?>" onclick="check(this)"></td>
                            <td><?php echo $value['software_name']; ?></td>
                            <td><?php echo $value['product_name']; ?></td>
                            <td><?php echo $value['note']; ?></td>
                        </tr>
                        <?php
                                         }
                                     }
                                     ?>
                        </tbody>
                    </table>
                    <input type="hidden" name="software_id" id="software_id">
                    <div class="content-pagination clearfix">
                        <nav class="pull-right">
                            <ul id='example' attrs="example">
                            </ul>
                        </nav>
                    </div>
                </form>

            </div>
            <div class="col-md-offset-5">
                <button type="submit" id="soft_submit" class="btn btn-primary">保存</button>
                <!--<a type="button" href="<?php echo $this->Url->build(array('controller' => 'Role','action'=>'index')); ?>" class="btn btn-danger">返回</a>-->
                <a type="button" onclick="window.history.go(-1)" class="btn btn-danger">返回</a>
            </div>
        </div>
    </div>
    <!--</form>-->
</div>
<?=$this->Html->script(['adminjs.js','validator.bootstrap.js','bootstrap-datetimepicker.js','jquery.cookie.js']); ?>
<?= $this->start('script_last'); ?>
<?= $this->Html->css(['zTreeStyle.css']) ?>
<?= $this->Html->script(['jquery.ztree.core-3.5.js']); ?>
<?= $this->Html->script(['jquery.ztree.excheck-3.5.js']); ?>
<script type="text/javascript">

    //    搜索

    $('#search_btn').on('click', function () {
        var search = $('#deptname').val();
        location.href="<?= $this->Url->build(['controller'=>'Role','action'=>'software']);?>?id=<?=$id; ?>&search="+encodeURI(search);
    });

    //分页
    function paging(page){
        var search =encodeURI($('#deptname').val());
        $.ajax({
            type: "GET",
            data:{search:search},
            url: "<?php echo $this->Url->build(array('controller' => 'Role','action'=>'getsoftware')); ?>/"+page,
            dataType:"json",
            success: function(msg){
                if(msg.data){
                    var type = '';
                    $.each(msg.data, function(i, n){
                        if (n.id) {
                            type+='<tr><td><input name="soft_id" onclick="check(this)" value="'+n.id+'"  type="checkbox"/></td><td>'+n.software_name+'</td><td>'+n.product_name+'</td><td>'+n.note+'</td></tr>';

                        }
                    });
                    $("#soft-content").html(type);
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
            totalPages:<?= $software['software']['total']?>,
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
        var selectID = "<?php echo $selectID;?>";
        $.cookie("softids",selectID);
        str = $.cookie("softids");
        checkCheck();
    })


    //点击桌面是修改cookie
    function check(obj){
        str = $.cookie("softids");
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
        $.cookie("softids",strs);
        str = $.cookie("softids");
        $("#software_id").val(str);

        checkAll();
    }

    //检查是否全选
    function checkAll(){
        var imagelen = $("input:checkbox[name='soft_id']:checked").length;
        var imagelens =$("input:checkbox[name='soft_id']").length;
        if(imagelen == imagelens){
            $('#total_soft').prop('checked','true');
        }else{
            $('#total_soft').prop('checked','');
        }
    }

    //更具cookie的值添加check
    function checkCheck(){
        str = $.cookie("softids");
        strs=str.split(",");
        $("input:checkbox[name='soft_id']").each(function(){
            for(var i=0;i<strs.length;i++){
                if(strs[i]===$(this).val()){
                    $(this).prop('checked','true')
                }
            }
        });
        checkAll();
        $("#role_id").val(strs);
    }

    $('#total_soft').on('click',function(){
        str = $.cookie("softids");
        var checked=true;
        strs=str.split(",");
        if($('#total_soft').is(":checked")){
            $("input:checkbox[name='soft_id']").prop('checked','true');
        }else{
            $("input:checkbox[name='soft_id']").prop('checked','');
        }
        $("input:checkbox[name='soft_id']").each(function(){
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
        $.cookie("softids",strs);
        str = $.cookie("softids");
        $("#software_id").val(str);
    })

    $(function(){
        var imagelen = $("input:checkbox[name='soft_id']:checked").length;
        var imagelens =$("input:checkbox[name='soft_id']").length;
        if(imagelen == imagelens){
            $('#total_soft').prop('checked','true');
        }else{
            $('#total_soft').prop('checked','');
        }
    })










//
//    //软件列表
//    $('#total_soft').on('click',function(){
//        if($('#total_soft').is(":checked")){
//            $("input:checkbox[name='soft_id']").prop('checked','true')
//        }else{
//            $("input:checkbox[name='soft_id']").prop('checked','');
//        }
//    });
//
//    $("input:checkbox[name='soft_id']").on('click',function(){
//        var len = $("input:checkbox[name='soft_id']:checked").length;
//        var lens =$("input:checkbox[name='soft_id']").length;
//        if(len == lens){
//            $('#total_soft').prop('checked','true');
//        }else{
//            $('#total_soft').prop('checked','');
//        }
//    });
//
//
//    $('#total').on('click',function(){
//        if($('#total').is(":checked")){
//            $("input:checkbox[name='id']").prop('checked','true')
//        }else{
//            $("input:checkbox[name='id']").prop('checked','');
//        }
//    });
//
//    $("input:checkbox[name='id']").on('click',function(){
//        var len = $("input:checkbox[name='id']:checked").length;
//        var lens =$("input:checkbox[name='id']").length;
//        if(len == lens){
//            $('#total').prop('checked','true');
//        }else{
//            $('#total').prop('checked','');
//        }
//    });
    //保存软件权限
    $("#soft_submit").on('click', function () {
        //获取选中节点的值
        var softwareid=$('#software_id').val();
        $.ajax({
            type: "POST",
            url: "<?php echo $this->Url->build(array('controller' => 'Role', 'action' => 'postsoftware')); ?>?id=<?=$id; ?>",
            dataType: "json",
            data: {
                softwareid:softwareid,
                type:'software'
            },
            success: function (data) {
                if (data.code == 0) {
                    tentionHide(data.msg, 0);
//                    location.href="<?= $this->Url->build(array('controller' => 'Role', 'action' => 'index')); ?>";
                    window.location.reload();
                } else {
                    tentionHide(data.msg, 1);
                }

            }

        });
    });







</script>
<?= $this->end() ?>