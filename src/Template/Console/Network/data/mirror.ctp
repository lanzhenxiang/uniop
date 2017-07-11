<!--镜像-->
<?= $this->Html->css(['network/hosts']); ?>
<?= $this -> element('network/lists/left', ['active_action' => 'hosts']); ?>
<div class="wrap-nav-right hosts-content">
    <?= $this -> element('network/lists/left_nav', ['active_action' => 'mirror','id'=>$_request_params['pass']['1']]); ?>
    <div class="mirror-con hosts-right clearfix host-static">

        <h5>  
            <?= $this -> element('network/data/breadcrumb', ['breadcrumbTitle' => '镜像','biz_tid' => $_data['0']['Biz_tid']]); ?>
            <!--<div class="dropdown pull-right theme-color add-card">添加网卡</div>-->
        </h5>
        <div class="hosts-table">
            <div class="bnt-box" 
                    data-uniqueid="<?=$_data['0']['H_ID']?>" 
                    data-status="<?=$_data['0']['H_Status']?>" 
                    data-code="<?=$_data['0']['H_Code']?>" 
                    data-id="<?=$_data['0']['H_ID']?>"
                    data-name="<?=$_data['0']['H_Name']?>"
            >
                <button class="btn btn-addition build-bnt"><i class="icon-plus marginR1"></i>新建</button>
                <button class="btn btn-addition btn-default revamp-bnt" disabled><i class=""></i>修改</button>
                <button class="btn btn-addition btn-default del-bnt" disabled id="image-del"><i class="icon-remove marginR1"></i>删除</button>
                <?php if($_data['0']['H_Status'] == '运行中'): ?>
                <button id="shutdown" class="btn btn-addition build-bnt"><i class="icon-off "></i>关机</button>
                <?php endif;?>
            </div>
            <div class="bootstrap-table margint20 snap-tab">
                <table id="image-table" data-toggle="table" data-pagination="true"
         data-side-pagination="server"
         data-locale="zh-CN"
         data-click-to-select="true"
         data-url="<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'hosts', 'imageList','?'=>['basic_id'=>$_data['0']['H_ID']]]); ?>"
         data-unique-id="id">
                    <thead>
                    <tr>
                        <th data-checkbox="true"></th>
                        <th data-field="image_code">镜像CODE</th>
                        <th data-field="image_name">镜像名称</th>
                        <th data-field="is_private" data-formatter="formatter_is_private">保密类型</th>
                        <th data-field="create_time" data-formatter="formatter_create_time">创建时间</th>
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
                                <input id="image_name" name="image_name" type="text" maxlength="15" onblur="if($(this).val()!=''){$('#name-warning').html('')}"  />
                                <span class="text-danger" id="name-warning" style="font-size:12px;line-height:28px;margin-left:5px;"></span>
                            </div>
                        </div>
                        <div id="image-type" class="modal-form-group">
                            <label>保密类型:</label>
                            <div>
                                <select class="select-style" id="is_private" name="is_private">
                                    <option value="1">私有镜像</option>
                                    <option value="0">公共镜像</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-form-group">
                            <label>所属租户:</label>
                            <div>
                                <input id="m-lessee" value="<?=$user_name?>" readonly="true" name="name" type="text" maxlength="15" />
                            </div>
                        </div>
                        <div class="modal-form-group">
                            <label>描述:</label>
                            <div>
                                <textarea id="image_note" name="image_note" rows="5"></textarea>
                            </div>
                        </div>
                        <input id="modal-modify-id" name="id" type="hidden" />
                    </div>
                    <div class="modal-footer">
                        <input type="hidden" value="<?=$_data['0']['H_ID']?>" id="basic_id" name="basic_id">
                        <input type="hidden" value="<?=$_data['0']['H_Code']?>" id="ecscode" name="ecscode">
                        <input type="hidden" value="<?=$_data['0']['H_Status']?>" id="status" name="status">
                        <input type="hidden" value="" id="image_id" name="image_id">
                        <button id="image-add-sumbiter" type="button" class="btn btn-primary">确认</button>
                        <button id="reseter" type="button" class="btn btn-danger"
                                data-dismiss="modal">取消</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>