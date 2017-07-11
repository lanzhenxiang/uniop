<?= $this->element('security/left',['active_action'=>'security_group']); ?>
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
    .info{
        margin-right: 40px;
    }
    hr{
        border:1px #ddd solid;
    }
</style>
<div class="wrap-nav-right">
    <div class="wrap-manage">
        <div class="top">
            <span class="title pull-left">实例管理</span>
            <a href="<?= $this->Url->build(['controller'=>'SecurityGroup','action'=>'index']); ?>" class="btn btn-addition pull-right">返回</a>
            <div style="clear: both"></div>
            <div id="maindiv-alert"></div>
        </div>

        <div class="center clearfix">
            <span class="info">安全组名:<?=$data['name'];?></span>
            <span class="info">安全组CODE:<?=$data['code'];?></span>
            <span class="info">所在VPC名:<?=$data['vpcName'];?></span>
            <span class="info">所在VPC CODE:<?=$data['vpc'];?></span>
            <span class="info">部署区位:<?=$data['location_name'];?></span>
            <div style="clear: both;"></div>
        </div>
        <br>
        <div class="center clearfix">
            <span>安全组内实例列表</span>
            <hr>
        </div>
        <div class="clearfix center">
            <a href="javascript:refreshTable();" id="btnRefresh" class="btn btn-addition"><i class="icon-refresh"></i>&nbsp;&nbsp;刷新</a>
            <a class="btn btn-addition" id="addListen" href="<?=$this->Url->build(['controller'=>'SecurityGroup','action'=>'addEntry']);?>?basic_id=<?=$id;?>"><i class="icon-plus"></i>&emsp;<span>添加实例</span></a>
            <button class="btn btn-default" onclick="" id="remove" disabled="disabled">
                <i class="icon-remove"></i>&emsp;<span>移出实例</span>
            </button>
        </div>
        <!--表格-->
        <div class="bot">
            <div>
                <div class="tab-content">

                        <table class="table table-striped" id="table" data-toggle="table" data-pagination="true"
                               data-side-pagination="server"
                               data-page-list="[20,30]" data-page-size="20"
                               data-locale="zh-CN" data-click-to-select="true"
                               data-url="<?=$this->Url->build(['controller'=>'SecurityGroup','action'=>'entryList']);?>?id=<?=$id;?>"
                               data-unique-id="id">
                            <thead>
                            <tr>
                                <th data-checkbox="true"></th>
                                <th data-field="network_code">网卡CODE</th>
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



<!--移出实例-->
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

    //table选中
    $('#table').on('all.bs.table',function(){
        if($('tbody input:checked').length>=1){
            $('#remove').attr('disabled',false);
        }else{
            $('#remove').attr('disabled',true);
        }
    });
    //刷新表格
    function refreshTable() {

        $('#table').bootstrapTable('refresh', {
            url: "<?= $this->Url->build(['controller'=>'SecurityGroup','action'=>'entryList']);?>?id=<?=$id;?>"
    });
    }


    //移出安全组
    $('#remove').on('click',function(){
        var rows = $('#table').bootstrapTable('getSelections');
        if(rows.length==0){
            made_modal('提示', '请选择要移出安全组的实例');
        }else{
            $('#modal-delete').modal('show');
        }
    });
    $('#yes_delete').on('click',function(){
        var rows = $('#table').bootstrapTable('getSelections');
        $.ajax({
            type:'post',
            url:"<?= $this-> Url->build(['controller'=>'SecurityGroup','action'=>'removeEntry']);?>",
            data:{rows:rows,basic_id:'<?=$id;?>'},
            success:function(data){
                datas= $.parseJSON(data);
                $('#modal-delete').modal('hide');
                refreshTable();
                if(datas.code==0){
                    made_modal('移出实例',datas.msg)
                }else{
                    made_modal('移出实例',datas.msg);
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