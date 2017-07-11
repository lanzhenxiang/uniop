<style>
	.quota {
		width:60%;
	}
	.quota h5{
		padding:5px 0;
	}
	.quota .progress{
		margin:10px 0 20px;
	}
	.quota:last-child{
		margin-bottom: 100px;
	}
</style>
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
                        }?>核</h5>
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
                        }?>GB</h5>
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
                        }?>个</h5>
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
			<div class="quota">
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
                        }?>个</h5>
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
			</div>
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
                        }?>GB</h5>
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
		</div>
	</div>
</div>		