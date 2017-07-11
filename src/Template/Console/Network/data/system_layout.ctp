<!--系统配置-->
<?= $this->Html->css(['network/hosts']); ?>
<?php if($type == "desktop"){ ?>
    <?= $this -> element('desktop/lists/left', ['active_action' => 'desktop']); ?>
<?php }else{?>
<?= $this -> element('network/lists/left', ['active_action' => 'hosts']); ?>
<?php }?>
<div class="wrap-nav-right hosts-content" ng-app>
    <?= $this -> element('network/lists/left_nav', ['active_action' => 'system_layout','id'=>$_data['0']['H_ID']]); ?>
    <div class="system-con hosts-right clearfix host-static">
        <h5>  
            <?= $this -> element('network/data/breadcrumb', ['breadcrumbTitle' => '系统配置','biz_tid' => $_data['0']['Biz_tid']]); ?>
            
            <div class="dropdown pull-right">
                <i class="icon-reorder"></i>
                <div class="basic-down">
                <p class="right-revamp" 
                    id="instance"
                    data-uniqueid="<?=$_data['0']['H_ID']?>" 
                    data-status="<?=$_data['0']['H_Status']?>" 
                    data-code="<?=$_data['0']['H_Code']?>" 
                    data-id="<?=$_data['0']['H_ID']?>"
                    data-name="<?=$_data['0']['H_Name']?>"
                >
                <i class="icon-caret-up"></i>
                    <?php if (in_array('ccf_host_startup', $this->Session->read('Auth.User.popedomname'))) { ?>
                    <span id="start">开机</span>
                    <?php }?>
                    <?php if (in_array('ccf_host_shutdonw', $this->Session->read('Auth.User.popedomname'))) { ?>
                    <span id="shutdown">关机</span>
                    <?php }?>
                    <?php if (($type =="hosts" && in_array('ccf_host_change', $this->Session->read('Auth.User.popedomname'))) || ($type =="desktop" && in_array('ccf_desktop_edit', $this->Session->read('Auth.User.popedomname')))) { ?>
                    <span  class="sys-revamp">修改系统配置</span>
                    <?php }?>
                </p></div>
            </div>
        </h5>
        <div class="hosts-table">
            <table>
                <tr>
                    <td>状态：</td>
                    <td id="status"><?=$_data['0']['H_Status']?></td>
                </tr>
                <tr>
                    <td>所属VPC：</td>
                    <td><?=$_data['0']['H_VPC']?></td>
                </tr>
                <tr>
                    <td>CPU：</td>
                    <td><?=$_data['0']['D_Cpu']?>核</td>
                </tr>
                <tr>
                    <td>内存：</td>
                    <td><?=$_data['0']['D_Memory']?>GB</td>
                </tr>
                <tr>
                    <td>GPU：</td>
                    <td><?=$_data['0']['D_Gpu']?>MB</td>
                </tr>
            </table>
            <div class="hint-box">
                <div><i class="icon-info-sign"></i>友情提示：</div>
                <p>修改系统配置前，请先将云主机关机。</p>
            </div>
        </div>
    </div>
    <!--系统配置修改modal-->
    <div class="modal fade"  ng-controller="hostset" id="modal-sys-revamp" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"
                            aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h5 class="modal-title">修改系统配置</h5>
                </div>
                <div class="modal-body m-sys-body">
                    <div class="ng-binding">
                        <div class="clearfix">
                            <label for="" class="pull-left">CPU:</label>
                            <div class="bk-form-row-cell">
                                <div >
                                    <ul class="clearfix city" >
                                        <li ng-repeat="set in setList" ng-class="{active:$first}" ng-click="changeSet(set)">
                                            {{set.cpu}}核
                                        </li>
                                    </ul>
                                </div>
                                <span id="set-warning"></span>
                            </div>
                        </div>
                        <div class="clearfix">
                            <label for="" class="pull-left">内存:</label>
                            <div class="bk-form-row-cell">
                                <div>
                                    <div>
                                        <ul class="clearfix city" ng-repeat="set in setList | filter: cpuFilter">
                                            <li  ng-class="{active:$first}"  ng-repeat="rom in set.rom" ng-click="changeRom(rom)">
                                                {{rom.num}} GB
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix">
                            <label for="" class="pull-left">GPU:</label>
                            <div class="bk-form-row-cell">
                                <div ng-repeat="set in setList | filter: cpuFilter">
                                    <div ng-repeat="rom in set.rom | filter: romFilter">
                                        <ul class="clearfix city" >
                                            <li  ng-class="{active:$first}"  ng-repeat="gpu in rom.gpu" ng-click="changeGpu(gpu)">
                                                {{gpu.gpu}} M
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input id="basicId" type="hidden" value="<?=$_data['0']['H_ID']?>" />
                    <input id="basiccode" type="hidden" value="<?=$_data['0']['H_Code']?>" />
                    <input id="txtTypeCode" type="hidden" value="{{currentSetCode}}"  />
                    <button id="system-edit-sumbiter" type="button" class="btn btn-primary">确认</button>
                    <button id="reseter" type="button" class="btn btn-danger"
                            data-dismiss="modal">取消</button>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="maindiv"></div>
<?=$this->Html->script(['angular.min.js','network/hosts.js','jquery-ui-1.10.0.custom.min.js']); ?>
