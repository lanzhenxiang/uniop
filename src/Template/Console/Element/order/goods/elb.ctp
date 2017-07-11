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
        <dt>ELB名称:</dt>
        <dd><?=$good_info->lbsName?></dd>
    </dl>
    <dl>
        <dt>VPC名称:</dt>
        <dd><?=$good_info->vpcName?></dd>
    </dl>
    <dl>
        <dt>子网名称:</dt>
        <dd><?=$good_info->netName?></dd>
    </dl>
<?php endif;?>