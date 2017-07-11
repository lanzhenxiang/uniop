<?= $this->Html->css(['network/hosts']); ?>
<?php if($type == "desktop"){ ?>
    <?= $this -> element('desktop/lists/left', ['active_action' => 'desktop']); ?>
<?php }else{?>
<?= $this -> element('network/lists/left', ['active_action' => 'hosts']); ?>
<?php }?>
<div class="wrap-nav-right hosts-content">
    <?= $this -> element('network/lists/left_nav', ['active_action' => 'imaging','id'=>$_data['0']['H_ID']]); ?>
    <div class="imaging-con hosts-right clearfix host-static">
        <div class="static-detailInfo">
            <h5>  
              <?= $this -> element('network/data/breadcrumb', ['breadcrumbTitle' => '图形化','biz_tid' => $_data['0']['Biz_tid']]); ?>
            </h5>
            <div class="hosts-table static-limit">
                <div class="detail-drawing">
                <input id="hostsCode" type="hidden" value="<?=$_data['0']['H_Code']?>" />
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
</div>

<!-- 硬盘 -->
<div class="modal fade " id="disk-manage" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"
        aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
      <h5 class="modal-title">主机硬盘管理</h5>
    </div>
    <input type="hidden" id="hostsCode" value ="<?=$_data['0']['H_Code']?>"/>
    <input type="hidden" id="hostsId"/  value ="<?=$_data['0']['H_ID']?>">
    <input type="hidden" id="txtvpcCode" value ="<?=$_data['0']['F_Code']?>" />
    <input type="hidden" id="txtclass_code" value ="<?=$_data['0']['H_L_Code']?>" />
    <input type="hidden" id="txtisFusion" value ="<?=$_data['0']['D_isFusion']?>"/>
    <div class="modal-body ">
      <div class="modal-title-list">
        <ul class="clearfix">
          <li class="active" no="1">添加硬盘</li>
          <li no="2">已用硬盘</li>
        </ul>
      </div>
      <div class="modal-disk-content" style="display:block;">
                      <div class="modal-form-group">
                       <label>硬盘名称:</label>
                       <div>
                        <input id="txtdisks_name" type="text" />
                      </div>
                      
                    </div>
                    <div class="modal-form-group">
                      <label>容量大小:</label>
                      <div class="slider-area">
                        <div id="slider"></div>
                      </div>
                      <div class="amount pull-left">
                        <input type="text" id="amount" readonly="true" placeholder="10"> GB
                      </div>
                    </div>
                    <div class="modal-form-group">
                      <label></label>
                      <div>
                        <h6 class="warm">请输入范围10GB-1000GB</h6>
                      </div>
                    </div>

                    <div class="modal-form-point">
                    </div>
                    <div class="modal-footer">
                      <button onclick="btnaddDisks()" type="button" class="btn btn-primary">添加</button>
                      <button type="button" class="btn btn-danger" data-dismiss="modal">取消</button>
                    </div>
                  </div>
                  <div class="modal-disk-content" style="display:none">
                    <table id="use_table">
                      <thead>
                        <tr>
                          <th data-field="code" >硬盘ID</th>
                          <th data-field="name">名称</th>
                          <th data-field="capacity">容量(GB)</th>
                          <th data-formatter="operateFormatter">操作</th>
                        </tr>
                      </thead>
                    </table>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-danger" data-dismiss="modal">关闭</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>