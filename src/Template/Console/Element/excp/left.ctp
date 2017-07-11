<div class="wrap-nav-center">
    <div>
        <div class="title"><span>接口日志</span><i class="icon-angle-down"></i></div>
        <ul class="center-nav total">
            <?php if (in_array('ccm_ps_interface_logs', $this->Session->read('Auth.User.popedomname'))) { ?>
            <li <?php if(isset($interface)&&($interface=='excp')){echo 'class="active"';} ?>><a href="<?= $this->Url->build(['controller'=>'excp','action'=>'lists','excp']); ?>"><span>异常日志</span></a></li>
           <?php }
            if (in_array('ccm_ps_interface_logs', $this->Session->read('Auth.User.popedomname'))) {
            ?>
            <li <?php if(isset($interface)&&($interface=='normal')) echo 'class="active"'; ?>><a href="<?= $this->Url->build(['controller'=>'excp','action'=>'lists','normal']); ?>"><span>正常日志</span></a></li>
            <?php }
             if (in_array('ccm_ps_interface_logs', $this->Session->read('Auth.User.popedomname'))) {
            ?>
            <li <?php if(isset($interface)&&($interface=='executing')) echo 'class="active"'; ?>><a href="<?= $this->Url->build(['controller'=>'excp','action'=>'lists','executing']); ?>"><span>执行中日志</span></a></li>
            <?php } ?>
        </ul>
        <span class="iconpic iconpic-spread"></span>
    </div>
</div>