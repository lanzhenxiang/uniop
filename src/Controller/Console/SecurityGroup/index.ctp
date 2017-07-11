<!--  安全  列表 -->
<?= $this->element('security/left',['active_action'=>'security_group']); ?>
<div class="wrap-nav-right">
    <div class="wrap-manage">
        <div class="top">
            <span class="title">安全组列表</span>

            <div id="maindiv-alert"></div>
        </div>
        <div class="center clearfix">
            <button class="btn btn-addition" onclick="refreshTable();">
                <i class="icon-refresh"></i>&nbsp;&nbsp;刷新
            </button>
            <a class="btn btn-addition"
               href="<?= $this->Url->build(['controller' => 'SecurityGroup', 'action' => 'addGroup']) ?>"><i
                    class="icon-plus"></i>&nbsp;&nbsp;新建安全组</a>
            <!--<button class="btn btn-default" id="btnEdit" disabled>-->
                <!--<i class="icon-pencil"></i>&nbsp;&nbsp;编辑策略-->
            <!--</button>-->
            <button class="btn btn-default" id="btnDel" disabled>
                <i class="icon-remove "></i>&nbsp;&nbsp;删除安全组
            </button>
            <div class="pull-right">
                <?php if (in_array('ccf_all_select_department', $this->Session->read('Auth.User.popedomname'))) { ?>
                <div class="dropdown">
                    租户:
                    <a href="#" class="dropdown-toggle btn btn-addition text-right" data-toggle="dropdown"
                       role="button">
                        <span class="pull-left" id="deparmets" val="<?= $_default[" id"]
                        ?>"><?= $_default["name"] ?></span>
                        <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <?php foreach($_deparments as $value) { ?>
                        <li><a href="#"
                               onclick="departmentlist(<?php echo $value['id'] ?>,'<?php echo $value['name'] ?>')"><?php echo $value['name'] ?></a>
                        </li>
                        <?php }?>
                    </ul>
                </div>
                <?php }?>
                <input type="hidden" id="txtdeparmetId" value="<?= $_default[" id"] ?>" />
                <div class="dropdown">
                    厂商:
                    <a href="#" class="dropdown-toggle btn btn-addition text-right" data-toggle="dropdown" role="button"
                       aria-haspopup="true" aria-expanded="false">
                        <span class="pull-left" id="agent" val="">全部</span>
                        <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="javascript:;" onclick="local(0,'','全部')">全部</a></li>
                        <?php if(isset($agent)){
                        foreach($agent as $value) {
                            if ($value['parentid'] == 0) {
                    ?>
                        <li><a href="#"
                               onclick="local(<?php echo $value['id'] ?>,'<?php echo $value['class_code'] ?>','<?php echo $value['agent_name'] ?>')"><?php echo $value['agent_name'] ?></a>
                        </li>
                        <?php }}} ?>
                    </ul>
                </div>
                <div class="dropdown">
                    地域:
                    <a href="javascript:;" class="dropdown-toggle btn btn-addition text-right" data-toggle="dropdown"
                       role="button" aria-haspopup="true" aria-expanded="false">
                        <span class="pull-left" id="agent_t" val="">全部</span>
                        <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu" id="agent_two"></ul>
                </div>
            <span class="search"><input type="text" id="txtsearch" name="search" placeholder="搜索">
                  <i class="icon-search"></i>
              </span>

            </div>
        </div>
        <div class="bot ">
            <table id="table" data-toggle="table"
                   data-pagination="true"
            data-side-pagination="server"
            data-locale="zh-CN"
            data-click-to-select="true"
            data-url="
            <?= $this->Url->build(['prefix'=>'console','controller'=>'SecurityGroup','action'=>'groupList','?'=>['department_id'=>$_default['id']]]); ?>"
            data-unique-id="id">
            <thead>
            <tr>
                <th data-checkbox="true"></th>
                <th data-field="id">Id</th>
                <th data-field="code">安全组CODE</th>
                <th data-field="name" data-formatter=formatter_name>安全组名称</th>
                <th data-field="entry_count">实例数</th>
                <th data-field="location_name">部署区位</th>
                <th data-field="vpcName">所属VPC</th>
                <th data-field="create_time" data-formatter=timestrap2date>创建时间</th>
                <th data-field="id" data-formatter="formatter_operate">操作</th>
            </tr>
            </thead>
            </table>
        </div>
    </div>

</div>
</div>

<!-- 右键弹框 -->
<div class="context-menu" id="context-menu">
    <ul>
        <li id="modify"><a href="javascript:;"><i class="icon-pencil"></i> 修改</a></li>

        <li><a id="dele" href="javascript:;"><i class="icon-trash"></i> 删除</a></li>
        <?php if (in_array('ccm_ps_interface_logs', $this->Session->read('Auth.User.popedomname'))) { ?>
        <li id="excp"><a href="javascript:;"><i class="icon-book"></i> 异常日志</a></li>
        <?php } ?>
        <?php if (in_array('ccm_ps_interface_logs', $this->Session->read('Auth.User.popedomname'))) { ?>
        <li id="normal"><a href="javascript:;"><i class="icon-book"></i> 正常日志</a></li>
        <?php } ?>
    </ul>
</div>

<!-- 修改 -->
<div class="modal fade" id="modal-modify" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"
                        aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h5 class="modal-title">修改</h5>
            </div>
            <form id="modal-modify-form" action="" method="post">
                <div class="modal-body">
                    <div class="modal-form-group">
                        <label>安全组名称:</label>
                        <div>
                            <input id="modal-modify-name" name="name" type="text"/>
                        </div>
                    </div>
                    <!--<div class="modal-form-group">-->
                        <!--<label>描述:</label>-->
                        <!--<div>-->
                            <!--<textarea id="modal-modify-description" name="description" rows="5"></textarea>-->
                        <!--</div>-->
                    <!--</div>-->
                    <input id="modal-modify-id" name="id" type="hidden"/>
                </div>
                <div class="modal-footer">
                    <button id="sumbiter" type="button" class="btn btn-primary">确认</button>
                    <button id="reseter" type="button" class="btn btn-danger"
                            data-dismiss="modal">取消
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="maindiv"></div>
<!-- 删除 -->

<div class="modal fade" id="modal-dele" tabindex="-1" role="dialog">
    <input type="hidden" value="" id="del_ids">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">提示</h5>
            </div>
            <form id="modal-dele-form" action="" method="post">
                <div class="modal-body">
                    <i class="icon-question-sign icon-color-gray text-primary"></i>&nbsp;&nbsp;<span id="modal-info"></span><span id="word1">确定要删除选中安全组么？</span></br>
                    <i class="icon-exclamation-sign icon-color-gray text-primary"></i>&nbsp;&nbsp;<span id="word2">删除安全组,会同时删除安全组绑定的规则！</span>
                    <input type="hidden" value="" id="modal-dele-id" name="ids">
                    <input type="hidden" value="" id="modal-status" name="status">
                </div>
                <div class="modal-footer">
                    <button type="button" id="sumbiter-dele" class="btn btn-primary">确认</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">取消</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div style="display: none;">
    <iframe id="lauchFrame" name="lauchFrame" src=""></iframe>
</div>

<div class="modal fade" id="modal-msg" tabindex="-1" role="dialog"></div>
<?php
$this->start('script_last');
?>
<script type="text/javascript">
    //input 存在一个被选中状态
    $("#table").on('all.bs.table', function (e, row, $element) {

        if ($("tbody input:checked").length > 1) {
//            $("#btnEdit").attr('disabled', true);
            $("#btnDel").attr('disabled', false);
        } else if ($("tbody input:checked").length == 1) {
//            $("#btnEdit").attr('disabled', false);
            $("#btnDel").attr('disabled', false);
        } else {
            $(".center .btn-default").attr('disabled', true);
        }
    });
    //编辑策略
    $('#btnEdit').on('click', function () {
        var row = $('#table').bootstrapTable('getSelections');
        var url = '/console/SecurityGroup/securityGroupRule?id=' + row[0].id;
        self.location = url;
    });

    $('#btnDel').on('click', function () {
        var row = $('#table').bootstrapTable('getSelections');
        var ids = '';
        if(row.length==0){
            made_modal('提示', '请先选择要删除的安全组');
        }else{
            $.each(row, function (e, i) {
                if (ids.length > 0) {
                    ids += ',';
                }
                ids += i.id;
                $('#del_ids').val(ids);
            });
            $('#modal-dele').modal('show');
        }

    });
    $('#sumbiter-dele').on('click',function(){
        $.ajax({
            url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'SecurityGroup','action'=>'delGroup']); ?>",
            data: {
                ids: $('#del_ids').val()
            },
            method: 'post',
            dataType: 'json',
            success: function (e) {
                $('#modal-dele').modal("hide");
                if (e.code == "0") {
                    refreshTable();
                    made_modal('删除安全组', e.msg);
                } else {
                    made_modal('删除安全组', e.msg);
                }
            }
        });
    });


    //右键
    $('#table').contextMenu('context-menu', {

        bindings: {
            'modify': function (event) {
                //获取数据
                index = $(event).attr('data-index');
                var uniqueId = $(event).attr('data-uniqueid');
                var row = $('#table').bootstrapTable('getRowByUniqueId', uniqueId);
                //填充数据
                //TODO 根据bootstrap方法
                $('#modal-modify-name').val(row.name);
//                $('#modal-modify-description').val(row.description);
                $('#modal-modify-id').val(row.id);
                $('#modal-modify').modal("show");

                //填充数据
                $('#sumbiter').one('click',
                        function () {
                            $.ajax({
                                url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'SecurityGroup','action'=>'modifyGroup']); ?>",
                                data: $('#modal-modify-form').serialize(),
                                method: 'post',
                                dataType: 'json',
                                success: function (e) {
                                    $('#modal-modify').modal("hide");
                                    refreshTable();
                                    //操作成功
                                    if (e.code == '0') {
                                        made_modal('修改安全组', e.msg);
                                    } else {
                                        // tentionHide('修改失败',1);
                                        made_modal('修改安全组', e.msg);
                                    }
                                }
                            });
                        });
            },


            'dele': function (event) {
                //获取数据
                var uniqueId = $(event).attr('data-uniqueid');
                var row = $('#table').bootstrapTable('getRowByUniqueId', uniqueId);
                $('#del_ids').val(uniqueId);
                $('#modal-dele').modal('show');
            },


            'excp': function (event) {
                var uniqueId = $(event).attr('data-uniqueid');
                var row = $('#table').bootstrapTable('getRowByUniqueId', uniqueId);

                var department_id = row.department_id;
                window.location.href = "/console/excp/lists/excp/securityGroup/" + department_id + '/all/0/0/' + row.id;
            },
            //正常
            'normal': function (event) {
                var uniqueId = $(event).attr('data-uniqueid');
                var row = $('#table').bootstrapTable('getRowByUniqueId', uniqueId);
                var department_id = row.department_id;
                window.location.href = "/console/excp/lists/normal/securityGroup/" + department_id + '/all/0/0/' + row.id;
            }
        }

    });

    //刷新表格
    function refreshTable() {
        $('#del_ids').val('');
        var class_code = $("#agent").attr('val');
        var class_code2 = $("#agent_t").attr('val');
        var search = $("#txtsearch").val();
        $('#table').bootstrapTable('refresh', {
            url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'SecurityGroup','action'=>'groupList']); ?>",
            query: {
                class_code2: class_code2,
                class_code: class_code,
                search: search,
                department_id: $("#txtdeparmetId").val()
            }
        });
    }

    //搜索绑定
    $("#txtsearch").on('keyup',
            function () {
                if (timer != null) {
                    clearTimeout(timer);
                }
                var timer = setTimeout(refreshTable(),
                        1000);
            });

    //切换厂商
    function local(id, class_code, agent_name) {
        if (agent_name) {
            $('#agent_t').html('全部').attr('val', '');
            $('#agent').html(agent_name).attr('val', class_code);
            refreshTable();
            //刷新地域列表
            var jsondata = <?php echo json_encode($agent); ?>;
        if(id!=0){
            var data='<li><a href="javascript:;" onclick="local_two(\'\',\'全部\',\'' + class_code + '\')">全部</a></li>';
            $.each(jsondata, function (i, n) {
            if(n.parentid == id){
            data += '<li><a href="javascript:;" onclick="local_two(\'' + n.class_code + '\',\'' + n.agent_name + '\',\'' + class_code + '\')">' + n.agent_name + '</a></li>';
        }
        })
            $('#agent_two').html(data);
        }else {
            data = '';
            $('#agent_two').html(data);
        }
        }
        }
//切换地域
    function local_two(class_code2, agent_name, class_code) {

        $('#agent_t').html(agent_name).attr('val', class_code2);
        refreshTable();
    }
//切换租户
    function departmentlist(id, name) {
        $("#txtdeparmetId").val(id);
        $("#deparmets").html(name);
        refreshTable();
    }

    //编辑安全组名
    function formatter_name(value, row, index) {
        var url = "/console/SecurityGroup/securityGroupRule?id=" + row.id;
        return '<a href="' + url + '">' + value + '</a>';
    }
    //操作
    function formatter_operate(){
        return "<a href='Javascript::' onclick='entry_manage(this)'>实例管理</a> <a href='Javascript::' onclick='rule_manage(this)'>规则管理</a>";
    }
    function entry_manage(event){
        var uniqueId = $(event).parent().parent().attr('data-uniqueid');
        location.href="<?=$this->Url->build(['controller'=>'SecurityGroup','action'=>'securityGroupEntry']);?>?id="+uniqueId;
    }
    function rule_manage(event){
        var uniqueId = $(event).parent().parent().attr('data-uniqueid');
        location.href="<?=$this->Url->build(['controller'=>'SecurityGroup','action'=>'securityGroupRule']);?>?id="+uniqueId;
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
<?php
$this->end();
?>
