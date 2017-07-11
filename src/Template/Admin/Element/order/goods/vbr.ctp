<?php if(isset($good_info) && is_object($good_info) && isset($good_info->vpcCode)): ?>

    <dl>
        <dt>接口场景:</dt>
        <dd>专线接入阿里云</dd>
    </dl>
    <dl>
        <dt>区域:</dt>
        <dd><?=$good_info->regionCode?></dd>
    </dl>
    <dl>
        <dt>当前子网:</dt>
        <dd><?=$good_info->subnetCode?></dd>
    </dl>
    <dl>
        <dt>对端vpc:</dt>
        <dd><?=$good_info->vpcCode?></dd>
    </dl>
    <dl>
        <dt>接口名称:</dt>
        <dd><?=$good_info->name?></dd>
    </dl>
    <dl>
        <dt>是否冗余:</dt>
        <dd><?=$good_info->isVbrRedundancy == '1' ? "是" : "否"?></dd>
    </dl>
<?php endif;?>