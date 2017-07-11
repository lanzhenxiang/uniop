<?php if(isset($good_info) && is_object($good_info) && isset($good_info->goods_info->goods)){?>     
    <dl>
        <dt>VPC商品:</dt>
        <dd><?= $good_info->goods_info->goods[0]->name ?></dd>
    </dl>
    <dl>
        <dt>配置信息:</dt>
        <dd>
        <?php
            $goods = new App\Controller\Admin\GoodsVpcController();
            $good = $goods->getGoodsInfoByID($good_info->goods_info->goods[0]->id);
            if($good == null){
                echo "商品已删除";
            }else{
                $vpcInfo = $goods->findVpcEcsConfigure($good->goods_vpc);
                foreach ($vpcInfo as $key => $spec) {
                    switch ($spec["type"]) {
                        case 'ecs':
                            # code...
                            echo "云主机:".$spec['tagname']; ?><br/><?php
                            break;
                        case 'desktop':
                            echo "云桌面:".$spec['tagname']; ?><br/><?php
                            break;
                        case 'subnet':
                            echo "子网:".$spec['tagname']; ?><br/><?php
                            break;
                        case 'firewall':
                            echo "防火墙:".$spec['tagname']; ?><br/><?php
                            break;
                        case 'router':
                            echo "路由器:".$spec['tagname']; ?><br/><?php
                            break;
                        case 'elb':
                            echo "负载均衡:".$spec['tagname']; ?><br/><?php
                            break;
                        case 'oceanstor9k':
                            echo "华为9000:".$spec['tagname']; ?><br/><?php
                            break;
                        case 'fics':
                            echo "FICS:".$spec['tagname']; ?><br/><?php
                            break;
                }
            }
                ?>
            <?php } ?>
            </dd>
    </dl>
<?php }else{?>
    <dl>
        <dd>订单商品快照被删除</dd>
    </dl>
<?php }?>