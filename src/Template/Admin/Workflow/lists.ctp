<?= $this->element('content_header'); ?>
<div class="content-body clearfix">

	<div class="content-operate clearfix">
			<button class="btn btn-addition" type="button" onclick="location.href='flow'"><i class="icon-plus"></i> 新建</button>
		<div class="input-group content-search pull-right">
			<input type="text" class="form-control" id="searchtext" placeholder="搜索流程名称...">
			<span class="input-group-btn">
				<button class="btn btn-primary" id="search" type="button">搜索</button>
			</span>
		</div>
	</div>
	<div style="margin-top:20px;">
		<table class="table table-striped">
			<thead>
				<tr>
					<th >序号</th>
					<th >名称</th>
					<!-- <th >流程实例</th> -->
					<!-- <th >数量</th> -->
					<th >描述</th>
					<th >操作</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($data as  $value) {?>
				<tr>
					<td><?= $value['flow_id']?></td>
					<td><?= $value['flow_name']?></td>
					<!-- <td>实例11</td> -->
					<!-- <td>21</td> -->
					<td><?= $value['flow_note']?></td>
					<td class="text-center">
						<a  href="<?php echo $this->Url->build(array('controller' => 'Workflow','action'=>'flow')); ?>/<?php if(isset($value['flow_id'])){ echo $value['flow_id'];} ?>">修改步骤</a> |
						<a  href="<?php echo $this->Url->build(array('controller' => 'Workflow','action'=>'addedit')); ?>/<?php if(isset($value['flow_id'])){ echo $value['flow_id'];} ?>">修改</a> |
						<a  href="#" onclick="deletes(<?php echo $value['flow_id']; ?>)">删除</a>
					</td>
				</tr>
				<?php }?>
			</tbody>
		</table>
	</div>
</div>
<div class="modal fade" id="modal-delete" tabindex="-1" role="dialog">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h5 class="modal-title">提示</h5>
				</div>
				<div class="modal-body">
					<i class="icon-question-sign text-primary"></i>&nbsp;&nbsp;确认要删除该步骤么？<span class="text-primary" id="sure"></span>？
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" id="yes">确认</button>
					<button type="button" class="btn btn-danger" data-dismiss="modal">取消</button>
				</div>
			</div>
		</div>
	</div>
<?=$this->Html->script(['adminjs.js']); ?>
<?= $this->start('script_last'); ?>
<script type="text/javascript">
	$('#search').on('click',function(){
		var name = $('#searchtext').val();
		location.href = "<?php echo $this->Url->build(array('controller'=>'Workflow','action'=>'lists'));?>/"+name;
	})
	$(function() {
		var name ="<?php echo $name ?>";
		$('#searchtext').val(name);
	})

	function deletes(id){
		$('#modal-delete').modal("show");
		$('#yes').one('click',function() {
			$.ajax({
				type: 'post',
				url: '<?php echo $this->Url->build(array('controller' => 'Workflow','action'=>'delete')); ?>',
				dataType: "json",
				data: {'id': id},
				success: function (data) {
					if (data.code == 0) {
						tentionHide(data.msg, 0);
						location.href = '<?php echo $this->Url->build(array('controller'=>'Workflow','action'=>'lists'));?>';
					} else {
						$('#modal-delete').modal("hide");
						tentionHide(data.msg, 1);
					}

				}
			});
		})
	}
</script>
<?= $this->end() ?>