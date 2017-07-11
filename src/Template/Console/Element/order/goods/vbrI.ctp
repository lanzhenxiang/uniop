<?php if(isset($good_info) && is_object($good_info) && isset($good_info->routerCode)): ?>

    <dl>
        <dt>CODE:</dt>
        <dd><?=$good_info->routerCode?></dd>
    </dl>
    <dl>
        <dt>对端vpc:</dt>
        <dd><?=$good_info->oppositeVpcCode?></dd>
    </dl>
    <dl>
        <dt>接口名称:</dt>
        <dd><?=$good_info->customName?></dd>
    </dl>
    <dl>
        <dt>规格:</dt>
        <dd><?=$good_info->spec?></dd>
    </dl>
<?php endif;?>