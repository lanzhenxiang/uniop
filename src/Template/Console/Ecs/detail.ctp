
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
            <div class="row show-grid title-center">
              <div class="col-md-2">更换日期</div>
              <div class="col-md-5">更换内容</div>
              <div class="col-md-5">更换原因</div>
            </div>
            <div class="row  title-center">
              <div class="col-md-12">暂无更换记录</div>
            </div>
            <!--<div class="row  title-center">-->
              <!--<div class="col-md-2">fff</div>-->
              <!--<div class="col-md-5">更换内容</div>-->
              <!--<div class="col-md-5">更换原因</div>-->
            <!--</div>-->
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
            <div class="row show-grid title-center">
              <div class="col-md-2">日期</div>
              <div class="col-md-2">服务人员</div>
              <div class="col-md-2">故障现象</div>
              <div class="col-md-3">处理方法</div>
              <div class="col-md-3">备注</div>
            </div>
            <div class="row  title-center">
              <div class="col-md-12">暂无维护记录</div>
            </div>
            <!--<div class="row  title-center">-->
            <!--<div class="col-md-2">fff</div>-->
            <!--<div class="col-md-5">更换内容</div>-->
            <!--<div class="col-md-5">更换原因</div>-->
            <!--</div>-->
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
          <button class="btn btn-default" id="btnPrint" >
            <i class="icon-print "></i>&nbsp;&nbsp;打印此页面
          </button>
        </div>
      </div>
      <!-- /.col-lg-4 -->
    </div>

  </div>
  <!-- 添加硬件更换记录 -->
  <div class="modal fade" id="hardware-instead" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"
                  aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h5 class="modal-title">硬件更换记录</h5>
        </div>
        <input type="hidden" id="ecsCode"/>
        <input type="hidden" id="ecsId"/>
        <div class="modal-body">

          <div class="modal-disk-contentsys" style="display:block;">

            <div class="modal-form-group">
              <label for="instead_date">更换日期:</label>
              <div class="amount pull-left">
                <div class="order-number input-append date" id="instead_date_datetimepicker" data-date-format="yyyy-mm-dd" style="height:28px;margin:0;line-height:28px;">
                  <input  type="text" name="time" id="instead_date" value="" readonly style="height:28px;margin:0;line-height:28px;">
                  <span class="add-on"><i class="icon-th"></i></span>
                </div>
              </div>
            </div>
            <div class="modal-form-group">
              <label for="instead_content">更换内容:</label>
              <div class="amount pull-left">
                <textarea id="instead_content" name="instead_content" rows="5" placeholder="更换内容"></textarea>
              </div>
            </div>
            <div class="modal-form-group">
              <label for="instead_reason">更换原因:</label>
              <div class="amount pull-left">
                <textarea id="instead_reason" name="instead_reason" rows="5" placeholder="更换原因"></textarea>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-primary">确认</button>
              <button type="button" class="btn btn-danger" data-dismiss="modal">取消</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- 添加硬件更换记录 -->
  <div class="modal fade" id="hardware-repair" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
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

            <div class="modal-form-group">
              <label for="instead_date">更换日期:</label>
              <div class="amount pull-left">
                <div class="order-number input-append date" id="repair_date_datetimepicker" data-date-format="yyyy-mm-dd" style="height:28px;margin:0;line-height:28px;">
                  <input  type="text" name="time" id="repair_date" value="" readonly style="height:28px;margin:0;line-height:28px;">
                  <span class="add-on"><i class="icon-th"></i></span>
                </div>
              </div>
            </div>
            <div class="modal-form-group">
              <label for="instead_content">更换内容:</label>
              <div class="amount pull-left">
                <textarea id="repair_content" name="instead_content" rows="5" placeholder="更换内容"></textarea>
              </div>
            </div>
            <div class="modal-form-group">
              <label for="instead_reason">更换原因:</label>
              <div class="amount pull-left">
                <textarea id="repair_reason" name="instead_reason" rows="5" placeholder="更换原因"></textarea>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-primary">确认</button>
              <button type="button" class="btn btn-danger" data-dismiss="modal">取消</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?= $this->Html->script('bootstrap-datetimepicker.js'); ?>

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
    });

</script>