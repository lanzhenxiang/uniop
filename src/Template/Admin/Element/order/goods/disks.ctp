<?php if(isset($good_info) && is_object($good_info)): ?> 

    <dl>
        <dt>部署区位:</dt>
        <dd><?=$good_info->dyName?></dd>
    </dl>
    <dl>
        <dt>块存储名称:</dt>
        <dd><?=$good_info->disksName?></dd>
    </dl>
    <dl>
        <dt>容量大小:</dt>
        <dd><?=$good_info->size?>GB</dd>
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