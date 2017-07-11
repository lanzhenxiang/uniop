<script type="text/javascript">
	$(document).ready(
		function(){
			var myDate = new Date();
			var year = myDate.getFullYear();
			var month =myDate.getMonth()+1;
			var day =  myDate.getDate();
			var time =year+'-'+month+'-'+day;
			$('#datetimepicker-end').datetimepicker({
				autoclose:true,
				minView:2,
				startDate:'<?= $start?>',
				endDate:time
			}
			).on('changeDate', function(){
				var end = $('#end-time').val();
				location.href="<?= $this ->Url ->build(['prefix' =>'admin', 'controller' =>'bill', 'action' =>'detail',$department_id,$in_type,$instance_id,$type,$start]);?>/"+end;
			});
			$('#datetimepicker-start').datetimepicker({
				autoclose:true,
				minView:2,
				endDate:'<?= $end?>',
			}
			).on('changeDate', function(){
				var start = $('#start-time').val();
				location.href="<?= $this ->Url ->build(['prefix' =>'admin', 'controller' =>'bill', 'action' =>'detail',$department_id,$in_type,$instance_id,$type]);?>/"+start+'/<?php echo $end?>';
			});
		}
		);

	function formatter_type(value){
		return value.service_name;
	}
	function formatter_name(value){
		return value.name;
	}
	function countMode(value, row, index) {
		switch(value){
			case '1':{
				return "按天计费";
				break;
			}
			case '2':{
				return "包月计费";
				break;
			}
			case '3':{
				return "按分钟计费";
				break;
			}
			default:{
				return "按分钟计费";
			}
		}
	}
	function timestrap2date(value) {
		var now = new Date(value);

		var y = now.getFullYear();  
		var m = now.getMonth() + 1;  
		m = m < 10 ? '0' + m : m;  
		var d = now.getDate();  
		d = d < 10 ? ('0' + d) : d;  
		now = y + '-' + m + '-' + d;  

		return now;
	}
	function operation(value,row,index){
		if(row.charge_type ==3){

			var a = "<a href = '<?= $this->Url->build(['prefix' =>'admin','controller' =>'bill', 'action' =>'subjectDetail',$start,$end]);?>/"+row.basic_name+"/"+row.type_id+"'>详情</a>";
			return a;
		}else{
			return '-';
		}
	}

	function clickDepartment(){
		department_id = $('#select-depart').val();
    	location.href = "<?= $this ->Url ->build(['prefix' =>'admin', 'controller' =>'bill', 'action' =>'detail']);?>/"+department_id+"/<?= $type?>/<?= $start?>/<?= $end?>";
    }


    function clickService(){
    	
    	value =$('#select-service').val();
		value = value.split(',');
		charge =value[0];
    	type =value[1];
    	location.href = "<?= $this ->Url ->build(['prefix' =>'admin', 'controller' =>'bill', 'action' =>'detail',$department_id])?>/"+charge+"/0/"+type+"/<?= $start?>/<?= $end?>";
    }


    function clickInstance(){
    	value =$('#select-instance').val();
		value = value.split(',');
    	charge =value[0];
    	instance =value[1];
    	location.href = "<?= $this ->Url ->build(['prefix' =>'admin', 'controller' =>'bill', 'action' =>'detail',$department_id])?>/"+charge+"/"+instance+"/<?= $type?>/<?= $start?>/<?= $end?>";
    }
</script> 	