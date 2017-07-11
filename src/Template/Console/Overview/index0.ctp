<div class="wrap-nav-right wrap-index-page ">
	<div id="maindiv-alert"></div>

  <?php foreach ($vpcs as $key => $value) {?>
  <div class="plate" id="<?= $value['code']?>" data-company-id="<?php echo $value['agentId']?>" data-area-id="<?php echo $value['regionId']?>" data-vpc-code="<?php echo $value['code']?>" style="min-width:1200px;">
	<div class="line bg-default"></div>
	<div class="plate-header">
	  <h4><?php echo $value['name']?></h4>
	</div>
		<div class="plate-body" style="min-width:1200px;">
			<div class="plate-body-wrapper">
			<?php if(!empty($value['lbs'])){ ?>
			<div class="elb-addition-panel clearfix" style="display:none">
			<div class="elb-addition">
				<div class="drawing-elb-group text-center">
					<div class="drawing-btn-prev" style="margin-top:-44px;">
						<i class="icon-caret-left"></i>
					</div>
					<div class="swiper-container drawing-elb-list">
						<div class="swiper-wrapper">
							<?php foreach ($value['lbs'] as $l => $lv) { ?>
								<div class="drawing-elb swiper-slide">
									<div class="drawing-elb-name">
										<a href=""><?= $lv["name"] ?></a>
									</div>
									<div class="drawing-elb-model">
										<div class="drawing-model-remove" data-toggle="modal"  data-code="<?php echo $lv['code'] ?>" data-ip="<?php echo $lv['ip']  ?>"
						data-basic-id="<?php echo $lv["basic_id"] ?>" data-name="<?php echo $lv['name'] ?>" data-status="<?php echo $lv['status'] ?>" data-listen="<?php echo $lv['listen'] ?>" onclick="lbsDel_click(this)" >
											<i class="icon-remove"></i>
										</div>
									</div>
								</div>
						<?php } ?>
						</div>
					</div>
					<div class="drawing-btn-next" style="margin-top:-44px;">
						<i class="icon-caret-right"></i>
					</div>
				</div>
			</div>
			</div>
			<?php } ?>

			<div class="drawing-content clearfix">
		<div class="drawing-option-prev">
		  <i class="icon-caret-up"></i>
		</div>
		<div class="drawing-main pull-left">
		  <div class="drawing-internet drawing-model pull-left">
			<div class="drawing-main-model <?php if(empty($value['code'])){echo 'poweroff';}?>">
		  		<img src="/imgs/bg.png" />
		  	</div>
			<div class="drawing-name text-center">
			  <a href="">公网IP</a>
			</div>
		  </div>
		  <div class="drawing-wall drawing-model pull-left">
			<div class="drawing-main-model <?php if(empty($value['firewallArr']['code'])){echo 'poweroff';}?>">
		  		<img src="/imgs/bg.png" />
		  	</div>
			<div class="drawing-name text-center">
			  <a href=""><?php echo $value['firewallArr']['name']?></a>
			</div>
		  </div>
		  <div class="drawing-domain drawing-model pull-left">
		  	<div class="drawing-main-model <?php if(empty($value['routerArr']['code'])){echo 'poweroff';}?> ">
		  		<img src="/imgs/bg.png" />
		  	</div>
			<div class="drawing-name text-center">
			  <a><?php echo $value['routerArr']['name']?></a>
			</div>
		  </div>
		</div>

		<div class="drawing-option pull-left swiper-container">


		  <div class="drawing-unit-list swiper-wrapper">

		  <?php foreach ($value['subnets'] as $s => $subnet) {?>

		  <div class="drawing-unit swiper-slide" data-firewall-code="<?php echo $subnet['code']?>">
			<div class="drawing-vpc pull-left">
			  
			  <div class="drawing-vpc-model <?php switch ($subnet['status']) {
						case '创建中':
							echo 'poweroff01';
							break;
						case '创建失败':
							echo 'powerofferror';
							break;
						case '已停止':
							echo 'poweroffclosed';
							break;
						case '已暂停(欠费)':
							echo 'poweroffclosed';
							break;
						case '销毁中':
							echo 'poweroffclosing';
							break;
						case '销毁失败':
							echo 'powerofferror';
							break;				
					}?>" data-isFusion="<?php echo $subnet['isFusion']?>" data-subnetCode="<?php echo $subnet['code']?>">
				<div class="drawing-model-remove" data-toggle="modal"  data-isFusion="<?php echo $subnet['isFusion']?>" data-subnetname="<?php echo $subnet['name']?>" data-subnetid="<?php echo $subnet['basic_id']?>" data-routerCode="<?php echo $value['routerArr']['code']?>" data-subnetCode="<?php echo $subnet['code']?>"  data-status="<?php echo $subnet['status'] ?>" onclick = "delSubnet(this)" >
				  <i class="icon-remove"></i>
				</div>
			  </div>
			  <div class="drawing-vpc-name" title="<?php  echo $subnet['name']?>">
				<a href=""><?php  echo $subnet['name'];?></a>
			  </div>
			</div>
			<div class="drawing-ip-group pull-left">
			  <div class="drawing-btn-prev">
				<i class="icon-caret-left"></i>
			  </div>
			  <div class="swiper-container drawing-group">
				<div class="swiper-wrapper text-center">
				  <?php foreach ($subnet['hosts'] as $k => $hosts) {?>
				  <div class="swiper-slide drawing-ip">
					
					<div class="<?php if($hosts['type'] == 'hosts'){echo 'drawing-ip-model ';}else{echo 'drawing-desktop-model ';}?> <?php switch ($hosts['status']) {
						case '创建中':
							echo 'poweroff01';
							break;
						case '创建失败':
							echo 'powerofferror';
							break;
						case '已停止':
							echo 'poweroffclosed';
							break;
						case '已暂停(欠费)':
							echo 'poweroffclosed';
							break;
						case '销毁中':
							echo 'poweroffclosing';
							break;
						case '销毁失败':
							echo 'powerofferror';
							break;			
					}?>" <?php
					if($hosts['type']=='hosts'){
						$_host_loc_name = explode('-', $hosts['location_name']);
						if ($_host_loc_name[0] == "阿里云") {
							$_os = $hosts['plat_form'];
							if ($_os != null) {
								if ($hosts['status'] == "运行中") {
									echo 'onclick="aliyunHosts(this)"';
								}
							}
						}else if ($_host_loc_name[0] == "索贝") {
							if ($hosts['status'] == "运行中") {
								$_os = $hosts['plat_form'];
								if ($_os != null) {
									if ($hosts['status'] == "运行中") {
										echo 'onclick="sobeyHosts(this)"';
									}
								}
							}
						}
					}elseif($hosts['type']=='desktop'){
					    if ($hosts['desktop_name'] != null) {
					        $_os = $hosts['plat_form'];
							if ($_os != null) {
								if ($hosts['status'] == "运行中") {
									echo 'onclick="desktopFormatter(this)"';
								}
							}
					    }
					}?> data-password= "<?= $hosts['vnc_password']?>" data-code ="<?= $hosts['code']?>" data-id = "<?= $hosts['id']?>" data-desktop-name = "<?= $hosts['desktop_name']?>">
					<?php if($hosts['isdelete'] !='1'){?>
					  <div class="drawing-model-remove">
						<i class="icon-remove" data-code="<?php echo $hosts['code']?>"
						data-basic-id="<?php echo $hosts["id"]?>" data-name="<?php echo $hosts['name']?>" data-eip="<?php echo $hosts['ip']?>" data-status="<?php echo $hosts['status']?>" data-toggle="modal"  <?php if($hosts['type']=='hosts'){echo 'onclick="ecsDel_click(this)"';}else{echo 'onclick="desktopDel_click($this)"';}?> ></i>
					  </div>
					  <?php }?>
					</div>
					<div class="drawing-group-name">
						<?php if($hosts['isdelete']=='1'){?>
							<span title="已移到回收站" class="drawing-del"></span>
						<?php }?>
						<?php if($hosts['type']=='hosts'){?>
							<a title="<?php echo $hosts['name'] ?>" href="/console/network/data/hosts/<?= $hosts['id'] ?>"><?php echo $hosts['name'] ?></a>
						<?php }else{?>
							<a ><?php echo $hosts['name'] ?></a>
						<?php }?>
					  
					</div>
				  </div>
				  <?php } ?>
				  <?php foreach ($subnet['ad'] as $k => $ad) {?>
				  <div class="swiper-slide drawing-ip">
					<div class="drawing-zmtj-model <?php switch ($ad['status']) {
						case '创建中':
							echo 'poweroff01';
							break;
						case '创建失败':
							echo 'powerofferror';
							break;
						case '已停止':
							echo 'poweroffclosed';
							break;
						case '已暂停(欠费)':
							echo 'poweroffclosed';
							break;
						case '销毁中':
							echo 'poweroffclosing';
							break;
						case '销毁失败':
							echo 'powerofferror';
							break;			
					}?>"  data-password= "<?= $ad['vnc_password']?>" data-code ="<?= $ad['code']?>" data-id = "<?= $ad['id']?>" data-desktop-name = "<?= $ad['desktop_name']?>">
					</div>
					<div class="drawing-group-name">
						<?php if($ad['isdelete']=='1'){?>
							<span title="已移到回收站" class="drawing-del"></span>
						<?php }?>
						<a title = "<?php echo $ad['name'] ?>"><?php echo $ad['name'] ?></a>
					</div>
				  </div>
				  <?php } ?>
				  <?php foreach ($subnet['lbs'] as $k => $lbs) {?>
				  <div class="swiper-slide drawing-ip">
					
					<div class="drawing-elb-model">
						<div class="drawing-model-remove" data-toggle="modal"  data-code="<?php echo $lv['code'] ?>" data-ip="<?php echo $lv['ip']  ?>"
						data-basic-id="<?php echo $lv["basic_id"] ?>" data-name="<?php echo $lv['name'] ?>" data-status="<?php echo $lv['status'] ?>" data-listen="<?php echo $lv['listen'] ?>" onclick="lbsDel_click(this)" >
							<i class="icon-remove"></i>
						</div>
					</div>
					<div class="drawing-group-name" title="<?php echo $lbs['name'] ?>">
					  <a href="#"><?php echo $lbs['name'] ?></a>
					</div>
				  </div>
				  <?php } ?>
				  <!-- <div class="swiper-slide drawing-ip-add" data-toggle="modal" data-target="#modal-host">
					<div class="drawing-ip-add-model"></div>
				  </div> -->
				</div>
			  </div>
			  <div class="drawing-btn-next">
				<i class="icon-caret-right"></i>
			  </div>
			  <div class="drawing-ip-text text-center">
				<?php echo $subnet['cidr'] ?>
			  </div>
			</div>
			<?php if(!empty($subnet['code']) && $subnet['status'] =='运行中'){?>
            <div class="drawing-ip-add-model pull-left"  id="drawing-ip-add">
              	<img src="/images/drawing-ip-add.png" />
              	<div class="drawing-ip-add-tip">
              		<p><a href="javascript:;" data-toggle="modal" data-target="#modal-host">添加主机</a></p>
              		<?php if($value['is_desktop'] == 1){?>
              		<p><a href="javascript:;" data-toggle="modal" data-target="#modal-desktop">添加云桌面</a></p>
              		<?php }?>
              		<!-- <p><a href="javascript:;" data-toggle="modal" data-target="#modal-elb">添加负载均衡</a></p> -->
              	</div>
            </div>
            <?php }?>
          </div>
		  <?php } ?>
		  <div class="drawing-unit swiper-slide" >
		  <?php if(!empty($value['routerArr']['code'])){?>
			<div class="drawing-vpc-add pull-left" data-toggle="modal" data-target="#modal" data-router="<?php echo $value['routerArr']['name']?>" data-routercode="<?php echo $value['routerArr']['code']?>" data-agentcode="<?php echo $value['routerArr']['location_code']?>" data-routerid="<?php echo $value['routerArr']['id']?>">
			</div>
			<?php }?>
		  </div>
		  </div>
		</div>


		<div class="drawing-option-next">
		  <i class="icon-caret-down"></i>
		</div>

	  </div>


	</div>
		<div class="statics">
			<div class="items eip eip-null" style="cursor: pointer;">
				<div></div>EIP池
			</div>
			<div class="items store store-null" style="cursor: pointer;">
				<div></div>媒体云存储
			</div>
			<div class="items cycle cycle-null" style="cursor: pointer;">
				<div></div>回收站
			</div>
		</div>
	</div>
  </div>
  <?php }?>
</div>

<!-- Modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="modal">
  <div class="modal-dialog" role="document">
	<div class="modal-content">
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title">添加子网</h4>
	  </div>
	  <div class="modal-body">
		<div class="modal-form-group">
		  <label>厂商 : </label>
		  <div><input type="text" value="" id="firm"  disabled />
			  <input type="hidden" value="" id="region_code" disabled />
			  <input type="hidden" id="goods_id">
		  </div>
		</div>
		<div class="modal-form-group">
		  <label>地域 : </label>
		  <div><input type="text" value="" id="area" disabled /></div>
		</div>
		<div class="modal-form-group" id="virtual" hidden="hidden">
			  <label for="" class="pull-left">虚拟化技术:</label>
			  <div class="bk-form-row-cell">
				  <ul class="clearfix city">
					  <li class="active">
						  VMware
					  </li>
					  <li>
						  OpenStack
					  </li>
				  </ul>
			  </div>
			<input type="hidden" id="virtual-value" />
		</div>
		<div class="modal-form-group">
		  <label>路由器 : </label>
		  <div><input type="text" value="" id="router" disabled />
			  <input type="hidden" value="" id="routerCode" disabled />
			  <input type="hidden" value="" id="routerid" disabled />
			  <span class="text-danger rou-ter"></span>
		  </div>

		</div>
		<div class="modal-form-group">
		  <label>子网名称 : </label>
		  <div>
			<input type="text" id="name" />
			  <span class="text-danger warning-text sub-net"></span>
		  </div>
		</div>
		<div class="modal-form-group">
		  <label>网络地址 : </label>
		  <div class="modal-form-group-more">
			<input type="text" class="ip1" id="ip1" disabled>
			&nbsp;&nbsp;<strong>·</strong>&nbsp;&nbsp;
			<input type="text" class="ip2" id="ip2" disabled>
			&nbsp;&nbsp;<strong>·</strong>&nbsp;&nbsp;
			<input type="text" class="ip3" id="net">
			&nbsp;&nbsp;<strong>·</strong>&nbsp;&nbsp;
			<input type="text" class="ip4"  disabled>
			&nbsp;&nbsp;/&nbsp;&nbsp;24
			  <span class="text-danger i-p"></span>
		  </div>
		</div>
		<div class="modal-form-group">
		  <label>默认网关 : </label>
		  <div class="modal-form-group-more">
			<input type="text" class="ip1" disabled>
			&nbsp;&nbsp;<strong>·</strong>&nbsp;&nbsp;
			<input type="text" class="ip2" disabled>
			&nbsp;&nbsp;<strong>·</strong>&nbsp;&nbsp;
			<input type="text" class="ip3" id="net-follow">
			&nbsp;&nbsp;<strong>·</strong>&nbsp;&nbsp;
			<input type="text" value="1"  disabled>
		  </div>
		</div>
	  </div>
	  <div class="modal-footer">
		<button type="button" class="btn btn-primary" id="submit">确 认</button>
		<button type="button" class="btn btn-danger" data-dismiss="modal">取 消</button>
	  </div>
	</div>
  </div>
</div>

<!-- ModalHost -->
<div class="modal fade" tabindex="-1" role="dialog" id="modal-host">
  <div class="modal-dialog" role="document">
	<div class="modal-content">
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title">添加主机</h4>
	  </div>
	  <form action=""  method="POST">
	  <div class="modal-body">
    	<input type="hidden" id="host-uid" name="uid" value="<?=$this->Session->read('Auth.User.id')?>" />
		<div class="modal-form-group">
		  <label>厂商 : </label>
		  <div><input type="text" disabled id="host-company" /></div>
		</div>
		<div class="modal-form-group">
		  <label>地域 : </label>
		  <div><input type="text" disabled id="host-area" /></div>
		  <input type="hidden" id="isFusion" name="isFusion" />
		  <input type="hidden" id="host-area-code" name="regionCode" />
		</div>
		<div class="modal-form-group">
		  <label>CPU : </label>
		  <div class="bk-form-row-cell" id="host-cpu">
			  <ul class="clearfix city">

			  </ul>
		  </div>
		</div>
		<div class="modal-form-group">
		  <label>内存 : </label>
		  <div class="bk-form-row-cell" id="host-rom">

		  </div>
		  <input type="hidden" id="host-set" name="instanceTypeCode" />
		</div>
		<div class="modal-form-group">
		  <label>实例名称 : </label>
		    <div>
		  		<input type="text" id="ecsName" name="ecsName"/>
		  		<span id="ecs-name-warning" class="text-danger warning-text"></span>
		  	</div>
		</div>
		<div class="modal-form-group">
		  <label>VPC : </label>
		  <div>
			<input type="text" disabled id="host-vpc" />
			<input type="hidden" id="host-vpc-code"  name="vpcCode"/>
		  </div>
		</div>
		<div class="modal-form-group">
		  <label>网络 : </label>
		  <div>
			<input type="text" disabled id="host-net" />
			<input type="hidden" id="host-net-code"  name="subnetCode"/>
		  </div>
		</div>
		<div class="modal-form-group">
		  <label>镜像类型 : </label>
		  <div class="bk-form-row-cell">
			<ul class="clearfix city">
			  <li class="active">
				系统镜像
			  </li>
			</ul>
		  </div>
		</div>
		<div class="modal-form-group">
		  <label>操作系统 : </label>
		  <div class="bk-form-row-cell" id="host-os">
			<ul class="clearfix city">

			</ul>
		  </div>
		</div>
		<div class="modal-form-group">
		  <label></label>
		  <div class="bk-form-row-cell" id="host-os-type">
		  </div>
		  <input type="hidden" id="host-os-code"  name="imageCode"/>
		</div>
		<div class="modal-form-group">
		  <label>数量 : </label>
		  <div>
			<input type="number" id="text_number" value="1" />台
		  </div>
		</div>
	  </div>
	  <div class="modal-footer">
  		<button type="button" id ="creatHost" class="btn btn-primary" >确 认</button>
		<button type="button" class="btn btn-danger" data-dismiss="modal">取 消</button>
	  </div>
	  </form>
	</div>
  </div>
</div>

<!-- ModalDesktop -->
<div class="modal fade" tabindex="-1" role="dialog" id="modal-desktop">
  <div class="modal-dialog" role="document">
	<div class="modal-content">
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title">添加云桌面</h4>
	  </div>
	  <form action=""  method="POST">
	  <div class="modal-body">
    	<input type="hidden" id="desktop-uid" name="uid" value="<?=$this->Session->read('Auth.User.id')?>" />
		<div class="modal-form-group">
		  <label>厂商 : </label>
		  <div><input type="text" disabled id="desktop-company" /></div>
		</div>
		<div class="modal-form-group">
		  <label>地域 : </label>
		  <div><input type="text" disabled id="desktop-area" /></div>
		  <input type="hidden" id="desktop-isFusion" name="isFusion" />
		  <input type="hidden" id="desktop-area-code" name="regionCode" />
		</div>
		<div class="modal-form-group">
		  <label>实例名称 : </label>
		    <div>
		  		<input type="text" id="desktop-name" />
		  		<span id="desktop-name-warning" class="text-danger warning-text"></span>
		  	</div>
		</div>
		<div class="modal-form-group">
		  <label>VPC : </label>
		  <div>
			<input type="text" disabled id="desktop-vpc" />
			<input type="hidden" id="desktop-vpc-code"  name="vpcCode"/>
		  </div>
		</div>
		<div class="modal-form-group">
		  <label>网络 : </label>
		  <div>
			<input type="text" disabled id="desktop-net" />
			<input type="hidden" id="desktop-net-code"  name="subnetCode"/>
		  </div>
		</div>
		<div class="modal-form-group">
		  <label>品牌 : </label>
		  <div class="bk-form-row-cell" id="desktop-brand">
			<ul class="clearfix city">

			</ul>
		  </div>
		</div>
		<div class="modal-form-group">
		  <label>规格 : </label>
		  <div class="bk-form-row-cell" id="desktop-format">
		  </div>
		  <input type="hidden" id="desktop-os-code"  name="imageCode"/>
		  <input type="hidden" id="desktop-hard-code"  name="imageCode"/>
		  <input type="hidden" id="desktop-cpu"  name="imageCode"/>
		  <input type="hidden" id="desktop-memory"  name="imageCode"/>
		  <input type="hidden" id="desktop-gpu"  name="imageCode"/>
		</div>
		<div class="modal-form-group">
		  <label>账号类型 : </label>
		  <div class="bk-form-row-cell" >
			<ul class="clearfix city" id="account-tab">
				<li class="active">创建账号(AD)</li>
				<li>选择账号</li>
			</ul>
			<input type="hidden" name="ad" id="ad" value="1"/>
		  </div>
		</div>
		<div class="modal-form-group">
		  <label></label>
		  <div class="bk-form-row-cell">
		  	<div class="account-tab-content">
				<p style="margin-bottom:5px;">登录账号 :<input type="text" id="txtaduser" style="margin-left:15px;" /><span class="text-danger txtaduser"></span></p>
				<p style="margin-bottom:5px;">登录密码 :<input type="password" id="txtpwd1" style="margin-left:15px;" /><span class="text-danger txtpwd1"></span></p>
				<p style="margin-bottom:5px;">登录密码 :<input type="password" id="txtpwd2" style="margin-left:15px;" /><span class="text-danger txtpwd2"></span></p>
			</div>
			<div style="display:none;" class="account-tab-content">

				<p>登录账号 :<select id="desktop-account" style="margin-left:15px;"></select></p>
			</div>
		  </div>
		</div>
		<div class="modal-form-group">
		  <label>数量 : </label>
		  <div>
			<input type="number" id="desktop_number" value="1" />台
		  </div>
		</div>
	  </div>
	  <div class="modal-footer">
  		<button type="button" id ="creatDesktop" class="btn btn-primary" >确 认</button>
		<button type="button" class="btn btn-danger" data-dismiss="modal">取 消</button>
	  </div>
	  </form>
	</div>
  </div>
</div>

<!-- ModalElb -->
<div class="modal fade" tabindex="-1" role="dialog" id="modal-elb">
  <div class="modal-dialog" role="document">
	<div class="modal-content">
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title">添加负载均衡</h4>
	  </div>
	  <form method="POST" id="add-elb-form">
	  <div class="modal-body">
		<input type="hidden" id="host-uid" name="uid" value="<?=$this->Session->read('Auth.User.id')?>" />
		<div class="modal-form-group">
		  <label>厂商 : </label>
		  <div>
		  	<input type="text" disabled id="elb-company" />
		  </div>
		</div>
		<div class="modal-form-group">
		  <label>地域 : </label>
		  <div>
		  	<input type="text" disabled id="elb-area" />
		  	<input type="hidden" name="regionCode" id="elb-area-code" />
		  </div>
		</div>
		<div class="modal-form-group">
		  <label>名称 : </label>
		  <div>
		  		<input type="text" name="lbsName" id="elb-name"/>
		  		<span class="text-danger warning-text" id="elb-name-warning"></span>
		  </div>
		</div>
		<div class="modal-form-group">
		  <label>VPC : </label>
		  <div>
		  		<input type="text" id="elb-vpc" disabled />
		  		<input type="hidden" id="elb-vpc-code" name="vpcCode" />
		  </div>
		</div>
		<div class="modal-form-group">
		  <label>网络 : </label>
		  <div id="elb-net"></div>
		</div>
		<div class="modal-form-group">
		  <label>公网IP : </label>
		  <div id="elb-eip"></div>
		</div>
	  </div>
	  <div class="modal-footer">
		<button type="button" class="btn btn-primary" onclick="elbSubmit()" id="elb-btn">确 认</button>
		<button type="button" class="btn btn-danger" data-dismiss="modal">关 闭</button>
	  </div>
	  </form>
	</div>
  </div>
</div>

<!-- ModalHostRemove -->
<div class="modal fade" tabindex="-1" role="dialog" id="modal-host-remove">
  <div class="modal-dialog" role="document">
	<div class="modal-content">
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title">删除子网</h4>
	  </div>
	  <div class="modal-body">
		<i class="icon-question-sign text-primary"></i>&nbsp;&nbsp;确定要删除&nbsp;<span class="text-primary" id="sub_name"></span>
	  </div>
	  <div class="modal-footer">
		<button type="button" class="btn btn-primary" id="yes">确 认</button>
		<button type="button" class="btn btn-danger" data-dismiss="modal">取 消</button>
	  </div>
	</div>
  </div>
</div>

<!-- ModalEcsRemove -->
<div class="modal fade" tabindex="-1" role="dialog" id="modal-ecs-remove">
  <div class="modal-dialog" role="document">
	<div class="modal-content">
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title">删除主机</h4>
	  </div>
	  <div class="modal-body">
		<i class="icon-question-sign text-primary"></i>&nbsp;&nbsp;确定要删除&nbsp;<span class="text-primary" id="delete_host_name_span"> </span>
	  </div>
	  <div class="modal-footer">
		<button type="button" id="delete_ecs_button" class="btn btn-primary">确 认</button>
		<button type="button" class="btn btn-danger" data-dismiss="modal">取 消</button>
	  </div>
	</div>
  </div>
</div>

<!-- ModalElbRemove -->
<div class="modal fade" tabindex="-1" role="dialog" id="modal-elb-remove" data-basic-id="">
  <div class="modal-dialog" role="document">
	<div class="modal-content">
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title">删除负载均衡</h4>
	  </div>
	  <div class="modal-body">
		<i class="icon-question-sign text-primary"></i>&nbsp;&nbsp;确定要删除&nbsp;<span class="text-primary" id="delete_lbs_name_span"></span>
	  </div>
	  <div class="modal-footer">
		<button type="button" class="btn btn-primary" id="delete-lbs-button" >确 认</button>
		<button type="button" class="btn btn-danger" data-dismiss="modal">取 消</button>
	  </div>
	</div>
  </div>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="modal-ecs">
  <div class="modal-dialog" role="document">
	<div class="modal-content">
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title" id="modal_ecs_name_title"></h4>
	  </div>
	  <div class="modal-body">
		<i class="icon-exclamation-sign icon-color-gray text-primary"></i><span id="modal_ecs_name_context1"></span><span class="text-primary" id="modal_ecs_name_context2"></span>
	  </div>
	  <div class="modal-footer">
		<!-- <button type="button" id="delete_ecs_button" class="btn btn-primary">确 认</button> -->
		<button type="button" class="btn btn-danger" data-dismiss="modal">关 闭</button>
	  </div>
	</div>
  </div>
</div>

<!-- 悬浮框 -->
<div class="drawing-pin">
  <div class="drawing-pin-title">
	<h5>vpc列表</h5>
	<input type="checkbox" checked="true" /> 全选
  </div>
  <?php foreach ($vpcs as $key => $value) {?>
  <div class="drawing-pin-option">
	<input type="checkbox" value="<?= $value['code']?>" checked="true" />&nbsp;&nbsp;<?= $value['name']?>
  </div>
  <?php }?>
  <div class="drawing-pin-btn">
	<button class="btn btn-primary" id="drawing-pin-btn" type="button">显示已选项</button>
  </div>
  <div class="drawing-pin-toggle">
	<i class="icon-reorder"></i>
  </div>
</div>

<!-- 错误框 -->
<div class="modal fade" role="dialog" id="modal-error">
  <div class="modal-dialog" role="document">
	<div class="modal-content">
	  <div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title">错误提示</h4>
	  </div>
	  <div class="modal-body">
		<i class="icon-remove-sign text-danger"></i>&nbsp;&nbsp;<span class="text-danger" id="modal-error-text"></span>
	  </div>
	  <div class="modal-footer">
		<button type="button" class="btn btn-primary" data-dismiss="modal">确 定</button>
	  </div>
	</div>
  </div>
</div>


<div id="maindiv"></div>

<?=$this->Html->script(['angular.min.js','controller/controller.js']); ?>
<script type="text/javascript">
  $(function() {

  $('.drawing-pin-toggle').bind('click', function() {
	var $pin = $('.drawing-pin');
	if ($pin.css("right") == "0" || $pin.css("right") == "0px") {
	  $pin.css("right", "-175px");
	} else {
	  $pin.css("right", "0");
	}
  });

  plateResize();

  $('.drawing-pin-title').on('click', 'input[type="checkbox"]', function() {
	if ($(this).prop('checked')) {
	  $('.drawing-pin-option').children('input[type="checkbox"]').prop('checked', true);
	} else {
	  $('.drawing-pin-option').children('input[type="checkbox"]').prop('checked', false);
	}
  });

  $('.drawing-pin-option').on('click', 'input[type="checkbox"]', function() {
	if (!$(this).prop('checked')) {
	  $('.drawing-pin-title').children('input[type="checkbox"]').prop('checked', false);
	} else {
	  return;
	}
  });

  function plateResize(){
  	  if($('.plate:visible').length==1){
	  	$('.plate:visible').height($(document).height()-180);
	  }else{
	  	$('.plate').css('min-height','871px');
	  }
  }
  
  


  $('#drawing-pin-btn').click(

  function() {
	$('.plate').css('display', 'none');
	$('.drawing-pin-option').children('input[type="checkbox"]:checked').each(

	function() {
	  $('#' + $(this).val()).css('display', 'block');
	  plateResize();
	});
  });

  $('.drawing-content').each(

  function() {
	var group = $(this).find('.drawing-option');
	var prev = $(this).find('.drawing-option-prev');
	var next = $(this).find('.drawing-option-next');
	new Swiper(group.get(0), {
	  direction: 'vertical',
	  slidesPerView: 3,
	  slidesPerGroup: 3,
	  nextButton: next.get(0),
	  prevButton: prev.get(0)
	});
  });

  $('.drawing-ip-group').each(

  function() {
	var group = $(this).find('.drawing-group');
	var prev = $(this).find('.drawing-btn-prev');
	var next = $(this).find('.drawing-btn-next');
	new Swiper(group.get(0), {
	  slidesPerView: 7,
	  slidesPerGroup: 7,
	  prevButton: prev.get(0),
	  nextButton: next.get(0)
	});
  });


  $('.drawing-elb-group').each(
  	function(){
  		var group = $(this).find('.drawing-elb-list');
  		var prev = $(this).find('.drawing-btn-prev');
		var next = $(this).find('.drawing-btn-next');
  		new Swiper(group.get(0),{
		  	slidesPerView: 4,
		  	sildesPerGroup: 4,
		  	prevButton: prev.get(0),
		  	nextButton: next.get(0)
		});
  	}
  );



  $('.drawing-content').hover(
  function() {
	$(this).find('.drawing-option-prev').css('display', 'block');
	$(this).find('.drawing-option-next').css('display', 'block');
  }, function() {
	$(this).find('.drawing-option-prev').css('display', 'none');
	$(this).find('.drawing-option-next').css('display', 'none');
  });


  $('#modal').on('hidden.bs.modal', function() {
	$('.i-p').html('');
	$('.sub-net').html('');
	$('.rou-ter').html('');
  })

  $('#modal-host').on('hidden.bs.modal', function() {
	$('#host-company').val("");
	$('#host-area').val("");
	$('#host-cpu ul').html("");
	$('#host-rom').html("");
	$('#host-vpc').val("");
	$('#host-net').val("");
	$('#host-os ul').html("");
	$('#host-os-type').html("");
	$('#host-os').off('click', 'li');
	$('#host-cpu').off('click', 'li');
  });
  $('#modal-desktop').on('hidden.bs.modal', function() {
	$('#desktop-company').val("");
	$('#desktop-area').val("");
	$('#desktop-vpc').val("");
	$('#desktop-net').val("");
	$('#desktop-brand ul').html("");
	$('#desktop-format').html("");
  });

  $('#modal-elb').on('hidden.bs.modal', function() {
  	$('#elb-company').val('');
	$('#elb-area').val('');
	$('#elb-name').val('')
	$('#elb-vpc').html('');
	$('#elb-net').html('');
	$('#elb-eip').html('');
	$('#elb-name-warning').html('');
	$('#elb-vpc').off('change');
	$('#elb-name').off('blur');
  });

  $('#modal-elb').on('show.bs.modal',function(event){
  	// console.log($(event.relatedTarget));
  	var array, company, area;
	var $target = $(event.relatedTarget);
	var $plate = $target.closest('.plate');
	var companyId = $plate.attr('data-company-id');
	var areaId = $plate.attr('data-area-id');
	var vpcCode = $plate.attr('data-vpc-code');
	var _select = "<select></select>";
	var _option = "<option></option>";
  	$.ajax({
	  'url': '/console/ajax/network/hosts/createHostsArray.json',
	}).done(
		function(result){
			array = $.parseJSON(result);
			$.each(array, function(i, n) {
				if (n.id === parseInt(companyId)) {
				    company = n;
				}
			});
			$.each(company.area, function(i, n) {
				if (n.id === parseInt(areaId)) {
				    area = n;
				}
			});
			$('#elb-company').val(company.company.name);
	  		$('#elb-area').val(area.name);
	  		$('#elb-area-code').val(area.areaCode);
	  		$.each(area.vpc, function(i, n) {
				if (n.vpCode === vpcCode) {
				  $('#elb-vpc').val(n.name);
				  $('#elb-vpc-code').val(n.vpCode);
				  var $netSelect = $(_select).attr('id','elb-net-code');
				  $.each(n.net, function(i, n){
					$netSelect.append($(_option).text(n.name).val(n.netCode));
				  });
				  $('#elb-net').append($netSelect);
				}
			});

			var $eipSelect = $(_select).attr('id','elb-eip-code');
			$.each(area.eip, function(i, n) {
				$eipSelect.append($(_option).text(n.name).val(n.eipCode));
			});
			$('#elb-eip').append($eipSelect);
			$('#elb-name').on('blur',function(){
				$('#elb-name-warning').html('');
			});
		}
	);
  });

  $('#modal-desktop').on('show.bs.modal', function(event) {
  	var array, company, area, software;
	var $target = $(event.relatedTarget);
	var $plate = $target.closest('.plate');
	var $subnet = $target.closest('#drawing-ip-add').prevAll('.drawing-vpc').find('.drawing-vpc-model');
	var companyId = $plate.attr('data-company-id');
	var areaId = $plate.attr('data-area-id');
	var vpcCode = $plate.attr('data-vpc-code');
	var netCode = $subnet.attr('data-subnetCode');
	var _ul = "<ul></ul>";
	var _li = "<li></li>";
	var _select = "<select></select>";
	var _option = "<option></option>";
  	$.ajax({
	  'url': '/console/ajax/desktop/desktop/createDesktopArray.json',
	}).done(
		function(result){
			array = $.parseJSON(result);
		    $.each(array, function(i, n) {
				if (n.id === parseInt(companyId)) {
				  company = n;
				}
			});
			$.each(company.area, function(i, n) {
				if (n.id === parseInt(areaId)) {
				  area = n;
				}
			});
			$.each(company.setsoftwareInfo,function(i,n){
				$('#desktop-brand ul').append($(_li).text(n.name));
				var $select = $(_select).on('change', function() {
					var set = $(this).val();
					set = set.split(',');
				  	$('#desktop-os-code').val(set[0]);
				  	$('#desktop-hard-code').val(set[1]);
				  	$('#desktop-cpu').val(set[2]);
				  	$('#desktop-memory').val(set[3]);
				  	$('#desktop-gpu').val(set[4]);
				});
				var $softwareSelect = $(_select);
				$.each(n.softwareInfo,function(i,n){
					$softwareSelect.append($(_option).text(n.name).val(n.imageCode+','+n.hardwareCode+','+n.hardwareCpu+','+n.hardwareMemory+','+n.hardwareGpu));
				});
				$('#desktop-format').append($softwareSelect);
			});



			$('#desktop-company').val(company.company.name);
			$('#desktop-area').val(area.name);
			$('#desktop-isFusion').val($subnet.attr('data-isFusion'))
	  		$('#desktop-area-code').val(area.areaCode);
			$.each(area.vpc, function(i, n) {
				if (n.vpcCode === vpcCode) {
				  $('#desktop-vpc').val(n.name);

				  $('#desktop-vpc-code').val(n.vpcCode);
				  $.each(n.aduser,function(i, n){
				  	$('#desktop-account').append($(_option).text(n.loginName));
				  });
				}
				$.each(n.net, function(i, n) {
				  if (n.netCode == netCode) {
					$('#desktop-net').val(n.name);
					$('#desktop-net-code').val(n.netCode);
				  }
				});
			});
			$('#desktop-brand li:first').addClass('active');
			$('#desktop-format select:first').css('display', 'block');

			if ($('#desktop-format select').eq(0) != null) {
				var set = $('#desktop-format select').eq(0).val();
					set = set.split(',');
				  	$('#desktop-os-code').val(set[0]);
				  	$('#desktop-hard-code').val(set[1]);
				  	$('#desktop-cpu').val(set[2]);
				  	$('#desktop-memory').val(set[3]);
				  	$('#desktop-gpu').val(set[4]);
	  		}
			$('#desktop-brand').on('click', 'li', function() {
				$('#desktop-format select').css('display', 'none');
				$('#desktop-format select').eq($(this).index()).css('display', 'block');
				var set = $('#desktop-format select').eq($(this).index()).val();
					set = set.split(',');
				  	$('#desktop-os-code').val(set[0]);
				  	$('#desktop-hard-code').val(set[1]);
				  	$('#desktop-cpu').val(set[2]);
				  	$('#desktop-memory').val(set[3]);
				  	$('#desktop-gpu').val(set[4]);
			});

			$('#desktop-format select').on('change', function() {
				var set = $('#desktop-format select').eq($(this).index()).val();
					set = set.split(',');
				  	$('#desktop-os-code').val(set[0]);
				  	$('#desktop-hard-code').val(set[1]);
				  	$('#desktop-cpu').val(set[2]);
				  	$('#desktop-memory').val(set[3]);
				  	$('#desktop-gpu').val(set[4]);
			});
			$('#account-tab').on('click', 'li', function(){
				$('.account-tab-content').css('display', 'none');
				$('.account-tab-content').eq($(this).index()).css('display', 'block');
				switch($(this).index()){
					case 0:{
						$("#ad").val(1);
						break;
					}
					case 1:{
						$("#ad").val(2);
						break;
					}
					default:{
						$("#ad").val("");
					}
				}
			});
		}
	);
  });

  $('#modal-host').on('show.bs.modal', function(event) {
	var array, company, area;
	var $target = $(event.relatedTarget);
	var $plate = $target.closest('.plate');
	var $subnet = $target.closest('#drawing-ip-add').prevAll('.drawing-vpc').find('.drawing-vpc-model');
	var companyId = $plate.attr('data-company-id');
	var areaId = $plate.attr('data-area-id');
	var vpcCode = $plate.attr('data-vpc-code');
	var netCode = $subnet.attr('data-subnetCode');
	var _ul = "<ul></ul>";
	var _li = "<li></li>";
	var _select = "<select></select>";
	var _option = "<option></option>";
	$.ajax({
	  'url': '/console/ajax/network/hosts/createHostsArray.json',
	}).done(

	function(result) {
	  array = $.parseJSON(result);
	  $.each(array, function(i, n) {
		if (n.id === parseInt(companyId)) {
		  company = n;
		}
	  });
	  $.each(company.area, function(i, n) {
		if (n.id === parseInt(areaId)) {
		  area = n;
		}
	  });
	  $('#host-company').val(company.company.name);
	  $('#host-area').val(area.name);
	  $('#isFusion').val($subnet.attr('data-isFusion'))
	  $('#host-area-code').val(area.areaCode);
	  $.each(area.vpc, function(i, n) {
		if (n.vpCode === vpcCode) {
		  $('#host-vpc').val(n.name);
		  $('#host-vpc-code').val(n.vpCode);
		}
		$.each(n.net, function(i, n) {
		  if (n.netCode == netCode) {
			$('#host-net').val(n.name);
			$('#host-net-code').val(n.netCode);
		  }
		});
	  });
	  $.each(area.set, function(i, n) {
		$('#host-cpu ul').append($(_li).text(n.cpu + "核").val(n.cpu));
		var $ul = $(_ul).addClass('clearfix city');
		$.each(n.rom, function(i, n) {
		  $ul.append($(_li).text(n.num + "G").data('set-code', n.setCode).val(n.num));
		});
		$('#host-rom').append($ul);
	  });
	  $.each(area.Os, function(i, n) {
		$('#host-os ul').append($(_li).text(n.name));
		var $select = $(_select).on('change', function() {
		  $('#host-os-code').val($(this).val())
		});
		$.each(n.types, function(i, n) {
		  $select.append($(_option).text(n.name).val(n.typeCode));
		});
		$('#host-os-type').append($select);
	  });
	  $('#host-cpu li:first').addClass('active');
	  $('#host-os li:first').addClass('active');
	  $('#host-rom li:first').addClass('active');
	  $('#host-rom ul:first').css('display', 'block');
	  $('#host-os-type select:first').css('display', 'block');
	  if ($('#host-rom li').eq(0) != null) {
		$('#host-set').val($('#host-rom li').eq(0).data('set-code'));
	  }
	  if ($('#host-os-type select').eq(0) != null) {
		$('#host-os-code').val($('#host-os-type select').eq(0).val());
	  }
	  $('#host-os').on('click', 'li', function() {
		$('#host-os-type select').css('display', 'none');
		$('#host-os-type select').eq($(this).index()).css('display', 'block');
		$('#host-os-code').val($('#host-os-type select').eq($(this).index()).val());
	  });
	  $('#host-cpu').on('click', 'li', function() {
		$('#host-rom ul').css('display', 'none');
		$('#host-rom ul').eq($(this).index()).css('display', 'block');
	  });
	  $('#host-rom').on('click', 'li', function() {
		$('#host-set').val($(this).data('set-code'));
	  });
	});


  });

  $('#modal').on('show.bs.modal', function(event) {
	var router_id = $(event.relatedTarget).attr('data-routerid');
	var agent_code = $(event.relatedTarget).attr('data-agentcode');
	var router_code = $(event.relatedTarget).attr('data-routercode');
	var router = $(event.relatedTarget).attr('data-router');
	$.ajax({
	  type: "post",
	  url: "<?php echo $this->Url->build(['prefix'=>'console','controller'=>'Overview','action'=>'datainfo']); ?>",
	  async: true,
	  data: {
		router_id: router_id,
		agent_code: agent_code
	  },
	  success: function(data) {
		data = $.parseJSON(data);
		if (data.code == 0) {
		  $('#goods_id').val(data.goods_id);
		  $('#firm').val(data.display_name.firm);
		  if (data.display_name.firm == '索贝') {
			$('#virtual').show()
		  } else {
			$('#virtual').hide()
		  }
		  $('#area').val(data.display_name.area);
		  $('#router').val(router);
		  $('#routerCode').val(router_code);
		  $('#region_code').val(data.region_code);
		  $('#routerid').val(router_id);
		  $('.ip1').val(data.cidr.ip1);
		  $('.ip2').val(data.cidr.ip2);
		  $('.ip3').val(data.cidr.ip3);
		  $('.ip4').val(data.cidr.ip4);

		} else {
		  $('#virtual').hide()
		  $('#goods_id').val('');
		  $('#firm').val('');
		  $('#area').val('');
		  $('#router').val('');
		  $('#routerCode').val('');
		  $('#routerid').val('');
		  $('.ip1').val('');
		  $('.ip2').val('');
		  $('.ip3').val('');
		  $('.ip4').val('');
		}
	  }
	});
  });

  $('#submit').on('click', function() {
	var cidr = $('#ip1').val() + '.' + $('#ip2').val() + '.' + $('#net').val() + '.0/24';
	if ($('#virtual').css('display') == 'block') {
	  $('#virtual-value').val($('#virtual li').filter('.active').html());
	}
	if (Trim($('#virtual-value').val()) == "OpenStack") {
	  isFusion = true;
	} else {
	  isFusion = false;
	}
	var name = $('#name').val();
	var router = $('#router').val();
	var validate = true;
	var ip = $('#net').val();
	if (/^(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])$/.test(ip)) {
	  console.log(parseInt(ip), typeof parseInt(ip));
	  if (parseInt(ip) <= 255 && 0 <= parseInt(ip)) {
		$('.i-p').html('');
	  } else {
		$(".i-p").html('请输入正确的网络地址');
		validate = false;
	  }
	} else {
	  $(".i-p").html('请输入正确的数字');
	  validate = false;
	}

	if (name == '') {
	  $(".sub-net").html('请输入子网名');
	  validate = false;
	} else {
	  $(".sub-net").html('');
	}
	if (router == '') {
	  $(".rou-ter").html('请选择路由器');
	  validate = false;
	} else {
	  $(".rou-ter").html('');
	}
	if (validate) {
	  $.ajax({
		type: "post",
		url: "<?php echo $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'network','subnet','cidr']); ?>",
		async: true,
		data: {
		  cidr: cidr,
		  routerid: $("#routerid").val()
		},
		success: function(data) {
		  data = $.parseJSON(data);
		  if (data.code == 1) {
			$(".i-p").html(data.msg);
		  } else {
			// $(".i-p").html(data.msg);
			$.ajax({
			  type: "post",
			  url: "/orders/addShoppingCar",
			  async: true,
			  data: {
				is_console: 2,
				goods_id: <?php echo $define[6] ?> ,
				attr: {
				  "subnetName": $("#name").val(),
				  "routerid": $("#routerid").val(),
				  "routerName": $("#router").val(),
				  "area": $('#area').val(),
				  "vpcCode": '',
				  "cidr": cidr,
				  "regionCode": $('#region_code').val(),
				  "isFusion": isFusion
				},
				type: 1
			  },
			  success: function(data) {
				data = $.parseJSON(data);
				if (data.Code == 0) {

				  $('#modal').modal("hide");
				} else {
				  $('#modal').modal("hide");
				  tentionHide(data.msg, 1);
				}
			  }
			});
		  }
		}
	  });
	}
  })

     

  $("#net").on('input', function() {
	$("#net-follow").val($(this).val());
  })


});

function delSubnet(event){
	// console.log(event);
	var subnet_id = $(event).attr('data-subnetid');
	var subnet_code = $(event).attr('data-subnetCode');
	var router_code = $(event).attr('data-routerCode');
	$('#sub_name').html($(event).attr('data-subnetname'));
	var status = $(event).attr('data-status')
	if(status =="创建中"){
		showModal('提示', 'icon-exclamation-sign', '当前设备状态无法进行操作', '', '', 0);
	}else{
		$('#modal-host-remove').modal('show');
	}

	//删除子网
  	$("#yes").unbind("click");
	$('#yes').one('click', function() {
		$.ajax({
			url: '<?= $this->Url->build(['controller'=>'ajax','action'=>'network']); ?>/<?php echo "Subnet" ?>/<?php echo "deleteSubnet" ?>',
			data: {
			  id: subnet_id,
			  subnetCodes: subnet_code,
			  routerCode: router_code
			},
			success: function(data) {
			  data = $.parseJSON(data);
			  if (data.code == '0000') {
				window.location.reload();
				$('#modal-host-remove').modal("hide"); /*tentionHide(data.msg,0);*/
			  } else {
				$('#modal-host-remove').modal("hide");
				tentionHide(data.msg, 1);
			  }
			}
		})
  	});
}

function notifyCallBack(value) {
  if (value.MsgType == "success") {
	if (value.Data.method == "router_bind" || value.Data.method == "subnet_del" || value.Data.method == "subnet_add" || value.Data.method == "ecs_add" || value.Data.method == "ecs_del"||value.Data.method == "lbs_del"||value.Data.method == "ecs_reboot"|| value.Data.method == "desktop_add") {
	  setTimeout(function() {
		window.location.reload();
	  }, 3000);

	}

  }
}

function Trim(str) {
  return str.replace(/(^\s*)|(\s*$)/g, "");
}

function tentionHide(content, state) {
  $("#maindiv-alert").empty();
  var html = "";
  if (state == 0) {
	html += '<div class="point-host-startup tips-station"><i></i>' + content + '</div>';
	$("#maindiv-alert").append(html);
	$(".point-host-startup ").slideUp(3000);
  } else {
	html += '<div class="point-host-startup point-host-startdown tips-station"><i></i>' + content + '</div>';
	$("#maindiv-alert").append(html);
	$(".point-host-startdown").slideUp(3000);
  }
}


$("#creatHost").click(function() {
	$('#creatHost').attr('disabled',true);
	var $modalHost = $('#modal-host');
	if($('#ecsName').val()==''){
		$('#ecs-name-warning').html('名称不能为空');
 		$('#creatHost').attr('disabled',false);
 		return false;
	}

	num = $("#text_number").val();
	$("#host-cpu li").each(function(){
		if($(this).attr('class') == 'active'){
			cpu = $(this).attr('value');

		}
  	});
  	$("#host-rom li").each(function(){
		if($(this).attr('class') == 'active'){
			memory = $(this).attr('value');
		}
  	});


	gpu = 0;
	var aaa =  checkQuota(num,cpu,memory,gpu,$modalHost);
	if(aaa != 3){
		$('#creatHost').attr('disabled',false);
		return false;
	}
  $.ajax({
	type: "post",
	url: "/orders/addShoppingCar",
	async: true,
	data: {
	  is_console: 1,
	  goods_id: <?php echo $define[1] ?>,
	  attr: {
		"regionCode": $("#host-area-code").val(),
		"uid": $("#host-uid").val(),
		"instanceTypeCode": $("#host-set").val(),
		"ecsName": $("#ecsName").val(),
		"vpcCode": $("#host-vpc-code").val(),
		"subnetCode": $("#host-net-code").val(),
		"imageCode": $("#host-os-code").val(),
		"number": $("#text_number").val(),
		"isFusion": $("#isFusion").val()
	  },
	  type: 1
	},
	success: function(data) {
	  data = $.parseJSON(data);
	  if (data.Code == "0") {
		window.location.reload()
	  } else {
		alert(data.Message);
	  }
	}
  });
})

$("#creatDesktop").click(function() {
	var $modalDesktop = $('#modal-desktop');
	$('#desktop-name-warning').html('');
	$(".txtaduser").html('');
	$(".txtpwd1").html('');
	$(".txtpwd2").html('');
	$('#creatDesktop').attr('disabled',true);
	if($('#desktop-name').val()==''){
		$('#desktop-name-warning').html('名称不能为空');
 		$('#creatDesktop').attr('disabled',false);
 		return false;
	}
	num = $("#desktop_number").val();
	cpu = $("#desktop-cpu").val();
	memory = $("#desktop-memory").val();
	gpu = $("#desktop-gpu").val();
	var aaa =  checkQuota(num,cpu,memory,gpu,$modalDesktop);
	if (aaa != 3){
		$('#creatDesktop').attr('disabled',false);
		return false;
	}
	var value1 = true;
	if ($("#ad").val()==1){
		if($('#txtaduser').val()==''||$('#txtaduser').val()==null){
			$(".txtaduser").html('请输入账号');
			$('#creatDesktop').attr('disabled',false);
 		return false;
		}
		var checkadministrator = $('#txtaduser').val().toLocaleLowerCase();
		if(checkadministrator=='administrator'){
			$(".txtaduser").html('账号不能为administrator');
			$('#creatDesktop').attr('disabled',false);
 			return false;
		}
		if($('#txtpwd1').val()==''||$('#txtpwd1').val()==null){
			$(".txtpwd1").html('请输入密码');
			$('#creatDesktop').attr('disabled',false);
 			return false;
		}
		if($('#txtpwd2').val()!=$('#txtpwd1').val() || $('#txtpwd2').val()==''){
			$(".txtpwd2").html('两次密码不一致');
			$('#creatDesktop').attr('disabled',false);
 			return false;
		}
		if($('#txtaduser').val()!=''){
			$.ajax({
				type:"post",
				url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'Ajax','action'=>'desktop','desktop','checkAduser']); ?>",
				async:true,
				data:{
					aduser:$('#txtaduser').val(),
					vpcCode:$('#desktop-vpc-code').val(),
				},
				async:false,
			}).done(function(data){
				result = $.parseJSON(data);
				if(result==1){
					$(".txtaduser").html('账号名已被创建');
					value1 = false;
					$('#creatDesktop').attr('disabled',false);
 					return false;
				}
			});
		}
	}

  $.ajax({
	type: "post",
	url: "/orders/addShoppingCar",
	async: true,
	data: {
	  is_console: 1,
	  goods_id: <?php echo $define[5] ?>,
	  attr: {
			"regionCode": $("#desktop-area-code").val(),
			"uid": $("#desktop-uid").val(),
			"instanceTypeCode": $("#desktop-hard-code").val(),
			"vpcCode": $("#desktop-vpc-code").val(),
			"subnetCode": $("#desktop-net-code").val(),
			"imageCode": $("#desktop-os-code").val(),
			"number": $("#desktop_number").val(),
			"name":$("#desktop-name").val(),
			"bandwidth":0,
			"netCode":$("#desktop-net-code").val(),
			"ad":$("#ad").val(),
			"aduser":$('#txtaduser').val(),
			"txtaduser":$('#desktop-account').val(),
			"adpass":$('#txtpwd2').val()
	  	},
	  	type: 1
	},
	success: function(data) {
	  data = $.parseJSON(data);
	  if (data.Code == "0") {
		window.location.reload()
	  } else {
		alert(data.Message);
	  }
	}
  });
})

function desktopDel_click(obj){
	// obj.stopPropagation();
	$(obj).on('click',function(e){
		e.preventDefault();
	});
	var basicId = $(obj).attr('data-basic-id');
	var code = $(obj).attr('data-code');
	var name = $(obj).attr('data-name');
	var status = $(obj).attr('data-status');
	var ip = $(obj).attr('data-ip');
	if(status!="创建中"){
		showModal('提示', 'icon-question-sign', '确认要停止机器', name, '');
	}else{
		showModal('提示', 'icon-exclamation-sign', '当前设备状态无法进行操作', '', '', 0);
	}
}

function ecsDel_click(obj){
	// obj.stopPropagation();
  $(obj).on('click',function(e){
	 e.preventDefault();
  });
  $("#delete_ecs_button").unbind("click");
  var basicId = $(obj).attr('data-basic-id');
  var code = $(obj).attr('data-code');
  var name = $(obj).attr('data-name');
  var status = $(obj).attr('data-status');
  var ip = $(obj).attr('data-ip');
  if(status!="创建中"){
	if(ip!=""&&ip!=null){
		$("#modal_ecs_name_title").html("删除主机");
		$("#modal_ecs_name_context1").html("ECS绑定了EIP,无法删除,请先解绑EIP");
		$("#modal_ecs_name_context2").html("请稍后再试");
		$("#modal-ecs").modal("show");
	}else{
		$("#modal-ecs-remove").modal("show");
		$("#delete_ecs_button").unbind("click");
		$("#delete_host_name_span").html(name)
		$("#delete_ecs_button").click(function() {
		  $("#modal-ecs-remove").modal("hide");
		  $.ajax({
			type: "post",
			url: "/console/ajax/network/hosts/ajaxHosts",
			async: true,
			data: {
			  basicId: basicId,
			  instanceCode: code,
			  uid: "<?=$this->Session->read('Auth.User.id')?>",
			  isEach: false,
			  method: "ecs_delete"
			},
			success: function(data) {
			  data = $.parseJSON(data);
			  if (data.Code == "0") {
			  	window.location.reload();
			  } else {
				alert(data.Message)
			  }
			}
		  });
		});
	  }
  }else{
	$("#modal_ecs_name_title").html("删除主机");
	$("#modal_ecs_name_context1").html("当前设备状态无法进行操作，");
	$("#modal_ecs_name_context2").html("请稍后再试");
	$("#modal-ecs").modal("show");
  }
}

 function elbSubmit(){
 	$('#elb-btn').attr('disabled',true);
 	if($('#elb-name').val()==''){
 		$('#elb-name-warning').html('名称不能为空');
 		$('#elb-btn').attr('disabled',false);
 		return false;
 	}
 	if($('#elb-net-code').val()==''||$('#elb-net-code').val()==null){
 		$('#elb-btn').attr('disabled',false);
 		return false;
 	}
	$.ajax({
		type:"post",
		url:"/orders/addShoppingCar",
		async:true,
		data:{
		    is_console:1,
		    goods_id: <?php echo $define[2] ?>,
		    attr:{
		            "lbsName":$("#elb-name").val(),
		            "vpcCode":$("#elb-vpc-code").val(),
		            "subnetCode":$("#elb-net-code").val(),
		            "eipCode":$("#elb-eip-code").val(),
		            "regionCode":$("#elb-area-code").val(),
		            "imageCode":"Image-n6Kg75BL"//成都
		        },
		    type:1
		},
		success: function (data) {
		  data = $.parseJSON(data);
		  if (data.Code == "0") {
			window.location.reload()
		  } else {
			alert(data.Message);
			$('#elb-btn').attr('disabled',false);
		  }
		}
	});
 }

$('#modal-elb-remove').on('shown.bs.modal',function(e){
	var basicId = $(e.relatedTarget).attr('data-basic-id');
	var elbName = $(e.relatedTarget).attr('data-name');
	$(this).attr('data-basic-id',basicId);
	$(this).find('#modal-elb-name').html(elbName);
});

function lbsDel_click(obj){
	$("#delete-lbs-button").unbind("click");
	var basicId = $(obj).attr('data-basic-id');
	var code = $(obj).attr('data-code');
	var name = $(obj).attr('data-name');
	var status = $(obj).attr('data-status');
	var listen = $(obj).attr('data-listen');
	var ip = $(obj).attr('data-ip');
	if( ip != null && ip != ''){
	  	$("#modal_ecs_name_title").html("删除负载均衡");
		$("#modal_ecs_name_context1").html("该ELB已绑定EIP");
		$("#modal_ecs_name_context2").html("请先解绑");
		$("#modal-ecs").modal("show");
	}else{
		if(status!="创建中"){
			if(listen!=""&&listen!=null){
				$("#modal_ecs_name_title").html("删除负载均衡");
				$("#modal_ecs_name_context1").html("该ELB下存在监听器, ");
				$("#modal_ecs_name_context2").html("请先删除");
				$("#modal-ecs").modal("show");
			}else{
				$("#modal-elb-remove").modal("show");
				$("#delete-lbs-button").unbind("click");
				$("#delete_lbs_name_span").html(name)
				$("#delete-lbs-button").click(function() {
					$("#modal-ecs-remove").modal("hide");
					$.ajax({
					  type: "post",
					  url: "<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'elb', 'ajaxElb']); ?>",
					  async: true,
					  data: {
						method: "lbs_del",
						loadbalanceCode: code,
						basicId: basicId,
						isEach: "false"
					  },
					  success: function(data) {
						data = $.parseJSON(data);
						if (data.Code != "0") {
						  alert(data.Message);
						}
					  }
					});
				});
		  	}
	  	}else{
			$("#modal_ecs_name_title").html("删除负载均衡");
			$("#modal_ecs_name_context1").html("该负载均衡正在创建中或创建失败，不能正常使用");
			$("#modal_ecs_name_context2").html("请稍后再试");
			$("#modal-ecs").modal("show");
	  	}
	}
}

//动态创建modal
function showModal(title, icon, content, content1, method, type) {
	$("#maindiv").empty();
	html = "";
	html += '<div class="modal fade" id="modal-ali" tabindex="-1" role="dialog">';
	html += '<div class="modal-dialog" role="document">';
	html += '<div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
	html += '<h5 class="modal-title">' + title + '</h5>';
	html += '</div><div class="modal-body"><i class="'+icon+' icon-color-gray text-primary"></i>&nbsp;&nbsp;' + content + '<span class="text-primary">' + content1 + '</span></div>';
	html += '<div class="modal-footer"><button id="btnModel_ok" onclick="' + method + '" type="button" class="btn btn-primary">确认</button><button type="button" class="btn btn-danger" id="btnEsc" data-dismiss="modal">取消</button></div></div></div></div>';
	$("#maindiv").append(html);
	if (type == 0) {
		$("#btnModel_ok").remove();
	}
	$('#modal-ali').modal("show");
}


function webadmin(code,id) {
	$("#modal-ali").modal("hide");
	$.ajax({
		type: "post",
		url: "<?= $this ->Url ->build(['prefix' =>'console', 'controller' =>'ajax', 'action' =>'network', 'hosts', 'webadmin']); ?>",
		async: true,
		data: {
			method: "ecs_up_vnc_password",
			instanceCode: code
		},
		success: function(data) {
			data = $.parseJSON(data);
			if (data.Code != 0) {
				alert(data.Message);
			} else {
				showModal('提示', 'icon-question-sign', '初始化已完成，是否立即重启？', '重启后生效', 'ajaxFun(\''+code+',\'ecs_reboot\',\''+id+'\')');
				$("#btnModel_ok").html("立即重启");
				$("#btnEsc").html("稍后重启");
			}
			// $('#disk-manage').modal("hide");
		}
	});
}

function is_password(password,code,id) {
	var url;
	if (password != null && password != "") {
		url = "/console/network/webConsole/" + code;
		window.open(url);
	} else {
		showModal('提示', 'icon-question-sign', '当前是第一次操作,是否进行初始化操作', '', 'webadmin(\'' + code + '\',\'' + id + '\')');
	}
}

	function aliyunHosts(obj){
		$(obj).on('click',function(e){
			e.stopPropagation();
		});
		var password = $(obj).attr('data-password');
		var code = $(obj).attr("data-code");
		var id = $(obj).attr("data-id");
		//is_login(password,code);
		is_password(password,code,id);

	}

	function sobeyHosts(obj){
		$(obj).on('click',function(e){
			e.stopPropagation();
		});
		var code = $(obj).attr("data-code");
		url = "/console/network/webConsole/" + code;
		window.open(url);
	}

	function desktopFormatter(obj){
		$(obj).on('click',function(e){
			e.stopPropagation();
		});
		var name = $(obj).attr("data-desktop-name");
		url = "<?= $this->Url->build(['prefix'=>'xdesktop','controller'=>'citrix','action'=>'launch']); ?>/"+name+".ica";
		window.open(url);
	}


	function ajaxFun(code, method,id) {
	$('#modal-ali').modal("hide");
		$.ajax({
			type: "post",
			url: "<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'hosts', 'ajaxHosts']); ?>",
			async: true,
			timeout: 9999,
			data: {
				method: method,
				instanceCode: code,
				basicId: id,
				isEach: "false"
			},
			//dataType:'json',
			success: function(data) {
				data = $.parseJSON(data);
				if (data.Code != "0") {
					alert(data.Message);
				}
			}
		});
	}



	function checkQuota(num,cpu,memory,gpu,target){
		if(num <1){
			target.modal('hide');
			$('#modal-error').modal('show');
			$('#modal-error-text').text('').text('商品个数出错');
			return false;
		}
		var res = 3;
		$.ajax({
			type:"post",
			url:"/console/home/getUserLimit",
			async:false,
			success: function (data) {
		  		data = $.parseJSON(data);
				if((cpu*num+data.cpu_used) > data.cpu_bugedt){
					target.modal('hide');
					$('#modal-error').modal('show');
					$('#modal-error-text').text('').text('GPU配额不足');
					res = 1;
				}
				if((memory*num+data.memory_used) > data.memory_buget){
					target.modal('hide');
					$('#modal-error').modal('show');
					$('#modal-error-text').text('').text('内存配额不足');
					res = 1;
				}
				if((gpu*num+data.gpu_used) > data.gpu_bugedt){
					target.modal('hide');
					$('#modal-error').modal('show');
					$('#modal-error-text').text('').text('GPU配额不足');
					res = 1;
				}
			}
		});
		return res;
	};

	$('.eip').on('click',function(){
		window.location.href="<?php echo $this->Url->build(['prefix'=>'console','controller'=>'network','action'=>'lists','eip'])?>";
	});
	$('.store').on('click',function(){
		window.location.href="<?php echo $this->Url->build(['prefix'=>'console','controller'=>'network','action'=>'lists','fics'])?>";
	});
	$('.cycle').on('click',function(){
		window.location.href="<?php echo $this->Url->build(['prefix'=>'console','controller'=>'recycled','action'=>'index'])?>";
	});

</script>