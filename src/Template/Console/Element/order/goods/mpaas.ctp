<?php if(isset($good_info) && is_object($good_info)): ?>        
    <dl>
        <dt>名称:</dt>
        <dd>mPaaS服务</dd>
    </dl>
    <dl>
        <dt>版本:</dt>
        <dd><?=$good_info->name?></dd>
    </dl>
    <dl>
        <dt>服务配置:</dt>
        <dd>厂商自定义配置</dd>
    </dl>

<?php endif;?>