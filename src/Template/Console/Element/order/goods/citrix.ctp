<?php if($value['is_console'] == 1): ?>
    <?php if(isset($good_info) && is_object($good_info)): ?>    
    <dl>
        <dt>名称:</dt>
        <dd><?=$good_info->goods_info->goods[0]->name?></dd>
    </dl>
    <dl>
        <dt>部署区位:</dt>
        <dd><?=$good_info->dyName?></dd>
    </dl>
    <?php if(isset($good_info->ecsName)):?>
    <dl>
        <dt>实例名称:</dt>
        <dd><?=$good_info->ecsName?></dd>
    </dl>
    <?php endif;?>
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
            <dl class="children">
                <dt>GPU:</dt>
                <dd><?=$good_info->gpu?>MB</dd>
            </dl>
        </dd>
    </dl>
    <dl>
        <dt>所属子网:</dt>
        <dd><?=$good_info->netName?></dd>
    </dl>
    <dl>
        <dt>系统镜像:</dt>
        <dd><?=$good_info->spec_name?></dd>
    </dl>
    <?php endif;?>
<?php else:?>
    <?php if(isset($good_info) && is_object($good_info) && isset($good_info->spec)): ?>    
        <dl>
            <dt>名称:</dt>
            <dd><?=$good_info->goods_info->goods[0]->name?></dd>
        </dl>
        <dl>
            <dt>版本:</dt>
            <dd><?=$good_info->name?></dd>
        </dl>
        <dl>
            <dt>部署区位:</dt>
            <dd><?=$good_info->region->name?></dd>
        </dl>
        <?php if(isset($good_info->ecsName)):?>
        <dl>
            <dt>实例名称:</dt>
            <dd><?=$good_info->ecsName?></dd>
        </dl>
        <?php endif;?>
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
<?php endif;?>