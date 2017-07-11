<?php if(isset($good_info) && is_object($good_info) && isset($good_info->eipName)): ?> 

    <?php if(isset($good_info->goods_info->goods[0]->version_info[0]->name)): ?>
    <dl>
        <dt>版本:</dt>
        <dd><?=$good_info->goods_info->goods[0]->version_info[0]->name?></dd>
    </dl>
    <?php endif;?>
    <dl>
        <dt>部署区位:</dt>
        <dd><?=$good_info->dyName?></dd>
    </dl>
    <dl>
        <dt>实例名称:</dt>
        <dd><?=$good_info->eipName?></dd>
    </dl>
    <dl>
        <dt>所在VPC:</dt>
        <dd><?=$good_info->vpcName?></dd>
    </dl>
<?php endif;?>