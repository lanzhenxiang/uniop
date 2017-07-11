<div class="wrap-nav-center">
    <div>
        <div class="title"><span>计算与网络</span><!-- <i class="icon-angle-down"></i> --></div>
        <ul class="center-nav total">
            <?php if (in_array('ccm_ps_hosts', $this->Session->read('Auth.User.popedomname'))) { ?>
            <li <?php if(isset($active_action)&&($active_action=='hosts')){echo 'class="active"';} ?>><a href="<?= $this->Url->build(['controller'=>'network','action'=>'lists','hosts']); ?>"><span>主机</span></a></li>
            <?php }
            if (in_array('ccm_ps_disks', $this->Session->read('Auth.User.popedomname'))) {
            ?>
            <li <?php if(isset($active_action)&&$active_action=='disks') echo 'class="active"'; ?>><a href="<?= $this->Url->build(['controller'=>'network','action'=>'lists','disks']); ?>"><span>硬盘</span></a></li>
            <!-- <li <?php if(isset($active_action)&&$active_action=='snapshots') echo 'class="active"'; ?>><a href="<?= $this->Url->build(['controller'=>'network','action'=>'lists','snapshots']); ?>"><span>快照</span></a></li> -->
            <?php }
            if (in_array('ccm_ps_images', $this->Session->read('Auth.User.popedomname'))) {
            ?>
            <li <?php if(isset($active_action)&&$active_action=='images') echo 'class="active"'; ?>><a href="<?= $this->Url->build(['controller'=>'network','action'=>'lists','images']); ?>"><span>镜像</span></a></li>
            <?php }
            if (in_array('ccm_ps_routers', $this->Session->read('Auth.User.popedomname'))) {
            ?>
            <li <?php if(isset($active_action)&&$active_action=='router') echo 'class="active"'; ?>><a href="<?= $this->Url->build(['controller'=>'network','action'=>'lists','router']); ?>"><span>路由器</span></a></li>
            <?php }
            if (in_array('ccm_ps_subnets', $this->Session->read('Auth.User.popedomname'))) {
            ?>
            <li <?php if(isset($active_action)&&$active_action=='subnet') echo 'class="active"'; ?>><a href="<?= $this->Url->build(['controller'=>'network','action'=>'lists','subnet']); ?>"><span>子网</span></a></li>
            <?php }
            if (in_array('ccm_ps_load_banlance', $this->Session->read('Auth.User.popedomname'))) {
            ?>
            <li <?php if(isset($active_action)&&$active_action=='elb') echo 'class="active"'; ?>><a href="<?= $this->Url->build(['controller'=>'network','action'=>'lists','elb']); ?>"><span>负载均衡</span></a></li>
            <?php }
            if (in_array('ccm_ps_eip', $this->Session->read('Auth.User.popedomname'))) {
            ?>
            <li <?php if(isset($active_action)&&$active_action=='eip') echo 'class="active"'; ?>><a href="<?= $this->Url->build(['controller'=>'network','action'=>'lists','eip']); ?>"><span>公网ip</span></a></li>
        	<?php }
            if (in_array('ccm_ps_vpc', $this->Session->read('Auth.User.popedomname'))) {
            ?>
            <li <?php if(isset($active_action)&&$active_action=='vpc') echo 'class="active"'; ?>><a href="<?= $this->Url->build(['controller'=>'network','action'=>'lists','vpc']); ?>"><span>VPC</span></a></li>
            <?php }  ?>
            <?php 
            if (in_array('ccm_ps_vpx', $this->Session->read('Auth.User.popedomname'))) {
            ?>
            <!--  <li <?php if(isset($active_action)&&$active_action=='vpx') echo 'class="active"'; ?>><a href="<?= $this->Url->build(['controller'=>'network','action'=>'lists','vpx']); ?>"><span>VPX</span></a></li>  -->
            <?php }  ?>
        </ul>
        <span class="iconpic iconpic-spread"></span>
    </div>
</div>