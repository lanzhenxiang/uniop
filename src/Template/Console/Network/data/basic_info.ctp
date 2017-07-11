
<!--基本信息-->
<?= $this->Html->css(['network/hosts']); ?>
<?php if($type == "desktop"){ ?>
    <?= $this -> element('desktop/lists/left', ['active_action' => 'desktop']); ?>
<?php }else{?>
<?= $this -> element('network/lists/left', ['active_action' => 'hosts']); ?>
<?php }?>
<div class="wrap-nav-right hosts-content">
    <?= $this -> element('network/lists/left_nav', ['active_action' => 'basic_info','id'=>$_data['0']['H_ID']]); ?>
    <div class="basic-info hosts-right clearfix host-static">
        <!--<div class="space-tit-box">  -->
            <h5>
            <?= $this -> element('network/data/breadcrumb', ['breadcrumbTitle' => '基本信息','biz_tid' => $_data['0']['Biz_tid']]); ?>
            <?php if (($type =="hosts" && in_array('ccf_host_change', $this->Session->read('Auth.User.popedomname'))) || ($type =="desktop" && in_array('ccf_desktop_change', $this->Session->read('Auth.User.popedomname')))) { ?>
            <div class="dropdown pull-right">
                <i class="icon-reorder"></i>
                <div class="basic-down"><p class="right-revamp"><i class="icon-caret-up"></i><span class="basic-revamp">修改名称/备注</span></p></div>
            </div>
            <?php }?></h5>
        <!--</div>-->

        <div class="hosts-table">
            <table>
                <tr>
                    <td>部署区位：</td>
                    <td><?=$_data['0']['E_Name']?></td>
                    <td></td>
                </tr>
                <tr>
                    <td>所属VPC：</td>
                    <td><?=$_data['0']['H_VPC']?></td>
                    <td></td>
                </tr>
                <tr>
                    <td>所属子网：</td>
                    <td><?=$_data['0']['H_Subnet']?></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Code：</td>
                    <td><?=$_data['0']['H_Code']?></td>
                </tr>
                <tr>
                    <td>名称：</td>
                    <td><?=$_data['0']['H_Name']?></td>
                    <td rowspan="4" class="image-td">
                    <?php if($type == "desktop"){ ?>
                         <div id="<?=$type?>-login" data-id="<?=$_data['0']['H_ID'] ?>" data-code="<?=$_data['0']['H_Code']?>" data-vnc-password="<?=$_data['0']['D_Vnc_password'] ?>" data-status="<?=$_data['0']['H_Status']?>" data-os="<?=$_data['0']['D_Os_Form']?>" data-fusionType="<?=$_data['0']['fusionType']?>" data-host-name="<?=$_data['0']['Host_extend_name']?>" data-host-platform="<?=$_data['0']['Host_extend_plat_form']?>" data-host-connect-status="<?=$_data['0']['Host_extend_connect_status']?>" class="static-run">
                            <p>状态：<span class="text-primary"><?=$_data['0']['H_Status']?></span></p>
                        </div>
                    <?php }else{?>
                        <p>状态：<span class="text-primary"><?=$_data['0']['H_Status']?></span></p>
                    <?php }?>
                        
                    </td>
                </tr>
                <tr>
                    <td>操作系统：</td>
                    <td><?=$_data['0']['D_Os_Form']?></td>
                </tr>
                <tr>
                    <td>公网IP：</td>
                    <td><?=$_data['0']['E_Ip']?></td>
                </tr>
                <tr>
                    <td>公网带宽（Mbps/s）：</td>
                    <td><?=$_data['0']['E_BandWidth']?></td>
                </tr>
                <?php if($type =='desktop'){ ?>
                <tr>
                    <td>CItrix 网关访问地址：</td>
                    <td>
                        <p><?=$_data['0']['desktop_server_url'] ?></p>
                    </td>
                </tr>
                <tr>
                    <td>CItrix 网关登陆账号：</td>
                    
                    <td>
                        <p>ADUser：<?=$_data['0']['aduser'] ?></p>
                    </td>
                    <td>
                        <p style="padding-left:10px;">Password：<?=$_data['0']['adpwd'] ?></p>
                    </td>
                </tr>
                <tr>
                    <td>ComputerName：</td>
                    <td>
                        <p><?=$_data['0']['Host_extend_name'] ?></p>
                    </td>
                </tr>
                <?php }?>
                <tr>
                    <td>备注：</td>
                    <td>
                        <?=$_data['0']['H_Description']?>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!--修改modal-->
    <div class="modal fade" id="modal-modify" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h5 class="modal-title">修改</h5>
                </div>
                <form id="modal-modify-form" action="" method="post">
                    <div class="modal-body">
                        <?=$this->Form->hidden('id',['value'=>$_data['0']['H_ID'],'id'=>'modal-modify-id'])?>

                        <div class="modal-form-group">
                            <?=$this->Form->input('name',['label'=>'名称:','value'=>$_data['0']['H_Name'],'id'=>'modal-modify-name'])?>
                        </div>
                        <div class="modal-form-group">
                            <label for="modal-modify-description">备注:</label>
                            <div>
                                <textarea id="modal-modify-description" name="description" rows="5"><?=$_data['0']['H_Description'] ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button id="sumbiter" type="button" class="btn btn-primary">确认</button>
                        <button id="reseter" type="button" class="btn btn-danger"
                                data-dismiss="modal">取消</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>