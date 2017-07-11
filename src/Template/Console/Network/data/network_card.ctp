<!--网卡-->
<?= $this->Html->css(['network/hosts']); ?>
<?php if($type == "desktop"){ ?>
    <?= $this -> element('desktop/lists/left', ['active_action' => 'desktop']); ?>
<?php }else{?>
<?= $this -> element('network/lists/left', ['active_action' => 'hosts']); ?>
<?php }?>
<div class="wrap-nav-right hosts-content">
    <?= $this -> element('network/lists/left_nav', ['active_action' => 'network_card','id'=>$_data['0']['H_ID']]); ?>

    <div class="network-card-con  hosts-right clearfix host-static">
        <h5>  
            <?= $this -> element('network/data/breadcrumb', ['breadcrumbTitle' => '网卡设置','biz_tid' => $_data['0']['Biz_tid']]); ?>
            <div data-status="<?=$_data['0']['H_Status']?>" class="dropdown pull-right theme-color add-card">添加网卡</div>
        </h5>
        <?php foreach($_data as $network){ ?>
        <div class="card-section hosts-table">
            <div class="net-card-img">
                <img src="/images/nophoto.jpg" alt="网卡管理"/>
                <p>
                    <?=($network['I_Default'] == 1)?"默认网卡":"扩展网卡" ?>
                </p>
            </div>
            <div class="section-table">
                <table>
                    <tr>
                        <td>IPv4地址：</td>
                        <td><?=$network['I_Ip']?></td>
                    </tr>
                    <tr>
                        <td>NIC CODE：</td>
                        <td><?=$network['I_NetCode']?></td>
                    </tr>
                    <tr>
                        <td>子网名称：</td>
                        <td><?=$network['J_SubnetName']?></td>
                    </tr>
                    <tr>
                        <td>子网CODE：</td>
                        <td><?=$network['I_SubnetCode']?></td>
                    </tr>
                </table>
            </div>
            <?php if($network['I_Default'] != 1): ?>
                <div id="del-subnet" data-id="<?= $network['I_NetCardId'] ?>" data-code="<?= $network['I_NetCode'] ?>" data-subnetCode="<?=$network['I_SubnetCode']?>" onclick="delNetCard(this)" class="del-card theme-color">删除</div>
            <?php endif?>
        </div>
        <?php }?>
    </div>
    <!--添加网卡-->
    <div class="modal fade" tabindex="-1" role="dialog" id="subnet-manage">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">添加网卡</h4>
                </div>
                <form action=""  method="POST">
                    <div class="modal-body">
                        <div class="ng-binding">
                            <div class="clearfix">

                                <div style="width:80%" class="bk-form-row-cell">
                                    <table id="subnet-extend-table" data-toggle="table"
                                           data-pagination="true"
                                           data-side-pagination="server"
                                           data-locale="zh-CN"
                                           data-click-to-select="true"
                                           data-url="<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'hosts', 'getPublicSubnetExtend']); ?>"
                                           data-unique-id="id"  class="network-table">
                                        <thead>
                                        <tr>
                                            <th data-checkbox="true"></th>
                                            <th data-field="vpc_name">VPC</th>
                                            <th data-field="subnet_name">子网</th>
                                        </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" id="vpc-code" name="vpc-code" value="<?=$_data['0']['F_Code'] ?>">
                        <input type="hidden" id="basic-id" name="basic-id" value="<?=$_data['0']['H_ID'] ?>">
                        <input type="hidden" id="basic-code" name="basic-code" value="<?=$_data['0']['H_Code'] ?>">
                        <button type="button" id ="addSubnet" class="btn btn-primary" >确 认</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">取 消</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>