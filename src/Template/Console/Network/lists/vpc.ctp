<!-- VPC -->
<?= $this->element('network/lists/left',['active_action'=>'vpc']); ?>
<div class="wrap-nav-right">
  <div class="wrap-manage">
    <div class="top">
      <span class="title">VPC列表</span>

      <div class="callback-info pull-right text-success"><i class="icon-ok"></i>&nbsp;操作成功</div>
    </div>
    <div class="center clearfix">

      <button class="btn btn-addition" id="btn-refresh"><i class="icon-refresh"></i>&nbsp;&nbsp;刷新</button>
        <?php if (in_array('ccf_Vpc_delete', $this->Session->read('Auth.User.popedomname'))) { ?>
        <button class="btn btn-default" id="btnDel" disabled="disabled">
            <i class="icon-remove "></i>&nbsp;&nbsp;删除
        </button>
        <?php } ?>
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
            <?php if(isset($agent)){
              foreach($agent as $value) {
                if ($value['parentid'] == 0) {
                  ?>
                  <li><a href="#" onclick="local(<?php echo $value['id'] ?>,'<?php echo $value['class_code'] ?>','<?php echo $value['agent_name'] ?>')"><?php echo $value['agent_name'] ?></a></li>
                  <?php }}} ?>
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
       <!--  <button class="btn btn-addition dropdown" role="presentation">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-tag"></i>&nbsp;&nbsp;标签</a>
          <ul class="dropdown-menu">
            <li><a href="#">测试环境</a></li>
            <li><a href="#">只是环境</a></li>
          </ul>
        </button> -->
      </div>
    </div>
    <div class="bot ">
      <table id="table" data-toggle="table"
      data-pagination="true" 
      data-side-pagination="server"
      data-locale="zh-CN"
      data-click-to-select="true"
      data-url="<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network','Vpc','lists','?'=>['department_id'=>$_default['id']]]); ?>"
      data-unique-id="id">
      <thead>
        <tr>
          <th data-checkbox="true"></th>
          <th data-field="id">Id</th>
          <th data-field="code">VPCCode</th>
          <th data-field="name">VPC名称</th>
          <th data-field="status" data-formatter=formatter_state>状态</th>
          <th data-field="location_name">部署区位</th>
          <!-- <th data-field="vpcip">VPC网络地址</th> -->
          <th data-field="create_time" data-formatter=timestrap2date>创建时间</th>
        </tr>
      </thead>
    </table>
  </div>
</div>
</div>


<!-- 右键弹框 -->
<div class="context-menu" id="context-menu">
  <ul>
    <?php if (in_array('ccf_Vpc_change', $this->Session->read('Auth.User.popedomname'))) { ?>
    <li id="modify"><a href="#"><i class="icon-pencil"></i> 修改</a></li>
    <?php } ?>
    <?php if (in_array('ccf_Vpc_delete', $this->Session->read('Auth.User.popedomname'))) { ?>
    <li id="dele"><a href="#"><i class="icon-trash"></i> 删除</a></li>
    <?php } ?>
    <?php if (in_array('ccm_ps_interface_logs', $this->Session->read('Auth.User.popedomname'))) { ?>
    <li id="excp"><a  href="javascript:;"><i class="icon-book"></i> 异常日志</a></li>
    <?php } ?>
     <?php if (in_array('ccm_ps_interface_logs', $this->Session->read('Auth.User.popedomname'))) { ?>
        <li id="normal"><a  href="javascript:;"><i class="icon-book"></i> 正常日志</a></li>
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
      <label>名称:</label>
      <div>
        <input id="modal-modify-name" name="name" type="text" />
      </div>
    </div>
    <div class="modal-form-group">
      <label>描述:</label>
      <div>
        <textarea id="modal-modify-description" name="description" rows="5"></textarea>
      </div>
    </div>
    <input id="modal-modify-id" name="id" type="hidden" />
  </div>
  <div class="modal-footer">
   <button id="sumbiter" type="button" class="btn btn-primary">确认</button>
   <button id="reseter" type="button" class="btn btn-danger"
   data-dismiss="modal">取消</button>
 </div>
</form>
</div>
</div>
</div>
<!-- 删除 -->
<div class="modal fade" id="modal-dele" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h5 class="modal-title">提示</h5>
      </div>
      <form id="modal-dele-form" action="" method="post">
        <div class="modal-body">
          <i class="icon-question-sign icon-color-gray text-primary"></i>&nbsp;&nbsp;确认要删除VPC<span class=" text-primary" id="modal-dele-name"></span>？</br>
          <i class="icon-exclamation-sign icon-color-gray text-primary"></i>&nbsp;&nbsp;删除VPC,会同时删除VPC绑定的路由器！
          <input type="hidden" value="" id="modal-dele-id" name="ids">
          <input type="hidden" value="" id="modal-dele-code" name="codes">
        </div>
        <div class="modal-footer">
          <button type="button" id="sumbiter-dele" class="btn btn-primary">确认</button>
          <button type="button" class="btn btn-danger" data-dismiss="modal">取消</button>
        </div>
      </form>
    </div>
  </div>
</div>



<div id="maindiv"></div>
<?php
$this->start('script_last');
?>
<script type="text/javascript">
    //input 存在一个被选中状态
    $("#table").on('all.bs.table.table', function(e, row, $element) {
        if ($("tbody input:checked").length >= 1) {
            $(".center .btn-default").attr('disabled', false);
        } else {
            $(".center .btn-default").attr('disabled', true);
        }
    });


    //搜索绑定
    $("#txtsearch").on('keyup',
      function() {
        if(timer!=null){
          clearTimeout(timer);
        }
        var class_code = $("#agent").attr('val');

        var class_code2 = $("#agent_t").attr('val');
        var search= $("#txtsearch").val();
        var timer = setTimeout(function(){
          $('#table').bootstrapTable('refresh', {
            url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network','Vpc','lists']); ?>?search=" + search+'&class_code='+class_code+'&class_code2='+class_code2+'&department_id='+$("#txtdeparmetId").val()
          });
        },1000);
      });

    //input 存在一个被选中状态
    $("table").on('click',' input', function(){
      if($("tbody input:checked").length>=1){
        $(".center .btn-shutdown").prop('disabled',false);
      }else{
        $(".center .btn-shutdown").prop('disabled',true);
      }
    })

    $('#table').contextMenu('context-menu', {

      bindings: {
        'modify': function(event) {
            //获取数据
            index=$(event).attr('data-index');
            var uniqueId = $(event).attr('data-uniqueid');
            var row = $('#table').bootstrapTable('getRowByUniqueId', uniqueId);
            //填充数据
            //TODO 根据bootstrap方法
            $('#modal-modify-name').val(row.name);
            $('#modal-modify-description').val(row.description);
            $('#modal-modify-id').val(row.id);

            $('#modal-modify').one('show.bs.modal',
              function() {
                $('#sumbiter').one('click',
                  function() {
                    //ajax提交页面
                    $.ajax({
                      url: "<?= $this -> Url -> build(['controller' => 'ajax', 'action' => 'network']); ?>/<?php echo "Vpc" ?>/<?php echo "editNetworkVpc" ?>",
                      async: false,
                      data: $('#modal-modify-form').serialize(),
                      method: 'post',
                      dataType: 'json',
                      success: function(e) {
                            //操作成功
                            if (e.code == '0000') {
                              $('#modal-modify').modal("hide");
                              // tentionHide('修改成功',0);
                              $('#table').bootstrapTable('updateRow', {index: index, row: e.data});

                            } else {
                                //操作失败
                                // tentionHide('修改失败',1);
                              }

                            }
                          });
                    return false;
                  });
});

$('#modal-modify').modal("show");

},
'dele':function(event){
          //获取数据
          var uniqueId = $(event).attr('data-uniqueid');
          var row = $('#table').bootstrapTable('getRowByUniqueId', uniqueId);
          if(row.status!="创建中"){
            $('#modal-dele-name').html(row.name);
            $('#modal-dele-id').val(row.id);
            $('#modal-dele-code').val(row.code);
            $('#modal-dele').modal("show");
            $('#sumbiter-dele').one('click',function(){
              $('#modal-dele').modal("hide");
              //ajax提交页面
              $.ajax({
                url:'<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network']); ?>/<?php echo "vpc"; ?>/<?php echo "deleteNetworkVpc"; ?>',
                data:$('#modal-dele-form').serialize(),
                method:'post',
                dataType:'json',
                success:function(e){
                  //操作成功
                  if(e.code == '0000'){
                    heartbeat(3, row.id);
                    //$('#table').bootstrapTable('removeByUniqueId', $(event).data("uniqueid"));
                  }else{
                    //操作失败
                    alert(e.msg);
                  }
                }
              });
            });
          }else{
            showModal('提示','icon-exclamation-sign', '当前设备状态无法进行操作','', '',0,'关闭');
            return false;
          }
        },
        'excp':function(event){
        	var uniqueId = $(event).attr('data-uniqueid');
        	var row = $('#table').bootstrapTable('getRowByUniqueId', uniqueId);

        	var department_id = row.department_id;
        	window.location.href = "/console/excp/lists/excp/vpc/"+department_id+'/all/0/0/'+row.id;
        },
        //正常
        'normal':function(event){
        	var uniqueId = $(event).attr('data-uniqueid');
        	var row = $('#table').bootstrapTable('getRowByUniqueId', uniqueId);
        	var department_id = row.department_id;
        	window.location.href = "/console/excp/lists/normal/vpc/"+department_id+'/all/0/0/'+row.id;
        }

      }

    });

//动态创建modal
function showModal(title, icon, content, content1, method, type ,info) {
  $("#maindiv").empty();
  html = "";
  html += '<div class="modal fade" id="modal-confirm" tabindex="-1" role="dialog">';
  html += '<div class="modal-dialog" role="document">';
  html += '<div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
  html += '<h5 class="modal-title">' + title + '</h5>';
  html += '</div><div class="modal-body"><i class="'+icon+' icon-color-gray text-primary"></i>&nbsp;&nbsp;' + content + '<span class="text-primary">' + content1 + '</span></div>';
  html += '<div class="modal-footer"><button id="btnMk" onclick="' + method + '" type="button" class="btn btn-primary">确认</button><button type="button" class="btn btn-danger" data-dismiss="modal">'+ info +'</button></div></div></div></div>';
  $("#maindiv").append(html);
  if (type == 0) {
    $("#btnMk").remove();
  }
  $('#modal-confirm').modal("show");
}
//    删除按钮
    $("#btnDel").on('click', function() {
                //获取数据
        var name='';
        var id='';
        var code='';
        var not=0;
        var notname='';
        var rows = $('#table').bootstrapTable('getSelections');
        $.each(rows,function(i,n){
            id += n.id+',';
            name += n.name+',';
            code += n.code+',';
            if(n.status=='创建中'){
                not +=1;
                notname+= n.name+',';
            }
        });
        if(not>0){
            notname=notname.substring(0,notname.length-1);
            showModal('提示','icon-exclamation-sign', notname+'设备状态无法进行操作','', '',0,'关闭');
            return false;
        }else{
            id=id.substring(0,id.length-1);
            name=name.substring(0,name.length-1);
            code=code.substring(0,code.length-1);
            $('#modal-dele-name').html(name);
            $('#modal-dele-id').val(id);
            $('#modal-dele-code').val(code);
            $('#modal-dele').modal("show");
            $('#sumbiter-dele').one('click',function(){
                $('#modal-dele').modal("hide");
                //ajax提交页面
                $.ajax({
                    url:'<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network']); ?>/<?php echo "vpc"; ?>/<?php echo "deleteNetworkVpc"; ?>',
                    data:$('#modal-dele-form').serialize(),
                    method:'post',
                    dataType:'json',
                    success:function(e){
                        //操作成功
                        if(e.code == '0000'){
                            heartbeat(3, id);
                        }else{
                            //操作失败
                            alert(e.msg);
                        }
                    }
                });
            });
        }

            });

$("#btn-refresh").on(
  "click",function(){
   var search= $("#txtsearch").val();
            //$('#table').bootstrapTable('showLoading');
            console.log($("#agent").attr('val'))
            var class_code = $("#agent").attr('val');
            var class_code2 =$("#agent_t").attr('val');
            $('#table').bootstrapTable('refresh', {
              url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network','Vpc','lists']); ?>",
              query: {class_code2:class_code2,class_code: class_code,search: search,department_id:$("#txtdeparmetId").val()}
            });
          });

$("#table").on('check.bs.table uncheck.bs.table check-all.bs.table uncheck-all.bs.table',function(){
  if($("tbody input:checked").length>=1){
    $("#stop, #start").removeClass('disabled');
    $("#menu1 li").removeClass('disabled');
  }else{
    $("#stop, #start").addClass('disabled');
    $("#menu1 li").addClass('disabled');
  }
})
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


  //地域查询
  function local(id,class_code,agent_name) {
    if (agent_name) {
      $('#agent_t').html('全部');
      $('#agent').html(agent_name);
      $('#agent').attr('val', class_code);
      var search= $("#txtsearch").val();
      $('#table').bootstrapTable('refresh', {
        url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network','Vpc','lists']); ?>",
        query: {class_code: class_code,search: search,department_id:$("#txtdeparmetId").val()}
      });
      var url = "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network','Vpc','lists']); ?>?search=" + search+'&class_code='+class_code+'&department_id='+$("#txtdeparmetId").val();
      $('#table').attr('data-url',url);
      var jsondata = <?php echo json_encode($agent); ?>;
      if(id!=0){
        var data='<li><a href="javascript:;" onclick="local_two(\'\',\'全部\',\'' + class_code + '\')">全部</a></li>';
        $.each(jsondata, function (i, n) {
          if(n.parentid == id){
            data += '<li><a href="#" onclick="local_two(\'' + n.class_code + '\',\'' + n.agent_name + '\',\'' + class_code + '\')">' + n.agent_name + '</a></li>';
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
    $('#table').bootstrapTable('refresh', {
      url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network','Vpc','lists']); ?>",
      query: {class_code2: class_code2,class_code:class_code,search: search,department_id:$("#txtdeparmetId").val()}
    });
    var url = "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network','Vpc','lists']); ?>?search=" + search+'&class_code='+class_code+'&class_code2='+class_code2+'&department_id='+$("#txtdeparmetId").val();
    $('#table').attr('data-url',url);
  }

  //心跳
  function heartbeat(type, id) {
    if (id != undefined && id != "") {
      $("#imgState" + id).removeClass('circle-stopped');
      $("#imgState" + id).removeClass('circle-run');
      $("#imgState" + id).addClass('circle-create'); //添加样式，样式名为className
      if (type == 0) {
        $("#txtState" + id).html('正在启动...');
      } else if (type == 1) {
        $("#txtState" + id).html('正在停止...');
      } else if(type==2){
        $("#txtState" + id).html('正在重启...');
      }else{
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
          } else if(type==2){
            $("#txtState" + e).html('正在重启...');
          }else{
            $("#txtState" + e).html('正在销毁...');
          }
        });
    }
  }

function notifyCallBack(value) {
    //console.log(value);

    var search = $("#txtsearch").val();
    var department_id = $("#txtdeparmetId").val();
    var class_code = $("#agent").attr('val');
    var class_code2 = $("#agent_t").attr('val');
    var url = "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network','Vpc','lists']); ?>?search=" + search + '&class_code=' + class_code + '&class_code2=' + class_code2+'&department_id='+department_id;

    if (value.MsgType == "success" || value.MsgType == "error") {
        if (value.Data.method == "router_del"||value.Data.method=="vpc"||value.Data.method=="vpc_del") {
        setTimeout(function() {
          $('#table').bootstrapTable('refresh', {
            url: url,
            silent: true
          });
        }, 3000);
      }
    }
}
function departmentlist(id,name){
    $("#txtdeparmetId").val(id);
  $("#deparmets").html(name);
  var search = $("#txtsearch").val();
  var department_id = id;
  var url;
  var class_code = $("#agent").attr('val');
  var class_code2 = $("#agent_t").attr('val');
  $("#table").bootstrapTable('refresh', {
    url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network','vpc','lists']); ?>?search=" + search + '&class_code=' + class_code + '&class_code2=' + class_code2+'&department_id='+$("#txtdeparmetId").val()
  });
}
</script>
<?php
$this->end();
?>