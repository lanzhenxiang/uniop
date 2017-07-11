
<div class="wrap-nav-right wrap-nav-right-left">


    <div class="wrap-manage">
        <!--[if IE]>
        <object id="vmrc" classid="CLSID:4AEA1010-0A0C-405E-9B74-767FC8A998CB"
                style="width: 100%; height: 100%;"></object>
        <![endif] -->
        <!--[if !IE]><!-->

        <!--<![endif]-->
        <div class="top">
            <span class="title">边界路由器列表</span>
            <div class="callback-info pull-right text-success"><i class="icon-ok"></i>&nbsp;操作成功</div>
            <div id="maindiv-alert"></div>
        </div>
        <div class="center clearfix">
            <?php if (in_array('ccf_add_vbr', $this->Session->read('Auth.User.popedomname'))) { ?>
            <a class="btn btn-addition" href="<?=$this->Url->build(['controller'=>'HighSpeedChannel','action'=>'add']);?>">
                <i class="icon-plus"></i>&nbsp;&nbsp;新建边界路由器</a>
            <?php }?>
            <?php if (in_array('ccf_del_vbr', $this->Session->read('Auth.User.popedomname'))) { ?>
            <button class="btn btn-addition" onclick="delVbr();">
                <i class="icon-remove"></i>&nbsp;&nbsp;删除边界路由器
            </button>
            <?php }?>

            <div class="pull-right">
                <input type="hidden" id="txtdepartmentId" value="0">
            <?php if (in_array('ccf_all_select_department', $this->Session->read('Auth.User.popedomname'))) : ?>
            <div class="dropdown">
                            租户:
                    <a href="javascript:;" class="dropdown-toggle btn btn-addition text-right" data-toggle="dropdown"
                       role="button" aria-haspopup="true" aria-expanded="false">
                        <span class="pull-left" id="departments" val="0">全部</span>
                        <span class="caret"></span>
                    </a>

                    <ul class="dropdown-menu">
                        <li><a href="#" onclick="departmentlist(0,'全部')">全部</a></li>
                        <?php if(isset($departments)&&!empty($departments)){?>
                        <?php foreach($departments as $key => $value){?>
                        <li><a href="#"
                               onclick="departmentlist(<?php echo $value['id'] ?>, '<?php echo $value['name'] ?>')"><?php echo $value['name'] ?></a>
                        </li>
                        <?php }?>
                        <?php }?>
                    </ul>
                </div>
                <?php endif;?>
                
    			<span class="search">
    			    <input type="text" id="txtsearch" name="search" placeholder="搜索边界路由器名">
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
                   data-unique-id="id"
                   data-url="<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'BoundaryRouterList', 'action' => 'vbrList', '?'=>['department_id'=>$department_id] ]);?>">
                <thead>
                <tr>
                    <th data-checkbox="true"></th>
                    <th data-field="name">边界路由器</th>
                    <th data-field="other">冗余边界路由器</th>
                    <th data-field="status" data-formatter="formatter_state">运行状态</th>
                    <th data-field="location_name">本端部署区位</th>
                    <th data-field="vbr_extend.physicallineCode">连接专线</th>
                    <th data-field="vpc">本端VPC</th>
                    <th data-field="vbr_extend.subnet">本端子网</th>
                    <th data-field="vbr_extend.customName"  data-formatter="local">对端部署区位</th>
                    <th data-field="vbr_extend.aliyun_vpcCode">对端VPC</th>
                    <th data-field="id" data-formatter="formatter_operate">操作</th>
                </tr>
                </thead>
            </table>
        </div>

    </div>
</div>

<!-- 右键弹框 -->
<div class="context-menu" id="context-menu">
    <ul>

        <li id="rename"><a href="javascript:void(0);"><i class="icon-pencil"></i> 重命名</a></li>


    </ul>
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
    //搜索绑定
    $("#txtsearch").on('keyup', function () {
        if (timer != null) {
            clearTimeout(timer);
        }

        var timer = setTimeout(function () {
            searchs();
        }, 500);
    });
    function searchs() {
        var department_id = $('#txtdepartmentId').val();
        var search = $('#txtsearch').val();
        $('#table').bootstrapTable('refresh', {
            url: "<?=$this->Url->build(['controller'=>'BoundaryRouterList','action'=>'vbrList']);?>?department_id=" + department_id + '&search=' + encodeURI(search)
        });
    }

    //更改租户
    function departmentlist(id, name) {
        $('#txtdepartmentId').val(id);
        $('#departments').html(name);

        searchs();
    }

    function local(value) {
		return "阿里云--浙江";
    }

    function formatter_operate(value) {
        return "<a href='javascript::' onclick='port_manage(this)'>接口管理</a>"
    }
    function port_manage(event) {
        var uniqueId = $(event).parent().parent().attr('data-uniqueid');
        location.href = "<?=$this->Url->build(['controller'=>'BoundaryRouterList','action'=>'vbrPorts']);?>?vbr_id=" + uniqueId;
    }

    $('#table').contextMenu('context-menu',{
        bindings:{
            'rename':function(event){
                var uniqueId = $(event).attr('data-uniqueid');
                var row = $('#table').bootstrapTable('getRowByUniqueId', uniqueId);
                $('#modal-rename-id').val(row.id);
                $('#modal-old-name').html(row.name);
                $('#modal-rename').one('show.bs.modal',function(){
                    $('#sumbiter').one('click',function(){
                        $.ajax({
                            url:"<?=$this->Url->build(['controller'=>'BoundaryRouterList','action'=>'renameVbr']);?>",
                            method:'post',
                            data:$('#modal-rename-form').serialize(),
                            success:function(data){
                                data= $.parseJSON(data);
                                $('#modal-rename').modal('hide');
                                searchs();
                                if(data.code==0){
                                    tentionHide(data.msg, 0);
                                }else{
                                    tentionHide(data.msg, 1);
                                }
                            }
                        });
                    });
                });
                $('#modal-rename').modal('show');
            }
        }
    });

    /**
     * [notifyCallBack 异步消息执行完成回调函数]
     */
    function notifyCallBack(value) {
        window.location.reload();
    }


    //删除vbr
    function delVbr(){
        var rows=$('#table').bootstrapTable('getSelections');
        if(rows.length>0){
            $('#modal-delete').modal('show');
        }else{
            tentionHide('请选择要删除的边界路由器', 1);
        }
    }
    $('#yes_delete').on('click',function(){
        var rows=$('#table').bootstrapTable('getSelections');
        $.ajax({
            type:'post',
            url:"<?= $this-> Url->build(['controller'=>'BoundaryRouterList','action'=>'deleteVbr']);?>",
            data:{rows:rows},
            success:function(data){
                datas= $.parseJSON(data);
                $('#modal-delete').modal('hide');
                searchs();
                if(datas.code==0){
                    layer.msg(datas.msg);
                }else{
                    layer.alert(datas.msg);
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

    function notifyCallBack(value) {
  	  //console.log(value);

  		if (value.MsgType == "success" || value.MsgType == "error") {
  			if (value.Data.method == "vbr_add" || value.Data.method == "vbr_del") {
  				searchs()
  			}
  		}
  	}

    //返回状态
    function formatter_state(value, row, index) {
      switch (value) {
        case "创建中":
        {
          return '<span id="imgState' + row.id + '" class="circle circle-create"></span><span id="txtState' + row.id + '">创建中</span>';
          break;
        }
        case "运行中":
        {
          return '<span id="imgState' + row.id + '" class="circle circle-run"></span><span id="txtState' + row.id + '">运行中</span>';
          break;
        }
        case "已停止":
        {
          return '<span id="imgState' + row.id + '" class="circle circle-stopped"></span><span id="txtState' + row.id + '">已停止</span>';
          break;
        }
        case "创建失败":
        {
          return '<span id="imgState' + row.id + '" class="circle circle-stopped"></span><span id="txtState' + row.id + '">创建失败</span>';
          break;
        }
        case "销毁中":
        {
          return '<span id="imgState' + row.id + '" class="circle circle-create"></span><span id="txtState' + row.id + '">销毁中</span>';
          break;
        }
        case "销毁失败":
        {
          return '<span id="imgState' + row.id + '" class="circle circle-stopped"></span><span id="txtState' + row.id + '">销毁失败</span>';
          break;
        }
        default:
        {
          return '<span id="imgState' + row.id + '" class="circle circle-create"></span>-';
        }
      }
    }
</script>
<?php  $this->end();?>
