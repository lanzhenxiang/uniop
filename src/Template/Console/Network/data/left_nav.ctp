<?= $this->Html->css(['network/hosts']); ?>
<?= $this -> element('network/lists/left', ['active_action' => 'hosts']); ?>
<div class="wrap-nav-right hosts-content">
    <!--left nav-->
    <div class="hosts-left pull-left">
        <ol class="nav-left">
            <li class="active-bg"><a href=""><i class="icon-list-alt"></i>基本信息</a></li>
            <li><a href=""><i class="  icon-wrench"></i>系统配置</a></li>
            <li><a href=""><i class="icon-cog"></i>网卡设置</a></li>
            <li><a href=""><i class="icon-tasks"></i>块存储</a></li>
            <li><a href=""><i class="icon-camera-retro"></i>快照</a></li>
            <li><a href=""><i class="  icon-road"></i>镜像</a></li>
            <li><a href=""><i class=" icon-bar-chart"></i>图形化</a></li>
            <li><a href=""><i class="icon-lightbulb"></i>监控信息</a></li>
            <li><a href=""><i class="icon-file-alt"></i>操作记录</a></li>
            <li><a href=""><i class=" icon-check "></i>正常日志</a></li>
            <li><a href=""><i class="  icon-bolt"></i>异常日志</a></li>
        </ol>
    </div>
    <!--right section-->
    <div class="hosts-right clearfix host-static">
 <!--基本信息-->
    <div class="basic-info">
        <h5>基本信息
            <div class="dropdown pull-right">
                <i class="icon-reorder"></i>
                <div class="basic-down"><p class="right-revamp"><i class="icon-caret-up"></i><span class="basic-revamp">修改名称/备注</span></p></div>
            </div>
        </h5>
        <div class="hosts-table">
            <table>
                <tr>
                    <td>部署区位：</td>
                    <td>索贝-成都测试区</td>
                    <td></td>
                </tr>
                <tr>
                    <td>所属VPC：</td>
                    <td>Vpc-cKO6VqIq</td>
                    <td></td>
                </tr>
                <tr>
                    <td>所属子网：</td>
                    <td>subnet-1212</td>
                    <td></td>
                </tr>
                <tr>
                    <td>Code：</td>
                    <td>Ecs-BxSFWcOd</td>
                </tr>
                <tr>
                    <td>名称：</td>
                    <td>服务器1</td>
                    <td rowspan="4" class="image-td">
                        <div class="static-run">
                            <p>状态：<span class="text-primary">运行中</span></p>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>操作系统：</td>
                    <td>centos</td>
                </tr>
                <tr>
                    <td>公网IP：</td>
                    <td></td>
                </tr>
                <tr>
                    <td>公网带宽（Mbps/s）：</td>
                    <td>0</td>
                </tr>
                <tr>
                    <td>备注：</td>
                    <td>
                        因工作安排原因，中方今年不派员参加首尔防务对话。”昨天，国防部一句话回应了海内外媒体猜测。瓦罐里点灯，韩国心里亮肚里明。
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
                            <div class="modal-form-group">
                                <label>名称:</label>
                                <div>
                                    <input id="modal-modify-name" name="name" type="text" maxlength="15" />
                                </div>
                            </div>
                            <div class="modal-form-group">
                                <label>描述:</label>
                                <div>
                                    <textarea id="modal-modify-description" name="description" rows="5"></textarea>
                                </div>
                            </div>
                            <input id="modal-modify-id" name="id" type="hidden" />
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

    <!--系统配置-->
<div class="system-con">
    <h5>系统配置
        <div class="dropdown pull-right">
            <i class="icon-reorder"></i>
            <div class="basic-down"><p class="right-revamp"><i class="icon-caret-up"></i>
                <span>开机</span>
                <span>关机</span><span class="sys-revamp">修改系统配置</span>
            </p></div>
        </div>
    </h5>
    <div class="hosts-table">
        <table>
            <tr>
                <td>状态：</td>
                <td>索贝-成都测试区</td>
            </tr>
            <tr>
                <td>所属VPC：</td>
                <td>Vpc-cKO6VqIq</td>
            </tr>
            <tr>
                <td>CPU：</td>
                <td>subnet-1212</td>
            </tr>
            <tr>
                <td>内存：</td>
                <td>Ecs-BxSFWcOd</td>
            </tr>
            <tr>
                <td>GPU：</td>
                <td>服务器1</td>
            </tr>
        </table>
        <div class="hint-box">
            <div><i class="icon-info-sign"></i>友情提示：</div>
            <p>修改系统配置前，请先将云主机关机。</p>
        </div>
    </div>
</div>
        <!--系统配置修改modal-->
        <div class="modal fade" id="modal-sys-revamp" tabindex="-1" role="dialog">
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
                                    <div ng-repeat="host in hostList | filter: {company.name : currentCompany.name }">
                                        <ul class="clearfix city" ng-repeat="area in host.area | filter: {areaCode : currentArea.areaCode , name : currentArea.name} :true">
                                            <li ng-repeat="set in area.set" ng-class="{active:$first}" ng-click="changeSet(set)">
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
                                    <div ng-repeat="host in hostList | filter: {company.companyCode : currentCompany.companyCode , company.name : currentCompany.name} :true">
                                        <div ng-repeat="area in host.area | filter: {areaCode : currentArea.areaCode , name : currentArea.name}">
                                            <ul class="clearfix city" ng-repeat="set in area.set | filter: {cpu:currentSet.cpu}">
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
                                    <div ng-repeat="host in hostList | filter: {company.companyCode : currentCompany.companyCode , company.name : currentCompany.name} :true">
                                        <div ng-repeat="area in host.area | filter: {areaCode : currentArea.areaCode , name : currentArea.name}">
                                            <ul class="clearfix city" ng-repeat="set in area.set | filter: {cpu:currentSet.cpu}">
                                                <li  ng-class="{active:$first}"  ng-repeat="rom in set.rom" ng-click="changeRom(rom)">
                                                    {{rom.num}} M
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button id="sumbiter" type="button" class="btn btn-primary">确认</button>
                        <button id="reseter" type="button" class="btn btn-danger"
                                data-dismiss="modal">取消</button>
                    </div>
                </div>
            </div>
        </div>

    <!--网卡设置-->
<div class="network-card-con">
    <h5>网卡设置
        <div class="dropdown pull-right theme-color add-card">添加网卡</div>
    </h5>
    <div class="card-section hosts-table">
        <div class="net-card-img">
            <img src="/images/nophoto.jpg" alt="网卡管理"/>
            <p>默认网卡</p>
        </div>
        <div class="section-table">
            <table>
                <tr>
                    <td>状态：</td>
                    <td>索贝-成都测试区</td>
                </tr>
                <tr>
                    <td>所属VPC：</td>
                    <td>Vpc-cKO6VqIq</td>
                </tr>
                <tr>
                    <td>CPU：</td>
                    <td>subnet-1212</td>
                </tr>
                <tr>
                    <td>内存：</td>
                    <td>Ecs-BxSFWcOd</td>
                </tr>
                <tr>
                    <td>GPU：</td>
                    <td>服务器1</td>
                </tr>
            </table>
        </div>
        <div class="del-card theme-color">删除</div>
    </div>

    <div class="card-section hosts-table">
        <div class="net-card-img">
            <img src="/images/nophoto.jpg" alt="网卡管理"/>
            <p>默认网卡</p>
        </div>
        <div class="section-table">
            <table>
                <tr>
                    <td>状态：</td>
                    <td>索贝-成都测试区</td>
                </tr>
                <tr>
                    <td>所属VPC：</td>
                    <td>Vpc-cKO6VqIq</td>
                </tr>
                <tr>
                    <td>CPU：</td>
                    <td>subnet-1212</td>
                </tr>
                <tr>
                    <td>内存：</td>
                    <td>Ecs-BxSFWcOd</td>
                </tr>
                <tr>
                    <td>GPU：</td>
                    <td>服务器1</td>
                </tr>
            </table>
        </div>
        <div class="del-card theme-color">删除</div>
    </div>
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
                                    <label style="width:20%" for="" class="pull-left ">选择VPC:</label>
                                    <div style="width:80%" class="bk-form-row-cell">
                                        <select id="vpc2" onchange="loadSubnetPublic($(this).val())"></select>
                                    </div>
                                </div>
                                <div class="clearfix">
                                    <label style="width:20%" for="" class="pull-left ">选择网络:</label>
                                    <div style="width:80%" class="bk-form-row-cell">
                                        <select id="net2"></select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" id ="addSubnet" class="btn btn-primary" >确 认</button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal">取 消</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <!--块存储-->
<div class="storage-con">
    <!--<h5>块存储-->
        <!--<div class="dropdown pull-right theme-color add-card">添加网卡</div>-->
    <!--</h5>-->
    <div class="modal-title-list">
        <ul class="clearfix">
            <li class="active" no="1">添加硬盘</li>
            <li no="2">已用硬盘</li>
        </ul>
    </div>
    <div class=" hosts-table">
        <div class="modal-disk-content">
            <div class="modal-form-group">
                <label>硬盘名称:</label>
                <div>
                    <input id="txtdisks_name" type="text" onblur="if($(this).val()!=''){$('#name-warning').html('')}" />
                    <span class="text-danger" id="name-warning" style="font-size:12px;line-height:28px;margin-left:5px;"></span>
                </div>
            </div>
            <div class="modal-form-group">
                <label>容量大小:</label>
                <div class="slider-area">
                    <div id="slider"></div>
                </div>
                <div class="amount pull-left">
                    <input type="text" id="amount" placeholder="10" disabled="disabled"> GB
                </div>
            </div>
            <div class="modal-form-group">
                <label></label>
                <div>
                    <h6 class="warm">容量范围10GB-1000GB</h6>
                </div>
                <div class="storage-bnt">
                    <button onclick="btnaddDisks(null,this)" type="button" class="btn btn-primary">确认</button>
                </div>
            </div>
        </div>
        <div class="bootstrap-table modal-disk-content">
            <table id="use_table" data-toggle="table">
                <thead>
                <tr>
                    <th data-field="code" >硬盘Code</th>
                    <th data-field="name">名称</th>
                    <th data-field="capacity">容量(GB)</th>
                    <th data-formatter="operateFormatter">操作</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

    <!--快照-->
<div class="snap-con">
    <h5>快照
        <!--<div class="dropdown pull-right theme-color add-card">添加网卡</div>-->
    </h5>
    <div class="hosts-table ">
        <div class="bnt-box">
            <button class="btn btn-addition build-bnt"><i class="icon-plus"></i>新建</button>
        </div>
        <div class="bootstrap-table margint20 snap-tab">
            <table data-toggle="table">
                <thead>
                <tr>
                    <th data-field="code">时间</th>
                    <th data-field="name">描述</th>
                    <th data-formatter="snap_handle">操作</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
        <!--回滚modal-->
        <div class="modal fade" id="modal-rollBack" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"
                                aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h5 class="modal-title">回滚 - vmWare  / OpenStack</h5>
                    </div>
                    <div class="modal-body m-sys-body">
                       <h4><i class="icon-info-sign theme-color"></i> 友情提示：</h4>

                        <div class="m-roll-text">
                            <p>当前主机采用的虚拟化技术为vmWare。回滚后，主机如下配置将被还原：</p>
                            <p>
                                1、CPU、内存、GPU；<br/>
                                2、默认网卡；<br/>
                                3、扩展网卡，回滚后，扩展网卡可能出现IP冲突，需手动修改扩展网卡IP；<br/>
                                4、系统盘；<br/>
                                5、所有数据盘（块存储）。
                            </p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button  type="button" class="btn btn-primary">回滚</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">取消</button>
                    </div>
                </div>
            </div>
        </div>
        <!--新建modal-->
        <div class="modal fade" id="modal-addSnap" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"
                                aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h5 class="modal-title">新建快照</h5>
                    </div>
                    <form id="" action="" method="post">
                        <div class="modal-body">
                            <div class="modal-form-group">
                                <label>描述:</label>
                                <div>
                                    <textarea id="" name="description" rows="5"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary">确认</button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal">取消</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    <!--镜像-->
<div class="mirror-con">

    <h5>镜像
        <!--<div class="dropdown pull-right theme-color add-card">添加网卡</div>-->
    </h5>
    <div class="hosts-table ">
        <div class="bnt-box">
            <button class="btn btn-addition build-bnt"><i class="icon-plus marginR1"></i>新建</button>
            <button class="btn btn-addition revamp-bnt"><i class=""></i>修改</button>
            <button class="btn btn-addition del-bnt"><i class="icon-remove marginR1"></i>删除</button>
        </div>
        <div class="bootstrap-table margint20 snap-tab">
            <table data-toggle="table">
                <thead>
                <tr>
                    <th data-checkbox="true"></th>
                    <th data-field="code">镜像CODE</th>
                    <th data-field="name">镜像名称</th>
                    <th>保密类型</th>
                    <th>创建时间</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
        <!--修改/新建modal-->
        <div class="modal fade" id="m-addMirror" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"
                                aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h5 class="modal-title">新建镜像</h5>
                    </div>
                    <form id="" action="" method="post">
                        <div class="modal-body">
                            <div class="modal-form-group">
                                <label>镜像名称:</label>
                                <div>
                                    <input id="" name="name" type="text" maxlength="15" />
                                </div>
                            </div>
                            <div class="modal-form-group">
                                <label>保密类型:</label>
                                <div>
                                    <select class="select-style">
                                        <option value="">私有镜像</option>
                                        <option value="">共有镜像</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-form-group">
                                <label>所属租户:</label>
                                <div>
                                    <input id="m-lessee" name="name" type="text" maxlength="15" />
                                </div>
                            </div>
                            <div class="modal-form-group">
                                <label>描述:</label>
                                <div>
                                    <textarea id="modal-modify-description" name="description" rows="5"></textarea>
                                </div>
                            </div>
                            <input id="modal-modify-id" name="id" type="hidden" />
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

    <!--图形化-->
<div class="imaging-con">
    <div class="static-detailInfo">
        <h5>图形化</h5>
        <div class="hosts-table static-limit">
            <div class="detail-drawing">
                <div class="static-drawing">
                    <span class="static-stay">所在子网：</span>
                    <div class="static-drawing-model static-unit ">
                        <p><?= $_data['0']['G_Name'] ?></p>
                    </div>
                    <?php
                            if($_data['0']['H_Status']=="运行中"){ ?>
                    <div class="static-drawing-model static-run ">
                        <p>状态:<span class="text-primary">运行中</span></p>
                    </div>
                    <?php }else{ ?>
                    <div class="static-drawing-model static-stop " style="display:none">
                        <p>状态:<span class="text-danger">已停止</span></p>
                    </div>
                    <?php }
                        ?>
                </div>
                <div class="static-branch">
                    <div class="branch-first branch-model">
                        <div class="image" onclick="open_disks();" style="cursor: pointer;">

                        </div>
                        <p>磁盘：<?= empty($_disks)==true?0:count($_disks); ?> 个 </p>
                    </div>
                    <div class="branch-second branch-model">
                        <div class="image">

                        </div>
                        <p>快照：</p>
                    </div>
                    <div class="branch-three branch-model">
                        <div class="image">

                        </div>
                        <p>镜像：<?= $_data['0']['D_Image_code'] ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    <!--运行状态-->
<div class="status-con">
    <div class="static-detailInfo">
        <h5>监控信息</h5>
        <div  class="chart-box">
            <p>CPU</p>
            <div>
                <canvas id="canvas1"></canvas>
            </div>
            <p class="chart-title"><span></span> CPU使用率(%)</p>
        </div>
        <div  class="chart-box">
            <p>网络</p>
            <div>
                <canvas id="canvas4"></canvas>
            </div>
            <p class="chart-title"><span ></span>出网&nbsp;KBps &nbsp;<span class="line"></span>入网&nbsp;KBps</p>
        </div>
    </div>
</div>

    <!--操作记录-->
    <div class="action-record">
        <div class="static-detailInfo">
            <h5>操作记录</h5>
            <ul>
                <?php foreach ($_log as $key => $value) { ?>
                <li><span class="deleft"> <?= date("Y-m-d H:i:s",$value["create_time"]) ?></span>&nbsp;&nbsp;<?= $value["user_name"] ?>&nbsp;&nbsp; 对设备:&nbsp;&nbsp;<?= $value["device_name"] ?>&nbsp;&nbsp;进行[<?= $value["device_event"] ?>]</li>
                <?php } ?>

            </ul>
        </div>
    </div>
    <!--正常日志-->
    <div class="success-log">
        <h5>正常日志</h5>
        <div class="hosts-table"></div>
    </div>
    <!--异常日志-->
    <div class="abnormal-log">
        <h5>异常日志</h5>
        <div class="hosts-table"></div>
    </div>

        <div id="maindiv"></div>
</div>

</div>
<?php $this -> start('script_last'); ?>
<?= $this->Html->script(['network/hosts']); ?>

<?php
    $this -> end();
?>