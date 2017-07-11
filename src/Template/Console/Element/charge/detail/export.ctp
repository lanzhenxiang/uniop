<div class="center">
			<div class="pull-right">
					 <button class="btn btn-addition" onclick="exportExcel()">导出excel</button>
			</div>
</div>
<script type="text/javascript">
	function exportExcel(){
	    location.href="<?= $this ->Url ->build(['prefix' =>'console', 'controller' =>'Charge', 'action' =>'detail',$department_id,$type,$start,$end,'true']);?>";
	}
</script>