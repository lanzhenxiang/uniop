<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
	<div style="width:75%;margin:0 auto;">
	  <!-- Nav tabs -->
	  <ul class="nav nav-tabs" role="tablist">
	    <li role="presentation" class="active"><a href="#basic" aria-controls="home" role="tab" data-toggle="tab">基本信息</a></li>
	  </ul>

	  <!-- Tab panes -->
	  <div class="tab-content">
	    <div role="tabpanel" class="tab-pane active" id="basic">
	    	<div class="form-horizontal">
	    		<div class="form-group">
	    			<label class="col-sm-2 control-label">
	    				流程编码 : 
	    			</label>
	    			<div class="col-sm-8">
	    				<input type="text" class="form-control" />
	    				<!-- <button class="btn btn-primary" style="margin-left:5px;vertical-align:top;">生 成</button> -->
	    			</div>
	    		</div>
	    		<div class="form-group">
	    			<label class="col-sm-2 control-label">
	    				流程名称 : 
	    			</label>
	    			<div class="col-sm-8">
	    				<input type="text" class="form-control" />
	    			</div>
	    		</div>
	    		<div class="form-group">
	    			<label class="col-sm-2 control-label">
	    				备注 : 
	    			</label>
	    			<div class="col-sm-8">
	    				<textarea class="form-control"></textarea>
	    			</div>
	    		</div>
	    	</div>
			
	    </div>

	  </div>
	</div>

	<div class="text-center">
		<button class="btn btn-primary">提交</button>
		<!-- <button class="btn btn-negative" type="button" onclick="location.href='lists'">回列表</button> -->
		<a type="button" href="<?php echo $this->Url->build(array('controller' => 'Goods','action'=>'index')); ?>" class="btn btn-danger">返回</a>
	</div>
</div>
<?=$this->Html->script(['validator.bootstrap.js']); ?>
<script>
	$("#add-form").bootstrapValidator({
		fields : {
			name : {
				validators : {
	                notEmpty: {
	                    message: '名称不能为空'
	                }
	            } 
			},
			url : {
				validators : {
	                notEmpty: {
	                    message: 'URL不能为空'
	                }
	            } 
			}
		}
	});
</script>	