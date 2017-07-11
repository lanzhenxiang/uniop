<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
	<form action="<?php echo $this->Url->build(array('controller' => 'Goods','action'=>'upexcel')); ?>" method="post" enctype="multipart/form-data" id="up-excel">
		<div class="form-group">
			<label for="inputEmail3" class="col-sm-2 control-label">选择上传文件</label>
			<div class="col-sm-6">
				<input type="file" accept=".xls,.xlsx,.xl" name="userfile" data-show-preview="false" class="file"> 
				<input type="hidden" name="good_id" value="<?= $good_id?>">
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-offset-2 col-sm-10"  style = "margin-top:10px;">
				<input type="submit" value="导入" class="btn btn-primary import">
				<!-- <button type="button" id="ds" class="btn btn-default">提交</button> -->
				<!--<a href="<?php echo $this->Url->build(array('controller' => 'Goods','action'=>'index')); ?>" class="btn btn-danger">返回</a>-->
				<a onclick="window.history.go(-1)" class="btn btn-danger">返回</a>
			</div>
		</div>
	</form>
</div>