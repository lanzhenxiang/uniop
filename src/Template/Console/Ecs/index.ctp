    <!--  服务器  列表 -->

    <div class="wrap-nav-right wrap-nav-right-left">
       <div class="wrap-manage">
    		<div class="top">
    			<span class="title">服务器列表</span>
    			<div class="callback-info pull-right text-success"><i class="icon-ok"></i>&nbsp;操作成功</div>
    			<div id="maindiv-alert"></div>
    		</div>
    		<div class="center clearfix">
				<input name="delUrl" type="hidden" id="delUrl" value="<?=$this->Url->build(['prefix'=>'console','controller'=>'ecs','action'=>'del']); ?>" />
    		<button class="btn btn-addition" onclick="refreshTable();">
    		  <i class="icon-refresh"></i>&nbsp;&nbsp;刷新
    		</button>
				<a class="btn btn-addition" href="<?=$this->Url->build(['prefix'=>'console','controller'=>'ecs','action'=>'add']); ?>">
					<i class="icon-plus"></i>&nbsp;&nbsp;
					添加</a>
				<a class="btn btn-addition" id="btnImport" href="<?=$this->Url->build(['prefix'=>'console','controller'=>'ecs','action'=>'importExcel']); ?>">
					<i class="icon-plus"></i>&nbsp;&nbsp;导入信息
				</a>
				<a class="btn btn-addition" id="btnExport" href="<?=$this->Url->build(['prefix'=>'console','controller'=>'ecs','action'=>'exportExcel']); ?>">
					<i class="icon-file-alt"></i>&nbsp;&nbsp;导出信息
				</a>
				<button class="btn btn-default" id="btnDel" disabled>
					<i class="icon-remove"></i>&nbsp;&nbsp;删除
				</button>
				<button class="btn btn-default" id="btnPrint" disabled>
					<i class="icon-print "></i>&nbsp;&nbsp;批量二维码打印
				</button>
    			<div class="pull-right">

    			<span class="search">
    			    <input type="text" id="txtsearch" name="search" placeholder="搜索">
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
    	 data-url="<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ecs', 'action' => 'lists']); ?>"
    	 data-unique-id="H_ID">
    	 <thead>
    	   <tr>
    		<th data-checkbox="true"></th>
    		<th data-field="id">Id</th>
    		<th data-field="assets_no" data-formatter="formatter_code">资产编号</th>
    		<th data-field="SN" >SN</th>
    		<th data-field="manufacturer">品牌型号</th>
    		<th data-field="IP">IP地址</th>
    		<th data-field="hardware_assets_ec.EIP" >带外IP</th>
            <th data-field="status" data-formatter="formatter_status">运行情况</th>
    		<th data-field="location">位置</th>
    		<th data-field="cabinet_no">机架编号</th>
    		<th data-field="department">部门</th>
    		<th data-field="manager">所属人员</th>
			   <th data-field="buy_date">购买日期</th>
			   <th data-field="warranty">是否保修</th>
			   <th data-field="created_at">录入时间</th>
    	  </tr>
    	</thead>
      </table>
    </div>
    <!-- 图表 -->
    </div>
    </div>
    <div id="maindiv"></div>
<?= $this->Html->script(['hardware/assets']); ?>
<script type="text/javascript">
    /**
     * [refreshTable 刷新列表]
     */
    function refreshTable() {
        var search = $("#txtsearch").val();
        $('#table').bootstrapTable('refresh', {
            url :"<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ecs', 'action' => 'lists']); ?>?search="+search
        });
    }
    function formatter_code(value,rows) {
		return "<a href='/console/ecs/detail/"+value+"'>"+value+"</a>";
    }
</script>
<?php  $this->end();?>

