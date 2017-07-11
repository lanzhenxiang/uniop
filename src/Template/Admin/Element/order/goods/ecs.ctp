<?php if(isset($good_info) && is_object($good_info) && isset($good_info->ecsName)): ?> 
    <dl>
        <dt>名称:</dt>
        <dd><?=$good_info->goods_info->goods[0]->name?></dd>
    </dl>
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
        <dd><?=$good_info->ecsName?></dd>
    </dl>
    <dl>
        <dt>所在VPC:</dt>
        <dd><?=$good_info->vpcName?></dd>
    </dl>
    <dl>
        <dt>计算能力:</dt>
        <dd>
            <dl class="children">
                <dt>CPU:</dt>
                <dd><?=$good_info->cpu?>核</dd>
            </dl>
            <dl class="children">
                <dt>内存:</dt>
                <dd><?=$good_info->rom?>G</dd>
            </dl>
        </dd>
    </dl>
    <dl>
        <dt>默认子网:</dt>
        <dd><?=$good_info->netName?></dd>
    </dl>
    <?php if(isset($good_info->subnetName2) && $good_info->subnetName2 !=""): ?>
    <dl>
        <dt>扩展子网:</dt>
        <dd><?=$good_info->subnetName2?></dd>
    </dl>
    <?php endif;?>
    <dl>
        <dt>系统镜像:</dt>
        <dd><?=$good_info->imageName?></dd>
    </dl>
<?php endif;?>