

<style>
    .info{
        margin: 20px 40px;

    }
    .content-left{
        text-align: right;
        width: 500px;
    }
    .content-right{
        text-align:left;
        width:500px;
    }


    .modal-form-group label{
        width: 200px;
    }
    .col-sm-6{
        padding-left:0;
    }
</style>
<div class="wrap-nav-right wrap-nav-right-left">


    <div class="wrap-manage">
        <!--[if IE]>
        <object id="vmrc" classid="CLSID:4AEA1010-0A0C-405E-9B74-767FC8A998CB"
                style="width: 100%; height: 100%;"></object>
        <![endif] -->
        <!--[if !IE]><!-->

        <!--<![endif]-->
        <div class="top">
            <span class="title">路由管理</span>
            <div class="callback-info pull-right text-success"><i class="icon-ok"></i>&nbsp;操作成功</div>
            <div id="maindiv-alert"></div>
        </div>
        <table style="margin-left: 20px;">
            <tr >
                <td class="row-message-left">边界路由器</td>
                <td class="row-message-right" colspan="3">
                    <div class="ng-binding">
                        <div class="clearfix">
                            <p class="info"> <a class="btn btn-addition" onclick="window.history.go(-1)">
                                </i>&nbsp;&nbsp;返回路由器接口列表</a></p>
                            <p class="info"><span class="content-left">接入场景:</span>&#160;<span class="content-right"><?=$vbr_data['connectionScene'];?></span></p>
                            <p class="info"><span class="content-left">路由器:</span>&#160;<span class="content-right"><?=$vbr_data['name'];?></span></p>
                            <p class="info"><span class="content-left">部署区位:</span>&#160;<span class="content-right"><?=$vbr_data['location_name'];?></span></p>
                        </div>
                    </div>
                </td>
            </tr>
            <tr >
                <td class="row-message-left">路由器接口</td>
                <td class="row-message-right" colspan="3">
                    <div class="ng-binding">
                        <div class="clearfix">
                            <p class="info"><span class="content-left">部署区位:</span>&#160;<span class="content-right"><?=$vbr_data['location_name'];?></span></p>
                            <p class="info"><span class="content-left">路由器接口名:</span>&#160;<span class="content-right"><?=$port_data['customName'];?></span></p>
                            <p class="info"><span class="content-left">路由器接口类型:</span>&#160;<span class="content-right"><?=$port_data['type'];?></span></p>
                            <p class="info"><span class="content-left">规格:</span>&#160;<span class="content-right"><?=$port_data['spec'];?></span></p>
                        </div>
                    </div>
                </td>
            </tr>
        </table>


        <hr style="border-color: #ccc">
        <div class="center clearfix">
            <?php if (in_array('ccf_add_vbr_router', $this->Session->read('Auth.User.popedomname'))) { ?>
            <button class="btn btn-addition" onclick="addRouter()"><i class="icon-plus"></i>&nbsp;&nbsp;添加路由</button>
            <?php }?>
            <?php if (in_array('ccf_del_vbr_router', $this->Session->read('Auth.User.popedomname'))) { ?>
            <button class="btn btn-addition" onclick="delRouter();"><i class="icon-remove"></i>&nbsp;&nbsp;删除路由
            </button>
            <?php }?>



        </div>
        <div class="bot ">
            <table id="table" data-toggle="table"
                   data-pagination="true"
                   data-side-pagination="server"
                   data-locale="zh-CN"
                   data-click-to-select="true"
                   data-unique-id="id"
                   data-url="<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'BoundaryRouterList', 'action' => 'editvbrList' ]);?>?basic_id=<?=$vbr_data['id'];?>">
                <thead>
                <tr>
                    <th data-checkbox="true"></th>
                    <th data-field="subnetName">目标子网</th>
                    <th data-field="subnetCode">目标子网CODE</th>
                    <th data-field="segment">目标网段</th>
                    <th data-field="customName">路由器</th>
                    <th data-field="nextHop">下一跳接口</th>
                    <th data-field="status">状态</th>

                </tr>
                </thead>
            </table>
        </div>
        <!--边界路由器id-->
        <input type="hidden" id="basic_id" value="<?=$vbr_data['id'];?>">
    </div>
</div>
<!--添加路由-->

<div class="modal fade" id="modal-add" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h5 class="modal-title">添加路由</h5>
            </div>
            <form id="modal-add-form" action="" method="post">


                <input type="hidden" id="port_type" name="port_type" value="<?=$port_data['type'];?>">
                <input type="hidden" id="add_basic_id" name="add_basic_id" value="<?=$vbr_data['id'];?>">
                <input id="add_customName" name="add_customName" type="hidden"  value="<?=$port_data['customName'];?>">
                <input type="hidden" id="add_routerCode" name="add_routerCode" value="<?=$vbr_data['code'];?>">
                <input type="hidden" id="add_nextHop" name="add_nextHop" value="<?=$port_data['add_nextHop'];?>">


                <div class="modal-body">
                    <div class="modal-form-group">
                        <label id="label_location">发起端部署区位:</label>
                        <div>
                            <span id="add_location"><?=$vbr_data['location_name'];?></span>
                        </div>
                    </div>
                    <div class="modal-form-group">
                        <label id="label_vpc">接收端VPC:</label>
                        <div>
                            <span id="add_vpc"><?=$vbr_data['vpc'];?></span>
                        </div>
                    </div>

                    <div class="modal-form-group">
                        <label id="label_subnet">接收端子网:</label>
                        <div>
                            <!--<input id="add_subnet" name="add_subnet" type="text" maxlength="15" />-->
                            <div class="col-sm-6">
                                <select name="add_subnet" id="add_subnet" class="form-control" onchange="change()">
                                    <option value="">请选择</option>
                                    <?php if(isset($add_subnet)&&!empty($add_subnet)){?>
                                    <?php foreach($add_subnet as $key => $value){?>
                                    <option value="<?=$value['code'];?>"><?=$value['name'];?></option>
                                    <?php }?>
                                    <?php }?>
                                </select>
                                <span class="text-danger" style="display:none" id="subnet_validate">请选择子网</span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-form-group" id="ecm_div">
                        <label>是否等价路由:</label>
                        <div>
                            <!--<input id="add_ecm" name="add_ecm" type="text" maxlength="15" />-->
                            <div class="col-sm-6">
                            <select name="add_ecm" id="add_ecm" class="form-control" onchange="change()">
                                <option value="">请选择</option>
                                <option value="0" selected>否</option>
                                <option value="1">是</option>
                            </select>
                                <span class="text-danger" style="display:none" id="ecm_validate">请选择是否为等价路由</span>
                                <span class="text-danger" style="display:none" id="ecm_validate2">没有备用VBR，不能为等价路由</span>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button id="yes-add" type="button" class="btn btn-primary">确认</button>
                    <button id="no-add" type="button" class="btn btn-danger" data-dismiss="modal">取消</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!--删除-->
<div class="modal fade" id="modal-delete" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">提示</h5>
            </div>
            <div class="modal-body">
                <i class="icon-question-sign text-primary"></i>&nbsp;&nbsp;确认要删除选中边界路由器么？<span class="text-primary"
                                                                                               id="sure"></span>？
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="yes_delete">确认</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">取消</button>
            </div>
        </div>
    </div>
</div>
<!--重命名-->
<div class="modal fade" id="modal-rename" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h5 class="modal-title">重命名</h5>
            </div>
            <form id="modal-rename-form" action="" method="post">
                <div class="modal-body">
                    <div class="modal-form-group">
                        <label>原名:</label>
                        <div>
                            <span id="modal-old-name"></span>
                        </div>
                    </div>
                    <div class="modal-form-group">
                        <label>新名:</label>
                        <div>
                            <input id="modal-new-name" name="new_name" type="text" maxlength="15" />
                        </div>
                    </div>
                    <input id="modal-rename-id" name="vbr_id" type="hidden" />
                </div>
                <div class="modal-footer">
                    <button id="sumbiter" type="button" class="btn btn-primary">确认</button>
                    <button id="reseter" type="button" class="btn btn-danger" data-dismiss="modal">取消</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="maindiv"></div>
<?php $this -> start('script_last'); ?>
<!--<?= $this->Html->script(['network/hosts_list']); ?>-->
<script type="text/javascript">

    function change(){
        $('#subnet_validate').css('display','none');
        $('#ecm_validate').css('display','none');
        $('#ecm_validate2').css('display','none');
    }
//添加路由
    function addRouter(){
		var add_nextHop = $("#add_nextHop").val();
		if (add_nextHop == "" || add_nextHop == null) {
			layer.msg("没有接口Code，不能创建路由项");
		} else {
            if($('#port_type').val()=='发起端接口'){
                $('#ecm_div').css('display','block');
                $('#label_location').html('发起端部署区位');
                $('#label_subnet').html('发起端子网');
                $('#label_vpc').html('发起端vpc');
            }else{
                $('#ecm_div').css('display','none');
                $('#label_location').html('接收端部署区位');
                $('#label_subnet').html('接收端子网');
                $('#label_vpc').html('接收端vpc');
            }
            $('#add_subnet').val('');
            $('#add_ecm').val('');
            $('#modal-add').modal('show');
            $('#subnet_validate').css('display','none');
            $('#ecm_validate').css('display','none');
            $('#ecm_validate2').css('display','none');
		}

    }

    $('#yes-add').on('click',function(){
        if($('#add_subnet').val()==''){
            $('#subnet_validate').css('display','block');
        }else if($('#add_ecm').val()=='' && $('#port_type').val()=='发起端接口'){
            $('#ecm_validate').css('display','block');
        }else{
            if($('#add_ecm').val()==1){
                $.getJSON("<?=$this->Url->build(['controller'=>'BoundaryRouterList','action'=>'existRE']);?>?basic_id="+$('#add_basic_id').val(),function(data){
                    var add_ecm=$('#add_ecm').val();
                    if(data.code!=1&&add_ecm==1){
                        $("#ecm_validate2").html(data.msg);
                        $('#ecm_validate2').css('display','block');
                    }else{
                        layer.confirm('备VBR边界路由器接口Code:'+data.vbrCode, {
                            btn: ['确定','取消'] //按钮
                        }, ajaxRouter, function(){

                        });
                    }
                });
            }else{
                ajaxRouter();
            }

        }
    });

    /**
     * 提交创建路由项请求
      */
    function ajaxRouter(){
        $.ajax({
            type:'post',
            url:"<?= $this-> Url->build(['controller'=>'BoundaryRouterList','action'=>'addRouter']);?>",
            data:{
                basic_id:$('#add_basic_id').val(),
                add_subnet:$('#add_subnet').val(),
                add_ecm:$('#add_ecm').val(),
                type:$('#port_type').val(),
                add_customName:$('#add_customName').val(),
                add_routerCode:$('#add_routerCode').val(),
                add_nextHop:$('#add_nextHop').val()
            },
            success:function(data){
                datas= $.parseJSON(data);
                $('#modal-add').modal('hide');
                if(datas.code==0){
                    layer.msg(datas.msg);
                }else{
                    layer.alert(datas.msg);
                }
            }
        });
    }


    //删除路由项
    function delRouter(){
        var rows=$('#table').bootstrapTable('getSelections');
        if(rows.length>0){
            $('#modal-delete').modal('show');
        }else{
            tentionHide('请选择要删除的路由器', 1);
        }
    }
    $('#yes_delete').on('click',function(){
        var rows=$('#table').bootstrapTable('getSelections');
        $.ajax({
            type:'post',
            url:"<?= $this-> Url->build(['controller'=>'BoundaryRouterList','action'=>'delRouter']);?>",
            data:{rows:rows},
            success:function(data){
                datas= $.parseJSON(data);
                $('#modal-delete').modal('hide');
                searchs();
                if(datas.code==0){
                    tentionHide(datas.msg, 0);
                }else{
                    tentionHide(datas.msg, 1);
                }
            }
        });
    });
    function tentionHide(content, state) {
        $("#maindiv-alert").empty();
        var html = "";
        if (state == 0) {
            html += '<div class="point-host-startup "><i></i>' + content + '</div>';
            $("#maindiv-alert").append(html);
            $(".point-host-startup ").slideUp(5000);
        } else {
            html += '<div class="point-host-startup point-host-startdown"><i></i>' + content + '</div>';
            $("#maindiv-alert").append(html);
            $(".point-host-startdown").slideUp(5000);
        }
    }
</script>
<?php  $this->end();?>

