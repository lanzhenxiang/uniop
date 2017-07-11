<div class="wrap-nav-center">
    <div>
        <div class="title"><span>费用统计</span><i class="icon-angle-down"></i></div>
        <ul class="center-nav total">
            <?php if (in_array('ccm_user_charge_subject', $this->Session->read('Auth.User.popedomname'))) { ?>
            <li <?php if(isset($active_action)&&($active_action=='subject')){echo 'class="active"';} ?>><a href="<?= $this->Url->build(['controller'=>'charge','action'=>'subject']); ?>"><span>消费总览</span></a></li>
            <?php }
            if (in_array('ccm_user_charge_detail', $this->Session->read('Auth.User.popedomname'))) {
            ?>
            <li <?php if(isset($active_action)&&($active_action=='detail')) echo 'class="active"'; ?>><a href="<?= $this->Url->build(['controller'=>'charge','action'=>'detail']); ?>"><span>消费明细</span></a></li>
            <?php } ?>

        </ul>
        <span class="iconpic iconpic-spread"></span>
    </div>
</div>