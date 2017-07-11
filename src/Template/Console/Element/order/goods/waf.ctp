<?php if(isset($good_info) && is_object($good_info) && isset($good_info->firewallName)): ?> 

    
    <?php if(isset($good_info->goods_info->goods[0]->version_info[0]->name)): ?>
    <dl>
        <dt>版本:</dt>
        <dd><?=$good_info->goods_info->goods[0]->version_info[0]->name?></dd>
    </dl>
    <?php endif;?>
    <dl>
        <dt>厂商:</dt>
        <dd><?=$good_info->csName?></dd>
    </dl>
    <dl>
        <dt>部署区位:</dt>
        <dd><?=$good_info->dyName?></dd>
    </dl>
    <dl>
        <dt>VPC名称:</dt>
        <dd><?=$good_info->vpcName?></dd>
    </dl>
    <dl>
        <dt>实例名称:</dt>
        <dd><?=$good_info->firewallName?></dd>
    </dl>
<?php endif;?>