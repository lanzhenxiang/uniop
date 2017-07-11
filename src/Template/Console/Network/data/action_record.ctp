<!--操作记录-->
<?= $this->Html->css(['network/hosts']); ?>
<?php if($type == "desktop"){ ?>
    <?= $this -> element('desktop/lists/left', ['active_action' => 'desktop']); ?>
<?php }else{?>
<?= $this -> element('network/lists/left', ['active_action' => 'hosts']); ?>
<?php }?>
<div class="wrap-nav-right hosts-content">
    <?= $this -> element('network/lists/left_nav', ['active_action' => 'action_record','id'=>$_data['0']['H_ID']]); ?>
    <div class="action-record hosts-right clearfix host-static">
        <div class="static-detailInfo">
            <h5>  
                <?= $this -> element('network/data/breadcrumb', ['breadcrumbTitle' => '操作记录','biz_tid' => $_data['0']['Biz_tid']]); ?>
            </h5>
            <ul>
                <?php foreach ($_log as $key => $value) { ?>
                <li><span class="deleft"> <?= date("Y-m-d H:i:s",$value["create_time"]) ?></span>&nbsp;&nbsp;<?= $value["user_name"] ?>&nbsp;&nbsp; 对设备:&nbsp;&nbsp;<?= $value["device_name"] ?>&nbsp;&nbsp;进行[<?= $value["device_event"] ?>]</li>
                <?php } ?>

            </ul>
        </div>
    </div>
</div>