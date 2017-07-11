<!-- 主页模板 -->
<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div id="maindiv-alert" class="content-list-page"></div>
    <div class="content-operate clearfix">
        <button class="btn btn-addition" onclick="refreshTable();">
		  <i class="icon-refresh"></i>&nbsp;&nbsp;刷新
		</button>
		<a class="btn btn-addition" href="<?= $this -> Url -> build(['controller' => 'GoodsVpc', 'action' => 'addstore',$_id]) ?>">
		<i class="icon-plus"></i>&nbsp;&nbsp;新建</a>
			<button class="btn btn-default" disabled id="bntSetting">
				访问设置
			</button>
			<button class="btn btn-default" disabled id="bntRelation">
				关联设备
			</button>
		</span>
			</div>
				<div class="bot ">
			<table id="table" class="table-striped" data-toggle="table"
	 data-pagination="true"
	 data-side-pagination="server"
	 data-locale="zh-CN"
	 data-click-to-select="true"
	 data-url="<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'vpcStore', 'lists','?'=>['id'=>$_id]]); ?>"
	 data-unique-id="vol_id">
	 <thead>
	   <tr>
		<th data-radio="true"></th>
		<th data-field="agent_name">部署区位</th>
		<th data-field="vol_type" data-formatter="formatter_type">品牌</th>
		<th data-field="store_name">存储名</th>
		<th data-field="vol_name">卷名</th>
		<th data-field="total_cap" data-formatter="formatter_tot">容量（GB）</th>
		 <th data-field="warn_cap" data-formatter="formatter_cap">告警水位（%）</th>
		 <th data-field="name">租户</th>
		 <th data-field="create_time" data-formatter=timestrap2date>创建时间</th>
	  </tr>
	</thead>
  </table>
</div>
		</div>
    </div>
</div>
<!-- 右键弹框 -->
<div class="context-menu" id="context-menu">
    <ul>
        <?php if (in_array('ccf_host_change', $this->Session->read('Auth.User.popedomname'))) { ?>
        <li id="modify"><a href="javascript:void(0);"><i class="icon-pencil"></i> 修改</a></li>
        <?php } ?>
        <?php if (in_array('ccf_host_delete', $this->Session->read('Auth.User.popedomname'))) { ?>
        <li id="del"><a href="javascript:void(0);"><i class="icon-trash"></i> 删除</a></li>
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
                        <label>卷名:</label>
                        <div>
                            <input id="modal-modify-name" name="vol_name" type="text" maxlength="15" />
                        </div>
                    </div>
                    <input id="modal-modify-id" name="vol_id" type="hidden" />
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
<div id="maindiv"></div>

<?=$this->Html->script(['adminjs.js']); ?>
<?= $this->start('script_last'); ?>
<?= $this->Html->script(['jquery.mouse-content.js']); ?>
<script type="text/javascript">
    //动态创建modal
    function showModal(title, icon, content, content1, method, type) {
        $("#maindiv").empty();
        html = "";
        html += '<div class="modal fade" id="modal" tabindex="-1" role="dialog">';
        html += '<div class="modal-dialog" role="document">';
        html += '<div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
        html += '<h5 class="modal-title">' + title + '</h5>';
        html += '</div><div class="modal-body"><i class="'+icon+' icon-color-gray text-primary"></i>&nbsp;&nbsp;' + content + '<span class="text-primary">' + content1 + '</span></div>';
        html += '<div class="modal-footer"><button id="btnModel_ok" onclick="' + method + '" type="button" class="btn btn-primary">确认</button><button type="button" class="btn btn-danger" id="btnEsc" data-dismiss="modal">取消</button></div></div></div></div>';
        $("#maindiv").append(html);
        if (type == 0) {
            $("#btnModel_ok").remove();
        }
        $('#modal').modal("show");
    }
//tit按钮点击
    $("#bntSetting").on('click', function() {
        var rows = $('#table').bootstrapTable('getSelections');
        window.location = "/admin/store/setting/"+rows[0].vol_id+"/"+rows[0].vpcId;
    });
    $("#bntRelation").on('click', function() {
        var rows = $('#table').bootstrapTable('getSelections');
        window.location = "/admin/store/storehosts/"+rows[0].vol_id+"/"+rows[0].vpcId;
    });
    //input 存在一个被选中状态
    $("#table").on('all.bs.table', function(e, row, $element) {
        if ($("tbody input:checked").length >= 1) {
            $(".content-operate .btn-default").attr('disabled', false);
        } else {
            $(".content-operate .btn-default").attr('disabled', true);
        }
    })
//右键
    $('#table').contextMenu('context-menu', {
        bindings: {
            'modify': function(event) {
                //获取数据
                index = $(event).attr('data-index');
                var uniqueId = $(event).attr('data-uniqueid');
                var row = $('#table').bootstrapTable('getRowByUniqueId', uniqueId);
                $('#modal-modify-name').val(row.vol_name);
                $('#modal-modify-id').val(row.vol_id);
                $('#modal-modify').one('show.bs.modal', function() {
                    $('#sumbiter').one('click', function() {
                        //ajax提交页面
                        $.ajax({
                            url: "<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'vpcStore', 'editstore']); ?>",
                            async: false,
                            data: $('#modal-modify-form').serialize(),
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
            },
            'del': function(event) {
                var uniqueId = $(event).attr('data-uniqueid');
                var row = $('#table').bootstrapTable('getRowByUniqueId', uniqueId);
                //获取数据
                showModal('提示', 'icon-question-sign', '确认删除所选存储卷吗？', row.vol_name, 'ajaxFun(\'' + row.vol_id + '\')');
            }
        }
    });

    function ajaxFun(id) {
        $('#modal').modal("hide");
        $.ajax({
            type: "post",
            url: "<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'vpcStore', 'deleteId']); ?>",
            async: true,
            data: {
                id:id
            },
            success: function(data) {
                data = $.parseJSON(data);
                if (data.code != "0000") {
                    alert(data.Message);
                }
                refreshTable();
            }
        });
    }

//    刷新
    function refreshTable() {
        $('#table').bootstrapTable('refresh', {
            url: "<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'vpcStore', 'lists','?'=>['id'=>$_id]]); ?>",
        });
    }

    function formatter_cap(value,row,index){
	if(value!=""&&value!="0"&&value!=undefined){
		return value+"%";
	}
	return "-";
}

function formatter_tot(value,row,index){
	if(value!=""&&value!="0"&&value!=undefined){
		return value+"GB";
	}
	return "-";
}

function formatter_type(value, row, index) {
	switch (value) {
    	case "oceanstor9k":
    		{
    			return '华为9000';
    			break;
    		}
    	case "fics":
    		{
    			return 'FICS共享存储';
    		}
    	}
}

function timestrap2date(value) {
	var now = new Date(parseInt(value) * 1000);
	return now.toLocaleString().replace(/年|月/g, "-").replace(/日/g, " ");
}
</script>
<?= $this->end() ?>