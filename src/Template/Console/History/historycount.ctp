<style>
	.history-content{
		margin:40px 30px;
		padding:35px 50px;
		min-width:1200px;
		background:#fff;
		box-shadow:0 0 7px #333;
	}
	.history-content thead{
		background:#373d3d;
		color:#fff;
	}
	#history-canvas{
		width:100%;
		height:280px;
	}
</style>
<div class="history-content">
	<div>
		<canvas id="history-canvas"></canvas>
	</div>
	<div>
        <table id="table" data-toggle="table" data-pagination="true"
               data-side-pagination="server"
               data-locale="zh-CN" data-url="<?= $this->Url->build(['prefix'=>'console','controller'=>'history','action'=>'lists','?'=>['t'=>$this->request->query['t']]]); ?>" data-pagination="true"   data-unique-id="id">
            <thead>
            <tr>
                <th data-field="finish_date">日期</th>
                <th data-field="task_num">任务数</th>
                <th data-field="task_length">总时长</th>
                <th data-field="exec_length">执行总时长</th>
                <th data-field="efficiency">效率</th>
                <th data-field="max_instance">最大实例</th>
                <th data-field="min_instance">最小实例</th>
            </tr>
            </thead>
        </table>
	</div>
</div>
<script>
	$(function(){

		$('#table').on('load-success.bs.table',function(){
			var date = new Array();
			var duration = new Array();
			var json = $('#table').bootstrapTable('getData');
			$.each(json,function(i,n){
				date.push(n.finish_date);
				duration.push(n.task_length);
			});
		
			var data = {
			    labels: date,
			    datasets: [
			        {
			            label: "My First dataset",
			            fillColor: "rgba(220,220,220,0.2)",
			            strokeColor: "rgba(220,220,220,1)",
			            pointColor: "rgba(220,220,220,1)",
			            pointStrokeColor: "#fff",
			            pointHighlightFill: "#fff",
			            pointHighlightStroke: "rgba(220,220,220,1)",
			            data: duration
			        }
			    ]
			};

			var options = {};
			var historyCanvas = $('#history-canvas').get(0).getContext('2d');
			new Chart(historyCanvas).Line(data,options);


		})

	});
	
</script>