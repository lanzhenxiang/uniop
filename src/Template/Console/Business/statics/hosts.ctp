<?php echo $code;?>

<script type="text/javascript">	
function chart(code) {
	$.ajax({
		type: "POST",
		url: "/console/ajax/network/hosts/getmonitor",
		data: {
			code: code
		},
		beforeSend: function() {
			$(".chart-mask").show()
		},
		success: function(result) {
			$(".chart-mask").hide(), json = eval('(' + result + ')');
			var data = {
				labels: json.chart.cpu.time,
				datasets: [{
					fillColor: "rgba(255,255,255,0.5)",
					strokeColor: "rgba(68,210,228,1)",
					pointColor: "rgba(220,220,220,1)",
					pointStrokeColor: "#fff",
					data: json.chart.cpu.data
				}]
			}
			var data3 = {
				labels: json.chart.disk.time,
				datasets: [{
					fillColor: "rgba(255,255,255,0.5)",
					strokeColor: "rgba(68,210,228,1)",
					pointColor: "rgba(220,220,220,1)",
					pointStrokeColor: "#fff",
					data: json.chart.disk.data1
				}, {
					fillColor: "rgba(255,255,255,0.5)",
					strokeColor: "rgba(252,218,150,1)",
					pointColor: "rgba(220,220,220,1)",
					pointStrokeColor: "#fff",
					data: json.chart.disk.data2
				}]
			}
			var data4 = {
				labels: json.chart.network.time,
				datasets: [{
					fillColor: "rgba(255,255,255,0.5)",
					strokeColor: "rgba(68,210,228,1)",
					pointColor: "rgba(220,220,220,1)",
					pointStrokeColor: "#fff",
					data: json.chart.network.data1
				}, {
					fillColor: "rgba(255,255,255,0.5)",
					strokeColor: "rgba(252,218,150,1)",
					pointColor: "rgba(220,220,220,1)",
					pointStrokeColor: "#fff",
					data: json.chart.network.data2
				}]
			}
			var ctx = document.getElementById("canvas1").getContext("2d");
			window.myLine = new Chart(ctx).Line(data, {
				responsive: true
			});
			// var ctx2 = document.getElementById("canvas2").getContext("2d");
			// window.myLine = new Chart(ctx2).Line(data2, {
			//   responsive: true
			// });
			var ctx3 = document.getElementById("canvas3").getContext("2d");
			window.myLine = new Chart(ctx3).Line(data3, {
				responsive: true
			});
			var ctx4 = document.getElementById("canvas4").getContext("2d");
			window.myLine = new Chart(ctx4).Line(data4, {
				responsive: true
			});

		}
	});

}
</script>