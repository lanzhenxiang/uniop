<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
	<div id="maindiv-alert"></div>
	<form class="form-horizontal" id="work-flow-form" action="<?php echo $this->Url->build(array('controller' => 'Workflow','action'=>'addedit')); ?>" method="post">
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
								<input type="text" name="flow_code" class="form-control" value="<?php if(isset($data)){ echo $data['flow_code'];}  ?>" />
								<!-- <button class="btn btn-primary" style="margin-left:5px;vertical-align:top;">生 成</button> -->
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">
								流程名称 : 
							</label>
							<div class="col-sm-8">
								<input type="text" name="flow_name" class="form-control" value="<?php if(isset($data)){ echo $data['flow_name'];}  ?>" />
								<?php if(isset($data)){ ?>
								<input type="hidden" class="form-control" name="flow_id" value="<?php if(isset($data)){ echo $data['flow_id']; } ?>">
								<?php } ?>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-2 control-label">
								备注 : 
							</label>
							<div class="col-sm-8">
								<textarea name="flow_note" class="form-control"><?php if(isset($data)){ echo $data['flow_note'];}  ?></textarea>
							</div>
						</div>
					</div>

				</div>

			</div>
		</div>

		<div class="text-center">
			<button type="submit" class="btn btn-primary">提交</button>
			<!-- <button class="btn btn-negative" type="button" onclick="location.href='lists'">回列表</button> -->
			<!--<a type="button" href="<?php echo $this->Url->build(array('controller' => 'workflow','action'=>'lists')); ?>" class="btn btn-danger">返回</a>-->
			<a type="button" onclick="window.history.go(-1)" class="btn btn-danger">返回</a>
		</div>
	</form>
</div>
<?=$this->Html->script(['adminjs.js','validator.bootstrap.js']); ?>
<?= $this->start('script_last'); ?>
<script>
	$('#work-flow-form').bootstrapValidator({
		submitButtons: 'button[type="submit"]',
		submitHandler: function(validator, form, submitButton){
			$.post(form.attr('action'), form.serialize(), function(data){
				var data = eval('(' + data + ')');
				if(data.code==0){
					tentionHide(data.msg,0);
					location.href='<?php echo $this->Url->build(array('controller' => 'Workflow','action'=>'lists')); ?>';
				}else{
					tentionHide(data.msg,1);
				}
			});
		},
		fields : {
			flow_name: {
				group: '.col-sm-6',
				validators: {
					notEmpty: {
						message: '流程名称不能为空'
					},
					stringLength: {
						min: 1,
						max: 16,
						message: '请保持在1-16位'
					},
				}
			},
			flow_note: {
				group: '.col-sm-6',
				validators: {
					stringLength: {
						min: 0,
						max: 30,
						message: '请保持在1-30位'
					},
				}
			},
			flow_code: {
				group: '.col-sm-6',
				validators: {
					notEmpty: {
						message: '流程编码不能为空'
					},
					stringLength: {
						min: 1,
						max: 16,
						message: '请保持在1-16位'
					},
					regexp: {
						regexp: /^\S*$/,
						message: '流程编码不能有空格'
					}
				}
			}
		}
	}); 
</script>	
<?= $this->end() ?>