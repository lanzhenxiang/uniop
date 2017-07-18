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
            <td colspan="2">网卡</td>
          </tr>
          <tr>
            <td><?=$ecs['manufacturer']?></td>
            <td><?=$ecs['hardware_assets_ec']['cpu']?></td>
            <td><?=$ecs['hardware_assets_ec']['memory']?></td>
            <td><?=$ecs['hardware_assets_ec']['disks']?></td>
            <td colspan="2"><?=$ecs['hardware_assets_ec']['network']?></td>
          </tr>

          <tr class="detail-table-title">
            <td>IP地址</td>
            <td>带外IP</td>
            <td>是否保修期</td>
            <td>最后变更人</td>
            <td colspan="2">显卡型号</td>
          </tr>
          <tr>
            <td><?=$ecs['IP']?></td>
            <td><?=$ecs['hardware_assets_ec']['EIP']?></td>
            <td><?=$ecs['warranty']?></td>
            <td><?=$ecs['updated_by']?></td>
            <td colspan="2"><?=$ecs['hardware_assets_ec']['gpu']?></td>
          </tr>
          <tr class="detail-table-title">
            <td>运行情况</td>
            <td>位置</td>
            <td>机架编号</td>
            <td>部门</td>
            <td>所属人员</td>
            <td>购置日期</td>
          </tr>
          <tr>
            <td><?=$ecs['status'] == '1' ? '上线' : '下架'?></td>
            <td><?=$ecs['location']?></td>
            <td><?=$ecs['cabinet_no']?></td>
            <td><?=$ecs['department']?></td>
            <td><?=$ecs['manager']?></td>
            <td><?=$ecs['buy_date']?></td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="row section-body">
      <div class="col-lg-12">
        <div class="panel panel-default">
          <div class="panel-body">
            <h3>硬件更换记录
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
      <!-- /.col-lg-4 -->
    </div>

  </div>