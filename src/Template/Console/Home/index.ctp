<style>
	.graph-display-block{
		margin-right: 0;
		margin-left: 100px;
	}	
</style>
<div class="wrap-nav-right wrap-nav-right-left">
	<div class="summary-content">
		<div class="summary-left">
			<div class="wrap-manage">
				<div class="top">
					<span class="title">
						<?= $y?>年<?= $m?>月消费总览
					</span>
					<a href="<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'charge', 'action' => 'subject']); ?>" class="pull-right">更多</a>
				</div>
				<div class="center">
					<div class="graph-display-block">
		                <div class="graph-display-content clearfix">
		                    <div class="graph-display-canvas pull-left">
		                        <canvas id="canvas" width="200" height="200"></canvas>
		                        <div class="graph-display-account"></div>
		                        <div class="text-center" style="margin-top:15px;"><?= $y?>年<?= $m?>月消费共计: <span class="text-primary">￥<?= $sum?></span></div>
		                    </div>
		                    <div class="graph-display-info pull-right">
		                        <ul>
		                        <?php $color = ['#f64649','#e2ebea','#d5ccc5','#949fb1','#45d2e4','#F38630'];?>
		                        <?php $_i = 0; foreach($mon_charge as $_q_v){?>
		                        	<li>
		                                <span class="color-block" style="background-color: <?= $color[$_i]?>"></span>
		                                <?php if(!empty($_q_v['name'])){ echo $_q_v['name'];}?>：￥<?php if(!empty($_q_v)){ echo (float)$_q_v['cost'];}else{ echo 0;}?>
		                            </li>
		                        <?php $_i++; }?>
		                        </ul>
		                    </div>
		                </div>
		            </div>
				</div>
			</div>
			<div class="wrap-manage">
				<div class="top">
					<span class="title">
						资源配额使用情况
					</span>
				</div>
				<div class="center">
			<div class="quota">
				<h5>CPU可用<?php if(!empty($budget['cpu_bugedt'])&&!empty($used['cpu_used']))
                        { 
                            if ((int)$used['cpu_used'] > (int)$budget['cpu_bugedt']) {
                                $budget['cpu_bugedt'] = $used['cpu_used'];
                            }
                            echo $budget['cpu_bugedt']-$used['cpu_used'];
                        } elseif(!empty($budget['cpu_bugedt'])&&empty($used['cpu_used'])) {
                            echo $budget['cpu_bugedt'];
                        } else {
                            echo 0;
                        } ?>核，已使用<?php if(!empty($used['cpu_used']))
                        { 
                            echo $used['cpu_used'];
                        }else{
                            echo 0;
                        }?>核，使用率<?php if(!empty($budget['cpu_bugedt'])&&!empty($used['cpu_used']))
                        { 
                            $i=$used['cpu_used']/$budget['cpu_bugedt']*100;
                            echo ceil($i)."%";
                        }else{
                            echo "0%";
                        }?></h5>
				<div class="progress">
				  <div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: <?php if(!empty($budget['cpu_bugedt'])&&!empty($used['cpu_used']))
                            { 
                                $i=$used['cpu_used']/$budget['cpu_bugedt']*100;
                                echo ceil($i)."%";
                            }else{
                                echo "0%";
                            }?>">
				  </div>
				</div>
			</div>
			<div class="quota">
				<h5>内存可用<?php if(!empty($budget['memory_buget'])&&!empty($used['memory_used']))
                        { 
                            echo $budget['memory_buget']-$used['memory_used'];
                        }elseif (!empty($budget['memory_buget'])&&empty($used['memory_used'])) {
                            echo $budget['memory_buget'];
                        }else{
                            echo "0";
                        }?>GB，已使用<?php if(!empty($used['memory_used']))
                        { 
                            echo $used['memory_used'];
                        }else{
                            echo "0";
                        }?>GB，使用率<?php if(!empty($budget['memory_buget'])&&!empty($used['memory_used']))
                            { 
                                $i=$used['memory_used']/$budget['memory_buget']*100;
                                echo ceil($i)."%";
                            }else{
                                echo "0%";
                            }?></h5>
				<div class="progress">
				  <div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: <?php if(!empty($budget['memory_buget'])&&!empty($used['memory_used']))
                            { 
                                $i=$used['memory_used']/$budget['memory_buget']*100;
                                echo ceil($i)."%";
                            }else{
                                echo "0%";
                            }?>">
				  </div>
				</div>
			</div>
            <div class="quota">
                <h5>GPU可用<?php if(!empty($budget['gpu_bugedt'])&&!empty($used['gpu_used']))
                        { 
                            echo $budget['gpu_bugedt']-$used['gpu_used'];
                        }elseif (!empty($budget['gpu_bugedt'])&&empty($used['gpu_used'])) {
                            echo $budget['gpu_bugedt'];
                        }else{
                            echo "0";
                        }?>MB，已使用<?php if(!empty($used['gpu_used']))
                        { 
                            echo $used['gpu_used'];
                        }else{
                            echo "0";
                        }?>MB，使用率<?php if(!empty($budget['gpu_bugedt'])&&!empty($used['gpu_used']))
                            { 
                                $i=$used['gpu_used']/$budget['gpu_bugedt']*100;
                                echo ceil($i)."%";
                            }else{
                                echo "0%";
                            }?></h5>
                <div class="progress">
                  <div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: <?php if(!empty($budget['gpu_bugedt'])&&!empty($used['gpu_used']))
                            { 
                                $i=$used['gpu_used']/$budget['gpu_bugedt']*100;
                                echo ceil($i)."%";
                            }else{
                                echo "0%";
                            }?>">
                  </div>
                </div>
            </div>
			<div class="quota">
				<h5>路由器可用<?php if(!empty($budget['router_bugedt'])&&!empty($used['router_used']))
                        { 
                            echo $budget['router_bugedt']-$used['router_used'];
                        }elseif (!empty($budget['router_bugedt'])&&empty($used['router_used'])) {
                            echo $budget['router_bugedt'];
                        }else{
                            echo "0";
                        }?>个，已使用<?php if(!empty($used['router_used']))
                        { 
                            echo $used['router_used'];
                        }else{
                            echo "0";
                        }?>个，使用率<?php if(!empty($budget['router_bugedt'])&&!empty($used['router_used']))
                            { 
                                $i=$used['router_used']/$budget['router_bugedt']*100;
                                echo ceil($i)."%";
                            }else{
                                echo "0%";
                            }?></h5>
				<div class="progress">
				  <div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: <?php if(!empty($budget['router_bugedt'])&&!empty($used['router_used']))
                            { 
                                $i=$used['router_used']/$budget['router_bugedt']*100;
                                echo ceil($i)."%";
                            }else{
                                echo "0%";
                            }?>">
				  </div>
				</div>
			</div>
			<!--<div class="quota">
				<h5>子网可用<?php if(!empty($budget['subnet_bugedt'])&&!empty($used['subnet_used']))
                        {
                            echo $budget['subnet_bugedt']-$used['subnet_used'];
                        }elseif (!empty($budget['subnet_bugedt'])&&empty($used['subnet_used'])) {
                            echo $budget['subnet_bugedt'];
                        }else{
                            echo "0";
                        }?>个，已使用<?php if(!empty($used['subnet_used']))
                        {
                            echo $used['subnet_used'];
                        }else{
                            echo "0";
                        }?>个，使用率<?php if(!empty($budget['subnet_bugedt'])&&!empty($used['subnet_used']))
                            {
                                $i=$used['subnet_used']/$budget['subnet_bugedt']*100;
                                echo ceil($i)."%";
                            }else{
                                echo "0%";
                            }?></h5>
				<div class="progress">
				  <div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: <?php if(!empty($budget['subnet_bugedt'])&&!empty($used['subnet_used']))
                            {
                                $i=$used['subnet_used']/$budget['subnet_bugedt']*100;
                                echo ceil($i)."%";
                            }else{
                                echo "0%";
                            }?>">
				  </div>
				</div>
			</div>-->
			<div class="quota">
				<h5>磁盘可用<?php if(!empty($budget['disks_bugedt'])&&!empty($used['disks_used']))
                        { 
                            echo $budget['disks_bugedt']-$used['disks_used'];
                        }elseif (!empty($budget['disks_bugedt'])&&empty($used['disks_used'])) {
                            echo $budget['disks_bugedt'];
                        }else{
                            echo "0";
                        }?>个，已使用<?php if(!empty($used['disks_used']))
                        { 
                            echo $used['disks_used'];
                        }else{
                            echo "0";
                        }?>个，使用率<?php if(!empty($budget['disks_bugedt'])&&!empty($used['disks_used']))
                            { 
                                $i=$used['disks_used']/$budget['disks_bugedt']*100;
                                echo ceil($i)."%";
                            }else{
                                echo "0%";
                            }?></h5>
				<div class="progress">
				  <div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: <?php if(!empty($budget['disks_bugedt'])&&!empty($used['disks_used']))
                            { 
                                $i=$used['disks_used']/$budget['disks_bugedt']*100;
                                echo ceil($i)."%";
                            }else{
                                echo "0%";
                            }?>">
				  </div>
				</div>
			</div>

<!-- <div class="quota">
	<h5>FICS存储卷可用<?php if(!empty($budget['fics_num_bugedt'])&&!empty($used['fics_num_used']))
                        {
                            echo $budget['fics_num_bugedt']-$used['fics_num_used'];
                        }elseif (!empty($budget['fics_num_bugedt'])&&empty($used['fics_num_used'])) {
                            echo $budget['fics_num_bugedt'];
                        }else{
                            echo "0";
                        }?>个，已使用<?php if(!empty($used['fics_num_used']))
                        {
                            echo $used['fics_num_used'];
                        }else{
                            echo "0";
                        }?>个，使用率<?php if(!empty($budget['fics_num_bugedt'])&&!empty($used['fics_num_used']))
                            {
                                $i=$used['fics_num_used']/$budget['fics_num_bugedt']*100;
                                echo ceil($i)."%";
                            }else{
                                echo "0%";
                            }?></h5>
	<div class="progress">
		<div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: <?php if(!empty($budget['fics_num_bugedt'])&&!empty($used['fics_num_used']))
                            {
                                $i=$used['fics_num_used']/$budget['fics_num_bugedt']*100;
                                echo ceil($i)."%";
		}else{
		echo "0%";
		}?>">
	</div>
</div>
</div>
<div class="quota">
	<h5>FICS存储卷可用<?php if(!empty($budget['fics_cap_bugedt'])&&!empty($used['fics_cap_used']))
                        {
                            echo $budget['fics_cap_bugedt']-$used['fics_cap_used'];
                        }elseif (!empty($budget['fics_cap_bugedt'])&&empty($used['fics_cap_used'])) {
                            echo $budget['fics_cap_bugedt'];
                        }else{
                            echo "0";
                        }?>GB，已使用<?php if(!empty($used['fics_cap_used']))
                        {
                            echo $used['fics_cap_used'];
                        }else{
                            echo "0";
                        }?>GB，使用率<?php if(!empty($budget['fics_cap_bugedt'])&&!empty($used['fics_cap_used']))
                            {
                                $i=$used['fics_cap_used']/$budget['fics_cap_bugedt']*100;
                                echo ceil($i)."%";
                            }else{
                                echo "0%";
                            }?></h5>
	<div class="progress">
		<div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: <?php if(!empty($budget['fics_cap_bugedt'])&&!empty($used['fics_cap_used']))
                            {
                                $i=$used['fics_cap_used']/$budget['fics_cap_bugedt']*100;
                                echo ceil($i)."%";
		}else{
		echo "0%";
		}?>">
	</div>
</div>
</div> -->
<div class="quota">
	<h5>H9000存储卷可用<?php if(!empty($budget['oceanstor9k_num_bugedt'])&&!empty($used['oceanstor9k_num_used']))
                        {
                            echo $budget['oceanstor9k_num_bugedt']-$used['oceanstor9k_num_used'];
                        }elseif (!empty($budget['oceanstor9k_num_bugedt'])&&empty($used['oceanstor9k_num_used'])) {
                            echo $budget['oceanstor9k_num_bugedt'];
                        }else{
                            echo "0";
                        }?>个，已使用<?php if(!empty($used['oceanstor9k_num_used']))
                        {
                            echo $used['oceanstor9k_num_used'];
                        }else{
                            echo "0";
                        }?>个，使用率<?php if(!empty($budget['oceanstor9k_num_bugedt'])&&!empty($used['oceanstor9k_num_used']))
                            {
                                $i=$used['oceanstor9k_num_used']/$budget['oceanstor9k_num_bugedt']*100;
                                echo ceil($i)."%";
                            }else{
                                echo "0%";
                            }?></h5>
	<div class="progress">
		<div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: <?php if(!empty($budget['oceanstor9k_num_bugedt'])&&!empty($used['oceanstor9k_num_used']))
                            {
                                $i=$used['oceanstor9k_num_used']/$budget['oceanstor9k_num_bugedt']*100;
                                echo ceil($i)."%";
		}else{
		echo "0%";
		}?>">
	</div>
</div>
</div>
<div class="quota">
	<h5>H9000存储卷可用<?php if(!empty($budget['oceanstor9k_cap_bugedt'])&&!empty($used['oceanstor9k_cap_used']))
                        {
                            echo $budget['oceanstor9k_cap_bugedt']-$used['oceanstor9k_cap_used'];
                        }elseif (!empty($budget['oceanstor9k_cap_bugedt'])&&empty($used['oceanstor9k_cap_used'])) {
                            echo $budget['oceanstor9k_cap_bugedt'];
                        }else{
                            echo "0";
                        }?>GB，已使用<?php if(!empty($used['oceanstor9k_cap_used']))
                        {
                            echo $used['oceanstor9k_cap_used'];
                        }else{
                            echo "0";
                        }?>GB，使用率<?php if(!empty($budget['oceanstor9k_cap_bugedt'])&&!empty($used['oceanstor9k_cap_used']))
                            {
                                $i=$used['oceanstor9k_cap_used']/$budget['oceanstor9k_cap_bugedt']*100;
                                echo ceil($i)."%";
                            }else{
                                echo "0%";
                            }?></h5>
	<div class="progress">
		<div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: <?php if(!empty($budget['oceanstor9k_cap_bugedt'])&&!empty($used['oceanstor9k_cap_used']))
                            {
                                $i=$used['oceanstor9k_cap_used']/$budget['oceanstor9k_cap_bugedt']*100;
                                echo ceil($i)."%";
		}else{
		echo "0%";
		}?>">
	</div>
</div>
</div>
<div class="quota">
	<h5>桌面基础套件可用<?php if(!empty($budget['basic_budget'])&&!empty($used['basic_used']))
                        {
                            echo $budget['basic_budget']-$used['basic_used'];
                        }elseif (!empty($budget['basic_budget'])&&empty($used['basic_used'])) {
                            echo $budget['basic_budget'];
                        }else{
                            echo "0";
                        }?>套，已使用<?php if(!empty($used['basic_used']))
                        {
                            echo $used['basic_used'];
                        }else{
                            echo "0";
                        }?>个，使用率<?php if(!empty($budget['basic_budget'])&&!empty($used['basic_used']))
                            {
                                $i=$used['basic_used']/$budget['basic_budget']*100;
                                echo ceil($i)."%";
                            }else{
                                echo "0%";
                            }?></h5>
	<div class="progress">
		<div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: <?php if(!empty($budget['basic_budget'])&&!empty($used['basic_used']))
                            {
                                $i=$used['basic_used']/$budget['basic_budget']*100;
                                echo ceil($i)."%";
		}else{
		echo "0%";
		}?>">
	</div>
</div>
</div>
<div class="quota">
	<h5>防火墙可用<?php if(!empty($budget['fire_budget'])&&!empty($used['fire_used']))
                        {
                            echo $budget['fire_budget']-$used['fire_used'];
                        }elseif (!empty($budget['fire_budget'])&&empty($used['fire_used'])) {
                            echo $budget['fire_budget'];
                        }else{
                            echo "0";
                        }?>套，已使用<?php if(!empty($used['fire_used']))
                        {
                            echo $used['fire_used'];
                        }else{
                            echo "0";
                        }?>个，使用率<?php if(!empty($budget['fire_budget'])&&!empty($used['fire_used']))
                            {
                                $i=$used['fire_used']/$budget['fire_budget']*100;
                                echo ceil($i)."%";
                            }else{
                                echo "0%";
                            }?></h5>
	<div class="progress">
		<div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: <?php if(!empty($budget['fire_budget'])&&!empty($used['fire_used']))
                            {
                                $i=$used['fire_used']/$budget['fire_budget']*100;
                                echo ceil($i)."%";
		}else{
		echo "0%";
		}?>">
	</div>
</div>
</div>
<div class="quota">
	<h5>负载均衡可用<?php if(!empty($budget['elb_budget'])&&!empty($used['elb_used']))
                        {
                            echo $budget['elb_budget']-$used['elb_used'];
                        }elseif (!empty($budget['elb_budget'])&&empty($used['elb_used'])) {
                            echo $budget['elb_budget'];
                        }else{
                            echo "0";
                        }?>套，已使用<?php if(!empty($used['elb_used']))
                        {
                            echo $used['elb_used'];
                        }else{
                            echo "0";
                        }?>个，使用率<?php if(!empty($budget['elb_budget'])&&!empty($used['elb_used']))
                            {
                                $i=$used['elb_used']/$budget['elb_budget']*100;
                                echo ceil($i)."%";
                            }else{
                                echo "0%";
                            }?></h5>
	<div class="progress">
		<div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: <?php if(!empty($budget['elb_budget'])&&!empty($used['elb_used']))
                            {
                                $i=$used['elb_used']/$budget['elb_budget']*100;
                                echo ceil($i)."%";
		}else{
		echo "0%";
		}?>">
	</div>
</div>
</div>
<div class="quota">
	<h5>公网IP可用<?php if(!empty($budget['eip_budget'])&&!empty($used['eip_used']))
                        {
                            echo $budget['eip_budget']-$used['eip_used'];
                        }elseif (!empty($budget['eip_budget'])&&empty($used['eip_used'])) {
                            echo $budget['eip_budget'];
                        }else{
                            echo "0";
                        }?>个，已使用<?php if(!empty($used['eip_used']))
                        {
                            echo $used['eip_used'];
                        }else{
                            echo "0";
                        }?>个，使用率<?php if(!empty($budget['eip_budget'])&&!empty($used['eip_used']))
                            {
                                $i=$used['eip_used']/$budget['eip_budget']*100;
                                echo ceil($i)."%";
                            }else{
                                echo "0%";
                            }?></h5>
	<div class="progress">
		<div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: <?php if(!empty($budget['eip_budget'])&&!empty($used['eip_used']))
                            {
                                $i=$used['eip_used']/$budget['eip_budget']*100;
                                echo ceil($i)."%";
		}else{
		echo "0%";
		}?>">
	</div>
</div>
</div>



		</div>
			</div>
		</div>
		<div class="summary-right">
			<div class="wrap-manage" style="min-width:540px;">
				<div class="top">
					<span class="title">最近操作</span>
					<?php if($instance_logs_sum > 7){?>
					<a href="<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'InstanceLogs', 'action' => 'index']); ?>" class="pull-right">更多</a>
					<?php }?>
				</div>
				<div class="center">
				<?php foreach($instance_logs_data as $_log_key => $_log_data){?>
					<div class="operation-note clearfix">
						<div class="operation-user pull-left">
							<div class="operation-photo pull-left">
								<?php $image = $_log_data['account']['image'];
				                if(!empty($image)){
				                  
				                  echo '<img src="/'.$image.'" alt="" width="50px;">';            
				                }else{
				                  echo '<img src="/images/user-photo.png" alt="" width="50px;">';
				                }
				                ?>
							</div>
							<div class="operation-info pull-left">
								<h5><?= $_log_data['account']['username']?></h5>
								<h6 class="text-light"><?= date('m/d/y H:i',$_log_data['create_time']) ?></h6>
							</div>
						</div>
						<div class="operation-text pull-left">
							<p>
								设备CODE ：<span class="text-primary"><?= $_log_data['device_code']?></span>
							</p>
							<p style="margin-bottom:15px;">
								设备名 : <span class="text-primary"><?= $_log_data['device_name']?></span>
							</p>
							<p>
								<?= $_log_data['device_event']?>
							</p>
						</div>
					</div>	
				<?php }?>
				</div>
			</div>
		</div>
	</div>
</div>

<?php
$this->start('script_last');
?>
<script>
	var binOptions = {
	    animationSteps:40,
	    animationEasing:"linear",
        showTooltips:false
	};

	
    var canvas = $('#canvas').get(0).getContext('2d');
    
    
    <?php $i =0;$binData=''; foreach ($mon_charge as $_v){
    	$binData .= '{value:'.$_v["cost"].',color:"'.$color[$i].'"},';
    	$i++;
    }?>
    var binData = [
		<?= $binData?>	
	]
    new Chart(canvas).Doughnut(binData,binOptions);
</script>
<?php
$this->end();
?>