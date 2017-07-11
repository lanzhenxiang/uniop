<?= $this->element('content_header'); ?>
    <div class="content-body clearfix">
        <div id="maindiv-alert"></div>
            <div class="now-role">当前角色:<span> <?php if(isset($rolename)){ echo $rolename;} ?></span></div>
        <form class="form-horizontal">

                <!-- Nav tabs -->
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">功能权限</a></li>
                    <li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">软件权限</a></li>
                    <li role="presentation"><a href="#user" aria-controls="user" role="tab" data-toggle="tab">用户列表</a></li>
                </ul>
                 <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="home">
                    <div>
                        <ul id="treeDemo" class="ztree"></ul>
                    </div>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <a type="submit" id="pope_submit" class="btn btn-primary">保存</a>
                                <a type="submit"
                                   href="<?php echo $this->Url->build(array('controller' => 'Roles', 'action' => 'index')); ?>"  class="btn btn-danger">返回</a>
                            </div>
                        </div>
                    </div>

                     <div role="tabpanel" class="tab-pane" id="profile">
                         <div>
                             <form>
                                 <table class="table table-striped">
                                     <thead>
                                     <tr>
                                         <th><input type="checkbox"  id="total_soft"></th>
                                         <th>软件id</th>
                                         <th>软件名称</th>
                                         <th>软件code</th>
                                     </tr>
                                     </thead>
                                     <tbody>
                                     <?php if(isset($software)){
                                         foreach($software as $value){
                                             ?>
                                             <tr>
                                                 <td><input type="checkbox" <?php if(isset($value['checked'])){ echo 'checked='.$value['checked'];} ?> name="soft_id" data-role="<?php echo $value['id']; ?>"></td>
                                                 <td><?php echo $value['id']; ?></td>
                                                 <td><?php echo $value['software_name']; ?></td>
                                                 <td><?php echo $value['software_code']; ?></td>
                                             </tr>
                                         <?php
                                         }
                                     }
                                     ?>
                                     </tbody>
                                 </table>
                                 <div class="form-group">
                                     <div class="col-sm-offset-2 col-sm-10">
                                         <a type="submit" id="soft_submit" class="btn btn-primary">保存</a>
                                         <a type="submit"
                                            href="<?php echo $this->Url->build(array('controller' => 'Roles', 'action' => 'index')); ?>"  class="btn btn-danger">返回</a>
                                     </div>
                                 </div>
                             </form>
                             <!--<ul id="tree_soft" class="ztree"></ul>-->
                         </div>
                     </div>
                     <div role="tabpanel" class="tab-pane" id="user">
                         <div style="width: 30%;float: left">
                             <ul id="treeUser" class="ztree" ></ul>
                         </div>
                         <div style="width: 70%;float: right;">
                         <form>
                             <table class="table table-striped">
                                 <thead>
                                 <tr>
                                     <th><input type="checkbox"  id="total"></th>
                                     <th>用户名</th>
                                     <th>登录名</th>
                                 </tr>
                                 </thead>
                                 <tbody id="account">
                                 </tbody>
                             </table>
                            <div class="form-group">
                                <div class="col-sm-offset-2 col-sm-10">
                                    <input type="hidden" id="department">
                                    <a type="submit" id="account_submit" class="btn btn-primary">保存</a>
                                    <a type="submit"
                                       href="<?php echo $this->Url->build(array('controller' => 'Roles', 'action' => 'index')); ?>"  class="btn btn-danger">返回</a>
                                </div>
                            </div>
                        </form>
                         </div>
                     </div>
                 </div>
            </div>
        <?= $this->Html->css(['zTreeStyle.css']) ?>
        <?= $this->Html->script(['jquery.ztree.core-3.5.js']); ?>
        <?= $this->Html->script(['jquery.ztree.excheck-3.5.js']); ?>
    </div>

<?=$this->Html->script(['adminjs.js']); ?>
<?= $this->start('script_last'); ?>
    <script type="text/javascript">

        $('#total_soft').on('click',function(){
            if($('#total_soft').is(":checked")){
                $("input:checkbox[name='soft_id']").prop('checked','true')
            }else{
                $("input:checkbox[name='soft_id']").prop('checked','');
            }
        })

        $("input:checkbox[name='soft_id']").on('click',function(){
            var len = $("input:checkbox[name='soft_id']:checked").length;
            var lens =$("input:checkbox[name='soft_id']").length;
            if(len == lens){
                $('#total_soft').prop('checked','true');
            }else{
                $('#total_soft').prop('checked','');
            }
        })
        //权限列表
        var data_pope ='<?php echo $data; ?>';
        data_pope  = eval('(' + data_pope + ')');
        var setting_pope = {
            view: {
                showLine:false
            },
            check: {
                enable: true
            },
            data: {
                key:{
                    name:"popedomnote"
                },
                simpleData: {
                    enable: true,
                    idKey:'popedomid',
                    pIdKey:'parent_id'
                }
            }
        };
        var zNodes_pope =data_pope;
        var code_pope;
        $.fn.zTree.init($("#treeDemo"), setting_pope, zNodes_pope);
        var zTree_pope = $.fn.zTree.getZTreeObj("treeDemo");
        var type_pope = { "Y":'p' + 's', "N":'p' + 's'};
        zTree_pope.setting.check.chkboxType = type_pope;

        showCode_pope('setting.check.chkboxType = { "Y" : "' + type_pope.Y + '", "N" : "' + type_pope.N + '" };');

        function showCode_pope(str) {
            if (!code_pope) code_pope = $("#code_pope");
            code_pope.empty();
            code_pope.append("<li>"+str+"</li>");
        }

        //软件列表
        $('#total').on('click',function(){
            if($('#total').is(":checked")){
                $("input:checkbox[name='id']").prop('checked','true')
            }else{
                $("input:checkbox[name='id']").prop('checked','');
            }
        })

        $("input:checkbox[name='id']").on('click',function(){
            var len = $("input:checkbox[name='id']:checked").length;
            var lens =$("input:checkbox[name='id']").length;
            if(len == lens){
                $('#total').prop('checked','true');
            }else{
                $('#total').prop('checked','');
            }
        })

        //人员列表
        var data_depart ='<?php echo $depart; ?>';
        data_depart  = eval('(' + data_depart + ')');
        var setting = {
            view: {
                selectedMulti: false
            },
           /* check:{
                enable:true
            },
*/
            data: {
                key:{
                    name:'name'
                },
                simpleData: {
                    enable: true,
                    pIdKey:'parent_id'
                }
            },
            callback:{
                onClick:list
            }
        };

        var zNodes_depart =data_depart;

        $(document).ready(function(){
            $.fn.zTree.init($("#treeUser"), setting,zNodes_depart);
        });

        function list(treeId, parentNode,childNodes) {
            var id = childNodes.id;
            $.ajax({
                type: "POST",
                url: "<?php echo $this->Url->build(array('controller' => 'Roles', 'action' => 'accountlist')); ?>",
                dataType: "json",
                data: {
                    role_id:"<?php echo $id ?>",
                    depart_id:childNodes.id
                },
                success: function (data) {
                    if (data.code == 0) {
                        var datas= data.data;
                        var html = '';
                        $.each(datas, function (i) {
                            var check =''
                            if(datas[i].checked){
                                check = "checked ='"+datas[i].checked+"'";
                            }else{
                                check='';
                            }
                            html+="<tr><td><input type='checkbox' "+check+" name='id' data-role="+ datas[i].id +"></td><td>"+datas[i].username +"</td><td>"+datas[i].loginname +"</td></tr>";
                        })
                        if($('#account').html() !=''){
                            $('#account').html('');
                        }
                        $('#department').val(id);
                        $('#account').html(html);
                    } else {
                        $('#department').val(id);
                        $('#account').html('');
                    }

                }

            });
        }

        //保存权限
        $("#pope_submit").on('click', function () {
            //获取选中节点的值
            var treeObj=$.fn.zTree.getZTreeObj("treeDemo");
            var nodes=treeObj.getCheckedNodes(true);
            var popeid="";
            for(var i=0;i<nodes.length;i++){
                    popeid+=nodes[i].popedomid + ",";
            }

            $.ajax({
                type: "POST",
                url: "<?php echo $this->Url->build(array('controller' => 'Roles', 'action' => 'addpopedom')); ?>/<?php echo $id; ?>",
                dataType: "json",
                data: {
                    popeid:popeid,
                    type:'popedom'
                },
                success: function (data) {
                    if (data.code == 0) {
                        tentionHide(data.msg, 0);
                    } else {
                        tentionHide(data.msg, 1);
                    }

                }

            });
        });

        //保存软件权限
        $("#soft_submit").on('click', function () {
            //获取选中节点的值
            var check = $("input:checkbox[name='soft_id']:checked");
            var softwareid='';
            if(check.length!=0){
                check.each(
                    function(){
                        softwareid +=$(this).data("role")+',';
                    }
                );
            }
            $.ajax({
                type: "POST",
                url: "<?php echo $this->Url->build(array('controller' => 'Roles', 'action' => 'addpopedom')); ?>/<?php echo $id; ?>",
                dataType: "json",
                data: {
                    softwareid:softwareid,
                    type:'software'
                },
                success: function (data) {
                    if (data.code == 0) {
                        tentionHide(data.msg, 0);

                    } else {
                        tentionHide(data.msg, 1);
                    }

                }

            });
        });

        //保存用户
        $("#account_submit").on('click', function () {
        //获取选中节点的值
            var check = $("input:checkbox[name='id']:checked");
            var account_id='';
            if(check.length!=0){
                check.each(
                    function(){
                        account_id +=$(this).data("role")+',';
                    }
                );
            }
                $.ajax({
                    type: "POST",
                    url: "<?php echo $this->Url->build(array('controller' => 'Roles', 'action' => 'addpopedom')); ?>/<?php echo $id; ?>",
                    dataType: "json",
                    data: {
                        accountid:account_id,
                        type:'account',
                        department:$('#department').val()
                    },
                    success: function (data) {
                        if (data.code == 0) {
                            tentionHide(data.msg, 0);
                        } else {
                            tentionHide(data.msg, 1);
                        }

                    }

                });
        });
    </script>
<?= $this->end() ?>