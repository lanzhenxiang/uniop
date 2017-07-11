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
    			$(".chart-mask").hide(), json = eval('(' + result + ')');
    			var data = {
    				labels: json.chart.cpu.time,
    				datasets: [{
    					fillColor: "rgba(0,0,0,0)",
    					strokeColor: "rgba(68,210,228,1)",
    					pointColor: "rgba(220,220,220,1)",
    					pointStrokeColor: "#fff",
    					data: json.chart.cpu.data
    				}]
    			}
    			var data3 = {
    				labels: json.chart.disk.time,
    				datasets: [{
    					fillColor: "rgba(0,0,0,0)",
    					strokeColor: "rgba(68,210,228,1)",
    					pointColor: "rgba(220,220,220,1)",
    					pointStrokeColor: "#fff",
    					data: json.chart.disk.data1
    				}, {
    					fillColor: "rgba(0,0,0,0)",
    					strokeColor: "rgba(252,218,150,1)",
    					pointColor: "rgba(220,220,220,1)",
    					pointStrokeColor: "#fff",
    					data: json.chart.disk.data2
    				}]
    			}
    			var data4 = {
    				labels: json.chart.network.time,
    				datasets: [{
    					fillColor: "rgba(0,0,0,0)",
    					strokeColor: "rgba(68,210,228,1)",
    					pointColor: "rgba(220,220,220,1)",
    					pointStrokeColor: "#fff",
    					data: json.chart.network.data1
    				}, {
    					fillColor: "rgba(0,0,0,0)",
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
    			// var ctx2 = document.getElementById("canvas2").getContext("2d");
    			// window.myLine = new Chart(ctx2).Line(data2, {
    			//   responsive: true
    			// });
    			var ctx3 = document.getElementById("canvas3").getContext("2d");
    			window.myLine = new Chart(ctx3).Line(data3, {
    				responsive: true
    			});
    			var ctx4 = document.getElementById("canvas4").getContext("2d");
    			window.myLine = new Chart(ctx4).Line(data4, {
    				responsive: true
    			});

    		}
    	});

    }

    function chart_statics(value, row, index) {
    	var html = "";
    	var code = row.H_Code;
    	if(code != null){
    		html += "<span class='cursor' onclick='chartShow(" + row.H_ID + ")'><i class='icon-bar-chart'></i></span>";
    	}else{
    		html +='-';
    	}
    	return html;
    }

    function chartShow(id) {
    	$(".chart-range").show();
    	row = $('#table').bootstrapTable('getRowByUniqueId', id);
    	$(".host-name").html("主机CODE：" + row.H_Code + " &nbsp;主机名称： " + row.H_Name);
    	// location.href="/console/network/statics/hosts/" + row.H_Code
    	chart(row.H_Code);
    }

    $(".chart-range .remove").on("click", function() {
    	$(".chart-range").hide();
    }) /* 图表 end*/

    /* 主机Code */
    function formatter_main(value, row, index) {
        if(value!=null&&value!=""){
            return '<a href="/console/network/data/basic_info/' + row.H_ID + '">' + row.H_Code + '</a>';
        }else{
            return "-";
        }
    }

    /* 渲染页面 */
    function operateFormatter(value, row, index) {
    	return '<a href="javascript:;" data-id="' + row.id + '" data-code="' + row.code + '" class="del-disk"><i class="icon-remove"></i></a>';
    }

    // function fromatter_Capacity(value,row,index){
    //   return value+"GB";
    // }
    $(document).on("click", ".del-disk", function() {
    	deldisks($(this).data("id"), $(this).data("code"));
    });

    function deldisks(basicId, volumeCode) {
    	$.ajax({
    		type: "post",
            url:"/console/ajax/network/disks/ajaxDisks",
    		async: true,
    		timeout: 9999,
    		data: {
    			volumeCode: volumeCode,
    			method: 'volume_detach',
    			basicId: basicId
    		},
    		// beforeSend: function() {
    		// 	$(document).off("click", ".del-disk");
    		// },
    		//dataType:'json',
    		success: function(data) {
    			data = $.parseJSON(data);
    			if (data.Code != 0) {
    				alert(data.Message);
    			} else {
    				//showModal('提示', 'icon-exclamation-sign', '解绑硬盘成功', '', 'hidenModal()');
    			}
    			$('#disk-manage').modal("hide");
    		},

    	});
    }

    function modalReturn() {
    	$("#disk-manage .modal-title-list li").removeClass("active").eq(0).addClass("active");
    	$("#disk-manage .modal-disk-content").css("display", "none").eq(0).css("display", "block");
    }

    $('#disk-manage').on('hide.bs.modal', function() {
    	modalReturn();
    });

    $("#unuse_table").on("click", "tr", function() {
    	$("#unuse_table tr").removeClass("info");
    	$(this).addClass("info");
    });

    $("#btnattach").on('click', function() {
    	var idlist = "";
    	var selectDisksList = $("#unuse_table").bootstrapTable('getSelections');
    	selectDisksList.forEach(function(e) {
    		var id = e.H_Code;
    		idlist += id + ",";
    	});
    	btnaddDisks(idlist);
    });

    //slide
//    $("#amount").keyup(function() {
//    	var a = /^[1-9]\d*0$/;
//    	$(this).blur(function() {
//    		var amountVal = $(this).val();
//    		if (a.test(amountVal) == false || amountVal < 10 || amountVal > 1000) {
//    			$(this).val(10);
//    		} else {
//    			return;
//    		}
//    	})
//    	$("#slider").slider({
//    		value: $("#amount").val()
//    	})
//    });

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

    $("#amount").val($("#slider").slider("value"));
    $("#bandwidth").html($("#slider").slider("value"));

    //弹出框里面的界面切换
    $(".modal-title-list").on("click", "li", function() {
    	$(".modal-title-list li").removeClass("active");
    	$(this).addClass("active");
    	$(".modal-disk-content").css("display", "none");
    	$(".modal-disk-content").eq($(this).index()).css("display", "block");
    	if ($(this).attr("no") == "2") {
    		var code = $("#hostsCode").val();
    		$('#use_table').bootstrapTable('refresh', {
    			url: "/console/ajax/network/disks/uselist?id=" + code
    		});
    	} else if ($(this).attr("no") == "3") {
    		var code = $("#hostsId").val();
    		var vpc = $("#txtvpcCode").val();
    		$('#unuse_table').bootstrapTable('refresh', {
    			url: "/console/ajax/network/disks/unuselist?id=" + code + "&vpc=" + vpc,
    		});
    	}
    })

    $("#btnStart").on('click', function() {
    	var names = getRowsID('name');
    	var o = true;
    	var rows = $('#table').bootstrapTable('getSelections');
    		rows.forEach(function(e) {
    			if(e.H_Status=="创建中"){
    				showModal('提示', 'icon-exclamation-sign', '当前设备状态无法进行操作',e.H_Name, '', 0);
    				$("#btnEsc").html("关闭");
    				o = false;
    				return false;
    			}
    		});
    	if(o==true){
    		if (names != "") {
    			showModal('提示', 'icon-question-sign', '确认要启动机器', names, 'ajaxFun(getRowsID(),\'ecs_start\')');
    		} else {
    			showModal('提示', 'icon-exclamation-sign', '请选中一台主机', '', '', 0);
    			$("#btnEsc").html("关闭");
    		}
    	}
    });
    $("#btnStop").on('click', function() {
    	var names = getRowsID('name');
    	var o = true;
    	var rows = $('#table').bootstrapTable('getSelections');
    		rows.every(function(e, i) {
    			if(e.H_Status=="创建中"){
    				showModal('提示', 'icon-exclamation-sign', '当前设备状态无法进行操作',e.H_Name, '', 0);
    				$("#btnEsc").html("关闭");
    				o = false;
    				return false;
    			}
    		});
    	if(o==true){
    		if (names != "") {
    			showModal('提示', 'icon-question-sign', '确认要停止机器', names, 'ajaxFun(getRowsID(),\'ecs_stop\')');
    		} else {
    			showModal('提示', 'icon-exclamation-sign', '请选中一台主机', '', '', 0);
    			$("#btnEsc").html("关闭");
    		}
    	}
    });
$("#btnForceStop").on('click', function() {
	var names = getRowsID('name');
	var o = true;
	var rows = $('#table').bootstrapTable('getSelections');
	rows.every(function(e, i) {
		if(e.H_Status=="创建中"){
			showModal('提示', 'icon-exclamation-sign', '当前设备状态无法进行操作',e.H_Name, '', 0);
			$("#btnEsc").html("关闭");
			o = false;
			return false;
		}
	});
	if(o==true){
		if (names != "") {
			showModal('提示', 'icon-question-sign', '确认要停止机器', names, 'ajaxFun(getRowsID(),\'ecs_forced_poweroff\')');
		} else {
			showModal('提示', 'icon-exclamation-sign', '请选中一台主机', '', '', 0);
			$("#btnEsc").html("关闭");
		}
	}
});
    $("#btnDel").on('click', function() {
        var names = getRowsID('id');
        //判断机器状态
        var o = true;
    	var rows = $('#table').bootstrapTable('getSelections');

        var firewall = false;
                
    		rows.every(function(e, i) {
    			if(e.H_Status=="运行中") {
    				showModal('提示', 'icon-exclamation-sign', '请先关机，再删除主机',e.H_Name, '', 0);
    				$("#btnEsc").html("关闭");
    				o = false;
    				return false;
    			}else if (e.H_Status == '创建镜像中') {
					showModal('提示', 'icon-exclamation-sign', '创建镜像中，无法删除主机',e.H_Name, '', 0);
					o = false;
					return false;
				}
				if (e.E_Ip != "" && e.E_Ip != null) {
					showModal('提示', 'icon-exclamation-sign', '主机已绑定Eip，请解绑后再删除', e.H_Name, '', 0);
					$("#btnEsc").html("关闭");
					o = false;
					return false;
				}
                if(firewall != "true"){
                    firewall =  $.ajax({
                        url: "/console/ajax/network/hosts/firewall?id="+e.H_ID,async: false
                    }).responseText;
                }


               

    		});
        
        

    	if(o==true){
	        if (names != "") {
                
                if(firewall == "true"){
                    showModal('提示', 'icon-question-sign', '选定主机有防火墙策略，删除主机后请自行删除相关策略。确认要删除机器', names, 'ajaxFun(getRowsID(),\'ecs_delete\')');
                }else{
                    showModal('提示', 'icon-question-sign', '确认要删除机器', names, 'ajaxFun(getRowsID(),\'ecs_delete\')');
                }


	            
	        } else {
	        showModal('提示', 'icon-exclamation-sign', '请选中一台主机', '', '', 0);
	            $("#btnEsc").html("关闭");
	        }
    	}
    });

    //重启
    $("#btnreboot").on('click', function() {
    	var names = getRowsID('name');
    	if (names != "") {
    		showModal('提示', 'icon-question-sign', '确认要重启机器', names, 'ajaxFun(getRowsID(),\'ecs_reboot\')');
    	} else {
    		showModal('提示', 'icon-exclamation-sign', '请选中一台主机', '', '', 0);
    		$("#btnEsc").html("关闭");
    	}
    });

    //input 存在一个被选中状态
    $("#table").on('all.bs.table', function(e, row, $element) {
    	if ($("tbody input:checked").length >= 1) {
    		$(".center .btn-default").attr('disabled', false);
    	} else {
    		$(".center .btn-default").attr('disabled', true);
    	}
    })

    function hidenModal() {
    	$('#modal').modal("hide");
    }

    //动态创建modal

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

        html +='</div>';
    	html += '<div class="modal-footer"><button id="btnModel_ok" onclick="' + method + '" type="button" class="btn btn-primary" >确认</button><button type="button" class="btn btn-danger" id="btnEsc" data-dismiss="modal">取消</button></div></div></div></div>';
    	$("#maindiv").append(html);
    	if (type == 0) {
    		$("#btnModel_ok").remove();
    	}
    	$('#modal').modal("show");
    }

    $('#table').contextMenu('context-menu', {
    	bindings: {
    		'start': function(event) {
    			var uniqueId = $(event).attr('data-uniqueid');
    			var row = $('#table').bootstrapTable('getRowByUniqueId', uniqueId);
    			if (row.H_Status != '创建中' && row.H_Status != '创建失败') {
    				showModal('提示', 'icon-question-sign', '确认要启动机器', row.H_Name, 'ajaxFun(\'' + row.H_Code + '\',\'ecs_start\',\'' + row.H_ID + '\')');
    			} else {
    				showModal('提示', 'icon-exclamation-sign', '当前设备状态无法进行操作', '', '', 0);
    				$("#btnEsc").html("关闭");
    			}
    		},
    		'close': function(event) {
    			//获取数据
    			var uniqueId = $(event).attr('data-uniqueid');
    			var row = $('#table').bootstrapTable('getRowByUniqueId', uniqueId);
    			if (row.H_Status != '创建中' && row.H_Status != '创建失败') {
    				showModal('提示', 'icon-question-sign', '确认要停止机器', row.H_Name, 'ajaxFun(\'' + row.H_Code + '\',\'ecs_stop\',\'' + row.H_ID + '\')');
    			} else {
    				showModal('提示', 'icon-exclamation-sign', '当前设备状态无法进行操作', '', '', 0);
    				$("#btnEsc").html("关闭");
    			}
    		},
    		'restart': function(event) {
    			//获取数据
    			var uniqueId = $(event).attr('data-uniqueid');
    			var row = $('#table').bootstrapTable('getRowByUniqueId', uniqueId);
    			if (row.H_Status != '创建中' && row.H_Status != '创建失败' && row.H_Status != '已停止') {
    				showModal('提示', 'icon-question-sign', '确认要启动机器', row.H_Name, 'ajaxFun(\'' + row.H_Code + '\',\'ecs_reboot\',\'' + row.H_ID + '\')');
    			} else if(row.H_Status == '已停止') {
    				showModal('提示', 'icon-exclamation-sign', '该主机已关机，无法重启', '', '', 0);
    				$("#btnEsc").html("关闭");
    			}else{
                    showModal('提示', 'icon-exclamation-sign', '当前设备状态无法进行操作', '', '', 0);
                    $("#btnEsc").html("关闭");
                }
    		},
    		'modify': function(event) {
    			//获取数据
    			index = $(event).attr('data-index');
    			var uniqueId = $(event).attr('data-uniqueid');
    			var row = $('#table').bootstrapTable('getRowByUniqueId', uniqueId);
    			if (row.H_Status != '创建中' && row.H_Status != '创建失败') {
    				//console.log(row);
    				//填充数据
    				//TODO 根据bootstrap方法
    				$('#modal-modify-name').val(row.H_Name);
    				$('#modal-modify-description').val(row.H_Description);
    				$('#modal-modify-id').val(row.H_ID);
    				$('#modal-modify').one('show.bs.modal', function() {
    					$('#yes_edit').one('click', function() {
    						//ajax提交页面
    						$.ajax({
                                url:"/console/ajax/network/Hosts/edit",
    							async: false,
    							//data: $('#modal-modify-form').serialize(),
								data:{id:$('#modal-modify-id').val(),name:$('#modal-modify-name').val(),description:$('#modal-modify-description').val()},
    							method: 'post',
    							dataType: 'json',
    							success: function(e) {
    								//操作成功
    								if (e.code == '0000') {
    									$('#modal-modify').modal("hide");
    								}
    								refreshTable();
    							}
    						});
    						return false;
    					});
    				});
    				$('#modal-modify').modal("show");
    			} else {

    				showModal('提示', 'icon-exclamation-sign', '当前设备状态无法进行操作', '', '', 0);
    				$("#btnEsc").html("关闭");
    			}
    		},
    		'adddisks': function(event) {
    			//获取数据
    			var uniqueId = $(event).attr('data-uniqueid');
    			var row = $('#table').bootstrapTable('getRowByUniqueId', uniqueId);
    			if (row.H_Status != '创建中' && row.H_Status != '创建失败') {
    				$('#disk-manage').modal("show");
    				$("#txtdisks_name").val('');
    				$("#amount").val(10);
    				$("#slider").slider({
    					value: $("#amount").val()
    				})
    				$("#hostsCode").val(row.H_Code);
    				$("#hostsId").val(uniqueId);
    				$("#txtvpcCode").val(row.F_Code);
    				$("#txtclass_code").val(row.H_L_Code);
    				$("#txtisFusion").val(row.D_isFusion);
    				$('#use_table').bootstrapTable({
    					url: "/console/ajax/network/disks/uselist?id=" + row.H_Code,
    					// 'data-side-pagination': "server",
    					// //pagination:true,
    					// //sidePagination:"server",
    					// locale: "zh-CN",
    					// clickToSelect: "true",
    					// uniqueId: "id",
    					// pageSize: 7
    				});
    			} else {
    				showModal('提示', 'icon-exclamation-sign', '当前设备状态无法进行操作', '', '', 0);
    				$("#btnEsc").html("关闭");
    			}
    		},
            'sysdisks': function(event) {
                //获取数据
                var uniqueId = $(event).attr('data-uniqueid');

                var row = $('#table').bootstrapTable('getRowByUniqueId', uniqueId);
                if (row.H_Status != '创建中' && row.H_Status != '创建失败') {
                    $('#sysdisk-manage').modal("show");
                    $("#syssize").val(row.D_sys_size);
                    $("#slider").slider({
                        value: $("#slider-sysdisk").val()
                    })
                    $("#ecsCode").val(row.H_Code);
                    $("#ecsId").val(row.H_ID);
                    $("#slider-sysdisk").slider({
                        //min: row.D_sys_size,
                        max: 500,
                        step: 1,
                        orientation: "horizontal",
                        range: "min",
                        animate: true,
                        slide: function(event, ui) {
                            var val=ui.value-ui.value%10;
                            $("#syssize").val(val);
                        }
                    });
                    var min = parseInt(row.D_sys_size);
                    $("#sysdisk-size").html(min);
                    $("#sysdisk-size2").html(min);
                    $("#slider-sysdisk").slider( "option", "min",min);

                } else {
                    showModal('提示', 'icon-exclamation-sign', '当前设备状态无法进行操作', '', '', 0);
                    $("#btnEsc").html("关闭");
                }
            },
    		'del': function(event) {
    			//获取数据
    			var uniqueId = $(event).attr('data-uniqueid');
    			var row = $('#table').bootstrapTable('getRowByUniqueId', uniqueId);
    			if (row.E_Ip != "" && row.E_Ip != null) {
    				showModal('提示', 'icon-exclamation-sign', '该主机已绑定Eip，请解绑后再删除', '', '', 0);
    				$("#btnEsc").html("关闭");
    			} else {
                    if (row.H_Status == '运行中') {
                    	showModal('提示', 'icon-exclamation-sign', '请先关机，再删除主机',row.H_Name, '', 0);
                    } else if (row.H_Status == '创建镜像中') {
                    	showModal('提示', 'icon-exclamation-sign', '创建镜像中，无法删除主机',row.H_Name, '', 0);
                    } else {
                        showModal('提示',  'icon-question-sign','确认要删除机器', row.H_Name, 'ajaxFun(\'' + row.H_Code + '\',\'ecs_delete\',\'' + row.H_ID + '\')','1','1');
                    }
    			}
    		},
    		'defined': function(event) {
    			//获取数据
    			//$('#modal-defined').modal("show");
    			var uniqueId = $(event).attr('data-uniqueid');
                window.location.href = "/console/network/data/mirror/"+uniqueId+"/hosts";
    		},
            'excp':function(event){
            	var uniqueId = $(event).attr('data-uniqueid');
            	var row = $('#table').bootstrapTable('getRowByUniqueId', uniqueId);

            	var department_id = row.H_department;
            	window.location.href = "/console/excp/lists/excp/hosts/"+department_id+'/all/0/0/'+row.H_ID;
            },
            //正常
            'normal':function(event){
            	var uniqueId = $(event).attr('data-uniqueid');
            	var row = $('#table').bootstrapTable('getRowByUniqueId', uniqueId);
            	var department_id = row.H_department;
            	window.location.href = "/console/excp/lists/normal/hosts/"+department_id+'/all/0/0/'+row.H_ID;
            },
			'executing':function(event){
				var uniqueId = $(event).attr('data-uniqueid');
				var row = $('#table').bootstrapTable('getRowByUniqueId', uniqueId);
				var department_id = row.H_department;
				window.location.href = "/console/excp/lists/executing/hosts/"+department_id+'/all/0/0/'+row.H_ID;
			},
			'ecsUnbindEip': function(event) {
				var uniqueId = $(event).attr('data-uniqueid');
				var row = $('#table').bootstrapTable('getRowByUniqueId', uniqueId);
				var content = row.E_Ip;
				if (content != "" && content != null) {
					showModal('解绑', 'icon-question-sign', '是否解绑主机' + row.H_Name + '绑定的EIP，ip地址:', content, 'ajaxFun(\'' + row.EIP_code + '\',\'eip_unbind\',\'' + row.EIP_id + '\')');
				} else {
					showModal('提示', 'icon-exclamation-sign', '该主机没有绑定eip', '', '', 0);
					$("#btnEsc").html("关闭");
				}
			},
    	}

    });

    //搜索绑定
    $("#txtsearch").on('keyup', function() {
    	if (timer != null) {
    		clearTimeout(timer);
    	}
    	var timer = setTimeout(function() {
    		refreshTable()
    	}, 500);
    });

    //搜索绑定
    $("#txtsearchbiz").on('keyup', function() {
        if (timer != null) {
            clearTimeout(timer);
        }
        var timer = setTimeout(function() {
            refreshTable()
        }, 500);
    });

   
    //格式化配置

    function formatter_config(value, row, index) {
    	if (row.D_Cpu != 0) {
    		return row.D_Cpu + "核*" + row.D_Memory + "GB*" + row.D_Gpu + "MB";
    	} else {
    		return "-";
    	}
    }
    //格式化code
    function formatter_code(value, row, index) {
        var html = "";
        var code = row.H_Code;
        var fusionType = row.S_FusionType;
        if (fusionType == "aws") {
            return "-";
        } else if (fusionType == "aliyun") {
            var os = row.D_Plat_form;
            if (os != null) {
                var url = "/console/network/webConsole/" + code;
                if (row.H_Status == "运行中") {
                    if (os == "Linux") {
                        html += "<a href='#' onclick='is_login(\"" + row.H_ID + "\")'><i class='icon-laptop'></i></a>";
                        return html;
                    } else {
                        html += "<a href='#' onclick='is_login(\"" + row.H_ID + "\")'><i class='icon-desktop'></i></a>";
                        return html;
                    }
                } else {
                    return "-";
                }
            }
        } else if (fusionType == "vmware" || fusionType == 'openstack') {
            var url = "/console/network/webConsole/" + code;
            if (row.H_Status == "运行中") {
                if (os == "Linux") {
                    html += "<a href=" + url + " target='_blank'><i class='icon-laptop'></i></a>";
                    return html;
                } else {
                    html += "<a href=" + url + " target='_blank'><i class='icon-desktop'></i></a>";
                    return html;
                }
            } else {
                return "-";
            }
        }
        return "-";
    }
    /**旧版登陆入口
    function formatter_code(value, row, index) {
        var html = "";
        var code = row.H_Code;
        var name = row.E_Name.split('-');
        if (name[0] == "亚马逊") {
            return "-";
        } else if (name[0] == "阿里云") {
            var os = row.D_Plat_form;
            if (os != null) {
                var url = "/console/network/webConsole/" + code;
                if (row.H_Status == "运行中") {
                    if (os == "Linux") {
                        html += "<a href='#' onclick='is_login(\"" + row.H_ID + "\")'><i class='icon-laptop'></i></a>";
                        return html;
                    } else {
                        html += "<a href='#' onclick='is_login(\"" + row.H_ID + "\")'><i class='icon-desktop'></i></a>";
                        return html;
                    }
                } else {
                    return "-";
                }
            }
        } else if (name[0] == "索贝") {
            var url = "/console/network/webConsole/" + code;
            if (row.H_Status == "运行中") {
                if (os == "Linux") {
                    html += "<a href=" + url + " target='_blank'><i class='icon-laptop'></i></a>";
                    return html;
                } else {
                    html += "<a href=" + url + " target='_blank'><i class='icon-desktop'></i></a>";
                    return html;
                }
            } else {
                return "-";
            }
        }
        return "-";
    }*/

    //返回状态

    function formatter_state(value, row, index) {
    	switch (value) {
    	case "创建中":
    		{
    			return '<span id="imgState' + row.H_ID + '" class="circle circle-create"></span><span id="txtState' + row.H_ID + '">创建中</span>';
    			break;
    		}
    	case "运行中":
    		{
    			return '<span id="imgState' + row.H_ID + '" class="circle circle-run"></span><span id="txtState' + row.H_ID + '">运行中</span>';
    			break;
    		}
    	case "已停止":
    		{
    			return '<span id="imgState' + row.H_ID + '" class="circle circle-stopped"></span><span id="txtState' + row.H_ID + '">已停止</span>';
    			break;
    		}
    	case "创建失败":
    		{
    			return '<span id="imgState' + row.H_ID + '" class="circle circle-stopped"></span><span id="txtState' + row.H_ID + '">创建失败</span>';
    			break;
    		}
    	case "销毁中":
    		{
    			return '<span id="imgState' + row.H_ID + '" class="circle circle-create"></span><span id="txtState' + row.H_ID + '">销毁中</span>';
    			break;
    		}
    	case "销毁失败":
    		{
    			return '<span id="imgState' + row.H_ID + '" class="circle circle-stopped"></span><span id="txtState' + row.H_ID + '">销毁失败</span>';
    			break;
    		}
        case "创建镜像中":
            {
                return '<span id="imgState' + row.H_ID + '" class="circle circle-stopped"></span><span id="txtState' + row.H_ID + '">创建镜像中</span>';
                break;
            }
    	default:
    		{
    			return '<span id="imgState' + row.H_ID + '" class="circle circle-create"></span>-';
    		}
    	}
    }

    //返回操作系统

    function formatter_operateSystem(value, row, index) {
    	if (value != null) {
    		return value;
    	} else {
    		return "-";
    	}
    }
    //返回网络

    function formatter_vxnets(value, row, index) {
    	var val;
    	if (value != null) {
    		return value;
    	} else {
    		val = "-";
    	}
    	return val;
    }

    function formatter_eip(value, row, index) {
    	var val;
    	if (value != null) {
    		return value;
    	} else {
    		val = "-";
    	}
    	return val;
    }

    //返回ip

    function formatter_ip(value, row, index) {
    	if (value != null) {
    		return value;
    	} else {
    		return "-";
    	}
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

    function getRowsID(type) {
    	var idlist = '';
    	$("input[name='btSelectItem']:checkbox").each(function() {
    		if ($(this)[0].checked == true) {
    			//alert($(this).val());
    			var id = $(this).parent().parent().attr('data-uniqueid');
    			var row = $('#table').bootstrapTable('getRowByUniqueId', id);
    			if (row.H_Status != '') {
    				if (type == 'name') {
    					idlist += row.H_Name + ',';
    				} else if (type == "id") {
    					idlist += row.H_ID + ',';
    				} else {
    					idlist += row.H_Code + ',';
    				}
    			}
    		}
    	});
    	return idlist;
    }

    /**
     * [ajaxFun 主机操作]
     */
    function ajaxFun(code, method, id) {
    	$('#modal').modal("hide");
    	$("#disk-manage").modal("hide");
        var tot = $('#table').bootstrapTable('getSelections');
        var url = "/console/ajax/network/hosts/ajaxHosts";
    	
        if (method == "ecs_start") {
    		heartbeat(0, id);
    	} else if (method == "ecs_stop"||method=="ecs_force_stop") {
    		heartbeat(1, id);
    	} else if (method == "ecs_reboot") {
    		heartbeat(2, id);
    	} else if (method == "ecs_delete") {
            // $.each(tot, function(i,val){
            //     $('#table').bootstrapTable('removeByUniqueId',val.H_ID);
            // });
    	}
        
    	if (id != undefined) {
    		$.ajax({
    			type: "post",
                url: url,
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
                        //$('#modal').removeBackdrop();
                        //showModal('提示',  'icon-exclamation-sign',data.Message,'', '',0);
                        //$('#btnEsc').html('关闭');
    				}
    				refreshTable();
    			}
    		});
    	} else {
    		$.ajax({
    				type: "post",
                    url: url,
    				async: true,
    				timeout: 9999,
    				data: {
    					method: method,
    					table: tot,
    					isEach: "true"
    				},
    				//dataType:'json',
    				success: function(data) {
    					data = $.parseJSON(data);
    					if (data.Code != "0") {
    						//alert(data.Message);
                            layer.alert(data.Message);
                            //showModal('提示',  'icon-exclamation-sign',data.Message,'', '',0);
                            $('#btnEsc').html('关闭');
    					}
    					refreshTable();
    				}
    		});
    	}
    }
    /**
     * [refreshTable 刷新列表]
     */
    function refreshTable() {
    	var search = $("#txtsearch").val();
        var search_biz = $('#txtsearchbiz').val();
    	var class_code = $("#agent").attr('val');
    	var class_code2 = $("#agent_t").attr('val');
    	var vpc_code = $("#vpcCode").attr('val');
        var list_url = '';
        if(typeof(search_biz) == 'undefined'){//判断当前js加载在哪个列表页，分别设置列表url
            list_url = "/console/ajax/network/hosts/lists?search=" + search + '&class_code=' + class_code + '&class_code2=' + class_code2+'&department_id='+$("#txtdeparmetId").val() + '&vpc_code=' + vpc_code;
        }else{
            list_url = "/console/ajax/business/hosts/lists?search_biz=" + search_biz +"&search=" + search + '&department_id='+$("#txtdeparmetId").val() + '&vpc_code=' + vpc_code;
        }
    	$('#table').bootstrapTable('refresh', {
            url :list_url
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

   
    function local_two(class_code2, agent_name, class_code) {
    	var search = $("#txtsearch").val();
    	$('#agent_t').html(agent_name);
    	$('#agent_t').attr('val', class_code2);
    	refreshTable();
    }
    //选择vpc
    function selectVpc(id, code, name) {
    	var search = $("#txtsearch").val();
    	$('#vpcCode').html(name);
    	$('#vpcCode').attr('val', code);
    	refreshTable();
    }

    //扩容系统盘

	function  btnsysDisks(obj) {
        var $obj = $(obj);
        $obj.prop('disabled', true);

        size = $("#syssize").val();
        ecsCode = $("#ecsCode").val();
        ecsId = $("#ecsId").val();

        $.ajax({
            type: "post",
            url:"/console/ajax/network/disks/ajaxSysDisks",
            async: true,
            timeout: 9999999,
            data: {
                size: size,
                ecsCode: ecsCode,
                ecsId:ecsId
            },
            success: function(data) {
                data = $.parseJSON(data);
                if(data.Code != 0){
                    layer.msg(data.Message);
                }
                $('#sysdisk-manage').modal("hide");
                $obj.prop('disabled', false);
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
    	istrue = true

    	var a = /^[1-9]\d*0$/;
    	if (a.test(size) == false || size < 10 || size > 1000) {
    		$("#amount").val(10);
    	}
    	if (name == "") {
    		$('#name-warning').html('硬盘名称不能为空');
    		$obj.prop('disabled', false);
    		return false;
    	}

    	//$.get("/console/ajax/network/hosts/getDisksCount/?code=" + instanceCode, function(data) {
    	//	$.getJSON("/console/ajax/network/hosts/getDisksLimit", function(data2) {
    	//		if (Number(data) >= data2) {
    	//			alert('主机最多挂载' + data2 + '个硬盘');
    	//			istrue = false;
    	//		}
    	//	})
    	//})

		$.getJSON("/console/home/getUserLimit", function(data) {

			if (data.disks_used >= data.data.disks_bugedt) {
				alert('配额不足 \r\n 磁盘个数配额：' + data.data.disks_bugedt);
				istrue = false;
			}

		});

    	$.getJSON("/console/home/getUserLimit", function(data) {
            
    		//if (Number(size) + data.disks_used > data.data.disks_bugedt) {
    		//	alert("配额不足 \r\n 磁盘 配额：" + data.data.disks_bugedt + " 已使用：" + data.disks_used);
    		//	istrue = false;
    		//}
			if (Number(size) > data.data.disks_cap_bugedt) {
				alert("配额不足 \r\n 磁盘容量配额：" + data.data.disks_cap_bugedt );
				istrue = false;
			}

    	});
    	if (!istrue) {
    		$obj.prop('disabled', false);
    		return false
    	}
    	$.ajaxSettings.async = true;
    	if (idList != null && idList != '') {
    		method = "volume_attach"; //挂载
    		volumeCode = idList;
    	} else {
    		method = "volume_add";
    		isFusion = $("#txtisFusion").val();
    	}
    	$.ajax({
    		type: "post",
            url:"/console/ajax/network/disks/ajaxDisks",
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
    				alert(data.Message);
    			} else {
    				if (idList != null && idList != '') {
    					//showModal('提示', '添加硬盘成功','', 'hidenModal()');
    				} else {
    					//showModal('提示', '挂载硬盘成功','', 'hidenModal()');
    				}
    			}
    			$('#disk-manage').modal("hide");
    			$obj.prop('disabled', false);
    		}
    	});
    }
    /**
     * [notifyCallBack 异步消息执行完成回调函数]
     */
    function notifyCallBack(value) {
        var search = $("#txtsearch").val();
        var search_biz = $('#txtsearchbiz').val();
        var class_code = $("#agent").attr('val');
        var class_code2 = $("#agent_t").attr('val');
        var list_url = '';
        if(typeof(search_biz) == 'undefined'){//判断当前js加载在哪个列表页，分别设置列表url
            list_url = "/console/ajax/network/hosts/lists?search=" + search + '&class_code=' + class_code + '&class_code2=' + class_code2+'&department_id='+$("#txtdeparmetId").val();
        }else{
            list_url = "/console/ajax/business/hosts/lists?search_biz=" + search_biz +"&search=" + search + '&department_id='+$("#txtdeparmetId").val();
        }
    	if (value.MsgType == "success" || value.MsgType == "error") {
    		if (value.Data.method == "ecs_del" || value.Data.method == "ecs_add" || value.Data.method == "ecs_start" || value.Data.method == "ecs_stop" || value.Data.method == "ecs" || value.Data.method =="ecs_restart" || value.Data.method =="eip_unbind" || value.Data.method =="On_sys_disk_resize") {
    			refreshTable();
    		}else if(value.Data.method == "volume_detach"){
                showModal('提示', 'icon-exclamation-sign', '解绑硬盘成功', '', 'hidenModal()');
            }
    	}
    }

    function webadmin(id) {
    	var row = $('#table').bootstrapTable('getRowByUniqueId', id);
    	$("#modal").modal("hide");
    	$.ajax({
    		type: "post",
            url:"/console/ajax/network/hosts/webadmin",
    		async: true,
    		data: {
    			method: "ecs_up_vnc_password",
    			instanceCode: row.H_Code
    		},
    		success: function(data) {
    			data = $.parseJSON(data);
    			if (data.Code != 0) {
    				alert(data.Message);
    			} else {
    				showModal('提示', 'icon-question-sign', '初始化已完成，是否立即重启？', '重启后生效', 'ajaxFun(getRowsID(),\'ecs_reboot\')');
    				$("#btnModel_ok").html("立即重启");
    				$("#btnEsc").html("稍后重启");
    			}
    		}
    	});
    }

    function is_login(id) {
    	var url;
    	var row = $('#table').bootstrapTable('getRowByUniqueId', id);
    	if (row.D_Vnc_password != null && row.D_Vnc_password != "") {
    		url = "/console/network/webConsole/" + row.H_Code;
    		window.open(url);
    	} else {
    		showModal('提示', 'icon-question-sign', '当前是第一次操作,是否进行初始化操作', '', 'webadmin(\'' + id + '\')');
    	}
    }

    // function departmentlist(id,name){
    //     $("#txtdeparmetId").val(id);
    // 	$("#deparmets").html(name);
    //     $.ajax({

    //        type: "POST",
    //        url: "/console/console/switchDepartment",
    //        data: {
    //             create_department_id: id
    //         },
    //        success: function(rep){
    //             response = $.parseJSON(rep);
    //             if(response.code == '0'){
    //                 $("#create_department_name").val(response.department_name);
    //                 $("#create_department_id").children('option').removeAttr('selected');
    //                 $("#create_department_id").children('option[value="'+id+'"]').attr('selected','selected');
    //             }else{
    //                 alert( "切换租户失败！" );
    //             }
    //        }
    //     });
    // 	refreshTable()
    // }

    // function switchDepartment(){
    //     $.ajax({
    //        type: "POST",
    //        url: "/console/console/isCurrentDepartment",
    //        data: {},
    //        success: function(rep){
    //             response = $.parseJSON(rep);
    //             if(response.code == '0'){
    //                window.location.href="/console/network/add/hosts";
    //             }else{
    //                 $('#modal-admin-add').modal('show');
    //             }
    //        }
    //     });
    // }