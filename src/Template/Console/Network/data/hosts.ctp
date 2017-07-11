<?= $this -> element('network/lists/left', ['active_action' => 'hosts']); ?>
<div class="wrap-nav-right">
    <div class="clearfix host-static" >
        <div class="pull-left host-staticInfo" >
            <div class="static-detailInfo">
                <h5><a href="/console/network/data/basic_info">基本信息</a>
                <div class="dropdown pull-right">

<!--                 <a href="#" class="dropdown-toggle btn btn-addition text-right" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="true">
                    <span class="pull-left" id="agent" val="">全部</span>
                    <span class="caret"></span>
                </a>
                <ul class="dropdown-menu">
                  <li><a href="#">阿里云</a></li>
                </ul>
                 -->
                </div>
                </h5>
                <ul>
                    <li><span class="deleft">主机Code：</span><?= $_data['0']['H_Code'] ?></li>
                    <li><span class="deleft">部署区位：</span><?= $_data['0']['E_Name'] ?></li>
                    <li><span class="deleft">名称：</span><?= $_data['0']['H_Name'] ?></li>
                    <li><span class="deleft">描述：</span><?= $_data['0']['H_Description'] ?></li>
                    <!-- <li><span class="deleft">ID地域：</span><?= $_data['0']['E_A_Name'] ?></li> -->
                    <li><span class="deleft">操作系统：</span><?= $_data['0']['D_Os_Form'] ?></li>
                    <li><span class="deleft">公网IP：</span><?= empty($_data['0']['E_Ip'])?'-':$_data['0']['E_Ip'] ?></li>
                    <li><span class="deleft">当前使用宽带：</span>带宽 <?= empty($_data['0']['E_BandWidth'])?'0':$_data['0']['E_BandWidth'] ?> Mbps</li>

                </ul>
            </div>
            <div class="static-detailInfo">
                <h5>网络信息 <span class="pull-right more change" id="add-subnet">添加</span></h5>
                <?php foreach($_data as $_i){?>
                    <ul>
                    <li><span class="deleft">内网IP：</span><?= $_i['I_Ip'] ?>
                    <?php if($_i['I_Default'] != 1){?>
                      <span data-id="<?= $_i['I_NetCardId'] ?>" data-code="<?= $_i['I_NetCode'] ?>"  class="pull-right more change" style="color: #44d2e4;cursor: pointer;font-size: 14px;margin-left: 10px;" onclick="delNetCard(this)">删除网卡</span>
                    <?php }?>
                    </li>
                    <li><span class="deleft">子网Code：</span><?= $_i['I_SubnetCode'] ?></li>
                    <li><span class="deleft">子网名称：</span><?= $_i['J_SubnetName'] ?></li>
                    <li><span class="deleft">网卡Code：</span><?= $_i['I_NetCode'] ?></li>
                    <!-- <li><span class="deleft">宽带计费方式：</span>按固定带宽</li> -->
                </ul>
                <?php }?>
            </div>
            <div class="static-detailInfo static-write">
                <h5 class="first">基础配置 <span class="pull-right more change">修改</span><span class="pull-right more use">应用</span></h5>
                <div>
                   <span class="deleft">CPU:</span>
                        <span class="write"><?= $_data['0']['D_Cpu'] ?>核</span>
                    <span class="deleft">内存:</span>
                        <span class="write"><?= $_data['0']['D_Memory'] ?>GB</span>
                    <span class="deleft">GPU:</span>
                        <span class="write"><?= $_data['0']['D_Gpu'] ?>MB</span>
                </div>
                <div class="change-setting">
                    <p><span>请选择配置</span> <i class="icon icon-angle-down"></i></p>
                    <input type="hidden" value="" id="txtTypeCode" />
                    <ul>
                        <?php foreach ($_desc as $key => $value) { ?>
                            <li data-code="<?= $value['set_code'] ?>" >CPU:<?= $value['cpu_number'] ?>核&nbsp;&nbsp;&nbsp;&nbsp;*&nbsp;&nbsp;&nbsp;&nbsp;内存:<?= $value['memory_gb'] ?>GB&nbsp;&nbsp;&nbsp;&nbsp;*&nbsp;&nbsp;&nbsp;&nbsp;显存:<?= $value['gpu_gb'] ?>MB</li>
                       <?php } ?>
                    </ul>
                </div>
            </div>
            <div class="static-detailInfo">
                <h5>操作记录</h5>
                <ul>
                    <?php foreach ($_log as $key => $value) { ?>
                        <li><span class="deleft"> <?= date("Y-m-d H:i:s",$value["create_time"]) ?></span>&nbsp;&nbsp;<?= $value["user_name"] ?>&nbsp;&nbsp; 对设备:&nbsp;&nbsp;<?= $value["device_name"] ?>&nbsp;&nbsp;进行[<?= $value["device_event"] ?>]</li>
                    <?php } ?>

                </ul>
            </div>
        </div>

        <div class="static-imageDetail">

            <div class="static-detailInfo">
                <h5>图像化</h5>
                <div class="static-limit">
                <div class="detail-drawing">
                    <div class="static-drawing">
                        <span class="static-stay">所在子网：</span>
                        <div class="static-drawing-model static-unit ">
                            <p><?= $_data['0']['G_Name'] ?></p>
                        </div>
                        <?php
                            if($_data['0']['H_Status']=="运行中"){ ?>
                                <div class="static-drawing-model static-run ">
                                    <p>状态:<span class="text-primary">运行中</span></p>
                                </div>
                            <?php }else{ ?>
                                <div class="static-drawing-model static-stop " style="display:none">
                                    <p>状态:<span class="text-danger">已停止</span></p>
                                </div>
                           <?php }
                        ?>
                    </div>
                    <div class="static-branch">
                        <div class="branch-first branch-model">
                            <div class="image" onclick="open_disks();" style="cursor: pointer;">

                            </div>
                            <p>磁盘：<?= empty($_disks)==true?0:count($_disks); ?> 个 </p>
                        </div>
                        <div class="branch-second branch-model">
                            <div class="image">

                            </div>
                            <p>快照：</p>
                        </div>
                        <div class="branch-three branch-model">
                            <div class="image">

                            </div>
                            <p>镜像：<?= $_data['0']['D_Image_code'] ?></p>
                        </div>
                    </div>
                </div>
                </div>
                </div>
                <div class="static-detailInfo">
                    <h5>监控信息</h5>
                    <div  class="chart-box">
                        <p>CPU</p>
                        <div>
                            <canvas id="canvas1"></canvas>
                        </div>
                        <p class="chart-title"><span></span> CPU使用率(%)</p>
                    </div>
                    <div  class="chart-box">
                       <p>网络</p>
                       <div>
                         <canvas id="canvas4"></canvas>
                       </div>
                       <p class="chart-title"><span ></span>出网&nbsp;KBps &nbsp;<span class="line"></span>入网&nbsp;KBps</p>
                    </div>
                </div>

        </div>
    </div>
</div>

<!-- 子网 -->
<div class="modal fade" tabindex="-1" role="dialog" id="subnet-manage">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">添加网卡</h4>
      </div>
      <form action=""  method="POST">
      <div class="modal-body">
        <div class="ng-binding">
          <div class="clearfix">
              <label style="width:20%" for="" class="pull-left ">选择VPC:</label>
              <div style="width:80%" class="bk-form-row-cell">
                  <select id="vpc2" onchange="loadSubnetPublic($(this).val())"></select>
              </div>
          </div>
          <div class="clearfix">
              <label style="width:20%" for="" class="pull-left ">选择网络:</label>
              <div style="width:80%" class="bk-form-row-cell">
                  <select id="net2"></select>
              </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" id ="addSubnet" class="btn btn-primary" >确 认</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">取 消</button>
      </div>
      </form>
    </div>
  </div>
</div>


<!-- 硬盘 -->
<div class="modal fade" id="disk-manage" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"
        aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
      <h5 class="modal-title">主机硬盘管理</h5>
    </div>
    <input type="hidden" id="hostsCode"/>
    <input type="hidden" id="hostsId"/>
    <input type="hidden" id="txtvpcCode"/>
    <input type="hidden" id="txtclass_code"/>
    <input type="hidden" id="txtisFusion"/>
    <div class="modal-body">
      <div class="modal-title-list">
        <ul class="clearfix">
          <li class="active" no="1">添加硬盘</li>
          <li no="2">已用硬盘</li>
        </ul>
      </div>
      <div class="modal-disk-content" style="display:block;">
                      <div class="modal-form-group">
                       <label>硬盘名称:</label>
                       <div>
                        <input id="txtdisks_name" type="text" />
                      </div>
                    </div>
                    <div class="modal-form-group">
                      <label>容量大小:</label>
                      <div class="slider-area">
                        <div id="slider"></div>
                      </div>
                      <div class="amount pull-left">
                        <input type="text" id="amount" placeholder="10"> GB
                      </div>
                    </div>
                    <div class="modal-form-group">
                      <label></label>
                      <div>
                        <h6 class="warm">请输入范围10GB-1000GB</h6>
                      </div>
                    </div>

                    <div class="modal-form-point">
                    </div>
                    <div class="modal-footer">
                      <button onclick="btnaddDisks()" type="button" class="btn btn-primary">添加</button>
                      <button type="button" class="btn btn-danger" data-dismiss="modal">取消</button>
                    </div>
                  </div>
                  <div class="modal-disk-content" style="display:none">
                    <table id="use_table">
                      <thead>
                        <tr>
                          <th data-field="code" >硬盘ID</th>
                          <th data-field="name">名称</th>
                          <th data-field="capacity">容量(GB)</th>
                          <th data-formatter="operateFormatter">操作</th>
                        </tr>
                      </thead>
                    </table>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-danger" data-dismiss="modal">关闭</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

<div id="maindiv"></div>
<script>
//弹出框里面的界面切换
$(".modal-title-list").on("click", "li",
function() {
    $(".modal-title-list li").removeClass("active");
    $(this).addClass("active");
    $(".modal-disk-content").css("display", "none");
    $(".modal-disk-content").eq($(this).index()).css("display", "block");
    if ($(this).attr("no") == "2") {
        var code = '<?= $_data[0]['H_Code'] ?>';
        $('#use_table').bootstrapTable('refresh', {
            url: "/console/ajax/network/disks/uselist?id=" + code
        });
    }
})
$(function(){
    chart('<?= $_data[0]['H_Code'] ?>');
      //基础配置js
    $(".static-write .change").on("click",function(){
        $(".host-staticInfo h5 .use").show();
         $(".change-setting").show();
    })
    $(".change-setting p").on("click",function(){

        $(".change-setting ul").slideToggle();
    })
    $(".change-setting ul li").on("click",function(){
        var context = $(this).html();
        $("#txtTypeCode").val($(this).data("code"));
        $(".change-setting p span").html(context);
        $(".change-setting ul").hide();
    });
    //应用
    $(".static-write .use").on("click",function(){
        // console.log($("#txtTypeCode").val());
        update();
    })
});

$(document).on("click",".del-disk",function(){
  deldisks($(this).data("id"),$(this).data("code"));
});

/* 渲染页面 */
function operateFormatter(value, row, index) {
    return '<a href="javascript:;" data-id="' + row.id + '" data-code="'+row.code+'" data class="del-disk"><i class="icon-remove"></i></a>';
}

// function fromatter_Capacity(value,row,index){
//   return value+"GB";
// }

function open_disks(){
    $('#disk-manage').modal("show");
    $("#txtdisks_name").val('');
    $("#amount").val(10);
    $('#use_table').bootstrapTable({
        url: "/console/ajax/network/disks/uselist?id=" + '<?= $_data[0]['H_Code'] ?>',
    });
}

function deldisks(basicId,volumeCode){
  $.ajax({
    type: "post",
    url: "<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'disks', 'ajaxDisks']); ?>",
    async: true,
    timeout: 9999,
    data: {
        volumeCode: volumeCode,
        method: 'volume_detach',
        basicId:basicId
    },
    beforeSend:function(){
      $(document).off("click",".del-disk");
    },
    //dataType:'json',
    success: function(data) {
        data = $.parseJSON(data);
        if (data.Code != 0){
          alert(data.Message);
        }else{
          // showModal('提示', '解绑硬盘成功','', 'hidenModal()');
        }
        $('#disk-manage').modal("hide");
      }
  });
}

//挂载硬盘
function btnaddDisks() {
    var id = '<?= $_data[0]['H_ID'] ?>';
    var instanceCode = '<?= $_data[0]['H_Code'] ?>';
    var name = $("#txtdisks_name").val();
    var size = $("#amount").val();
    var vpcCode = '<?= $_data[0]['F_Code'] ?>';
    var class_code='<?= empty($_data[0]['H_L_Code'])==true?"":$_data[0]['H_L_Code'] ?>';
    isFusion = '<?= empty($_data[0]['D_isFusion'])==true?"":$_data[0]['D_isFusion'] ?>';
    var volumeCode, method;
    var number = 1;
    $.ajaxSettings.async = false;
    istrue = true

    $.get("/console/ajax/network/hosts/getDisksCount/?code=" + instanceCode, function(data) {
            $.getJSON("/console/ajax/network/hosts/getDisksLimit", function(data2) {
                if (Number(data) >= data2) {
                    alert('主机最多挂载' + data2 + '个硬盘');
                    istrue = false;
                }
            })
        })

        $.getJSON("/console/home/getUserLimit", function(data) {
            if (Number(size) + data.disks_used > data.disks_bugedt) {
                alert("配额不足 \r\n 磁盘 配额：" + data.disks_bugedt + " 已使用：" + data.disks_used);
                istrue = false;
            }
        });
    if(!istrue)
    {
      return false
    }
    $.ajaxSettings.async = true;
    method = "volume_add";
    $.ajax({
        type: "post",
        url: "<?= $this ->Url ->build(['prefix' =>'console', 'controller' =>'ajax', 'action' =>'network', 'disks', 'ajaxDisks']); ?>",
        async: true,
        data: {
            method: method,
            id: id,
            name: name,
            size: size,
            instanceCode: instanceCode,
            volumeCode: volumeCode,
            vpcCode: vpcCode,
            class_code:class_code,
            isFusion:isFusion
        },
        success: function(data) {
            data = $.parseJSON(data);
            if (data.Code != 0) {
                alert(data.Message);
            }else{
            }
            $('#disk-manage').modal("hide");
        }
    });
}

//动态创建modal
function showModal(title, content, content1) {
    $("#maindiv").empty();
    html = "";
    html += '<div class="modal fade" id="modal" tabindex="-1" role="dialog">';
    html += '<div class="modal-dialog" role="document">';
    html += '<div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
    html += '<h5 class="modal-title">' + title + '</h5>';
    html += '</div><div class="modal-body"><i class="icon-question-sign text-primary"></i>&nbsp;&nbsp;' + content + '<span class="text-primary">' + content1 + '</span></div>';
    html += '<div class="modal-footer"><button id="btnMk" type="button" class="btn btn-primary">确认</button><button type="button" class="btn btn-danger" id="btnEsc" data-dismiss="modal">关闭</button></div></div></div></div>';
    $("#maindiv").append(html);
    $('#modal').modal("show");
}
function showModal2(title, content, content1) {
    $("#maindiv").empty();
    html = "";
    html += '<div class="modal fade" id="modal" tabindex="-1" role="dialog">';
    html += '<div class="modal-dialog" role="document">';
    html += '<div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
    html += '<h5 class="modal-title">' + title + '</h5>';
    html += '</div><div class="modal-body"><i class="icon-question-sign text-primary"></i>&nbsp;&nbsp;' + content + '<span class="text-primary">' + content1 + '</span></div>';
    html += '<div class="modal-footer"><button type="button" class="btn btn-danger" id="btnEsc" data-dismiss="modal">关闭</button></div></div></div></div>';
    $("#maindiv").append(html);
    $('#modal').modal("show");
}
function update() {
    if ('<?= $_data[0]['H_Code'] ?>' != '' && '<?= $_data[0]['H_Code'] ?>' != null) {
        if ('<?= $_data[0]['H_Status'] ?>' != '已停止') {
            showModal2('提示', '修改主机配置需要关闭主机.请先关闭主机', '');
        } else {
            showModal('提示', '是否确认修改机器配置', '');
            $('#btnMk').bind('click',
                function() {
                    $("#modal").modal("hide");
                    $.ajax({
                        type: "post",
                        url: "<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'hosts', 'ajaxHosts']); ?>",
                        async: true,
                        timeout: 9999,
                        data: {
                            method: 'ecs_reconfig',
                            basicId: '<?= $_data[0]['H_ID'] ?>',
                            instanceCode: '<?= $_data[0]['H_Code'] ?>',
                            instanceTypeCode: $("#txtTypeCode").val(),
                            isEach: false
                        },
                        success: function(data) {
                            data = $.parseJSON(data);
                            if (data.Code != "0") {
                                alert(data.Message);
                            }
                            // window.location.reload();
                        }
                    });
                });
        }
    } else {
        showModal('提示', '该主机没有Code', '无法修改');
        $("#btnEsc").remove();
    }
}

function chart(code) {
    $.ajax({
        type: "POST",
        url: "/console/ajax/network/hosts/getmonitor",
        data: {
            code: code
        },
        beforeSend: function() {
            $(".chart-mask").show()
        },
        success: function(result) {
            $(".chart-mask").hide(),
            json = eval('(' + result + ')');
            var data = { //CPU
                labels: json.chart.cpu.time,
                datasets: [{
                    fillColor: "rgba(255,255,255,0.5)",
                    strokeColor: "rgba(68,210,228,1)",
                    pointColor: "rgba(220,220,220,1)",
                    pointStrokeColor: "#fff",
                    data: json.chart.cpu.data
                }]
            }
            var data3 = { //DISK
                labels: json.chart.disk.time,
                datasets: [{
                    fillColor: "rgba(255,255,255,0.5)",
                    strokeColor: "rgba(68,210,228,1)",
                    pointColor: "rgba(220,220,220,1)",
                    pointStrokeColor: "#fff",
                    data: json.chart.disk.data1
                },
                {
                    fillColor: "rgba(255,255,255,0.5)",
                    strokeColor: "rgba(252,218,150,1)",
                    pointColor: "rgba(220,220,220,1)",
                    pointStrokeColor: "#fff",
                    data: json.chart.disk.data2
                }]
            }
            var data4 = { //network
                labels: json.chart.network.time,
                datasets: [{
                    fillColor: "rgba(255,255,255,0.5)",
                    strokeColor: "rgba(68,210,228,1)",
                    pointColor: "rgba(220,220,220,1)",
                    pointStrokeColor: "#fff",
                    data: json.chart.network.data1
                },
                {
                    fillColor: "rgba(255,255,255,0.5)",
                    strokeColor: "rgba(252,218,150,1)",
                    pointColor: "rgba(220,220,220,1)",
                    pointStrokeColor: "#fff",
                    data: json.chart.network.data2
                }]
            }
            var ctx = document.getElementById("canvas1").getContext("2d");
            window.myLine = new Chart(ctx).Line(data, {
                responsive: true
            });
            // var ctx3 = document.getElementById("canvas3").getContext("2d");
            // window.myLine = new Chart(ctx3).Line(data3, {
            //     responsive: true
            // });
            var ctx4 = document.getElementById("canvas4").getContext("2d");
            window.myLine = new Chart(ctx4).Line(data4, {
                responsive: true
            });

        }
    });

}

function notifyCallBack(value){
  //console.log(value);
  if(value.MsgType=="success"){
    if(value.Data.method=="volume_attach"||value.Data.method=="volume_detach"||value.Data.method=="ecs_reconfig"||value.Data.method=="ecs_stop"||value.Data.method=="network_card_del"||value.Data.method=="network_card_add"){
        setTimeout(function() {
        	window.location.reload();
        	}, 3000);
    }
  }
}
$('#add-subnet').on('click',function(){
    loadSubnetPublic();
    $("#subnet-manage").modal('show');
});

$("#addSubnet").on('click',function(){
    // var vpc = $('#vpc2').val();
    var basicId = "<?= $_data[0]['H_ID'] ?>";
    var subnet = $('#net2').val();
    var ecsCode = "<?= $_data[0]['H_Code'] ?>";
    var codeArr=[];
        $(".network-code").each(function(index,obj){
            var nteCode=$(obj).text();
            codeArr.push(nteCode)
        });
    if( codeArr.indexOf(subnet)==-1) {
        $.ajax({
            url: "<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'hosts', 'ajaxNetCard']); ?>",
            data: {subnetCode: subnet, method: 'network_card_add', basic_id: basicId, ecsCode: ecsCode},
            async: false,
            success: function (data) {
            }
        })
    }else{
        showModal2('提示','同一子网不能同时绑定一台虚拟机的多张网卡','')
    }
    $("#subnet-manage").modal('hide');
})

function loadSubnetPublic(vpc){
    var h="";
    var h2="";
    if( vpc == null){
        vpc = "<?= $_data['0']['F_Code'] ?>";
    }

    $.ajax({
        type:"post",
        url:"<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'hosts', 'loadSubnetPublic']); ?>",
        data:{vpc:vpc},
        async:false,
        success:function(data){
          data= $.parseJSON(data);
          if (data.vpc == null) {
            showModal2('提示','只有一个vpc，不能添加网卡','');
          } else {
            $.each(data.vpc,function(i,n){
              if(i==0){
                  //<option value="4">$vpc$_822</option><option value="5">vpc[822]_fox</option><
                  h += "<option value='"+n.code+"' selected=\"selected\">"+n.name+"</option>";
              }else{
                  h += "<option value='"+n.code+"'>"+n.name+"</option>";
              }
            });
            $("#vpc2").html(h);
            vpc = $("#vpc2").val();
            $.each(data.vpc,function(i,n){
              if(n.code==vpc){
                $.each(n.subnet,function(x,y){
                  if(i==x){
                    h2 += "<option value='"+y.code+"' selected=\"selected\">"+y.name+"</option>";
                  }else{
                    h2 += "<option value='"+y.code+"'>"+y.name+"</option>";
                  }
                })
              }
            });
            $("#net2").html(h2);
          }
            // alert(data)
        }
    });
    $("#txtnet2").html($("#net2").find("option:selected").text());
    $("#txtnet22").val($("#net2").val());
}

function delNetCard(event){
    var networkCardCode = $(event).attr('data-code');
    var basicId = $(event).attr('data-id');

    var id = <?php if(!empty($_data[1])){ echo 1;}else{echo 0;} ?>;
    if(id == 0) {
        showModal2('提示','不能删除最后一个网卡','');
    } else {
        showModal('提示','是否删除选中的网卡','');
        $('#btnMk').on('click',function(){
            $("#modal").modal("hide");
            $.ajax({
                url:"<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'hosts', 'ajaxNetCard']); ?>",
                data:{networkCardCode:networkCardCode,method:'network_card_del',basic_id:basicId},
                async:false,
                success:function(data){

                }
            });
        });
    }
}


</script>
</body>
</html>