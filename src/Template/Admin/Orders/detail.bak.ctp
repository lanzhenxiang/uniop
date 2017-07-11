<style>
.order-crumb-info dl{
	margin-bottom: 0;
}
.order-crumb-info dt,
.order-crumb-info dd{
	display: inline-block;
	line-height: 35px;
}
.order-crumb-info dd{
	margin-right: 15px;
}
.order-flow{
	margin-bottom: 30px;
}
.order-flow li{
	position:relative;
	width:180px;
	float:left;
	margin-right: 5px;
	text-align: center;
}
.order-flow li:after{
	position:absolute;
	top:17px;
	left:0;
	content:"";
	width:100%;
	height:6px;
	background:#23BAB5;
}
.order-flow .order-flow-diff:after{
	background:#E4E4E4;
}	
.order-flow .order-flow-done{
	position:relative;
	z-index:3;
	width:38px;
	height:38px;
	line-height:38px;
	background-image:url(/images/order-done.gif);
	margin:0 auto;
	color:#fff;
}
.order-flow .order-flow-undo{
	position:relative;
	z-index:3;
	width:38px;
	height:38px;
	line-height:38px;
	background-color:#fff;
	background-image:url(/images/order-undo.png);
	margin:0 auto;
	color:#999;
}
.order-flow p{
	padding:3px;
}
.order-list > thead > tr > th{
	border:0;
	color:#333;
	background:#F5F5F5;
}
.order-list > tbody > tr > td{
	line-height: 45px;
}
.order-track{
	margin-bottom: 0;
}	
.order-track > tbody > tr > td{
	border:0;
	padding-top:8px;
	padding-bottom:6px;
}
.order-track > tfoot > tr > td{
	border-top: 0;
	padding-bottom: 2px; 
}
.order-track dl{
	margin-bottom: 0;
	display: inline-block;
}
.order-track dt,
.order-track dd{
	display: inline-block;
}
.order-track dd{
	margin-right: 12px;
}
.order-total h6{
	padding:3px 0;
}
.order-total h4{
	margin-top: 8px;
}
</style>
<div class="wrap-nav-right wrap-index-page">
	<div class="wrap-manage" style="padding:20px 30px">
		<div class="panel panel-default">
			<div class="order-crumb panel-body clearfix">
				<div class="order-crumb-info pull-left">
					<dl>
						<dt>订单号 : </dt>
						<dd>11777392160</dd>
						<dt>状态 : </dt>
						<dd class="text-success">等待财务人员审核</dd>
					</dl>
				</div>
				<div class="pull-right">
					<button class="btn btn-addition">订单打印</button>
					<button class="btn btn-addition" data-toggle="modal" data-target="#order-modal">通 过</button>
					<button class="btn btn-danger">退 回</button>
				</div>
			</div>
		</div>
		<div class="order-flow">
			<ul class="clearfix">
				<li>
					<div class="order-flow-done">
						1
					</div>
					<div class="text-center">
						<p>提交订单</p>
						<h6>2015 - 01 - 21</h6>
						<h6>14 : 25: 11</h6>
					</div>
				</li>
				<li>
					<div class="order-flow-done">
						2
					</div>
					<div class="text-center">
						<p>技术人员审核 (李乾星)</p>
					</div>
				</li>
				<li>
					<div class="order-flow-done">
						3
					</div>
					<div class="text-center">
						<p>财务人员审核 (陈强)</p>
					</div>
				</li>
				<li>
					<div class="order-flow-done">
						4
					</div>
					<div class="text-center">
						<p>部门主管审核 (刘邦国)</p>
					</div>
				</li>
				<li class="order-flow-diff">
					<div class="order-flow-undo">
						5
					</div>
					<div class="text-center">
						<p>完成</p>
					</div>
				</li>
			</ul>
		</div>
		<div class="panel panel-warning">
		    <div class="panel-heading">
		    	<h5 class="panel-title">订单跟踪</h5>
		    </div>
		  	<div class="panel-body">
		    	<table class="order-track table">
		    		<thead>
		    			<tr>
		    				<th>处理时间</th>
		    				<th>处理信息</th>
		    				<th>处理人</th>
		    			</tr>
		    		</thead>
		    		<tbody>
		    			<tr>
		    				<td>2016-01-05 10:22:25</td>
		    				<td>您提交了订单，请等待第三方卖家系统确认</td>
		    				<td>XX快递</td>
		    			</tr>
		    			<tr>
		    				<td>2016-01-05 10:22:25</td>
		    				<td>您提交了订单，请等待第三方卖家系统确认</td>
		    				<td>XX快递</td>
		    			</tr>
		    			<tr>
		    				<td>2016-01-05 10:22:25</td>
		    				<td>您提交了订单，请等待第三方卖家系统确认</td>
		    				<td>XX快递</td>
		    			</tr>
		    		</tbody>
		    		<tfoot>
		    			<tr>
		    				<td colspan="3">
		    					<dl>
		    						<dt>送货方式 : </dt>
		    						<dd>普通快递</dd>
		    						<dt>承运人 : </dt>
		    						<dd>申通快递</dd>
		    						<dt>承运人电话 : </dt>
		    						<dd>95543</dd>
		    						<dt>货运单号 : </dt>
		    						<dd>3303943420654</dd>
		    					</dl>
		    					<a href="">点击查询</a>
		    				</td>
		    			</tr>
		    		</tfoot>
		    	</table>
		  	</div>
		</div>
		<div class="panel panel-default">
		  	<div class="panel-heading">
		  		<h5 class="panel-title">订单信息</h5>
		  	</div>
		  	<div class="panel-body">
		  		<h6 style="padding:8px 0">商品清单</h6>
		    	<table class="order-list table table-bordered">
		    		<thead>
		    			<tr>
		    				<th>商品编号</th>
		    				<th>商品图片</th>
		    				<th>商品名称</th>
		    				<th>价格</th>
		    				<th>优惠券</th>
		    				<th>数量</th>
		    				<th>操作</th>
		    			</tr>
		    		</thead>
		    		<tbody>
		    			<tr>
		    				<td>100385765</td>
		    				<td><img src="/images/nova10-detail.png" width="45" height="45"></td>
		    				<td>索贝 非编模板 AAA-8053</td>
		    				<td>$50.00</td>
		    				<td>1</td>
		    				<td>1</td>
		    				<td><a href="">修改</a></td>
		    			</tr>
		    			<tr>
		    				<td>100385765</td>
		    				<td><img src="/images/product3.png" width="45" height="45"></td>
		    				<td>索贝 非编模板 AAA-8053</td>
		    				<td>$50.00</td>
		    				<td>1</td>
		    				<td>1</td>
		    				<td><a href="">修改</a></td>
		    			</tr>
		    		</tbody>
		    	</table>
		  	</div>
		  	<div class="panel-footer clearfix">
		  		<div class="order-total pull-right text-right">
			  		<h6>总商品金额 : ￥51.90</h6>
			  		<h6>- 返现 : ￥0.00</h6>
			  		<h6>- 商品优惠 : ￥5.00 </h6>
			  		<h6>+ 运费 : ￥0.00</h6>
			  		<h4>应付总额 : <span class="text-success">￥46.90</span></h4>
			  	</div>
		  	</div>
		</div>
	</div>	
</div>
<div class="modal fade" id="order-modal" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">通过审核</h4>
      </div>
      <div class="modal-body">
        <i class="icon-question-sign text-primary"></i>&nbsp;&nbsp;<span class="text-primary">确认通过审核?</span>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary">通 过</button>
        <button type="button" class="btn btn-negative" data-dismiss="modal">取 消</button>
      </div>
    </div>
  </div>
</div>	