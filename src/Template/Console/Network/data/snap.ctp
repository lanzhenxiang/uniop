<!--快照-->
<?= $this->Html->css(['network/hosts']); ?>
<?php if($type == "desktop"){ ?>
    <?= $this -> element('desktop/lists/left', ['active_action' => 'desktop']); ?>
<?php }else{?>
<?= $this -> element('network/lists/left', ['active_action' => 'hosts']); ?>
<?php }?>
<div class="wrap-nav-right hosts-content wrap-manage">
    <?= $this -> element('network/lists/left_nav', ['active_action' => 'snap','id'=>$_request_params['pass']['1']]); ?>

    <div class="snap-con hosts-right clearfix host-static">
        <h5>  
            <?= $this -> element('network/data/breadcrumb', ['breadcrumbTitle' => '快照','biz_tid' => $_data['0']['Biz_tid']]); ?>
            <!--<div class="dropdown pull-right theme-color add-card">添加网卡</div>-->
        </h5>
        <div class="hosts-table ">
            <div class="bnt-box">
                <button class="btn btn-addition build-bnt"><i class="icon-plus"></i>新建</button>
            </div>
            <div class="bootstrap-table margint20 snap-tab">
                <table id="snap-table" data-toggle="table" data-pagination="true"
         data-side-pagination="server"
         data-locale="zh-CN"
         data-click-to-select="true"
         data-url="<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'hosts', 'snapList','?'=>['basic_id'=>$_data['0']['H_ID']]]); ?>"
         data-unique-id="id">
                    <thead>
                    <tr>
                        <th data-field="code">快照Code</th>
                        <th data-field="isMemory" data-align="center" data-formatter="formatter_isMemory">内存快照</th>
                        <th data-field="status" data-formatter="formatter_state">状态</th>
                        <th data-field="description">描述</th>
                        <th data-field="create_time" data-formatter="timestrap2date">创建时间</th>
                        <th data-field="code" data-formatter="formatter_snap_handle">操作</th>
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
                    <?php if($fusiontype["fusionType"] =='vmware' || $fusiontype["fusionType"] =='openstack' ): ?>
                    <h5 class="modal-title">回滚 - vmWare  / OpenStack</h5>
                    <?php else:?>
                         <!--阿里亚马逊-->
                    <h5 class="modal-title">回滚 - 阿里/亚马逊</h5>
                    <?php endif;?>
                </div>
                    <?php if($fusiontype["fusionType"] =='vmware' || $fusiontype["fusionType"] =='openstack' ): ?>
                    <div class="modal-body m-sys-body">
                    <h4><i class="icon-info-sign theme-color"></i> 友情提示：</h4>
                    <div class="m-roll-text">
                        <p>当前主机采用的虚拟化技术为vmWare。回滚后，主机如下配置将被还原：</p>
                        <p>
                            1、CPU、内存、GPU；<br/>
                            2、默认网卡；<br/>
                            3、扩展网卡，回滚后，扩展网卡可能出现IP冲突，需手动修改扩展网卡IP；<br/>
                            4、系统盘；<br/>
                            5、所有数据盘（块存储）。<br/>
                            6、恢复普通快照会重启虚拟机。
                        </p>
                    </div>
                    </div>
                    <?php else: ?>
                       
                    <div class="modal-body m-sys-body">
                        <h4><i class="icon-info-sign theme-color"></i> 友情提示：</h4>
                        <div class="m-roll-text">
                            <p>当前主机采用的虚拟化技术为阿里。回滚后，主机如下配置将被还原：</p>
                            <p>
                                1、系统盘；<br/>
                                2、所有数据盘（块存储）。
                            </p>
                        </div>
                    </div>
                    <?php endif;?>
                <div class="modal-footer">
                    <input type="hidden" id="snap_id" name="basic_id" value="">
                    <input type="hidden" id="snap_code" name="code" value="">
                    <button id="snap-rollback-btn" type="button" class="btn btn-primary">回滚</button>
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
                                <textarea id="description" name="description" rows="5"></textarea>
                            </div>
                        </div>
                        <div class="modal-form-group">
                            <label>内存快照:</label>
                            <div>
                                <input type="radio" name="isMemory" value="false" checked="checked" />不启用
                                <input type="radio" name="isMemory" value="true"> 启用
                            </div>
                            
                        </div>
                        <div class="modal-form-group">
                            <label>提示信息:</label>
                            <div>
                                <p>
                                    <i class="icon-info-sign"></i>&nbsp;内存快照创建速度较慢
                                    
                                </p>
                                <p><i class="icon-info-sign"></i>&nbsp;如果挂载的额外磁盘为独立持久类型，快照无法影响。</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" id="basic_id" name="basic_id" value="<?=$_data[0]['H_ID']?>">
                        <input type="hidden" id="code" name="code" value="<?=$_data[0]['H_Code']?>">
                        <button id="snap-add" type="button" class="btn btn-primary">确认</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">取消</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    function formatter_state(value, row, index) {
        switch (value) {
        case "创建中":
            {
                return '<span id="imgState' + row.id + '" class="circle circle-create"></span><span id="txtState' + row.id + '">创建中</span>';
                break;
            }
        case "创建成功":
            {
                return '<span id="imgState' + row.id + '" class="circle circle-run"></span><span id="txtState' + row.id + '">创建成功</span>';
                break;
            }
        case "创建失败":
            {
                return '<span id="imgState' + row.id + '" class="circle circle-stopped"></span><span id="txtState' + row.id + '">创建失败</span>';
                break;
            }
        case "销毁中":
            {
                return '<span id="imgState' + row.id + '" class="circle circle-create"></span><span id="txtState' + row.id + '">销毁中</span>';
                break;
            }
        case "销毁失败":
            {
                return '<span id="imgState' + row.id + '" class="circle circle-stopped"></span><span id="txtState' + row.id + '">销毁失败</span>';
                break;
            }
        default:
            {
                return '<span id="imgState' + row.id + '" class="circle circle-create"></span>-';
            }
        }
    }

    function formatter_isMemory(value){
        if(value == 1){
            return "是";
        }else{
            return "否";
        }
    }


</script>