<?php if(isset($good_info) && is_object($good_info)): ?> 

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
        <dt>路由器名称:</dt>
        <dd><?=$good_info->routerName?></dd>
    </dl>
    <dl>
        <dt>VPC地址:</dt>
        <dd><?=$good_info->cidr?></dd>
    </dl>
<?php endif;?>