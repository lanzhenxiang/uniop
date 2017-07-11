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
                <span style="font-size: 20px;">关联人员</span>
            </div>

        </div>
        <hr>
        <div class=" pull-left">
        <div class="now-role"><span class="bold">角色名:&#160;</span><span><?php if(isset($rolename)){ echo $rolename;} ?></span></div>

        <div class="now-role"><span class="bold">关联人员——勾选关联</span></div>
        </div>



            <div class="input-group content-search pull-right">

                <input type="text" class="form-control" id="deptname" placeholder="搜索登录名、用户名" value="<?=$search;?>">
             <span class="input-group-btn">
                 <button class="btn btn-primary" id="search_btn" type="button">搜索</button>
             </span>

            </div>


        <div class="pull-right form-group">
            <span>关联范围:</span>
            <select class="form-control" id="department" name="department" style="width:150px;" onchange="changedepart()">
                <option value="0" <?php if($department_selected==0){echo 'selected';}?>>全部租户</option>
            <?php foreach($depart as $key => $value){?>
            <option value="<?= $value['id'];?>" <?php if($value['id']==$department_selected){echo 'selected';}?>><?= $value['name'];?></option>
            <?php }?>
            </select>
        </div>
        <div style="clear: both;"></div>
        <hr>
        <!--<?php var_dump($data);?>-->
        <!--添加内容-->
        <div role="tabpanel" class="tab-pane" id="profile">
            <div>
                <form>
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th><input type="checkbox"  id="total_account"></th>
                            <th>登录名</th>
                            <th>姓名</th>
                            <th>手机</th>
                            <th>邮箱</th>
                            <th>联系地址</th>
                            <th>租户</th>
                            <th>有效期</th>
                            <th>备注</th>
                            <th>创建人</th>
                            <th>创建时间</th>
                        </tr>
                        </thead>
                        <tbody id="account_content">
                        <?php if(isset($accounts)){
                                         foreach($accounts['accounts']['data'] as $value){
                                             ?>
                        <tr>
                            <td><input type="checkbox" name="account_id" value="<?php echo $value['id']; ?>" onclick="check(this)"></td>
                            <td><?php echo $value['loginname']; ?></td>
                            <td><?php echo $value['username']; ?></td>
                            <td><?php echo $value['mobile']; ?></td>
                            <td><?php echo $value['email']; ?></td>
                            <td><?php echo $value['address']; ?></td>
                            <td><?php echo $value['department_name']; ?></td>
                            <td><?php echo $value['expire']; ?></td>
                            <td><?php echo $value['note']; ?></td>
                            <td><?php echo $value['create_account']; ?></td>
                            <td><?php echo $value['create_time']; ?></td>
                        </tr>
                        <?php
                                         }
                                     }
                                     ?>
                        </tbody>
                    </table>
                    <input type="hidden" name="account_id" id="account_id">
                    <div class="content-pagination clearfix">
                        <nav class="pull-right">
                            <ul id='example' attrs="example">
                            </ul>
                        </nav>
                    </div>
                </form>

            </div>
        </div>
        <div class="col-sm-offset-5">
            <button type="submit" id="accounts_submit" class="btn btn-primary">保存</button>
            <!--<a type="button" href="<?php echo $this->Url->build(array('controller' => 'Role','action'=>'index')); ?>" class="btn btn-danger">返回</a>-->
            <a type="button" onclick="window.history.go(-1)" class="btn btn-danger">返回</a>
        </div>
    </div>
    <!--</form>-->
</div>
<?= $this->Html->css(['zTreeStyle.css']) ?>
<?= $this->Html->script(['jquery.ztree.core-3.5.js']); ?>
<?= $this->Html->script(['jquery.ztree.excheck-3.5.js']); ?>
<?=$this->Html->script(['adminjs.js','validator.bootstrap.js','bootstrap-datetimepicker.js','jquery.cookie.js']); ?>
<?= $this->start('script_last'); ?>
<script type="text/javascript">


    //    搜索
    $('#search_btn').on('click', function () {
        var search = $('#deptname').val();
        var department_id=$('#department').val();
        location.href="<?= $this->Url->build(['controller'=>'Role','action'=>'accounts']);?>?id=<?=$id; ?>&search="+encodeURI(search)+"&department_id="+department_id;
    });


    //分页
    function paging(page){
        var search =encodeURI($('#deptname').val());
        var department_id=$('#department').val();
        $.ajax({
            type: "GET",
            data:{search:search,department_id:department_id},
            url: "<?php echo $this->Url->build(array('controller' => 'Role','action'=>'getaccounts')); ?>/"+page,
            dataType:"json",
            success: function(msg){
                if(msg.data){
                    var type = '';
                    $.each(msg.data, function(i, n){
                        if (n.id) {
                            type+='<tr><td><input name="account_id" onclick="check(this)" value="'+n.id+'"  type="checkbox"/></td><td>'+n.loginname+'</td><td>'+n.username+'</td><td>'+n.mobile+'</td><td>'+n.email+'</td><td>'+n.address+'</td><td>'+n.department_name+'</td><td>'+n.expire+'</td><td>'+n.note+'</td><td>'+n.create_account+'</td><td>'+n.create_time+'</td></tr>';

                        }
                    });
                    $("#account_content").html(type);
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
            totalPages:<?= $accounts['accounts']['total']?>,
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
        $.cookie("accountids",selectID);
        str = $.cookie("accountids");
        checkCheck();
    })


    //点击桌面是修改cookie
    function check(obj){
        str = $.cookie("accountids");
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
        $.cookie("accountids",strs);
        str = $.cookie("accountids");
        $("#account_id").val(str);

        checkAll();
    }

    //检查是否全选
    function checkAll(){
        var imagelen = $("input:checkbox[name='account_id']:checked").length;
        var imagelens =$("input:checkbox[name='account_id']").length;
        if(imagelen == imagelens){
            $('#total_account').prop('checked','true');
        }else{
            $('#total_account').prop('checked','');
        }
    }

    //更具cookie的值添加check
    function checkCheck(){
        str = $.cookie("accountids");
        strs=str.split(",");
        $("input:checkbox[name='account_id']").each(function(){
            for(var i=0;i<strs.length;i++){
                if(strs[i]===$(this).val()){
                    $(this).prop('checked','true')
                }
            }
        });
        checkAll();
        $("#account_id").val(strs);
    }

    $('#total_account').on('click',function(){
        str = $.cookie("accountids");
        var checked=true;
        strs=str.split(",");
        if($('#total_account').is(":checked")){
            $("input:checkbox[name='account_id']").prop('checked','true');
        }else{
            $("input:checkbox[name='account_id']").prop('checked','');
        }
        $("input:checkbox[name='account_id']").each(function(){
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
        $.cookie("accountids",strs);
        str = $.cookie("accountids");
        $("#account_id").val(str);
    })

    $(function(){
        var imagelen = $("input:checkbox[name='account_id']:checked").length;
        var imagelens =$("input:checkbox[name='account_id']").length;
        if(imagelen == imagelens){
            $('#total_account').prop('checked','true');
        }else{
            $('#total_account').prop('checked','');
        }
    })


    //保存用户
    $("#accounts_submit").on('click', function () {
        //获取选中节点的值
        var account_id=$('#account_id').val();
        $.ajax({
            type: "POST",
            url: "<?php echo $this->Url->build(array('controller' => 'Role', 'action' => 'postaccounts')); ?>?id=<?=$id; ?>",
            dataType: "json",
            data: {
                accountid:account_id,
                type:'account',
                department_id:$('#department').val()
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


    //选择租户
    function changedepart() {
        var search = $('#deptname').val();
        var department_id = $('#department').val();
            location.href = "<?= $this->Url->build(['controller'=>'Role','action'=>'accounts']);?>?id=<?=$id; ?>&search=" + encodeURI(search) + "&department_id=" + department_id;

    }




</script>
<?= $this->end() ?>