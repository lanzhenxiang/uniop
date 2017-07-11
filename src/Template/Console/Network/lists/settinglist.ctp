

<style>
	.wrap-nav-right .wrap-manage .center input{
		height:18px;
		line-height:18px;
	}
</style>
<div class="wrap-nav-right wrap-nav-right-left storage-volume">
   <div class="wrap-manage">
		<div class="top">
			<span class="title">存储卷 - 访问设置</span>
			<!-- <button class="btn btn-default" id="bntSetting" onclick="saveFics();">保存</button> -->
			<a href="<?= $this->Url->build(['controller'=>'network','action'=>'lists','fics']); ?>" class="btn btn-addition">返回列表</a>
            <button class="btn btn-default" id="bntAddUser" onclick="addUser();">新建</button>
            <button class="btn btn-addition" onclick="refreshTable();">
              <i class="icon-refresh"></i>&nbsp;&nbsp;刷新
            </button>
		</div>


		<div class="center clearfix">
			<div class="">
				<label>
					<?= $_display_note  ?>
				</label>
                <!--tab-->
                <div class="modal-title-list margint20">
				<ul class="clearfix">
                    <li class="active" limit="1">完全控制</li>
					<!--<li class="" limit="4">读/写权限</li>-->
					<!--<li class="" limit="0">只读权限</li>-->
				</ul>
                </div>
			</div>
			<div class="images-checkbox">
                <div class="bot" style="padding:0;">
                    <table id="table_1" data-toggle="table"
                                         data-pagination="true"
                                         data-side-pagination="server"
                                         data-locale="zh-CN"
                                         data-click-to-select="true"
                                         data-url="<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'fics', 'settinglist','?'=>['department_id'=>$_default["id"],'t'=>'1','vol_name'=>$_template["vol_name"]]]); ?>"
                                         data-unique-id="id" class="table-bordered">
                        <thead>
                        <tr>
                            <th data-field="id">id</th>
                            <th data-field="name">登录名</th>
                            <th data-field="password">密码</th>
                        </tr>
                        </thead>
                    </table>
                </div>
				<div class="bot" style="display:none;padding:0;">
					<table id="table_4" data-toggle="table"
                                         data-pagination="true"
                                         data-side-pagination="server"
                                         data-locale="zh-CN"
                                         data-click-to-select="true"
                                         data-url="<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'fics', 'settinglist','?'=>['department_id'=>$_default["id"],'t'=>'4','vol_name'=>$_template["vol_name"]]]); ?>"
                                         data-unique-id="id" class="table-bordered">
	 					<thead>
						<tr>
							<th data-field="id">id</th>
							<th data-field="name">登录名</th>
							<th data-field="password">密码</th>
						</tr>
						</thead>
					</table>
				</div>
				<div class="bot" style="display:none;padding: 0">
          <table id="table_0" data-toggle="table"
                     data-pagination="true"
                     data-side-pagination="server"
                     data-locale="zh-CN"
                     data-click-to-select="true"
                     data-url="<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'fics', 'settinglist','?'=>['department_id'=>$_default["id"],'t'=>'0','vol_name'=>$_template["vol_name"]]]); ?>"
                     data-unique-id="id" class="table-bordered">
	 <thead>
						<tr>
                            <th data-field="id">id</th>
							<th data-field="name">登录名</th>
                            <th data-field="password">密码</th>
						</tr>
						</thead>
					</table>

				</div>
			</div>
		</div>
	</div>
</div>

<!-- 右键弹框 -->
<div class="context-menu" id="context-menu">
     <ul>
        <?php //if (in_array('ccf_host_change', $this->Session->read('Auth.User.popedomname'))) { ?>
           <!-- <li id="modify"><a href="javascript:void(0);"><i class="icon-pencil"></i> 修改</a></li> -->
        <?php //} ?>
        <?php if (in_array('ccf_host_delete', $this->Session->read('Auth.User.popedomname'))) { ?>
            <li id="del"><a href="javascript:void(0);"><i class="icon-trash"></i> 删除</a></li>
        <?php } ?>
    </ul>
  </div>

<!-- 修改 -->
      <div class="modal fade storage-volume" id="modal-add" tabindex="-1" role="dialog">
       <div class="modal-dialog" role="document">
    	<div class="modal-content">
    	 <div class="modal-header">
    	  <button type="button" class="close" data-dismiss="modal"
    	  aria-label="Close">
    	  <span aria-hidden="true">&times;</span>
    	</button>
    	<h5 class="modal-title">新建用户</h5>
      </div>
      <form id="modal-add-form" action="" method="post">
    	<div class="modal-body">
    	  <div class="modal-form-group form-group">
    		<label for="modal-add-name">用户名:</label>
    		<!--<div>-->
    		  <input id="modal-add-name" class="form-control" name="name" type="text" maxlength="32" />
    		<!--</div>-->
    	  </div>
    	  <input id="modal-add-pwd" class="form-control" name="password" type="hidden" maxlength="32" />
    	  <input id="modal-add-vol_id" name="vol_id" value="<?= $_id ?>" type="hidden" />
    	  <input id="modal-add-type" name="type" value="1" type="hidden" />
    	</div>
    	<div class="modal-footer">
    	 <button id="sumbiter" type="submit" class="btn btn-primary">确认</button>
    	 <button id="reseter" type="button" class="btn btn-danger"
    	 data-dismiss="modal">取消</button>
       </div>
     </form>
    </div>
    </div>
    </div>


<div id="maindiv"></div>
<?=$this->Html->script(['adminjs.js','bootstrap-datetimepicker.js','validator.bootstrap.js','jquery.cookie.js']); ?>
<?php $this -> start('script_last'); ?>
<script type="text/javascript">
$('#table_4').contextMenu('context-menu', {
    bindings: {
        'del': function(event) {
            var uniqueId = $(event).attr('data-uniqueid');
            var row = $('#table_4').bootstrapTable('getRowByUniqueId', uniqueId);
            //获取数据
            showModal('提示', 'icon-question-sign', '确认删除所选用户吗？', row.name, 'delUser(\'table_4\',\'' + row.id + '\')');
        }
    }

});
$('#table_1').contextMenu('context-menu', {
    bindings: {
        'del': function(event) {
            var uniqueId = $(event).attr('data-uniqueid');
            var row = $('#table_1').bootstrapTable('getRowByUniqueId', uniqueId);
            //获取数据
            showModal('提示', 'icon-question-sign', '确认删除所选用户吗？', row.name, 'delUser(\'table_1\',\'' + row.id + '\')');
        }
    }

});
$('#table_0').contextMenu('context-menu', {
    bindings: {
        'del': function(event) {
            var uniqueId = $(event).attr('data-uniqueid');
            var row = $('#table_0').bootstrapTable('getRowByUniqueId', uniqueId);
            //获取数据
            showModal('提示', 'icon-question-sign', '确认删除所选用户吗？', row.name, 'delUser(\'table_0\',\'' + row.id + '\')');
        }
    }

});

$(".modal-title-list").on("click", "li", function(){
    $(this).addClass("active");
    $(this).siblings().removeClass("active");
    var index=$(this).index();
    // console.log($(index).attr("limit"));
    //设置$(this).attr("limit")
    $("#modal-add-type").val($(this).attr("limit"));

    $(".service-content-navi li").removeClass('active')
    $(this).addClass('active');
    $(".images-checkbox .bot").hide();
    $(".images-checkbox .bot").eq(index).show();
});

//验证用户名密码
$('#modal-add-form').bootstrapValidator({
    submitButtons: 'button[type="submit"]',
    submitHandler: function(validator, form, submitButton){
        $('#modal-add').modal("hide");
        var pwd = _getRandomString(3)+'+';
        $("#modal-add-pwd").val(pwd);
        //ajax提交页面
        $.ajax({
            url: "<?= $this -> Url -> build(['controller' => 'ajax', 'action' => 'network']); ?>/<?php echo "fics" ?>/<?php echo "addUser" ?>",
            async: true,
            data: $('#modal-add-form').serialize(),
            method: 'post',
            dataType: 'json',
            success: function(e) {
            }
        });
    },
    fields : {
        name: {
            validators: {
                notEmpty: {
                    message: '用户名不能为空'
                },
                regexp: {
                    regexp: /^[^\\"/\[\]<>+: ;,?*|=@\s]+$/,
                    message: '用户名不能包含特殊字符'
                },
                stringLength: {
                    min: 8,
                    max: 32,
                    message: '请保持在8-32位'
                },remote: {
                    message: '用户名已被使用',
                    url: '<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'fics', 'checkVolUser']) ?>',
                    data: {
                        type: 'name'
                    }
                }
            }
        }
    }
});
//自定义验证
regControl()
function regControl(){
    var small=function(content,par){
       var node= '<small class="help-block definErr">'+content+'</small>';
        $(par).after(node);
        $(par).parent(".modal-form-group").addClass("has-error")
    }
    $("#modal-add-name").keyup(function(){
        var $name=$(this).val();
        var nameReg= /[\.]$/;
        if(nameReg.test($name)){
          small("用户名不能以.结尾","#modal-add-name");
        }else{
            $(this).siblings(".definErr").hide()
        }
        $(this).siblings("help-block").hide();
    });
}

function delUser(tab,uniqueId){
    $('#modal').modal("hide");
    var row = $('#'+tab).bootstrapTable('getRowByUniqueId', uniqueId);
    // console.log(row);
    $.ajax({
        url: "<?= $this -> Url -> build(['controller' => 'ajax', 'action' => 'network']); ?>/<?php echo "fics" ?>/<?php echo "delUser" ?>",
        data: {
            id:row.userid,
            storeCode:row.store_code,
            regionCode:row.region_code,
            storeType:row.storetype,
            volume_name:row.vol_name,
            username:row.name
        },
        method: 'post',
        dataType: 'json',
        success: function(e) {
        }
    });
}

function departmentlist(id,name){
	var fics_id="<?= $_id ?>";
	$("#deparmets_0").html(name);
	$("#deparmets_1").html(name);
	var department_id = id;
	var url;
	$("div[id*='table']").bootstrapTable('refresh', {
		url: "<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'fics', 'settinglist']); ?>",
		query: {
			department_id: department_id
		}
	});
	url = "<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'fics', 'settinglist']); ?>?department_id=" + department_id;
	$("div[id*='table']").attr('data-url', url);
	setTimeout(function() {checkhtml();}, 100);
}
function checkhtml(){
	var template_id = $("#btntemplate").val();
	var basic_id = "<?= $_id ?>";
	var index;
	$(".service-content-navi li").each(function(){
		if($(this).hasClass("active")==true){
			index=$(this).index();
		}
	});
	$.ajax({
		type: "post",
		url: "<?= $this ->Url ->build(['prefix' =>'console', 'controller' =>'ajax', 'action' =>'network', 'fics', 'cheklist']); ?>",
		async: true,
		data: {
			basic_id:basic_id,
			template_id:template_id
		},
		success: function(data) {
			data = $.parseJSON(data);
			$.each(data,function(i,v){
				$('#table_'+template_id).bootstrapTable("checkBy",{field:"id", values:[parseInt(v.account_id)]})
			});
		}
	});
}

function _getRandomStringByChars(len,$chars){
    var maxPos = $chars.length;
    var pwd = '';
    for (i = 0; i < len; i++) {
        pwd += $chars.charAt(Math.floor(Math.random() * maxPos));
    }
    return pwd;
}



//随机生成字符串
function _getRandomString(len) {
    var pwd = "";
    //生成大写字符串
    var $Dchars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    pwd += _getRandomStringByChars(len,$Dchars)

    //小写字符串
    var $Xchars = 'abcdefhijklmnoprstuvwxyz';
    pwd += _getRandomStringByChars(len,$Xchars)

    //数字字符串
    var $Nchars = '0123456789';
    pwd += _getRandomStringByChars(len,$Nchars)
    return pwd;  
} 

function saveFics(){
	var id = "<?= $_id ?>";
	var template_id = $("#btntemplate").val();
	var rows = $('#table_'+template_id).bootstrapTable('getSelections');
	var account="",account1="";
	var type = 0;
	rows.forEach(function(e) {
		account += e.id+",";
	});
	var rows1 = $('#table_'+template_id).bootstrapTable('getData');
	rows1.forEach(function(e) {
		account1 += e.id+",";
	});
	$.ajax({
		type:"post",
		url: "<?= $this ->Url ->build(['prefix' =>'console', 'controller' =>'ajax', 'action' =>'network', 'fics', 'savefics']); ?>",
		data:{
			id:id,
			account:account,
			account1:account1,
			template_id:template_id,
		},
		success: function(data) {
 			data = $.parseJSON(data)
 			if(data.code=="0000"){
 				showModal("提示", 'icon-exclamation-sign',data.msg, "", "", "");
				$("#btnEsc").html("关闭");
				$("#btnMk").remove();
 			}
		}
	});
}

function addUser(){
	$('#modal-add').one('show.bs.modal', function() {
    });
	$('#modal-add').modal("show");
}

function refreshTable() {
    $('#table_0').bootstrapTable('refresh', {
        url: "<?= $this ->Url->build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'fics', 'settinglist']) ?>",
        query: {
             department_id: "<?= $_default["id"] ?>",
             t: 0,
             vol_name: "<?= $_template["vol_name"] ?>",
             department_id:$("#txtdeparmetId").val()
            }
        // ,'?'=>['department_id'=>$_default["id"],'t'=>'0','vol_name'=>$_template["vol_name"]]]); ?>"
    });
    $('#table_1').bootstrapTable('refresh', {
        url: "<?= $this ->Url->build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'fics', 'settinglist']) ?>",
        query: {
             department_id: "<?= $_default["id"] ?>",
             t: 1,
             vol_name: "<?= $_template["vol_name"] ?>",
             department_id:$("#txtdeparmetId").val()
            }
        // ,'?'=>['department_id'=>$_default["id"],'t'=>'0','vol_name'=>$_template["vol_name"]]]); ?>"
    });
    $('#table_4').bootstrapTable('refresh', {
        url: "<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'fics', 'settinglist']) ?>",
        query: {
             department_id: "<?= $_default["id"] ?>",
             t: 4,
             vol_name: "<?= $_template["vol_name"] ?>",
             department_id:$("#txtdeparmetId").val()
            }
        // ,'?'=>['department_id'=>$_default["id"],'t'=>'4','vol_name'=>$_template["vol_name"]]]); ?>"
    });
}

$('#table_5').on('page-change.bs.table', function (e, arg1, arg2) {
	setTimeout(function() {checkhtml();}, 100);
});
 $(".wrap-nav-right").addClass('wrap-nav-right-left');
//新建 右边固定框
var offsetTop = $(".theme-right").offset().top;
var width = $(".buy-theme").width() * 0.24;
$(window).scroll(
    function(){
        if($(document).scrollTop() > offsetTop - 60){
            var offsetLeft = $(".theme-right").offset().left;
            $(".theme-right").css({position:"fixed",top:"60px",left:offsetLeft,width:width});
        }else{
            $(".theme-right").css("position","static");
        }
    }
    );

function showModal(title, icon, content, content1, method, type) {
	$("#maindiv").empty();
	html = "";
	html += '<div class="modal fade" id="modal" tabindex="-1" role="dialog">';
	html += '<div class="modal-dialog" role="document">';
	html += '<div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
	html += '<h5 class="modal-title">' + title + '</h5>';
	html += '</div><div class="modal-body"><i class="' + icon + ' icon-color-gray text-primary"></i>&nbsp;&nbsp;' + content + '<span class="text-primary">' + content1 + '</span></div>';
	html += '<div class="modal-footer"><button id="btnMk" onclick="' + method + '" type="button" class="btn btn-primary">确认</button><button id="btnEsc" type="button" class="btn btn-danger" data-dismiss="modal">取消</button></div></div></div></div>';
	$("#maindiv").append(html);

	$('#modal').modal("show");
}

function notifyCallBack(value) {
    if (value.MsgType == "success" || value.MsgType == "error") {
        if (value.Data.method == "store_setRoot" || value.Data.method == "store_delRoot") {
            refreshTable();
        }
    }
}
</script>
<?php
$this -> end();
?>