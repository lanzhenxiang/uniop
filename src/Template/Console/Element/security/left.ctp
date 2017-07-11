<div class="wrap-nav-center">
    <div>
        <div class="title"><span>安全</span><i class="icon-angle-down"></i></div>
        <ul class="center-nav total">
            <?php if (in_array('ccm_ps_security_firewall', $this->Session->read('Auth.User.popedomname'))) { ?>
            <li <?php if(isset($active_action)&&($active_action=='firewall')){echo 'class="active"';} ?>><a href="<?= $this->Url->build(['controller'=>'security','action'=>'lists','firewall']); ?>"><span>防火墙</span></a></li>
            <?php } ?>
            <?php if (in_array('ccm_ps_security_firewall', $this->Session->read('Auth.User.popedomname'))) { ?>
            <li <?php if(isset($active_action)&&($active_action=='security_group')){echo 'class="active"';} ?>><a href="<?= $this->Url->build(['controller'=>'securityGroup','action'=>'index']); ?>"><span>安全组</span></a></li>
            <?php } ?>
            <!--<?php if (in_array('ccm_ps_security_firewall', $this->Session->read('Auth.User.popedomname'))) { ?>
            <li <?php if(isset($active_action)&&($active_action=='security_group')){echo 'class="active"';} ?>><a href="<?= $this->Url->build(['controller'=>'SecurityGroup','action'=>'index']); ?>"><span>安全组</span></a></li>
            <?php } ?>-->

        </ul>
        <span class="iconpic iconpic-spread"></span>
    </div>
</div>