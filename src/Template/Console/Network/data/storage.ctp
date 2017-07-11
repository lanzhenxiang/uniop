<!--块存储-->
<?= $this->Html->css(['network/hosts']); ?>
<?php if($type == "desktop"){ ?>
    <?= $this -> element('desktop/lists/left', ['active_action' => 'desktop']); ?>
<?php }else{?>
<?= $this -> element('network/lists/left', ['active_action' => 'hosts']); ?>
<?php }?>
<div class="wrap-nav-right hosts-content">
    <?= $this -> element('network/lists/left_nav', ['active_action' => 'storage','id'=>$_data['0']['H_ID']]); ?>
    <div class="storage-con hosts-right clearfix host-static">
        <h5>  
            <?= $this -> element('network/data/breadcrumb', ['breadcrumbTitle' => '块存储','biz_tid' => $_data['0']['Biz_tid']]); ?>
            <!--<div class="dropdown pull-right theme-color add-card">添加网卡</div>-->
        </h5>
        <input id="hostsCode"  type="hidden" name="hostsCode"    value ="<?=$_data['0']['H_Code']?>" />
        <input id="hostsId"    type="hidden" name="hostsId"      value ="<?=$_data['0']['H_ID']?>" />
        <input id="txtvpcCode" type="hidden" name="txtvpcCode"   value ="<?=$_data['0']['F_Code']?>" />
        <input id="txtclass_code" type="hidden" name="txtclass_code"   value ="<?=$_data['0']['H_L_Code']?>" />
        <input id="txtisFusion" type="hidden" name="txtisFusion"   value ="<?=$_data['0']['D_isFusion']?>" />
        <div class="modal-title-list">
            <ul class="tab-stor">
                <li class="active" no="1">添加硬盘</li>
                <li no="2">已用硬盘</li>
            </ul>
        </div>
        <div class=" hosts-table">
            <div class="modal-disk-content" >
                <div class="modal-form-group">
                    <label>硬盘名称:</label>
                    <div>
                        <input id="txtdisks_name" type="text" onblur="if($(this).val()!=''){$('#name-warning').html('')}" />
                        <span class="text-danger" id="name-warning" style="font-size:12px;line-height:28px;margin-left:5px;"></span>
                        <div>
                    </div>
                    </div>
                </div>
                <div class="modal-form-group">
                    <label>容量大小:</label>
                    <div class="slider-area">
                        <div id="slider"></div>
                    </div>
                    <div class="amount pull-left">
                        <input type="text" id="amount" readonly="true" placeholder="10" > GB
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
</div>