<!--  云桌面  列表 -->
<div class="wrap-nav-right">

  <div class="wrap-manage">
    <div class="top">
      <span class="title">回收站</span>
      <div id="maindiv-alert"></div>
    </div>
    <div class="center clearfix">
      <div class="pull-right">
       <input type="hidden" id="txtdeparmetId" value="<?= $_default["id"] ?>" />
       <?php if (in_array('ccf_all_select_department', $this->Session->read('Auth.User.popedomname'))) { ?>
       <div class="dropdown">
        租户:
        <a href="#" class="dropdown-toggle btn btn-addition text-right" data-toggle="dropdown" role="button">
          <span class="pull-left" id="deparmets" val="<?= $_default["id"] ?>"><?= $_default["name"] ?></span>
          <span class="caret"></span>
        </a>
        <ul class="dropdown-menu">
          <?php foreach($_deparments as $value) { ?>
          <li><a href="#" onclick="departmentlist(<?php echo $value['id'] ?>,'<?php echo $value['name'] ?>')"><?php echo $value['name'] ?></a></li>
          <?php }?>
        </ul>
      </div>
      <?php }?>
      <div class="dropdown">
        厂商:
        <a href="#" class="dropdown-toggle btn btn-addition text-right" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
          <span class="pull-left" id="agent" val="">全部</span>
          <span class="caret"></span>
        </a>
        <ul class="dropdown-menu">
          <li><a href="javascript:;" onclick="local(0,'','全部')">全部</a></li>
          <?php
          if(isset($agent)){
            foreach($agent as $value) {
              if ($value['parentid'] == 0) {?>
              <li><a href="#" onclick="local(<?php echo $value['id'] ?>,'<?php echo $value['class_code'] ?>','<?php echo $value['agent_name'] ?>')"><?php echo $value['agent_name'] ?></a></li>
              <?php }
            }
          } ?>
        </ul>
      </div>
      <div class="dropdown">
        地域:
        <a  href="javascript:;" class="dropdown-toggle btn btn-addition text-right" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
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

  <div class="service-content-navi">
    <ul class="clearfix text-center">
      <li class="active"><a href="##">云主机</a></li>
      <li class=""><a href="##">云桌面</a></li>
    </ul>
  </div>

  <div class="images-checkbox">
    <div class="bot">
      <button class="btn btn-addition" onclick="refreshTable(1);">
        <i class="icon-refresh"></i>&nbsp;&nbsp;刷新
      </button>
      <?php if (in_array('ccf_host_delete', $this->Session->read('Auth.User.popedomname'))) { ?>
      <button class="btn btn-default" onclick="deleteall(1);" >
        <i class="icon-trash"></i>&nbsp;&nbsp;清空
      </button>
      <?php } ?>
      <?php if (in_array('ccf_host_delete', $this->Session->read('Auth.User.popedomname'))) { ?>
      <button class="btn btn-default btn-default01" onclick="deleteCheck('1')" disabled>
        <i class="icon-trash"></i>&nbsp;&nbsp;彻底删除
      </button>
      <?php } ?>
      <?php if (in_array('ccf_host_recover', $this->Session->read('Auth.User.popedomname'))) { ?>
      <button class="btn btn-default btn-default01" onclick="recoverCheck('1')" disabled>
        <i class="icon-reply "></i>&nbsp;&nbsp;恢复
      </button>
      <?php } ?>


      <table id="table01" data-toggle="table"
      data-pagination="true" ="false"
      data-side-pagination="server"
      data-locale="zh-CN"
      data-click-to-select="true"
      data-url="<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'recycled', 'action' => 'listsHosts','?'=>['department_id'=>$_default["id"]]]); ?>"
      data-unique-id="id">
      <thead>
        <tr>
          <th data-checkbox="true"></th>
          <th data-field="id" ="true">Id</th>
          <th data-field="code">主机Code</th>
          <th data-field="name">主机名称</th>
          <th data-field="host_extend" data-formatter="formatter_operateSystem">操作系统</th>
          <th data-field="location_name">部署区位</th>
          <th data-field="hosts_network_card" data-formatter="formatter_vxnets">子网</th>
          <th data-field="host_extend" data-formatter="formatter_config">配置</th>
          <th data-field="instance_recycle" data-formatter="formatter_user">删除人</th>
          <th data-field="instance_recycle" data-formatter="formatter_time">删除时间</th>
          <th data-field="instance_recycle" data-formatter="formatter_delete_time">保留截止时间</th>
          <th data-field="status" data-formatter="formatter_state">状态</th>
        </tr>
      </thead>
    </table>
  </div>
  <div class="bot "  style="display:none;">

    <button class="btn btn-addition" onclick="refreshTable(2);">
      <i class="icon-refresh"></i>&nbsp;&nbsp;刷新
    </button>
    <?php if (in_array('ccf_desktop_delete', $this->Session->read('Auth.User.popedomname'))) { ?>
    <button class="btn btn-default" onclick="deleteall(2);" >
    <i class="icon-trash"></i>&nbsp;&nbsp;清空
    </button>
    <?php } ?>
    <?php if (in_array('ccf_desktop_delete', $this->Session->read('Auth.User.popedomname'))) { ?>
    <button class="btn btn-default btn-default02" onclick="deleteCheck('2')" disabled>
      <i class="icon-trash "></i>&nbsp;&nbsp;彻底删除
    </button>
    <?php } ?>
    <?php if (in_array('ccf_desktop_recover', $this->Session->read('Auth.User.popedomname'))) { ?>
    <button class="btn btn-default btn-default02" onclick="recoverCheck('2')" disabled>
      <i class="icon-reply "></i>&nbsp;&nbsp;恢复
    </button>
    <?php } ?>

    <table id="table02" data-toggle="table"
    data-pagination="true"
    data-side-pagination="server"
    data-locale="zh-CN"
    data-click-to-select="true"
    data-url="<?= $this->Url->build(['prefix'=>'console','controller'=>'recycled','action'=>'listsDesktop','?'=>['department_id'=>$_default['id']]]); ?>"
    data-unique-id="id">
    <thead>
      <tr>
        <th data-checkbox="true"></th>
        <th data-field="id" >Id</th>
        <th data-field="code">云桌面Code</th>
        <th data-field="name">桌面名称</th>
        <th data-field="host_extend" data-formatter="formatter_operateSystem">操作系统</th>
        <th data-field="location_name">部署区位</th>
        <th data-field="hosts_network_card" data-formatter="formatter_vxnets">子网</th>
        <th data-field="host_extend" data-formatter="formatter_config">配置</th>
        <th data-field="instance_recycle" data-formatter="formatter_user">删除人</th>
        <th data-field="instance_recycle" data-formatter="formatter_time">删除时间</th>
        <th data-field="instance_recycle" data-formatter="formatter_delete_time">保留截止时间</th>
        <th data-field="status" data-formatter="formatter_state">状态</th>
      </tr>
    </thead>
  </table>
</div>

</div>
</div>

<!-- 右键弹框 -->
<div class="context-menu fade" id="context-menu01">
  <ul>
    <?php if (in_array('ccf_host_recover', $this->Session->read('Auth.User.popedomname'))) { ?>
    <li><a id="recover01" href="#"><i class="icon-reply "></i> 恢复</a></li>
    <?php } ?>
    <?php if (in_array('ccf_host_delete', $this->Session->read('Auth.User.popedomname'))) { ?>
    <li><a id="dele01" href="#"><i class="icon-trash"></i> 删除</a></li>
    <?php } ?>

    <?php if (in_array('ccm_ps_excp', $this->Session->read('Auth.User.popedomname'))) { ?>
    <li id="excp01"><a  href="javascript:;"><i class="icon-book"></i> 异常日志</a></li>
    <?php } ?>
    <!-- <?php if (in_array('ccf_normal_list', $this->Session->read('Auth.User.popedomname'))) { ?>
    <li id="normal01"><a  href="javascript:;"><i class="icon-book"></i> 正常日志</a></li>
    <?php } ?> -->
  </ul>
</div>

<!-- 右键弹框 -->
<div class="context-menu fade" id="context-menu02">
  <ul>
    <?php if (in_array('ccf_desktop_recover', $this->Session->read('Auth.User.popedomname'))) { ?>
    <li><a id="recover02" href="#"><i class="icon-reply "></i> 恢复</a></li>
    <?php } ?>
    <?php if (in_array('ccf_desktop_delete', $this->Session->read('Auth.User.popedomname'))) { ?>
    <li><a id="dele02" href="#"><i class="icon-trash"></i> 删除</a></li>
    <?php } ?>

    <?php if (in_array('ccm_ps_excp', $this->Session->read('Auth.User.popedomname'))) { ?>
    <li id="excp02"><a  href="javascript:;"><i class="icon-book"></i> 异常日志</a></li>
    <?php } ?>
    <!-- <?php if (in_array('ccf_normal_list', $this->Session->read('Auth.User.popedomname'))) { ?>
    <li id="normal02"><a  href="javascript:;"><i class="icon-book"></i> 正常日志</a></li>
    <?php } ?> -->
  </ul>
</div>

<div id="maindiv"></div>

<div style="width:0;height:0">
  <iframe id="lauchFrame" name="lauchFrame" src="" width=0  height=0 >
  </iframe>
</div>
<script src="/js/jQuery-2.1.3.min.js"></script>
<?= $this->Html->css(['zTreeStyle.css']) ?>
<?= $this->Html->script(['jquery.ztree.core-3.5.js']); ?>
<?= $this->Html->script(['jquery.ztree.excheck-3.5.js']); ?>
<?php
$this->start('script_last');
?>
<script type="text/javascript">
  $(function(){
    // 镜像切换
    $(".service-content-navi li").on('click',function(){
      var index=$(this).index();
      $(".service-content-navi li").removeClass('active')
      $(this).addClass('active');
      $(".images-checkbox .bot").hide();
      $(".images-checkbox .bot").eq(index).show();
    });
    $('#table01').contextMenu('context-menu01', {
      bindings:{
        'recover01': function(event) {
          var uniqueId = $(event).attr('data-uniqueid');
          var row = $('#table01').bootstrapTable('getRowByUniqueId', uniqueId);
          if(row.code==null){
            row.code="";
          }
          if(getQuotaByType(1,row.id,row.code)==true){
                showModal('提示', 'icon-question-sign', '确认要恢复机器', row.name, 'ajaxFun(\''+row.code+'\',\'RemoveTrash\',\''+row.id+'\')');
            }
        },
        'dele01': function(event){
            var rows = $('#table01').bootstrapTable('getSelections');
          var uniqueId = $(event).attr('data-uniqueid');
          var row = $('#table01').bootstrapTable('getRowByUniqueId', uniqueId);
          if(row.code==null){
            row.code="";
          }
//            if(row.hosts_network_card.length<=1){
                showModal('提示', 'icon-question-sign', '删除后挂载的硬盘数据无法恢复,确认要删除机器', row.name, 'ajaxFun(\''+row.code+'\',\'ecs_delete\',\''+row.id+'\')');
//            }else{
//                showModal('提示', 'icon-question-sign', '该主机下存在未删除网卡，不能删除主机', row.name, null,0);
//                $("#btnDesktop").html("关闭");
//            }
        },
        'excp01':function(event){
          var uniqueId = $(event).attr('data-uniqueid');
          var row = $('#table01').bootstrapTable('getRowByUniqueId', uniqueId);
          var department_id = row.department_id;
          window.location.href = "/console/excp/lists/excp/recycle/"+department_id+'/all/0/0/'+row.id;
        },
        //正常
        'normal01':function(event){
          var uniqueId = $(event).attr('data-uniqueid');
          var row = $('#table01').bootstrapTable('getRowByUniqueId', uniqueId);
          var department_id = row.department_id;
          window.location.href = "/console/excp/lists/normal/recycle/"+department_id+'/all/0/0/'+row.id;
        }

      }
    });


    $('#table02').contextMenu('context-menu02', {
      bindings:{
        'recover02': function(event) {
          var uniqueId = $(event).attr('data-uniqueid');
          var row = $('#table02').bootstrapTable('getRowByUniqueId', uniqueId);
          if(row.code==null){
            row.code="";
          }
          if(getQuotaByType(2,row.id,row.code)==true){
            showModal('提示', 'icon-question-sign', '确认要恢复机器', row.name, 'ajaxFun(\''+row.code+'\',\'RemoveTrash\',\''+row.id+'\')');
          }
        },
        'dele02': function(event){
          var uniqueId = $(event).attr('data-uniqueid');
          var row = $('#table02').bootstrapTable('getRowByUniqueId', uniqueId);
          if(row.code==null){
            row.code="";
          }
          showModal('提示', 'icon-question-sign', '确认要删除机器,删除后挂载的硬盘数据无法恢复', row.name, 'ajaxFun(\''+row.code+'\',\'desktop_del\',\''+row.id+'\')');
        },
        'excp02':function(event){
          var uniqueId = $(event).attr('data-uniqueid');
          var row = $('#table02').bootstrapTable('getRowByUniqueId', uniqueId);

          var department_id = row.department_id;
          window.location.href = "/console/excp/lists/excp/desktop/"+department_id+'/all/0/0/'+row.id;
        },
        //正常
        'normal02':function(event){
          var uniqueId = $(event).attr('data-uniqueid');
          var row = $('#table02').bootstrapTable('getRowByUniqueId', uniqueId);
          var department_id = row.department_id;
          window.location.href = "/console/excp/lists/normal/desktop/"+department_id+'/all/0/0/'+row.id;
        }
      }
    })
  });

//动态创建modal
function showModal(title, icon, content, content1, method, type) {
  $("#maindiv").empty();
  html = "";
  html += '<div class="modal fade" id="modal" tabindex="-1" role="dialog">';
  html += '<div class="modal-dialog" role="document">';
  html += '<div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
  html += '<h5 class="modal-title">' + title + '</h5>';
  html += '</div><div class="modal-body"><i class="'+icon+' icon-color-gray text-primary"></i>&nbsp;&nbsp;' + content + '<span class="text-primary batch-warning" style="white-space: nowrap;">' + content1 + '</span></div>';
  html += '<div class="modal-footer"><button id="btnMk" onclick="' + method + '" type="button" class="btn btn-primary">确认</button><button type="button" class="btn btn-danger" id="btnDesktop" data-dismiss="modal" id="btnEsc">取消</button></div></div></div></div>';
  $("#maindiv").append(html);
  if (type == 0) {
    $("#btnMk").remove();
  }
  $('#modal').modal("show");
}

    //搜索绑定
    $("#txtsearch").on('keyup', function() {
      if (timer != null) {
        clearTimeout(timer);
      }
      var timer = setTimeout(function() {
      refreshTable(1);
      refreshTable(2);
      }, 500);
    });

    //时间戳转换日期格式
    function timestrap2date(value) {
      var now = new Date(parseInt(value) * 1000);
      return now.toLocaleString().replace(/年|月/g, "-").replace(/日/g, " ");
    }
    //格式化配置
    function formatter_config(value, row, index) {
      if(value!=null){
        if (value.cpu != 0) {
          return value.cpu + "核*" + value.memory + "GB*"+value.gpu+"MB(GPU)";
        } else {
          return "-";
        }
      }else{
        return "-";
      }
    }

//返回状态
function formatter_state(value, row, index) {
  switch (value) {
    // case "创建中":
    // {
    //   return '<span id="imgState' + row.id + '" class="circle circle-create"></span><span id="txtState' + row.id + '">创建中</span>';
    //   break;
    // }
    // case "运行中":
    // {
    //   return '<span id="imgState' + row.id + '" class="circle circle-run"></span><span id="txtState' + row.id + '">运行中</span>';
    //   break;
    // }
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
      return '<span id="imgState' + row.id + '" class="circle circle-stopped"></span><span id="txtState' + row.id + '">已停止</span>';
      break;
    }
  }
  return '<span id="imgState' + row.id + '" class="circle circle-create"></span>-';
}

//返回操作系统
function formatter_operateSystem(value, row, index) {
  if (value != null) {
    return value.os_family;
  } else {
    return "-";
  }
}
//返回网络
function formatter_vxnets(value, row, index) {
  if (value != null) {
    var sub = '';
    $.each(value,function(i,n){
      if(i>1){
        sub += ',';
      }
      sub += n.subnet_code;
    });
    return sub;
  } else {
    return "-";
  }
}

function formatter_user(value, row, index){
  if (value != null) {
    return value.user_name;
  } else {
    return "-";
  }
}

function formatter_time(value, row, index){
  if (value != null) {
    var time = timestrap2date(value.create_time);
    return time;
  } else {
    return "-";
  }
}

function formatter_delete_time(value, row, index){
  if (value != null) {
    var time = timestrap2date(value.delete_time);
    return time;
  } else {
    return "-";
  }
}

//心跳
    function heartbeat(type, id) {
      if (id != undefined && id != "") {
        $("#imgState" + id).removeClass(' circle-stopped');
        $("#imgState" + id).removeClass(' circle-run');
        $("#imgState" + id).addClass('circle-create'); //添加样式，样式名为className
        if (type == 0) {
          $("#txtState" + id).html('正在启动...');
        } else if (type == 1) {
          $("#txtState" + id).html('正在停止...');
        } else if (type == 2) {
          $("#txtState" + id).html('正在重启...');
        } else {
          $("#txtState" + id).html('正在销毁...');
        }
      } else {
        var ids = getRowsID('id');
        var idList = ids.split(',');
        idList.forEach(function(e) {
          $("#imgState" + e).removeClass('circle-stopped');
          $("#imgState" + e).removeClass('circle-run');
          $("#imgState" + e).addClass('circle-create'); //添加样式，样式名为className
          if (type == 0) {
            $("#txtState" + e).html('正在启动...');
          } else if (type == 1) {
            $("#txtState" + e).html('正在停止...');
          } else if (type == 2) {
            $("#txtState" + e).html('正在重启...');
          } else {
            $("#txtState" + e).html('正在销毁...');
          }
        });
      }
    }
//获取选中行参数
function getRowsID(type,parm) {
  var idlist = '';
  $("#table0"+parm +" input[name='btSelectItem']:checkbox").each(function() {
    if ($(this)[0].checked == true) {
            //alert($(this).val());
            var id = $(this).parent().parent().attr('data-uniqueid');
            var row = $('#table0'+parm).bootstrapTable('getRowByUniqueId', id);
            if (row.status != '') {
              if (type == 'name') {
                idlist += row.name + ',';
              } else if (type == "id") {
                idlist += row.id + ',';
              } else {
                if(row.code==null){
                  row.code="";
                }
                idlist += row.code + ',';
              }
            }
          }
        });
        idlist=idlist.substring(0,idlist.length-1);
  return idlist;
}



function ajaxFun(code, method,id) {
      $('#modal').modal("hide");
      if(method == "RemoveTrash"){
        // tentionHide('启动云桌面', id);
        type='desktop';
        action = 'recover';
      }else if(method == "desktop_del"){
        // tentionHide('销毁云桌面', id);
        var _id = id.split(',');
        $.each(_id,function(i,n){
          if(n!=''){
            heartbeat(3, n);
          }
        })
        type='desktop';
        action = 'delete';
      }else if(method == "ecs_delete"){

        var _id = id.split(',');
        $.each(_id,function(i,n){
          if(n!=''){
            heartbeat(3, n);
          }
        })
        type='hosts';
        action = 'delete';
      }
      $.ajax({
        type: "post",
        url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'recycled','action'=>'ajaxFun']); ?>/"+type+"/"+action,
        async: true,
        timeout: 9999999,
        data: {
          method:method,
          code:code,
          id:id,
        },
        //dataType:'json',
        success: function(e) {
          e= $.parseJSON(e);
            //操作成功
            if(e.code == '0000'){
                  if(method=="RemoveTrash"){
                    alert('操作成功');
                  }
              }else{
                alert(e.msg);
              }
              refreshTable(1);
              refreshTable(2);
            }
          });
    }

    function refreshTable(data) {
      var search= $("#txtsearch").val();
      //$('#table').bootstrapTable('showLoading');
      var class_code = $("#agent").attr('val');
      var class_code2 =$("#agent_t").attr('val');

      switch (data) {
        case 1:
        var source = '<?= 'Hosts';?>';
        break;
        case 2:
        var source = '<?= 'Desktop';?>';
        break;
      }
      $('#table0'+data).bootstrapTable('refresh', {
        url: "<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'recycled', 'action' => 'lists']); ?>"+source,
        query: {class_code2:class_code2,class_code:class_code,search: search,department_id:$("#txtdeparmetId").val()}
      });
    }

//提示框消失
function tentionHide(content, state) {
  $("#maindiv-alert").empty();
  var html = "";
  if (state == 0) {
    html += '<div class="point-host-startup "><i></i>' + content + '</div>';
    $("#maindiv-alert").append(html);
    $(".point-host-startup ").slideUp(3000);
  } else {
    html += '<div class="point-host-startup point-host-startdown"><i></i>' + content + '</div>';
    $("#maindiv-alert").append(html);
    $(".point-host-startdown").slideUp(3000);
  }
}

//地域查询
function local(id,class_code,agent_name) {
  if (agent_name) {
    var search= $("#txtsearch").val();
    $('#agent_t').html('全部');
    $('#agent').html(agent_name);
    $('#agent').attr('val', class_code);
    refreshTable(1);
    refreshTable(2);
    var jsondata = <?php echo json_encode($agent); ?>;
    if(id!=0){
      var data='';
      var data='<li><a href="javascript:;" onclick="local_two(\'\',\'全部\',\'' + class_code + '\')">全部</a></li>';
      $.each(jsondata, function (i, n) {
        if(n.parentid == id){
          data += '<li><a href="#" onclick="local_two(\'' + n.class_code + '\',\'' + n.agent_name + '\')">' + n.agent_name + '</a></li>';
        }
      })
      $('#agent_two').html(data);
    }else{
      data='';
      $('#agent_t').attr('val', data);
      $('#agent_two').html(data);
    }

  }
}
function local_two(class_code2,agent_name,class_code){
  var search= $("#txtsearch").val();
  $('#agent_t').html(agent_name);
  $('#agent_t').attr('val',class_code2);
  refreshTable(1);
  refreshTable(2);
}

$(".wrap-nav-right").addClass('wrap-nav-right-left');

function notifyCallBack(value){
  if(value.MsgType=="success"||value.MsgType=="error"){
    if(value.Data.method=="desktop_del" ||value.Data.method=="ecs_delete"||value.Data.method=="ecs_del"){
      refreshTable(1);
      refreshTable(2);
    }
  }
}

function departmentlist(id,name){
  $("#txtdeparmetId").val(id);
  $("#deparmets").html(name);
  refreshTable(1);
  refreshTable(2);
}

//input 存在一个被选中状态
$("#table01").on('all.bs.table', function(e, row, $element) {

  if ($("#table01 input:checked").length >= 1) {
    $(".btn-default01").attr('disabled', false);
  } else {
    $(".btn-default01").attr('disabled', true);
  }
})
$("#table02").on('all.bs.table', function(e, row, $element) {

  if ($("#table02 input:checked").length >= 1) {
    $(".btn-default02").attr('disabled', false);
  } else {
    $(".btn-default02").attr('disabled', true);
  }
})


function deleteall(data){
  switch (data) {
    case 1:
    var source = '云主机';
    var type = 'hosts';
    break;
    case 2:
    var source = '云桌面';
    var type = 'desktop';
    break;
  }
  showModal('提示', 'icon-question-sign', '是否清空'+source+'?','','deleteAllData(\''+type+'\')');
}

function deleteAllData(type){
  $('#modal').modal("hide");
  $.ajax({
    type: "post",
    url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'recycled','action'=>'deleteAll']);?>/"+type+"/"+$("#txtdeparmetId").val(),
    async: true,
    timeout: 9999999,
  //dataType:'json',
  success: function(e) {
    e= $.parseJSON(e);
    //操作成功
    if(e.code == '0000'){
      // showModal('提示', 'icon-exclamation-sign', '操作成功', '', '', 0);
      // $("#btnEsc").html("关闭");
      // alert('操作成功');
    }else{
      //操作失败
      // showModal('提示', 'icon-exclamation-sign', e.msg, '', '', 0);
      // $("#btnEsc").html("关闭");
      alert(e.msg);
    }
    refreshTable(1);
    refreshTable(2);
  }
});
}


function deleteCheck(type){
  var names = getRowsID('name',type);
  var o = true;
  var rows = $('#table0'+type).bootstrapTable('getSelections');
  rows.forEach(function(e) {
    if(e.state =="销毁中"){
      showModal('提示', 'icon-exclamation-sign', '当前设备状态无法进行操作',e.name, '', 0);
      $("#btnEsc").html("关闭");
      o = false;
      return false;
    }
  });
  if(o==true){
    if (names != "") {
      if(type ==1){
        showModal('提示', 'icon-question-sign', '确认要彻底删除机器,删除后挂载的硬盘数据无法恢复', names, 'ajaxFun(getRowsID(\'code\','+type+'),\'ecs_delete\',getRowsID(\'id\','+type+'))');
      }else if(type ==2){
        showModal('提示', 'icon-question-sign', '确认要彻底删除机器,删除后挂载的硬盘数据无法恢复', names, 'ajaxFun(getRowsID(\'code\','+type+'),\'desktop_del\',getRowsID(\'id\','+type+'))');
      }

    } else {
      showModal('提示', 'icon-exclamation-sign', '请选中一台主机', '', '', 0);
      $("#btnEsc").html("关闭");
    }
  }
}

function recoverCheck(type){
    var names = getRowsID('name',type);
    if (names != "") {
        var code = getRowsID('code',type);
        var id = getRowsID('id',type);
        if(getQuotaByType(type,id,code)==true){
            showModal('提示', 'icon-question-sign', '确认要恢复机器', names, 'ajaxFun(getRowsID(\'code\','+type+'),\'RemoveTrash\',getRowsID(\'id\','+type+'))');
        }

    } else {
    showModal('提示', 'icon-exclamation-sign', '请选中一台主机', '', '', 0);
        $("#btnEsc").html("关闭");
    }
}

function getQuotaByType(type,basicId,code){
    var rel = true;
    if(type=="1"){
        type = "hosts";
    }else if(type=="2"){
        type = "desktop";
    }
    $.ajax({
        type: "post",
        url: "<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'hosts', 'getInstanceQuotaByType']); ?>",
        async: false,
        data: {
            type: type,
            basicId: basicId,
            code: code
        },
        success: function(e) {
        e= $.parseJSON(e);
            if(e.code != '0'){
                alert(e.msg+"无法恢复");
                rel = false;
            }
        }
    });
    return rel;
}

</script>
<?php
$this->end();
?>
