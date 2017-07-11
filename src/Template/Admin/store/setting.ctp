<div class="content-body storage-volume">
   <div class="wrap-manage">
		<div class="top">
			<span class="title">存储卷 - 访问设置</span>
			<!-- <button class="btn btn-default" id="bntSetting" onclick="saveFics();">保存</button> -->
			<a href="<?= $this->Url->build(['controller'=>'store','action'=>'index',$_id]); ?>" class="btn btn-addition">返回列表</a>
            <button class="btn btn-default" id="bntAddUser" onclick="addUser();">新建</button>
            <button class="btn btn-addition" onclick="refreshTable();">
              <i class="icon-refresh"></i>&nbsp;&nbsp;刷新
            </button>
		</div>


		<div class="center clearfix">
			<div class="">
				<label class="infor">
					
				</label>
                <!--tab-->
                <div class="modal-title-list margint20">
				<ul class="clearfix">
                    <li class="active" limit="1">完全控制</li>
					<li class="" limit="4">读/写权限</li>
					<li class="" limit="0">只读权限</li>
				</ul>
                </div>
			</div>
			<div class="images-checkbox">
                <div class="bot" style="padding:0;">
                    <table id="table_1" class="table table-striped" data-toggle="table"
                                         data-pagination="true"
                                         data-side-pagination="server"
                                         data-locale="zh-CN"
                                         data-click-to-select="true"
                                         data-url="<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'vpcStore', 'settinglist','?'=>['t'=>'1','vol_name'=>$_vol_name,'id'=>$_id]]); ?>"
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
					<table id="table_4" class="table table-striped" data-toggle="table"
                                         data-pagination="true"
                                         data-side-pagination="server"
                                         data-locale="zh-CN"
                                         data-click-to-select="true"
                                         data-url="<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'vpcStore', 'settinglist','?'=>['t'=>'4','vol_name'=>$_vol_name,'id'=>$_id]]); ?>"
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
          <table id="table_0" class="table table-striped" data-toggle="table"
                     data-pagination="true"
                     data-side-pagination="server"
                     data-locale="zh-CN"
                     data-click-to-select="true"
                     data-url="<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'vpcStore', 'settinglist','?'=>['t'=>'0','vol_name'=>$_vol_name,'id'=>$_id]]); ?>"
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
    	  <input id="modal-add-vol_id" name="vol_id" value="<?= $_vid ?>" type="hidden" />
          <input id="modal-add-vpc_id" name="vpcId" value="<?= $_id ?>" type="hidden" />
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
<?=$this->Html->script(['adminjs.js','bootstrap-datetimepicker.js','validator.bootstrap.js','jquery.cookie.js','jquery.mouse-content.js']); ?>
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
//    tab
    $(".modal-title-list").on("click", "li", function(){
        $(this).addClass("active");
        $(this).siblings().removeClass("active");
        var index=$(this).index();
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
                url: "<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network']); ?>/<?php echo "vpcStore" ?>/<?php echo "addUser" ?>",
                async: true,
                data: $('#modal-add-form').serialize(),
                method: 'post',
                dataType: 'json',
                success: function(e) {
                    refreshTable();
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
        $.ajax({
            url: "<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network']); ?>/<?php echo "vpcStore" ?>/<?php echo "delUser" ?>",
            data: {
                id:row.userid,
            },
            method: 'post',
            dataType: 'json',
            success: function(e) {
                refreshTable();
            }
        });
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
    function _getRandomStringByChars(len,$chars){
        var maxPos = $chars.length;
        var pwd = '';
        for (i = 0; i < len; i++) {
            pwd += $chars.charAt(Math.floor(Math.random() * maxPos));
        }
        return pwd;
    }

    function addUser(){
        $('#modal-add').one('show.bs.modal', function() {
        });
        $('#modal-add').modal("show");
    }

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
//    刷新
function refreshTable() {
    $("#table_0").bootstrapTable("refresh", {
        url:"<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'vpcStore', 'settinglist']); ?>",
        query:{
            t:0,
            id:"<?= $_id ?>",
            vol_name:"<?= $_vol_name ?>"
        }
    });
    $("#table_1").bootstrapTable("refresh", {
        url:"<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'vpcStore', 'settinglist']); ?>",
        query:{
            t:1,
            id:"<?= $_id ?>",
            vol_name:"<?= $_vol_name ?>"
        }
    });
    $("#table_4").bootstrapTable("refresh", {
        url:"<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'vpcStore', 'settinglist']); ?>",
        query:{
            t:4,
            id:"<?= $_id ?>",
            vol_name:"<?= $_vol_name ?>"
        }
    });
}
</script>
<?php
$this -> end();
?>