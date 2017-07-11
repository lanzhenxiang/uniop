<!-- 主页模板 -->
<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div id="maindiv-alert" class="content-list-page"></div>
    <div class="content-operate clearfix">
        <div class="pull-left">
        </div>
        <div class="input-group content-search pull-right">
            <input type="text" class="form-control" id="searchtext" placeholder="搜索任务名称...">
            <span class="input-group-btn">
                <button class="btn btn-primary" id="search" type="button">搜索</button>
            </span>
        </div>
    </div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>id</th>
                <th>执行主机</th>
                <th>任务id</th>
                <th>任务名称</th>
                <th>开始时间</th>
                <th>结束时间</th>
                <th>执行结果</th>
               	
            </tr>
        </thead>
        <tbody><?php if(isset($data)){
            foreach($data as $value){?>
            <tr>
                <td><?php if(isset($value['id'])){ echo $value['id'];} ?></td>
                <td><?php if(isset($value['exec_host'])){ echo $value['exec_host'];} ?></td>
                <td><?php if(isset($value['task_id'])){ echo $value['task_id'];} ?></td>
                <td><?php if(isset($value['task_name'])){ echo $value['task_name'];} ?></td>
                <td><?php if(isset($value['begin_time'])){ echo date("Y-m-d H:i:s" ,$value['begin_time']);} ?></td>
                <td><?php if(isset($value['end_time'])){ echo date("Y-m-d H:i:s" ,$value['end_time']);} ?></td>
                <td><?php if(isset($value['exec_status'])){ if($value['exec_status']==1){ echo '成功';}else{ echo '失败'; }} ?>  | 
                <a  href="javascript:;" data-toggle="modal" data-target="#modal" onclick='check("<?php if(isset($value['id'])){ echo $value['id'];}else{ echo '';} ?>")' >查看详情</a></td>
            </tr>
            <?php }} ?>
        </tbody>
    </table>
    <div class="content-pagination clearfix">
        <nav class="pull-right">
            <ul class="pagination">
                <?php echo $this->Paginator->first('<<');?>
                <?php echo $this->Paginator->numbers();?>
                <?php echo $this->Paginator->last('>>');?>
            </ul>
        </nav>
    </div>
</div>
</div>
			<div class="modal fade" id="modal" role="dialog" style="height: 900px">
			  <div class="modal-dialog" role="document">
			    <div class="modal-content">
			      <div class="modal-header">
			        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
			        <h4 class="modal-title">返回数据详情</h4>
			      </div>
			      <div class="modal-body" >
<textarea id="body-m" style="height: 400px;width:560px"></textarea>
			      </div>
			      <div class="modal-footer">
			        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
			        
			      </div>
			    </div>
			  </div>
			</div>
<?=$this->Html->script(['adminjs.js']); ?>
<?= $this->start('script_last'); ?>
<script type="text/javascript">
    $('#search').on('click',function(){
        var name = $('#searchtext').val();
        location.href = "<?php echo $this->Url->build(array('controller'=>'Log','action'=>'servicelog'));?>/"+name;
    })
    $(function() {
        var name ="<?php echo $name ?>";
        $('#searchtext').val(name);
    })

    function check(id){
    	 $.ajax({
             type: "POST",
             url: '<?php echo $this->Url->build(array('controller'=>'Log','action'=>'checkresult'));?>',
             data: {id: id},
             success: function (data) {
             	data = $.parseJSON(data);
             
                 $('#body-m').val(data);
             }
         });
    }
</script>
<?= $this->end() ?>