<?= $this->Html->css('bootstrap-datetimepicker.min.css'); ?>
<div class="wrap-nav-right wrap-index-page page-wrapper">
  <div class="index-total section">
    <div class="row">
      <div class="col-lg-12">
        <h1 class="page-header" style="margin-left:30px;">服务器详细信息表</h1>
      </div>
      <!-- /.col-lg-12 -->
    </div>
    <div class="section-body change-pw clearfix">
      <table class="table table-bordered title-center">
        <tbody>
          <tr>
            <td class="detail-table-title">资产编号:</td>
            <td colspan="3">sdsds</td>
            <td class="detail-table-title">SN:</td>
            <td colspan="3">sffdsfdf</td>
          </tr>
          <tr class="detail-table-title">
            <td>品牌型号</td>
            <td>CPU</td>
            <td>内存</td>
            <td>硬盘</td>
            <td>网卡</td>
            <td>显卡型号</td>
            <td>IP地址</td>
            <td>带外IP</td>
          </tr>
          <tr>
            <td><?=$ecs['manufacturer']?></td>
            <td><?=$ecs['hardware_assets_ec']['cpu']?></td>
            <td><?=$ecs['hardware_assets_ec']['memory']?></td>
            <td><?=$ecs['hardware_assets_ec']['disks']?></td>
            <td><?=$ecs['hardware_assets_ec']['network']?></td>
            <td><?=$ecs['hardware_assets_ec']['gpu']?></td>
            <td><?=$ecs['IP']?></td>
            <td><?=$ecs['hardware_assets_ec']['EIP']?></td>
          </tr>
          <tr class="detail-table-title">
            <td>运行情况</td>
            <td>位置</td>
            <td>机架编号</td>
            <td>部门</td>
            <td>所属人员</td>
            <td>购置日期</td>
            <td>是否保修期</td>
            <td>最后变更人</td>
          </tr>
          <tr>
            <td><?=$ecs['status'] == '1' ? '上线' : '下架'?></td>
            <td><?=$ecs['location']?></td>
            <td><?=$ecs['cabinet_no']?></td>
            <td><?=$ecs['department']?></td>
            <td><?=$ecs['manager']?></td>
            <td><?=$ecs['buy_date']?></td>
            <td><?=$ecs['warranty']?></td>
            <td><?=$ecs['updated_by']?></td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="row section-body">
      <div class="col-lg-12">
        <div class="panel panel-default">
          <div class="panel-body">
            <h3>硬件更换记录
              <div class="pull-right"><button class="btn btn-default" id="addChange" >
                <i class="icon-plus"></i>&nbsp;&nbsp;添加更换记录
              </button></div>
            </h3>
            <div class="panel-body">
              <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover">
                  <thead>
                  <tr>
                    <!--<th>#</th>-->
                    <th>更换日期</th>
                    <th>更换内容</th>
                    <th>更换原因</th>
                    <th>操作</th>
                  </tr>
                  </thead>
                  <tbody class="title-center">
                  <?php if(!empty($ecs['hardware_instead']) &&  is_array($ecs['hardware_instead'])):?>
                  <?php foreach($ecs['hardware_instead'] as $instead):?>
                      <tr>
                        <!--<td>1</td>-->
                        <td><?=$instead['instead_date']->i18nFormat('yyyy-MM-dd')?></td>
                        <td><?=$instead['instead_content']?></td>
                        <td><?=$instead['instead_reason']?></td>
                        <td>
                          <a href="<?php echo $this->Url->build(array('controller' => 'HardwareInstead','action'=>'edit',$instead['id'])); ?>" class="btn btn-xs " style="min-width: 0;"><i class="icon-edit"></i></a>
                          <a href="javascript:void(0);" onclick="delInstead(<?=$instead['id']?>)" class="btn btn-xs " style="min-width: 0;"><i class="icon-remove"></i></a>
                        </td>
                      </tr>
                  <?php endforeach;?>
                  <?php else:?>
                      <tr>
                        <td colspan="4">暂无更换记录</td>
                      </tr>
                  <?php endif;?>
                  </tbody>
                </table>
              </div>
              <!-- /.table-responsive -->
            </div>
          </div>
        </div>
      </div>
      <!-- /.col-lg-12 -->
    </div>

    <div class="row section-body">
      <div class="col-lg-12">
        <div class="panel panel-default">
          <div class="panel-body">
            <h3>维护记录/维修记录
              <div class="pull-right"><button class="btn btn-default" id="addRepair" >
                <i class="icon-plus"></i>&nbsp;&nbsp;添加维修记录
              </button></div>
            </h3>
            <div class="panel-body">
              <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover">
                  <thead>
                  <tr>
                    <!--<th>#</th>-->
                    <th>日期</th>
                    <th>服务人员</th>
                    <th>故障现象</th>
                    <th>处理方法</th>
                    <th>备注</th>
                    <th>操作</th>
                  </tr>
                  </thead>
                  <tbody class="title-center">
                  <?php if(!empty($ecs['hardware_repair']) && is_array($ecs['hardware_repair'])):?>
                  <?php foreach($ecs['hardware_repair'] as $repair):?>
                  <tr>
                    <!--<td>1</td>-->
                    <td><?=$repair['repair_date']->i18nFormat('yyyy-MM-dd')?></td>
                    <td><?=$repair['repair_by']?></td>
                    <td><?=$repair['repair_reason']?></td>
                    <td><?=$repair['repair_way']?></td>
                    <td><?=$repair['repair_note']?></td>
                    <td>
                      <a href="<?php echo $this->Url->build(array('controller' => 'HardwareRepair','action'=>'edit',$repair['id'])); ?>" class="btn btn-xs " style="min-width: 0;"><i class="icon-edit"></i></a>
                      <a href="javascript:void(0);" onclick="delRepair(<?=$repair['id']?>)" class="btn btn-xs " style="min-width: 0;"><i class="icon-remove"></i></a>
                    </td>
                  </tr>
                  <?php endforeach;?>
                  <?php else:?>
                  <tr>
                    <td colspan="6">暂无维修记录</td>
                  </tr>
                  <?php endif;?>
                  </tbody>
                </table>
              </div>
              <!-- /.table-responsive -->
            </div>
          </div>
        </div>
      </div>
      <!-- /.col-lg-12 -->
    </div>

    <div class="row section-body">
      <div class="col-lg-3" >
        <div class="panel " style="border: none;">
          <div class="panel-body">
            <?php echo $this->Html->image('QR/testqr.png',[
            "alt" => "test",
            'url' => ['controller' => 'ecs', 'action' => 'detail', $ecs['assets_no']]
            ]) ?>
          </div>
        </div>
      </div>
      <!-- /.col-lg-4 -->
      <div class="col-lg-9">
        <div class="pull-right">
          <button class="btn btn-default" id="btnDel" >
            <i class="icon-file"></i>&nbsp;&nbsp;导出此页面
          </button>
          <a href="<?php echo $this->Url->build(array('controller' => 'ecs','action'=>'printPage',$ecs['assets_no'])); ?>" class="btn btn-default" id="btnPrint" >
            <i class="icon-print "></i>&nbsp;&nbsp;打印此页面
          </a>
        </div>
      </div>
      <!-- /.col-lg-4 -->
    </div>

  </div>
  <!-- 添加硬件更换记录 -->
  <div class="modal fade" id="hardware-instead" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <form id="add-hardware-instead-form" action="<?php echo $this->Url->build(array('controller' => 'HardwareInstead','action'=>'add')); ?>" method="post">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"
                    aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
            <h5 class="modal-title">硬件更换记录</h5>
          </div>
          <div class="modal-body">

            <div class="modal-disk-contentsys" style="display:block;">

              <div class="modal-form-group form-group">
                <label for="instead_date">更换日期:</label>
                <div class="amount pull-left">
                  <div class="order-number input-append date" id="instead_date_datetimepicker" data-date-format="yyyy-mm-dd" style="height:28px;margin:0;line-height:28px;">
                    <input  type="text" name="instead_date" id="instead_date" value="" readonly style="height:28px;margin:0;line-height:28px;">
                    <span class="add-on"><i class="icon-th"></i></span>
                  </div>
                </div>
              </div>
              <div class="modal-form-group form-group">
                <label for="instead_content">更换内容:</label>
                <div class="amount pull-left">
                  <textarea id="instead_content" name="instead_content" rows="5" placeholder="更换内容"></textarea>
                </div>
              </div>
              <div class="modal-form-group form-group">
                <label for="instead_reason">更换原因:</label>
                <div class="amount pull-left">
                  <textarea id="instead_reason" name="instead_reason" rows="5" placeholder="更换原因"></textarea>
                </div>
              </div>
              <div class="modal-footer">
                <input type="hidden" id="assets_id" name="assets_id" value="<?=$ecs['id']?>" />
                <button type="submit" id="addHardwareInstead" class="btn btn-primary">确认</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">取消</button>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
  <!-- 添加硬件维修记录 -->
  <div class="modal fade" id="hardware-repair" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"
                  aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h5 class="modal-title">硬件维修记录</h5>
        </div>
        <div class="modal-body">
          <form id="add-hardware-repair-form" action="<?php echo $this->Url->build(array('controller' => 'HardwareRepair','action'=>'add')); ?>" method="post">

          <div class="modal-disk-contentsys" style="display:block;">

            <div class="modal-form-group form-group">
              <label for="repair_date_datetimepicker">维修日期:</label>
              <div class="amount pull-left">
                <div class="order-number input-append date" id="repair_date_datetimepicker" data-date-format="yyyy-mm-dd" style="height:28px;margin:0;line-height:28px;">
                  <input  type="text" name="repair_date" id="repair_date" value="" readonly style="height:28px;margin:0;line-height:28px;">
                  <span class="add-on"><i class="icon-th"></i></span>
                </div>
              </div>
            </div>
            <div class="modal-form-group form-group">
              <label for="repair_by">服务人员:</label>
              <div class="amount pull-left">
                <input type="text" id="repair_by" name="repair_by" rows="5" placeholder="服务人员">
              </div>
            </div>
            <div class="modal-form-group form-group">
              <label for="repair_reason">故障现象:</label>
              <div class="amount pull-left">
                <textarea id="repair_reason" name="repair_reason" rows="5" placeholder="故障现象"></textarea>
              </div>
            </div>
            <div class="modal-form-group form-group">
              <label for="repair_way">处理方法:</label>
              <div class="amount pull-left">
                <textarea id="repair_way" name="repair_way" rows="5" placeholder="处理方法"></textarea>
              </div>
            </div>
            <div class="modal-form-group form-group">
              <label for="repair_note">备注:</label>
              <div class="amount pull-left">
                <textarea id="repair_note" name="repair_note" rows="5" placeholder="备注"></textarea>
              </div>
            </div>
            <div class="modal-footer">
              <input type="hidden"  name="assets_id" value="<?=$ecs['id']?>" />
              <button type="submit" class="btn btn-primary">确认</button>
              <button type="button" class="btn btn-danger" data-dismiss="modal">取消</button>
            </div>
          </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<?= $this->Html->script(['bootstrap-datetimepicker.js','validator.bootstrap.js']); ?>
<script>
$("#addChange").click(function () {
    $("#hardware-instead").modal("show");
});

$("#addRepair").click(function () {
    $("#hardware-repair").modal("show");
});
$(document).ready(
    function(){

        var myDate = new Date();
        var year = myDate.getFullYear();
        var month =myDate.getMonth()+1;
        var day =  myDate.getDate();
        var time =year+'-'+month+'-'+day;
        $('#instead_date_datetimepicker').datetimepicker({
                autoclose:true,
                minView:2,
                startDate:'',
                endDate:time
            }
        );
        $('#repair_date_datetimepicker').datetimepicker({
                autoclose:true,
                minView:2,
                startDate:'',
                endDate:time
            }
        );
    });

$('#add-hardware-instead-form').bootstrapValidator({
    submitButtons: 'button[type="submit"]',
    submitHandler: function(validator, form, submitButton){
        $.post(form.attr('action'), form.serialize(), function(data){
            if(data.code== 0){
                layer.alert(data.msg);
                window.location.href ="<?php echo $this->Url->build(array('controller' => 'ecs','action'=>'detail')); ?>/<?=$ecs['assets_no']?>";
            }else{
                layer.alert(data.msg);
            }
        });
    },
    fields : {
        instead_date: {
            // group: '.col-sm-6',
            validators: {
                notEmpty: {
                    message: '更换日期不能为空'
                }
            }
        },
        instead_content: {
            // group: '.col-sm-6',
            validators: {
                notEmpty: {
                    message: '更换内容不能为空'
                }
            }
        },
        instead_reason: {
            // group: '.col-sm-6',
            validators: {
                notEmpty: {
                    message: '更换原因不能为空'
                }
            }
        }
    }
});

$('#add-hardware-repair-form').bootstrapValidator({
    submitButtons: 'button[type="submit"]',
    submitHandler: function(validator, form, submitButton){
        $.post(form.attr('action'), form.serialize(), function(data){
            if(data.code== 0){
                layer.alert(data.msg);
                window.location.href ="<?php echo $this->Url->build(array('controller' => 'ecs','action'=>'detail')); ?>/<?=$ecs['assets_no']?>";
            }else{
                layer.alert(data.msg);
            }
        });
    },
    fields : {
        repair_date: {
            // group: '.col-sm-6',
            validators: {
                notEmpty: {
                    message: '维修日期不能为空'
                }
            }
        },
        repair_by: {
            // group: '.col-sm-6',
            validators: {
                notEmpty: {
                    message: '服务人员不能为空'
                }
            }
        },
        repair_reason: {
            // group: '.col-sm-6',
            validators: {
                notEmpty: {
                    message: '故障现象不能为空'
                }
            }
        },
        repair_way: {
            // group: '.col-sm-6',
            validators: {
                notEmpty: {
                    message: '处理方法不能为空'
                }
            }
        }

    }
});

function  delInstead(id) {

    layer.confirm('确认删除硬件更换记录？', {
        btn: ['确认','取消'] //按钮
    }, function(){
        $.ajax({
            type: "post",
            url: "<?php echo $this->Url->build(array('controller' => 'HardwareInstead','action'=>'del')); ?>",
            async: true,
            timeout: 9999,
            data: {
                id: id,
            },
            dataType:'json',
            success: function(data) {
                if (data.code != "0") {
                    layer.alert(data.msg);
                }else{
                    window.location.href ="<?php echo $this->Url->build(array('controller' => 'ecs','action'=>'detail')); ?>/<?=$ecs['assets_no']?>";
                }
            }
        });
    }, function(){
    });
}


function  delRepair(id) {

    layer.confirm('确认删除硬件维修记录？', {
        btn: ['确认','取消'] //按钮
    }, function(){
        $.ajax({
            type: "post",
            url: "<?php echo $this->Url->build(array('controller' => 'HardwareRepair','action'=>'del')); ?>",
            async: true,
            timeout: 9999,
            data: {
                id: id,
            },
            dataType:'json',
            success: function(data) {
                if (data.code != "0") {
                    layer.alert(data.msg);
                }else{
                    window.location.href ="<?php echo $this->Url->build(array('controller' => 'ecs','action'=>'detail')); ?>/<?=$ecs['assets_no']?>";
                }
            }
        });
    }, function(){
    });
}

</script>