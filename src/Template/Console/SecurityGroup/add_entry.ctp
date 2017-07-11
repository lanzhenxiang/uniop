<?= $this->element('security/left',['active_action'=>'security_group']); ?>
<style>
    .form-control-blue {
        background-color: #44d2e4;
        cursor: pointer;
        color: white;
    }

    option {
        background-color: white;
        color: #888;
    }
    .right40{
        margin-right: 40px;
    }
    .info{
        margin-top: 8px;
    }
</style>
<div class="wrap-nav-right">
    <div class="wrap-manage">
        <div class="top">
            <span class="title pull-left">添加实例</span>
            <a onclick="window.history.go(-1)" class="btn btn-addition pull-right">返回</a>
            <div style="clear: both"></div>
            <div id="maindiv-alert"></div>
        </div>
        <!--安全组id-->
        <input type="hidden" id="basic_id" value="<?=$basic_id;?>">

        <div class="center clearfix">
            <span class="pull-left right40 info">所在VPC名: <?=$vpcName;?></span>
<!--搜索框-->
            <span class="search pull-right">
                <input type="text" id="txtsearch" name="search" placeholder="搜索实例名或CODE">
                  <i class="icon-search"></i>
              </span>

            <div class="pull-right right40 dropdown">
                <span>类型:</span>

                <a href="#" class="dropdown-toggle btn btn-addition text-right" data-toggle="dropdown" role="button">
                    <span class="pull-left" id="type" val="">云主机和云桌面</span>
                    <span class="caret"></span>
                </a>
                <ul class="dropdown-menu">

                    <li>
                        <a href="#" onclick="changetype('','云主机和云桌面')">云主机和云桌面</a>
                    </li>
                    <li>
                        <a href="#" onclick="changetype('hosts','云主机')">云主机</a>
                    </li>
                    <li>
                        <a href="#" onclick="changetype('desktop','云桌面')">云桌面</a>
                    </li>

                </ul>
            </div>


            <div class="pull-right right40 dropdown">
                <span>子网:</span>
                <a href="#" class="dropdown-toggle btn btn-addition text-right" data-toggle="dropdown" role="button">
                    <span class="pull-left" id="subnet" val="">全部</span>
                    <span class="caret"></span>
                </a>
                <ul class="dropdown-menu">
                    <li>
                        <a href="#" onclick="changesubnet('','全部')">全部</a>
                    </li>
                    <?php if(isset($subnet)&&!empty($subnet)){?>
                    <?php foreach($subnet as $key=> $value) { ?>
                    <li>
                        <?php $subcode=$value['code'];?>
                        <?php $sbuname=$value['name'];?>
                        <a href="#" onclick="changesubnet('<?=$subcode;?>','<?=$sbuname?>')"><?=$value['name'];?></a>
                    </li>
                    <?php }?>
                    <?php }?>
                </ul>
            </div>
            <div style="clear: both"></div>
        </div>
        <div class="center clearfix">
            <div class="pull-left">
                <button class="btn btn-default" id="into_group">
                    关联安全组
                </button>
                <button class="btn btn-default" id="out_group" style="display: none">
                    解绑安全组
                </button>
            </div>



            <div style="clear: both;"></div>
        </div>
        <!--表格-->
        <div class="bot">
            <div>
                <!--分页类型-->
                <input type="hidden" value="canbeused" id="tab">
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#had" aria-controls="canbeused" role="tab" data-toggle="tab" id="show_canbeused">可用网卡</a></li>
                    <li role="presentation"><a href="#not" aria-controls="beenused" role="tab" data-toggle="tab" id="show_beenused">已用网卡</a></li>
                </ul>

                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="canbeused">
                        <table class="table table-striped" id="table" data-toggle="table" data-pagination="true"
                               data-side-pagination="server"
                               data-page-list="[20,30]" data-page-size="20"
                               data-locale="zh-CN" data-click-to-select="true"

                               data-unique-id="id">
                            <!--data-url="<?=$this->Url->build(['controller'=>'SecurityGroup','action'=>'addEntryList']);?>?tab=canbeused&basic_id=<?=$basic_id?>"-->
                            <thead>
                            <tr>
                                <th data-checkbox="true"></th>
                                <th data-field="network_code">网卡CODE</th>
                                <th data-field="bind_group">已关联的安全组</th>
                                <th data-field="instance_basic.name">实例名</th>
                                <th data-field="instance_basic.code">实例CODE</th>
                                <th data-field="instance_basic.type" data-formatter="formatter_type">类型</th>
                                <th data-field="subnet_code">所在子网</th>
                                <th data-field="ip">IP</th>
                                <th data-field="eip">公网IP</th>
                                <th data-field="status">状态</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>


            </div>
        </div>
    </div>
</div>
<!--关联安全组-->
<div class="modal fade" id="modal-add" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h5 class="modal-title">关联实例到安全组</h5>
            </div>
            <form id="add-form"
                  action="<?php echo $this->Url->build(array('controller' => 'SecurityGroup','action'=>'connectEntry')); ?>"
                  method="post">
                <div class="modal-body">
                    <div class="modal-form-group">
                        <label style="width: 150px;">所选实例:</label>
                        <div class="col-sm-6">
                            <input type="hidden" class="form-control" name="add_codes" id="add_codes" value="">
                            <span id="selected_entry"></span>
                        </div>
                    </div>

                    <div class="modal-form-group">
                        <label style="width: 150px;">安全组名:</label>
                        <div class="col-sm-6">
                            <input type="hidden" name="group_code" id="group_code" value="<?=$group_info['code'];?>">
                            <span><?=$group_info['name'];?></span>
                        </div>
                    </div>


                </div>
                <div class="modal-footer">
                    <button id="yes_add" type="submit" class="btn btn-primary">确认</button>
                    <button id="add_cancel" type="button" class="btn btn-danger" data-dismiss="modal">取消</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!--移出分组-->
<div class="modal fade" id="modal-delete" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">提示</h5>
            </div>
            <div class="modal-body">
                <i class="icon-question-sign text-primary"></i>&nbsp;&nbsp;确认要从安全组移出选中实例么？<span class="text-primary"
                                                                                               id="sure"></span>？
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="yes_delete">确认</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">取消</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-msg" tabindex="-1" role="dialog"></div>
<?=$this->Html->script(['adminjs.js','validator.bootstrap.js','bootstrap-datetimepicker.js','jquery.cookie.js','bootstrap-paginator.js']); ?>
<?= $this->start('script_last'); ?>
<script type="text/javascript">
    $(function(){
        $('#table').bootstrapTable('hideColumn','bind_group').bootstrapTable('hideColumn','status');
        refreshTable();
    });
    //刷新表格
    function refreshTable() {
        var basic_id=$('#basic_id').val();
        var search = $('#txtsearch').val();
        var subnet = $('#subnet').attr('val');
        var type = $('#type').attr('val');
//        分页类型
        var tab=$('#tab').val();
        $('#table').bootstrapTable('refresh', {
            url: "<?= $this->Url->build(['controller'=>'SecurityGroup','action'=>'addEntryList']); ?>?search=" + encodeURI(search) + "&basic_id=" + basic_id + "&type=" + type+"&subnet="+subnet+"&tab="+tab
        });
    }
    //搜索
    $("#txtsearch").on('keyup', function () {
        if (timer != null) {
            clearTimeout(timer);
        }
        var timer = setTimeout(refreshTable(), 1000);
    });
    //切换子网
    function changesubnet(code,name){
        $('#subnet').attr('val',code).html(name);
        refreshTable();
    }
    //切换类型
    function changetype(type,name){
        $('#type').attr('val',type).html(name);
        refreshTable();
    }
    //分页
    $('#show_canbeused').on('click', function () {
        $('#table').bootstrapTable('hideColumn','bind_group').bootstrapTable('hideColumn','status');
        $('#tab').val('canbeused');
        $('#into_group').css('display', 'block');
        $('#out_group').css('display', 'none');
        refreshTable();
    });

    $('#show_beenused').on('click', function () {
        $('#table').bootstrapTable('showColumn','bind_group').bootstrapTable('showColumn','status');
        $('#tab').val('beenused');
        $('#into_group').css('display', 'none');
        $('#out_group').css('display', 'block');
        refreshTable();
    });

    //关联安全组
    $('#into_group').on('click', function () {
        var rows = $('#table').bootstrapTable('getSelections');
        var id = '';
        var name = '';
        if (rows.length == 0) {
            made_modal('提示', '请先选择要关联安全组的实例');
        } else {
            $.each(rows, function (i, n) {
                id += n.network_code + ',';
                name += n.instance_basic.name + ',';
            });
            $('#selected_entry').html(name);
            $('#add_codes').val(id);

            $('#modal-add').modal('show');
        }
    });
    $('#add-form').bootstrapValidator({
        submitButtons: 'button[type="submit"]',
        submitHandler: function (validator, form, submitButton) {
            $.post(form.attr('action'), form.serialize(), function (data) {
                $('#modal-add').modal('hide');
                $('#yes_add').prop("disabled", false)
                var data = eval('(' + data + ')');
                refreshTable();
                if (data.code == 0) {
                    made_modal('关联安全组',data.msg);
                } else {
                    made_modal('关联安全组',data.msg);
                }
            });
        },
        fields: {
            add_codes: {
                validators: {
                    notEmpty: {
                        message: '请重新选择'
                    }
                }
            }
        }
    });

    //解绑安全组
    $('#out_group').on('click', function () {
        var rows = $('#table').bootstrapTable('getSelections');
        if (rows.length == 0) {
            made_modal('提示', '请选择要解绑安全组的实例');
        } else {
            $('#modal-delete').modal('show');
        }
    });
    $('#yes_delete').on('click', function () {
        var rows = $('#table').bootstrapTable('getSelections');
        $.ajax({
            type: 'post',
            url: "<?= $this-> Url->build(['controller'=>'SecurityGroup','action'=>'removeEntry']);?>",
            data: {rows: rows,removeType:'otherGroup'},
            success: function (data) {
                datas = $.parseJSON(data);
                $('#modal-delete').modal('hide');
                refreshTable();
                if (datas.code == 0) {
                    made_modal('解绑安全组',datas.msg);
                } else {
                    made_modal('解绑安全组',datas.msg);
                }
            }
        });
    });
    function formatter_type(value){
        if(value=='hosts'){
            return '云主机';
        }else if(value=='desktop'){
            return '云桌面';
        }else{
            return '-';
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

    function notifyCallBack(value){
        if(value.MsgType=="success"||value.MsgType=="error"){
            if(value.Data.method=="security_group_associate"||value.Data.method=="security_group_dissociate"){
                refreshTable();
            }
        }
    }
</script>
<?= $this->end() ?>