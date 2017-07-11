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
		display: none;
		position: relative;
		width: 89%;
		margin-left: auto;
		margin-right: auto;
		margin-bottom: 30px;
	}
	.order-flow li{
		position:relative;
		float:left;
	}
	.order-flow li:after{
		position:absolute;
		top:17px;
		left:0;
		content:"";
		width:99%;
		height:6px;
		background:#23BAB5;
	}
	.order-flow .order-flow-diff:after{
		background:#E4E4E4;
	}
	.order-flow .order-flow-done{
		position: relative;
		z-index: 3;
		width: 38px;
		height: 38px;
		line-height: 38px;
		text-align: center;
		background-image: url(/images/order-done.gif);
		color: #fff;
	}
	.order-flow .order-flow-undo{
		position: relative;
		z-index: 3;
		width: 38px;
		height: 38px;
		line-height: 38px;
		text-align: center;
		background-color: #fff;
		background-image: url(/images/order-undo.png);
		color: #999;
	}
	.order-flow .order-flow-first{
		position: relative;
		width: 80px;
		margin-left: -40px;
		z-index: 9;
	}
	.order-flow .order-flow-last{
		position: relative;
		width: 80px;
		margin-right: -40px;
		z-index: 8;
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
	.order-num-edit{
		width:45px;
		line-height: 18px;
		border:1px solid #d2d2d2;
	}
</style>
<div class="wrap-nav-right wrap-index-page">

	<div class="wrap-manage" style="padding:20px 30px">
		 <div class="top" style="border: none;">
	      <span class="title">订单日志</span>
	    </div>
		<?= $this->cell('Workflow::top',[$_orders_info['id'],$this->request->session()->read('Auth.User.id'),$_orders_info])->render("logtop"); ?>

		<div class="panel panel-warning">
			<div class="panel-heading">
				<h5 class="panel-title">各环节记录</h5>
			</div>
			<div class="panel-body">
				<table class="order-track table">
					<thead>
						<tr>
							<th>流程环节</th>
							<th>处理结果</th>
							<th>经办人</th>
							<th>所在单位/部门</th>
							<th>完成时间</th>
							<th>处理意见</th>
						</tr>
					</thead>
					<tbody>
						<?= $this->cell('Workflow::trace',[$_orders_info['id']])->render("logtrace"); ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<!-- 修改 -->
<div class="modal fade" id="modal-modify" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h5 class="modal-title">提示</h5>
			</div>
			<form id="modal-modify-form" action="" method="post">
				<div class="modal-body">
					<i class="icon-question-sign text-primary"></i>&nbsp;&nbsp;确认要修改商品<span class=" text-primary" id="modal-modify-name"></span>的数量？</br>
				</div>
				<div class="modal-footer">
					<button type="button" id="sumbiter-modify" class="btn btn-primary">确认</button>
					<button type="button" class="btn btn-danger" data-dismiss="modal">取消</button>
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
					<i class="icon-question-sign text-primary"></i>&nbsp;&nbsp;确认要删除商品<span class=" text-primary" id="modal-dele-name"></span>？</br>
				</div>
				<div class="modal-footer">
					<button type="button" id="sumbiter-dele" class="btn btn-primary">确认</button>
					<button type="button" class="btn btn-danger" data-dismiss="modal">取消</button>
				</div>
			</form>
		</div>
	</div>
</div>

<script>
	function edit(goodId,orderGoodsId,goodName){
		// alert(goodName);
		$('#modal-modify-name').html(goodName);
		$('#modal-modify').modal("show");
		$('#sumbiter-modify').on('click',function(){
			$('#modal-modify').modal("hide");
			var num = $('#'+goodId).val();
			var settings = {
				'url':'<?= $this ->Url ->build(['prefix' =>'console', 'controller' =>'Workflow', 'action' =>'edit_good_num']);?>',
				'type':'POST',
				'data':{'num':num,'good_id':goodId,'order_goods_id':orderGoodsId},
				'success':function(data){
					var data = eval('(' + data + ')');
					if(data.code==0){
						alert(data.msg);
                        window.location.href=window.location.href;
                    }else{
                    	alert(data.msg);
                    	window.location.href=window.location.href;
                    }
                }
            }
            $.ajax(settings);
        });

	}

	function dele(orderGoodsId,orderId,goodName){
		$('#modal-dele-name').html(goodName);
		$('#modal-dele').modal("show");
		$('#sumbiter-dele').on('click',function(){
			$('#modal-dele').modal("hide");
			var settings = {
				'url':'<?= $this ->Url ->build(['prefix' =>'console', 'controller' =>'Workflow', 'action' =>'dele_order_good']);?>',
				'type':'POST',
				'data':{'order_goods_id':orderGoodsId,'order_id':orderId},
				'success':function(data){
					var data = eval('(' + data + ')');
					if(data.code==0){
						alert(data.msg);
						window.location.href=window.location.href;
					}else{
						alert(data.msg);
					}
				}
			}
			$.ajax(settings);
		});
	}
</script>
