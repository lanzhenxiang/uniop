function showModal(title, icon, content, content1, method, type, delete_info) {
        $("#maindiv").empty();

        var html = "";
        html += '<div class="modal fade" id="modal" tabindex="-1" role="dialog">';
        html += '<div class="modal-dialog" role="document">';
        html += '<div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
        html += '<h5 class="modal-title">' + title + '</h5>';
        html += '</div><div class="modal-body"><i class="'+icon+' icon-color-gray text-primary"></i>&nbsp;&nbsp;' + content + '<span class="text-primary batch-warning">' + content1 + '</span>';
        if(delete_info == 1){
            html +='<br /><i class="icon-exclamation-sign icon-color-gray text-primary"></i>&nbsp;&nbsp;<span > 可在回收站找回已删除的主机</span><span class=" text-primary batch-warning" id="modal-dele-name"></span>';
        }
        var cancelLabel = '取消';
        if(type == 0){
            cancelLabel = '关闭';
        }
        html +='</div>';
        html += '<div class="modal-footer"><button id="btnModel_ok" onclick="' + method + '" type="button" class="btn btn-primary">确认</button><button type="button" class="btn btn-danger" id="btnEsc" data-dismiss="modal">'+ cancelLabel +'</button></div></div></div></div>';
        $("#maindiv").append(html);
        if (type == 0) {
            $("#btnModel_ok").remove();
        }
        $('#modal').modal("show");
}
$(".right-revamp").each(function(index,obj){
    $(obj).find("span").eq(0).hover(function(){
        $(this).siblings("i").css("color","#44d2e4");
    },function(){
        $(this).siblings("i").css("color","#f5f5f5");
    });
});
//    下拉操作
$(".basic-down").prev().on('click',function(e){
    var $menu= $(this).next();
    if($menu.css("display")=="none"){
        $menu.slideDown(100);
    }else{
        $menu.hide();
    }
    var ev = e || window.event;
    if(ev.stopPropagation){
        ev.stopPropagation();
    }
    else if(window.event){
        window.event.cancelBubble = true;//兼容IE
    }
});
document.onclick=function(){
    $(".basic-down").hide();
};
//    基本信息修改
$(".basic-revamp").on("click",function(){
    $("#modal-modify").modal("show")
});
//    系统配置修改modal   
$(".sys-revamp").on('click',function(){
    var H_Status  = $(this).parent().attr('data-status');
    if(H_Status == "运行中"){
        showModal('提示', 'icon-exclamation-sign', '当前设备正在运行中，请关机后再进行操作', '', '', 0);
    }else if(H_Status == '已停止'){
        $("#modal-sys-revamp").modal("show");
    }else{
        showModal('提示', 'icon-exclamation-sign', '当前设备状态无法进行操作', '', '', 0);
    }   
});

//    添加网卡
$(".add-card").on('click',function(){
    loadSubnetPublic();
    var status = $(this).attr('data-status');
    if(status == '运行中'){
        $("#subnet-manage").modal("show");
    }else{
        showModal('提示', 'icon-exclamation-sign', '无法在当前状况（已关闭电源）下操作', '', '', 0);
    }  
});


//
// 块存储
function operateFormatter(value, row, index) {
    return '<a href="javascript:;" data-id="' + row.id + '" data-code="' + row.code + '" data class="del-disk"><i class="icon-remove"></i></a>';
}
$("#slider").slider({
//    	value: $("#amount").val(),
    min: 10,
    max: 1000,
    step: 1,
    orientation: "horizontal",
    range: "min",
    animate: true,
    slide: function(event, ui) {
        var val=ui.value-ui.value%10;
        $("#amount").val(val);
        $("#bandwidth").html(ui.value);
    }
});
//    tab切换
$(".modal-title-list").on("click", "li", function() {
    var $tabIndex=$(this).index();
    var $table=$(".storage-con .modal-disk-content").eq($tabIndex);
    $(this).addClass("active");
    $(this).siblings().removeClass("active");
    $table.show();
    $table.siblings().hide();
    if ($(this).attr("no") == "2") {
        var code = $("#hostsCode").val();
        $('#use_table').bootstrapTable('refresh', {
            url: "/console/ajax/network/disks/uselist?id=" + code
        });
    } else if ($(this).attr("no") == "3") {
        var code = $("#hostsId").val();
        var vpc = $("#txtvpcCode").val();
        $('#unuse_table').bootstrapTable('refresh', {
            url: "/console/ajax/network/disks/unuselist?id=" + code + "&vpc=" + vpc
        });
    }
});

//刷新镜像列表
function refreshImageList(){
    var id = $("#basic_id").val();
    $('#image-table').bootstrapTable('refresh', {
            url: "/console/ajax/network/hosts/imageList?basic_id=" + id 
    });
}
//格式化镜像列表镜像类型
function formatter_is_private(value){
    if(parseInt(value) == 0){
        return '公有镜像';
    }else if(parseInt(value) == 1){
        return '私有镜像';
    }
}
//格式化镜像列表时间
function formatter_create_time(value){
    var now = new Date(parseInt(value) * 1000);
    return now.toLocaleString().replace(/年|月/g, "-").replace(/日/g, " ");
}


$("#image-table").on('all.bs.table', function(e, row, $element) {
    if ($("tbody input:checked").length >= 1) {
        $(".bnt-box .btn-default").attr('disabled', false);
    } else {
        $(".bnt-box .btn-default").attr('disabled', true);
    }
})

function image_del(){
    $('#modal').modal("hide");
    $('#modal').modal("removeBackdrop");
    
    var image_id = getRowsID('id');
    if (image_id != undefined) {
        $.ajax({
            type: "post",
            url: '/console/ajax/network/hosts/ajaxImageDel',
            async: true,
            timeout: 9999,
            data: {
                method: 'image_del',
                basic_id: image_id,
            },
            //dataType:'json',
            success: function(data) {
                
                data = $.parseJSON(data);
                if (data.Code != "0") {
                      showModal('提示', 'icon-exclamation-sign', data.Message, '', '', 0);
                      //$("#btnEsc").html("关闭");
                } 
            }
        });
    }
}

//获取选中行参数
function getRowsID(type) {
    var idlist = '';
    $("input[name='btSelectItem']:checkbox").each(function() {
        if ($(this)[0].checked == true) {
            //layer.alert($(this).val());
            var id = $(this).parent().parent().attr('data-uniqueid');
            var row = $('#image-table').bootstrapTable('getRowByUniqueId', id);
            var delimiter = idlist =="" ? "" :',';
            if (type == 'name') {
                idlist +=delimiter + row.image_name;
            } else if (type == "id") {
                idlist +=delimiter + row.id;
            } else {
                idlist +=delimiter + row.image_code;
            }
        }
    });
    return idlist;
}
//弹出框里面的界面切换
$(".modal-title-list").on("click", "li",
function() {
    $(".modal-title-list li").removeClass("active");
    $(this).addClass("active");
    $(".modal-disk-content").hide();
    $(".modal-disk-content").eq($(this).index()).show();
    if ($(this).attr("no") == "2") {
        var code = $("#hostsCode").val();
        $('#use_table').bootstrapTable('refresh', {
            url: "/console/ajax/network/disks/uselist?id=" + code
        });
    }
});
//
// 快照
$(".snap-con .bnt-box").on('click','.build-bnt',function(){
    $("#modal-addSnap").modal("show")
});
function formatter_snap_handle(val,row){
    // $("#snap_id").val(row.id);
    // $("#snap_code").val(row.code);
    var bnt='<button type="button" class="btn btn-primary tab-roll-btn" code="'+val+'" id="'+row.id+'">回滚</button>' +
        ' <button type="button"  class="btn btn-danger tab-snap-del" code="'+val+'" id="'+row.id+'">删除</button>';
    return bnt;
}
$(".snap-tab").on("click",".tab-roll-btn",function(){
    $("#snap_id").val($(this).attr("id"));
    $("#snap_code").val($(this).attr("code"));
    $("#modal-rollBack").modal("show")
//   showModal('回滚 - vmWare  / OpenStack','icon-info-sign')
})
$(".snap-tab").on("click",".tab-snap-del",function(){
    var snap_id   = $(this).attr("id");
    var row = $('#snap-table').bootstrapTable('getRowByUniqueId', snap_id);
    showModal('提示',' icon-exclamation-sign','确定删除此快照？',row.description,'snap_del(\''+snap_id+'\')');
});
//快照回滚
$('#snap-rollback-btn').on('click', function() {
    var snap_id   = $("#snap_id").val();
    ajax_snap('snapshot_rollback',snap_id);
    $('#modal-rollBack').modal("hide");
});
//快照删除
function snap_del(snap_id){
    ajax_snap('snapshot_del',snap_id);
    $('#modal').modal("hide");
}

function ajax_snap(method,snap_id){
    var row = $('#snap-table').bootstrapTable('getRowByUniqueId', snap_id);
    var snap_code = typeof(row.code) != "undefined" && row.code  ? row.code : "";
    $.ajax({
        url: '/console/ajax/network/hosts/ajaxSnap',
        async: false,
        data: {
            method: method,
            snapshotCode: snap_code,
            basic_id: snap_id,
        },
        method: 'post',
        success: function(e) {
            data = $.parseJSON(e);
            if (data.Code != "0") {
                layer.alert(data.Message,{title: '错误提示',icon: 5});
            } 
        }
    });
}

// 镜像
$(".mirror-con .bnt-box .btn-addition").each(function(index,obj){
    var tit=$("#m-addMirror").find(".modal-title");
    var $lessee=$("#m-addMirror").find("#m-lessee");

    $(obj).on('click',function(){
        if(index==0){
            var status = $("#status").val();
            if(status == "已停止"){
                tit.html("新建镜像");
                $("#m-addMirror").modal("show");
                $lessee.prop("disabled",false);
            }else{
                showModal('提示', 'icon-exclamation-sign',"当前设备状态无法进行操作,（请确保主机属于关闭电源状态）", '','',0);
            }
        }
        else if(index==1){
            tit.html("修改");
            $("#image-type").hide();
            var id;
            $("input[name='btSelectItem']:checkbox").each(function() {
                if ($(this)[0].checked == true) {
                    //layer.alert($(this).val());
                    id = $(this).parent().parent().attr('data-uniqueid');
                }
            });
            var row = $('#image-table').bootstrapTable('getRowByUniqueId', id);
            $('#image_id').val(row.id);
            $("#image_name").val(row.image_name);
            $("#image_note").html(row.image_note);
            $("#m-addMirror").modal("show");
            $lessee.prop("disabled",true)
        }
        else if(index==2){
            var image_name = getRowsID('name');
            showModal('提示',' icon-exclamation-sign','确定删除此镜像？',image_name,'image_del()');
            //$('#modal').modal('hide'); 
        }
    });
})

$('#sumbiter').one('click', function() {
    //ajax提交页面
    $.ajax({
       // url: "<?= $this -> Url -> build(['controller' => 'ajax', 'action' => 'network']); ?>/<?php echo "Hosts" ?>/<?php echo "edit" ?>",
        url:'/console/ajax/network/hosts/edit',
        async: false,
        data: $('#modal-modify-form').serialize(),
        method: 'post',
        dataType: 'json',
        success: function(e) {
            //操作成功
            if (e.code == '0000') {
                $('#modal-modify').modal("hide");
                location.reload();   
            }
        }
        });
            return false;
});

//开机
$('#start').on('click',function(){
    var uniqueId    = $(this).parent().attr('data-uniqueid');
    var H_Status    = $(this).parent().attr('data-status');
    var H_Name      = $(this).parent().attr('data-name');
    var H_Code      = $(this).parent().attr('data-code');
    if(H_Status == '运行中'){
        showModal('提示', 'icon-exclamation-sign', '当前设备正在运行中', '', '', 0);
    }else if (H_Status != '创建中' && H_Status != '创建失败' && H_Status != '创建镜像中') {
        showModal('提示', 'icon-question-sign', '确认要启动机器', H_Name, 'ajaxFun(\'' + H_Code + '\',\'ecs_start\',\'' + uniqueId + '\')');
    }else {
        showModal('提示', 'icon-exclamation-sign', '当前设备状态无法进行操作', '', '', 0);
        $("#btnEsc").html("关闭");
    }
});
//关机
$('#shutdown').on('click',function(){
    var uniqueId    = $(this).parent().attr('data-uniqueid');
    var H_Status    = $(this).parent().attr('data-status');
    var H_Name      = $(this).parent().attr('data-name');
    var H_Code      = $(this).parent().attr('data-code');
    if(H_Status == '已停止'){
        showModal('提示', 'icon-exclamation-sign', '当前设备已经停止', '', '', 0);
    }else if (H_Status != '创建中' && H_Status != '创建失败' && H_Status != '创建镜像中') {
        showModal('提示', 'icon-question-sign', '确认要关闭机器', H_Name, 'ajaxFun(\'' + H_Code + '\',\'ecs_stop\',\'' + uniqueId + '\')');
    }else {
        showModal('提示', 'icon-exclamation-sign', '当前设备状态无法进行操作', '', '', 0);
        $("#btnEsc").html("关闭");
    }
});

function ajaxFun(code, method, id) {
        $('#modal').modal("hide");
        if (method == "ecs_start") {
           heartbeat(0);
        } else if (method == "ecs_stop") {
           heartbeat(1);
        }
        if (id != undefined) {
            $.ajax({
                type: "post",
                url: '/console/ajax/network/hosts/ajaxHosts',
                async: true,
                timeout: 9999,
                data: {
                    method: method,
                    instanceCode: code,
                    basicId: id,
                    isEach: "false"
                },
                //dataType:'json',
                success: function(data) {
                    data = $.parseJSON(data);
                    if (data.Code != "0") {
                        layer.alert(data.Message);
                    }else{
                       // setTimeout("location.reload()", 3000);
                    }                    
                }
            });
        }
}

function heartbeat(type) {
        if (type == 0) {
            $("#status").html('正在启动...');
        } else if (type == 1) {
            $("#status").html('正在停止...');
        }
}
//监控信息
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

function hostset($scope, $http) {
    H_ID = $("#instance").attr('data-id');

    /**
   * The workhorse; converts an object to x-www-form-urlencoded serialization.
   * @param {Object} obj
   * @return {String}
   */ 
  var param = function(obj) {
    var query = '', name, value, fullSubName, subName, subValue, innerObj, i;
      
    for(name in obj) {
      value = obj[name];
        
      if(value instanceof Array) {
        for(i=0; i<value.length; ++i) {
          subValue = value[i];
          fullSubName = name + '[' + i + ']';
          innerObj = {};
          innerObj[fullSubName] = subValue;
          query += param(innerObj) + '&';
        }
      }
      else if(value instanceof Object) {
        for(subName in value) {
          subValue = value[subName];
          fullSubName = name + '[' + subName + ']';
          innerObj = {};
          innerObj[fullSubName] = subValue;
          query += param(innerObj) + '&';
        }
      }
      else if(value !== undefined && value !== null)
        query += encodeURIComponent(name) + '=' + encodeURIComponent(value) + '&';
    }
      
    return query.length ? query.substr(0, query.length - 1) : query;
  };
 
  // Override $http service's default transformRequest
  $http.defaults.transformRequest = [function(data) {
    return angular.isObject(data) && String(data) !== '[object File]' ? param(data) : data;
  }];
    
$http({
        method: 'POST',
        url: '/console/ajax/network/hosts/getHostHardwareSet',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        data: {id:H_ID}
    }).success(function(data) {
        $scope.setList = data; 

        $scope.currentSet =data[0];
        $scope.currentRom = data[0].rom[0];
        $scope.currentSetCode = data[0].rom[0].gpu[0].setCode;
    });

  
    $scope.changeSet = function(obj) {
        $scope.currentSet = obj;
        if (obj.rom.length != 0) {
            $scope.currentRom = obj.rom[0];
            if(obj.rom[0].gpu.length != 0){
                $scope.currentGpu = obj.rom[0].gpu[0];
                $scope.currentSetCode = obj.rom[0].gpu[0].setCode;
            }
        }
    }

    $scope.changeRom = function(obj) {
        
        $scope.currentRom = obj;
        if(obj.gpu.length != 0){
            $scope.currentGpu = obj.gpu[0];
            $scope.currentSetCode = obj.gpu[0].setCode;
        }
    }

    $scope.changeGpu = function(obj) {

        $scope.currentGpu = obj;
        $scope.currentSetCode = obj.setCode;
    }

    $scope.cpuFilter = function (item) {
      return item.cpu === $scope.currentSet.cpu;
    };
    $scope.romFilter = function (item) {
      return item.num === $scope.currentRom.num;
    };
}
//主机修改配置
$("#system-edit-sumbiter").click(function(){
    var H_Status = $("#instance").attr('data-status');
    if (H_Status == "运行中") {
        showModal('提示', 'icon-exclamation-sign', '当前设备正在运行中，请关机后再进行操作', '', '', 0);
    } else if (H_Status == '已停止') {
        showModal('提示', 'icon-exclamation-sign', '是否确认修改系统配置', '', 'updateSystem()', 1);
        $("#modal-sys-revamp").modal("hide");
    } else {
        showModal('提示', 'icon-exclamation-sign', '当前设备状态无法进行操作', '', '', 0);
    }
    
});
//修改系统配置
function updateSystem(){
    $('#modal').modal("hide");
    $.ajax({
            type: "post",
            // url: "<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'hosts', 'ajaxHosts']); ?>",
            url: "/console/ajax/network/hosts/ajaxHosts",
            async: true,
            timeout: 9999,
            data: {
                method: 'ecs_reconfig',
                basicId: $("#basicId").val(),
                instanceCode: $("#basiccode").val(),
                instanceTypeCode: $("#txtTypeCode").val(),
                isEach: false
            },
            success: function(data) {
                data = $.parseJSON(data);
                if (data.Code != "0") {
                    layer.alert(data.Message,{title: '错误提示',icon: 5});
                }
                //location.reload();
            }
        });
}

//input 存在一个被选中状态
$(".mirror-con #table").on('all.bs.table', function(e, row, $element) {
    if ($("tbody input:checked").length >= 1) {
        $(".center .btn-default").attr('disabled', false);
    } else {
        $(".center .btn-default").attr('disabled', true);
    }
})
//删除网卡
function delNetCard(event){
    var networkCardCode = $(event).attr('data-code');
    var basicId = $(event).attr('data-id');
    var subnetCode=$(event).attr('data-subnetCode');

    showModal('提示', 'icon-exclamation-sign','是否删除选中的网卡', '');
    $('#btnModel_ok').on('click', function() {
        $("#modal").modal("hide");
        $.ajax({
            //url:"<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'hosts', 'ajaxNetCard']); ?>",
            url: "/console/ajax/network/hosts/ajaxNetCard",
            data: {
                networkCardCode: networkCardCode,
                method: 'network_card_del',
                basic_id: basicId,
                subnetCode:subnetCode
            },
            async: false,
            success: function(data) {
                data = $.parseJSON(data);
                if(data.code == 1){
                    showModal('提示', 'icon-exclamation-sign',data.msg, '','',0);
                }else{
                   // setTimeout("location.reload()", 3000);
                }
            }
        });
    });
}
//载入vpc和子网选项
function loadSubnetPublic(vpc){
    var h="";
    var h2="";
    if( vpc == null){
        vpc = $("#vpc-code").val();
    }
    var basicid = $("#basic-id").val();

    $.ajax({
        type:"post",
        //url:"<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'hosts', 'loadSubnetPublic']); ?>",
        url:"/console/ajax/network/hosts/loadAvailableSubnetPublic",
        data:{vpc:vpc,basicid:basicid},
        async:false,
        success:function(data){
          data= $.parseJSON(data);
          if (data.vpc == null) {
            showModal2('提示','icon-exclamation-sign','只有一个vpc，不能添加网卡','');
          } else {
            $.each(data.vpc,function(i,n){
                if(n.code == vpc){
                    h += "<option value='"+n.code+"' selected=\"selected\">"+n.name+"</option>";
                }else{
                    h += "<option value='"+n.code+"'>"+n.name+"</option>";
                }
              // if(i==0){
              //     //<option value="4">$vpc$_822</option><option value="5">vpc[822]_fox</option><
              //     h += "<option value='"+n.code+"' selected=\"selected\">"+n.name+"</option>";
              // }else{
              //     h += "<option value='"+n.code+"'>"+n.name+"</option>";
              // }
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
            // layer.alert(data)
        }
    });
    $("#txtnet2").html($("#net2").find("option:selected").text());
    $("#txtnet22").val($("#net2").val());
}
//添加网卡调用接口
$("#addSubnet").on('click',function() {
    $("#subnet-manage").modal('hide');
    var rows = $('#subnet-extend-table').bootstrapTable('getSelections');
    if (rows.length > 0) {
    var subnetall = '';
    $.each(rows, function (i, n) {
        subnetall += n.subnet_code + ',';
    });
    subnetall = subnetall.substring(0, subnetall.length - 1);
    var basicId = $('#basic-id').val();
    //var subnet  = $('#net2').val();
    var ecsCode = $('#basic-code').val();
    var codeArr = [];
    $(".network-code").each(function (index, obj) {
        var nteCode = $(obj).text();
        codeArr.push(nteCode)
    });
    //循环
    var subnetarr = subnetall.split(',');
    $.each(subnetarr, function (i, subnet) {
        if (codeArr.indexOf(subnet) == -1) {
            $.ajax({
                url: "/console/ajax/network/hosts/ajaxNetCard",
                data: {
                    subnetCode: subnet,
                    method: 'network_card_add',
                    basic_id: basicId,
                    ecsCode: ecsCode
                },
                success: function (data) {
                    data = $.parseJSON(data);
                    if (data.code == 1) {
                        showModal('提示', 'icon-exclamation-sign', data.message, '', '', 0);
                    } else {
                        // setTimeout("location.reload()", 1000);
                    }
                }
            })
        } else {
            showModal('提示', 'icon-exclamation-sign', '同一子网不能同时绑定一台虚拟机的多张网卡', '', '', 0)
        }
    });
}else{
        showModal('提示', 'icon-exclamation-sign', '未选择子网', '', '', 0)
    }




});
//$("#addSubnet").on('click',function(){
//	$("#subnet-manage").modal('hide');
//    var basicId = $('#basic-id').val();
//    var subnet  = $('#net2').val();
//    var ecsCode = $('#basic-code').val();
//    var codeArr = [];
//    $(".network-code").each(function(index, obj) {
//        var nteCode = $(obj).text();
//        codeArr.push(nteCode)
//    });
//    if (codeArr.indexOf(subnet) == -1) {
//        $.ajax({
//            url:"/console/ajax/network/hosts/ajaxNetCard",
//            data: {
//                subnetCode: subnet,
//                method: 'network_card_add',
//                basic_id: basicId,
//                ecsCode: ecsCode
//            },
//            success: function(data) {
//                data = $.parseJSON(data);
//                if(data.code == 1){
//                    showModal('提示', 'icon-exclamation-sign',data.msg, '','',0);
//                }else{
//                   // setTimeout("location.reload()", 1000);
//                }
//            }
//        })
//    } else {
//        showModal('提示', 'icon-exclamation-sign','同一子网不能同时绑定一台虚拟机的多张网卡', '','',0)
//    }
//
//});
//删除硬盘
$(document).on("click", ".del-disk", function() {
    deldisks($(this).data("id"), $(this).data("code"));
});
//删除硬盘
function deldisks(basicId, volumeCode) {
    $.ajax({
        type: "post",
        url: "/console/ajax/network/disks/ajaxDisks",
        async: true,
        timeout: 9999,
        data: {
            volumeCode: volumeCode,
            method: 'volume_detach',
            basicId: basicId
        },
        success: function(data) {
            data = $.parseJSON(data);
            if (data.Code != 0) {
                layer.alert(data.Message);
            } else {
                //showModal('提示', 'icon-exclamation-sign', '解绑硬盘成功', '', 'hidenModal()');
                // $(document).on("click",".del-disk",function(){
                //   deldisks($(this).data("row"));
                // });
            }
        }
    });
}

//挂载硬盘
function btnaddDisks(idList, obj) {
    var $obj = $(obj);
    $obj.prop('disabled', true);

    var id = $("#hostsId").val();
    var instanceCode = $("#hostsCode").val();
    var name = $("#txtdisks_name").val();
    var size = parseInt($("#amount").val());
    var vpcCode = $("#txtvpcCode").val();
    var class_code = $("#txtclass_code").val();
    var isFusion;
    var volumeCode, method;
    var number = 1;
    $.ajaxSettings.async = false;
    istrue = true;

    var a = /^[1-9]\d*0$/;
    if (a.test(size) == false || size < 10 || size > 1000) {
        $("#amount").val(10);
        size = 10;
    }

    
    if (name == "") {
        $('#name-warning').html('硬盘名称不能为空');
        $obj.prop('disabled', false);
        return false;
    }
    $.getJSON("/console/home/getUserLimit", function(data) {
        if (data.disks_used >= data.data.disks_bugedt) {
            layer.alert('配额不足 \r\n 磁盘个数配额：' + data.data.disks_bugedt+ " 已使用：" + data.disks_used);
            istrue = false;
        }

    });
    $.getJSON("/console/home/getUserLimit", function(data) {
        if (Number(size) > data.data.disks_cap_bugedt) {
            layer.alert("配额不足 \r\n 磁盘容量配额：" + data.data.disks_cap_bugedt );
            istrue = false;
        }

    });
    //$.get("/console/ajax/network/hosts/getDisksCount/?code=" + instanceCode, function(data) {
    //    $.getJSON("/console/ajax/network/hosts/getDisksLimit", function(data2) {
    //        if (Number(data) >= data2) {
    //            layer.alert('主机最多挂载' + data2 + '个硬盘');
    //            istrue = false;
    //        }
    //    })
    //})
    //
    //$.getJSON("/console/home/getUserLimit", function(data) {
    //    if (Number(size) + data.disks_used > data.data.disks_bugedt) {
    //        layer.alert("配额不足 \r\n 磁盘 配额：" + data.data.disks_bugedt + " 已使用：" + data.disks_used);
    //        istrue = false;
    //    }
    //});
    if (!istrue) {
        $obj.prop('disabled', false);
        return false
    }
    $.ajaxSettings.async = true;
    if (idList != null && idList != '') {
        method = "volume_attach"; //挂载2432
        volumeCode = idList;
    } else {
        method = "volume_add";
        isFusion = $("#txtisFusion").val();
    }
    $.ajax({
        type: "post",
        url: "/console/ajax/network/disks/ajaxDisks",
        async: true,
        timeout: 9999999,
        data: {
            method: method,
            id: id,
            name: name,
            size: size,
            instanceCode: instanceCode,
            volumeCode: volumeCode,
            vpcCode: vpcCode,
            class_code: class_code,
            isFusion: isFusion
        },
        success: function(data) {
            data = $.parseJSON(data);
            if (data.Code != 0) {
                layer.alert(data.Message,{title: '错误提示',icon: 5});
            } else {
                if (idList != null && idList != '') {
                  //  showModal('提示', '','添加硬盘成功','', '',0);
                } else {
                  //  showModal('提示', '','挂载硬盘成功','', '',0);
                }
            }
            //$('#disk-manage').modal("hide");
            $obj.prop('disabled', false);
            $("li[no='2']").click();
        }
    });
}
    function notifyCallBack(value) {
        if (value.MsgType == "success" ) {//|| value.MsgType == "error"
            if(value.Data.method == "volume_del" || value.Data.method == "volume_attach"){
                //showModal('提示', 'icon-exclamation-sign', '解绑硬盘成功', '', 'hidenModal()');
                $("li[no='2']").click();
            }else if(value.Data.method == 'image_add' || value.Data.method == 'image_del'){
                refreshImageList();//刷新镜像列表
            }else if(value.Data.method == "volume_add" || value.Data.method == "volume_detach"){
                
            }else{
                setTimeout('window.location.reload()',2000);
            }
        }
    }

//图形化页面，打开硬盘列表
function open_disks(){
    $('#disk-manage').modal("show");
    $("#txtdisks_name").val('');
    $("#amount").val(10);
    code = $("#hostsCode").val();
    $('#use_table').bootstrapTable({
        url: "/console/ajax/network/disks/uselist?id=" +code,
    });
}
//桌面登陆
$("#desktop-login").on("click", function() {
    // if (value!=null) {}else{};

    var html = "";
    var code                    = $(this).attr('data-code');
    var host_extend_name        = $(this).attr('data-host-name');
    var host_extend_platform    = $(this).attr('data-host-platform');
    var connect_status          = $(this).attr('data-host-connect-status');
    var status                  = $(this).attr('data-status');
    //TODO 加密解密token
    var url = "/xdesktop/citrix/launch/"+host_extend_name+'.ica';
    if (status != "创建失败" && status != "" && status != "销毁中" && status != "创建中" && host_extend_name != "" && status == "运行中") {
        window.open(url);
        // if (os == "Linux") {
        //     html += "<a href=" + url + " target='lauchFrame'><i class='icon-laptop'></i></a>";
        //     return html;

        // } else {
        //     html += "<a href=" + url + " target='lauchFrame'><i class='icon-desktop'></i></a>";
        //     return html;
        // }
    } 
});
$("#hosts-login").on("click", function() {
    var code = $(this).attr('data-code');
    var id   = $(this).attr('data-id');
    var vnc_password = $(this).attr('data-vnc-password');
    var os   = $(this).attr('data-os');
    var status = $(this).attr('data-status');
    var fusionType = $(this).attr('data-fusionType');

    var url = "/console/network/webConsole/" + code;
    
    if (fusionType == "aws") {
        return ;
    } else if (fusionType == "aliyun") {
        if (os != null) {
            var url = "/console/network/webConsole/" + code;
            if (status == "运行中") {
                is_login(code,vnc_password,id);
            } else {
                return ;
            }
        }
    } else if (fusionType == "vmware" || fusionType == "openstack") {
        var url = "/console/network/webConsole/" + code;
        if (status == "运行中") {
            window.open(url);
        } else {
            return ;
        }
    }
});

function webadmin(code,id) {
    $("#modal").modal("hide");
    $.ajax({
        type: "post",
        //url: "<?= $this ->Url ->build(['prefix' =>'console', 'controller' =>'ajax', 'action' =>'network', 'hosts', 'webadmin']); ?>",
        url: "/console/ajax/network/hosts/webadmin",
        async: true,
        data: {
            method: "ecs_up_vnc_password",
            instanceCode: code
        },
        success: function(data) {
            data = $.parseJSON(data);
            if (data.Code != 0) {
                layer.alert(data.Message);
            } else {
                showModal('提示', 'icon-question-sign', '初始化已完成，是否立即重启？', '重启后生效', 'ajaxFun(id,\'ecs_reboot\')');
                $("#btnModel_ok").html("立即重启");
                $("#btnEsc").html("稍后重启");
            }
        }
    });
}

function is_login(code,vnc_password,id) {
    var url;
    if (vnc_password != null && vnc_password != "") {
        url = "/console/network/webConsole/" + code;
        window.open(url);
    } else {
        showModal('提示', 'icon-question-sign', '当前是第一次操作,是否进行初始化操作', '', 'webadmin(\'' + code + '\',\'' + id + '\')');
    }
}
//新建镜像
$("#image-add-sumbiter").on('click',function(){
    var basic_id = $('#basic_id').val();
    var is_private = $('#is_private').val();
    var image_note = $('#image_note').val();
    var image_name = $('#image_name').val();
    var ecsCode   = $('#ecscode').val();
    var image_id  = $("#image_id").val();
    if (image_name == "") {
        $('#name-warning').html('镜像名称不能为空');
        //$obj.prop('disabled', false);
        return false;
    }
    if(image_id == ""){//添加
        $.ajax({
            type: "post",
            //url: "<?= $this ->Url ->build(['prefix' =>'console', 'controller' =>'ajax', 'action' =>'network', 'hosts', 'webadmin']); ?>",
            url: "/console/ajax/network/hosts/ajaxImage",
            async: true,
            data: {
                method: "image_add",
                basic_id: basic_id,
                is_private: is_private,
                image_name: image_name,
                image_note: image_note,
                ecsCode:ecsCode
            },
            success: function(data) {
                data = $.parseJSON(data);
                if (data.Code != 0) {
                    layer.alert(data.Message);
                }
                $('#m-addMirror').modal("hide");
            }
        });
    }else{//修改
        $.ajax({
            type: "post",
            url: "/console/ajax/network/hosts/editImage",
            async: true,
            data: {
                image_id: image_id,
                image_name: image_name,
                image_note: image_note,
            },
            success: function(data) {
                data = $.parseJSON(data);
                if (data.Code != 0) {
                    layer.alert(data.Message,{title: '错误提示',icon: 6});
                }
                location.reload();
                $('#m-addMirror').modal("hide");
            }
        });
    }
    
});

//新建快照
$('#snap-add').on('click',function(){
    $('#modal-addSnap').modal("show");
    var basic_id = $('#basic_id').val();
    var code     = $('#code').val();   
    var description = $('#description').val();
    var isMemory = $("input[name='isMemory']:checked").val();
    $.ajax({
        type: "post",
        url: "/console/ajax/network/hosts/ajaxSnap",
        async: true,
        data: {
            method: "snapshot_add",
            basic_id: basic_id,
            code:code,
            description:description,
            isMemory:isMemory
        },
        success: function(data) {
            data = $.parseJSON(data);
            if (data.Code != 0) {
                layer.alert(data.Message,{title: '错误提示',icon: 5});
            } 
            $('#modal-addSnap').modal("hide");
        }
    });

});
