<div class="wrap-nav-right wrap-nav-right-left">
	<div class="wrap-manage">
		<div class="top">
			<span class="title">资源配额使用情况</span>
		</div>
		<div class="center">
			<div class="quota">
				<h5>CPU可用<?php if(!empty($query['cpu_bugedt'])&&!empty($data['cpu_used']))
                        { 
                            echo $query['cpu_bugedt']-$data['cpu_used'];
                        }else if(!empty($query['cpu_bugedt'])&&empty($data['cpu_used']))
                        {
                            echo $query['cpu_bugedt'];
                        }else{
                            echo 0;
                        } ?>核，已使用<?php if(!empty($data['cpu_used']))
                        { 
                            echo $data['cpu_used'];
                        }else{
                            echo 0;
                        }?>核，使用率<?php if(!empty($query['cpu_bugedt'])&&!empty($data['cpu_used']))
                        { 
                            $i=$data['cpu_used']/$query['cpu_bugedt']*100;
                            echo ceil($i)."%";
                        }else{
                            echo "0%";
                        }?></h5>
				<div class="progress">
				  <div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: <?php if(!empty($query['cpu_bugedt'])&&!empty($data['cpu_used']))
                            { 
                                $i=$data['cpu_used']/$query['cpu_bugedt']*100;
                                echo ceil($i)."%";
                            }else{
                                echo "0%";
                            }?>">
				  </div>
				</div>
			</div>
			<div class="quota">
				<h5>内存可用<?php if(!empty($query['memory_buget'])&&!empty($data['memory_used']))
                        { 
                            echo $query['memory_buget']-$data['memory_used'];
                        }elseif (!empty($query['memory_buget'])&&empty($data['memory_used'])) {
                            echo $query['memory_buget'];
                        }else{
                            echo "0";
                        }?>GB，已使用<?php if(!empty($data['memory_used']))
                        { 
                            echo $data['memory_used'];
                        }else{
                            echo "0";
                        }?>GB，使用率<?php if(!empty($query['memory_buget'])&&!empty($data['memory_used']))
                            { 
                                $i=$data['memory_used']/$query['memory_buget']*100;
                                echo ceil($i)."%";
                            }else{
                                echo "0%";
                            }?></h5>
				<div class="progress">
				  <div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: <?php if(!empty($query['memory_buget'])&&!empty($data['memory_used']))
                            { 
                                $i=$data['memory_used']/$query['memory_buget']*100;
                                echo ceil($i)."%";
                            }else{
                                echo "0%";
                            }?>">
				  </div>
				</div>
			</div>
            <div class="quota">
                <h5>GPU可用<?php if(!empty($query['gpu_bugedt'])&&!empty($data['gpu_used']))
                        { 
                            echo $query['gpu_bugedt']-$data['gpu_used'];
                        }elseif (!empty($query['gpu_bugedt'])&&empty($data['gpu_used'])) {
                            echo $query['gpu_bugedt'];
                        }else{
                            echo "0";
                        }?>MB，已使用<?php if(!empty($data['gpu_used']))
                        { 
                            echo $data['gpu_used'];
                        }else{
                            echo "0";
                        }?>MB，使用率<?php if(!empty($query['gpu_bugedt'])&&!empty($data['gpu_used']))
                            { 
                                $i=$data['gpu_used']/$query['gpu_bugedt']*100;
                                echo ceil($i)."%";
                            }else{
                                echo "0%";
                            }?></h5>
                <div class="progress">
                  <div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: <?php if(!empty($query['gpu_bugedt'])&&!empty($data['gpu_used']))
                            { 
                                $i=$data['gpu_used']/$query['gpu_bugedt']*100;
                                echo ceil($i)."%";
                            }else{
                                echo "0%";
                            }?>">
                  </div>
                </div>
            </div>
			<div class="quota">
				<h5>路由器可用<?php if(!empty($query['router_bugedt'])&&!empty($data['router_used']))
                        { 
                            echo $query['router_bugedt']-$data['router_used'];
                        }elseif (!empty($query['router_bugedt'])&&empty($data['router_used'])) {
                            echo $query['router_bugedt'];
                        }else{
                            echo "0";
                        }?>个，已使用<?php if(!empty($data['router_used']))
                        { 
                            echo $data['router_used'];
                        }else{
                            echo "0";
                        }?>个，使用率<?php if(!empty($query['router_bugedt'])&&!empty($data['router_used']))
                            { 
                                $i=$data['router_used']/$query['router_bugedt']*100;
                                echo ceil($i)."%";
                            }else{
                                echo "0%";
                            }?></h5>
				<div class="progress">
				  <div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: <?php if(!empty($query['router_bugedt'])&&!empty($data['router_used']))
                            { 
                                $i=$data['router_used']/$query['router_bugedt']*100;
                                echo ceil($i)."%";
                            }else{
                                echo "0%";
                            }?>">
				  </div>
				</div>
			</div>
			<!--<div class="quota">
				<h5>子网可用<?php if(!empty($query['subnet_bugedt'])&&!empty($data['subnet_used']))
                        {
                            echo $query['subnet_bugedt']-$data['subnet_used'];
                        }elseif (!empty($query['subnet_bugedt'])&&empty($data['subnet_used'])) {
                            echo $query['subnet_bugedt'];
                        }else{
                            echo "0";
                        }?>个，已使用<?php if(!empty($data['subnet_used']))
                        {
                            echo $data['subnet_used'];
                        }else{
                            echo "0";
                        }?>个，使用率<?php if(!empty($query['subnet_bugedt'])&&!empty($data['subnet_used']))
                            {
                                $i=$data['subnet_used']/$query['subnet_bugedt']*100;
                                echo ceil($i)."%";
                            }else{
                                echo "0%";
                            }?></h5>
				<div class="progress">
				  <div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: <?php if(!empty($query['subnet_bugedt'])&&!empty($data['subnet_used']))
                            {
                                $i=$data['subnet_used']/$query['subnet_bugedt']*100;
                                echo ceil($i)."%";
                            }else{
                                echo "0%";
                            }?>">
				  </div>
				</div>
			</div>-->
			<div class="quota">
				<h5>磁盘可用<?php if(!empty($query['disks_bugedt'])&&!empty($data['disks_used']))
                        { 
                            echo $query['disks_bugedt']-$data['disks_used'];
                        }elseif (!empty($query['disks_bugedt'])&&empty($data['disks_used'])) {
                            echo $query['disks_bugedt'];
                        }else{
                            echo "0";
                        }?>GB，已使用<?php if(!empty($data['disks_used']))
                        { 
                            echo $data['disks_used'];
                        }else{
                            echo "0";
                        }?>GB，使用率<?php if(!empty($query['disks_bugedt'])&&!empty($data['disks_used']))
                            { 
                                $i=$data['disks_used']/$query['disks_bugedt']*100;
                                echo ceil($i)."%";
                            }else{
                                echo "0%";
                            }?></h5>
				<div class="progress">
				  <div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: <?php if(!empty($query['disks_bugedt'])&&!empty($data['disks_used']))
                            { 
                                $i=$data['disks_used']/$query['disks_bugedt']*100;
                                echo ceil($i)."%";
                            }else{
                                echo "0%";
                            }?>">
				  </div>
				</div>
			</div>
<!-- <div class="quota">
	<h5>FICS存储卷可用<?php if(!empty($query['fics_num_bugedt'])&&!empty($data['fics_num_used']))
                        {
                            echo $query['fics_num_bugedt']-$data['fics_num_used'];
                        }elseif (!empty($query['fics_num_bugedt'])&&empty($data['fics_num_used'])) {
                            echo $query['fics_num_bugedt'];
                        }else{
                            echo "0";
                        }?>个，已使用<?php if(!empty($data['fics_num_used']))
                        {
                            echo $data['fics_num_used'];
                        }else{
                            echo "0";
                        }?>个，使用率<?php if(!empty($query['fics_num_bugedt'])&&!empty($data['fics_num_used']))
                            {
                                $i=$data['fics_num_used']/$query['fics_num_bugedt']*100;
                                echo ceil($i)."%";
                            }else{
                                echo "0%";
                            }?></h5>
	<div class="progress">
		<div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: <?php if(!empty($query['fics_num_bugedt'])&&!empty($data['fics_num_used']))
                            {
                                $i=$data['fics_num_used']/$query['fics_num_bugedt']*100;
                                echo ceil($i)."%";
		}else{
		echo "0%";
		}?>">
	</div>
</div>
</div>
<div class="quota">
	<h5>FICS存储卷可用<?php if(!empty($query['fics_cap_bugedt'])&&!empty($data['fics_cap_used']))
                        {
                            echo $query['fics_cap_bugedt']-$data['fics_cap_used'];
                        }elseif (!empty($query['fics_cap_bugedt'])&&empty($data['fics_cap_used'])) {
                            echo $query['fics_cap_bugedt'];
                        }else{
                            echo "0";
                        }?>GB，已使用<?php if(!empty($data['fics_cap_used']))
                        {
                            echo $data['fics_cap_used'];
                        }else{
                            echo "0";
                        }?>GB，使用率<?php if(!empty($query['fics_cap_bugedt'])&&!empty($data['fics_cap_used']))
                            {
                                $i=$data['fics_cap_used']/$query['fics_cap_bugedt']*100;
                                echo ceil($i)."%";
                            }else{
                                echo "0%";
                            }?></h5>
	<div class="progress">
		<div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: <?php if(!empty($query['fics_cap_bugedt'])&&!empty($data['fics_cap_used']))
                            {
                                $i=$data['fics_cap_used']/$query['fics_cap_bugedt']*100;
                                echo ceil($i)."%";
		}else{
		echo "0%";
		}?>">
	</div>
</div>
</div> -->
<div class="quota">
	<h5>H9000存储卷可用<?php if(!empty($query['oceanstor9k_num_bugedt'])&&!empty($data['oceanstor9k_num_used']))
                        {
                            echo $query['oceanstor9k_num_bugedt']-$data['oceanstor9k_num_used'];
                        }elseif (!empty($query['oceanstor9k_num_bugedt'])&&empty($data['oceanstor9k_num_used'])) {
                            echo $query['oceanstor9k_num_bugedt'];
                        }else{
                            echo "0";
                        }?>个，已使用<?php if(!empty($data['oceanstor9k_num_used']))
                        {
                            echo $data['oceanstor9k_num_used'];
                        }else{
                            echo "0";
                        }?>个，使用率<?php if(!empty($query['oceanstor9k_num_bugedt'])&&!empty($data['oceanstor9k_num_used']))
                            {
                                $i=$data['oceanstor9k_num_used']/$query['oceanstor9k_num_bugedt']*100;
                                echo ceil($i)."%";
                            }else{
                                echo "0%";
                            }?></h5>
	<div class="progress">
		<div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: <?php if(!empty($query['oceanstor9k_num_bugedt'])&&!empty($data['oceanstor9k_num_used']))
                            {
                                $i=$data['oceanstor9k_num_used']/$query['oceanstor9k_num_bugedt']*100;
                                echo ceil($i)."%";
		}else{
		echo "0%";
		}?>">
	</div>
</div>
</div>
<div class="quota">
	<h5>H9000存储卷可用<?php if(!empty($query['oceanstor9k_cap_bugedt'])&&!empty($data['oceanstor9k_cap_used']))
                        {
                            echo $query['oceanstor9k_cap_bugedt']-$data['oceanstor9k_cap_used'];
                        }elseif (!empty($query['oceanstor9k_cap_bugedt'])&&empty($data['oceanstor9k_cap_used'])) {
                            echo $query['oceanstor9k_cap_bugedt'];
                        }else{
                            echo "0";
                        }?>GB，已使用<?php if(!empty($data['oceanstor9k_cap_used']))
                        {
                            echo $data['oceanstor9k_cap_used'];
                        }else{
                            echo "0";
                        }?>GB，使用率<?php if(!empty($query['oceanstor9k_cap_bugedt'])&&!empty($data['oceanstor9k_cap_used']))
                            {
                                $i=$data['oceanstor9k_cap_used']/$query['oceanstor9k_cap_bugedt']*100;
                                echo ceil($i)."%";
                            }else{
                                echo "0%";
                            }?></h5>
	<div class="progress">
		<div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: <?php if(!empty($query['oceanstor9k_cap_bugedt'])&&!empty($data['oceanstor9k_cap_used']))
                            {
                                $i=$data['oceanstor9k_cap_used']/$query['oceanstor9k_cap_bugedt']*100;
                                echo ceil($i)."%";
		}else{
		echo "0%";
		}?>">
	</div>
</div>
</div>
<div class="quota">
	<h5>桌面基础套件可用<?php if(!empty($query['basic_budget'])&&!empty($data['basic_used']))
                        {
                            echo $query['basic_budget']-$data['basic_used'];
                        }elseif (!empty($query['basic_budget'])&&empty($data['basic_used'])) {
                            echo $query['basic_budget'];
                        }else{
                            echo "0";
                        }?>套，已使用<?php if(!empty($data['basic_used']))
                        {
                            echo $data['basic_used'];
                        }else{
                            echo "0";
                        }?>个，使用率<?php if(!empty($query['basic_budget'])&&!empty($data['basic_used']))
                            {
                                $i=$data['basic_used']/$query['basic_budget']*100;
                                echo ceil($i)."%";
                            }else{
                                echo "0%";
                            }?></h5>
	<div class="progress">
		<div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: <?php if(!empty($query['basic_budget'])&&!empty($data['basic_used']))
                            {
                                $i=$data['basic_used']/$query['basic_budget']*100;
                                echo ceil($i)."%";
		}else{
		echo "0%";
		}?>">
	</div>
</div>
</div>
<div class="quota">
	<h5>防火墙可用<?php if(!empty($query['fire_budget'])&&!empty($data['fire_used']))
                        {
                            echo $query['fire_budget']-$data['fire_used'];
                        }elseif (!empty($query['fire_budget'])&&empty($data['fire_used'])) {
                            echo $query['fire_budget'];
                        }else{
                            echo "0";
                        }?>套，已使用<?php if(!empty($data['fire_used']))
                        {
                            echo $data['fire_used'];
                        }else{
                            echo "0";
                        }?>个，使用率<?php if(!empty($query['fire_budget'])&&!empty($data['fire_used']))
                            {
                                $i=$data['fire_used']/$query['fire_budget']*100;
                                echo ceil($i)."%";
                            }else{
                                echo "0%";
                            }?></h5>
	<div class="progress">
		<div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: <?php if(!empty($query['fire_budget'])&&!empty($data['fire_used']))
                            {
                                $i=$data['fire_used']/$query['fire_budget']*100;
                                echo ceil($i)."%";
		}else{
		echo "0%";
		}?>">
	</div>
</div>
</div>
<div class="quota">
	<h5>负载均衡可用<?php if(!empty($query['elb_budget'])&&!empty($data['elb_used']))
                        {
                            echo $query['elb_budget']-$data['elb_used'];
                        }elseif (!empty($query['elb_budget'])&&empty($data['elb_used'])) {
                            echo $query['elb_budget'];
                        }else{
                            echo "0";
                        }?>套，已使用<?php if(!empty($data['elb_used']))
                        {
                            echo $data['elb_used'];
                        }else{
                            echo "0";
                        }?>个，使用率<?php if(!empty($query['elb_budget'])&&!empty($data['elb_used']))
                            {
                                $i=$data['elb_used']/$query['elb_budget']*100;
                                echo ceil($i)."%";
                            }else{
                                echo "0%";
                            }?></h5>
	<div class="progress">
		<div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: <?php if(!empty($query['elb_budget'])&&!empty($data['elb_used']))
                            {
                                $i=$data['elb_used']/$query['elb_budget']*100;
                                echo ceil($i)."%";
		}else{
		echo "0%";
		}?>">
	</div>
</div>
</div>
<div class="quota">
	<h5>公网IP可用<?php if(!empty($query['eip_budget'])&&!empty($data['eip_used']))
                        {
                            echo $query['eip_budget']-$data['eip_used'];
                        }elseif (!empty($query['eip_budget'])&&empty($data['eip_used'])) {
                            echo $query['eip_budget'];
                        }else{
                            echo "0";
                        }?>个，已使用<?php if(!empty($data['eip_used']))
                        {
                            echo $data['eip_used'];
                        }else{
                            echo "0";
                        }?>个，使用率<?php if(!empty($query['eip_budget'])&&!empty($data['eip_used']))
                            {
                                $i=$data['eip_used']/$query['eip_budget']*100;
                                echo ceil($i)."%";
                            }else{
                                echo "0%";
                            }?></h5>
	<div class="progress">
		<div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: <?php if(!empty($query['eip_budget'])&&!empty($data['eip_used']))
                            {
                                $i=$data['eip_used']/$query['eip_budget']*100;
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