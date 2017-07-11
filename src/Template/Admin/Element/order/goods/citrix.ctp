<?php if(isset($good_info) && is_object($good_info) && isset($good_info->spec)): ?>    
    <dl>
        <dt>规格名称:</dt>
        <dd><?=$good_info->spec->name?></dd>
    </dl>
    <dl>
        <dt>版本名称:</dt>
        <dd><?=$good_info->name?></dd>
    </dl>
    <dl>
        <dt>部署区位:</dt>
        <dd><?=$good_info->region->name?></dd>
    </dl>
    <dl>
        <dt>所在VPC:</dt>
        <dd><?=$good_info->vpc->name?></dd>
    </dl>
    <dl>
        <dt>计算能力:</dt>
        <dd>
            <dl class="children">
                <dt>CPU:</dt>
                <dd><?=$good_info->spec->instancetype->cpu?>核</dd>
            </dl>
            <dl class="children">
                <dt>内存:</dt>
                <dd><?=$good_info->spec->instancetype->memory?>G</dd>
            </dl>
            <dl class="children">
                <dt>GPU:</dt>
                <dd><?=$good_info->spec->instancetype->gpu?>MB</dd>
            </dl>
        </dd>
    </dl>
    <dl>
        <dt>所属子网:</dt>
        <dd><?=$good_info->subnet->name?></dd>
    </dl>
    <dl>
        <dt>系统镜像:</dt>
        <dd><?=$good_info->spec->image->name?></dd>
    </dl>
<?php endif;?>