<?= $this->Html->css('bootstrap-datetimepicker.min.css'); ?>

<div class="wrap-nav-right wrap-index-page">
  <div class="index-total section">
    <div class="section-header clearfix relative">
      <h5 class="pull-left">
        录入服务器信息
      </h5>
      <div id="maindiv-alert"></div>
    </div>
    <div class="section-body change-pw clearfix">
      <form id="ecs-add-form" action="<?php echo $this->Url->build(array('controller' => 'ecs','action'=>'add')); ?>" method="post">
        <div class="pull-left col-sm-6">
          <div class="form-group">
            <label for="assets_no">资产编号</label>
            <div class="col-sm-6">
              <input name="assets_no" id="assets_no" value="<?php echo isset($data)?$data['assets_no']:''; ?>" type="text">
            </div>
          </div>
          <div class="form-group">
            <label for="SN">SN</label>
            <div class="col-sm-6">
              <input name="SN"  id="SN" value="<?php echo isset($data)?$data['SN']:''; ?>" type="text">
            </div>
          </div>
          <div class="form-group">
            <label for="memory">内存</label>
            <div class="col-sm-6">
              <input name="hardware_assets_ec[memory]" id="memory" value="<?php echo isset($data)?$data['memory']:''; ?>" type="text">
            </div>
          </div>
          <div class="form-group">
            <label for="disks">硬盘</label>
            <div class="col-sm-6">
              <input name="hardware_assets_ec[disks]" id="disks" value="<?php echo isset($data)?$data['disks']:''; ?>" type="text">
            </div>
          </div>
          <div class="form-group">
            <label for="network">网卡</label>
            <div class="col-sm-6">
              <input name="hardware_assets_ec[network]" id="network" value="<?php echo isset($data)?$data['network']:''; ?>" type="text">
            </div>
          </div>
          <div class="form-group">
            <label for="status">运行情况</label>
            <div class="col-sm-6">
              <input name="status" id="status" type="radio" value="1" checked="checked"/>上架
              <input name="status" type="radio" value="0" />上架
            </div>
          </div>
          <div class="form-group">
            <label for="location">位置</label>
            <div class="col-sm-6">
              <select id="location" name="location">
                <option value="BJ">北京</option>
                <option value="XA">西安</option>
                <option value="CDE">成都E区</option>
                <option value="CDF">成都F区</option>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label for="manager">所属人员</label>
            <div class="col-sm-6">
              <input name="manager" id="manager" value="<?php echo isset($data)?$data['manager']:''; ?>" type="text">
            </div>
          </div>
        </div>
        <div class="pull-right col-sm-6">
          <div class="form-group">
            <label for="manufacturer">品牌型号</label>
            <div class="col-sm-6">
              <input name="manufacturer" id="manufacturer" value="<?php echo isset($data)?$data['manufacturer']:''; ?>" type="text">
            </div>
          </div>
          <div class="form-group">
            <label for="cpu">CPU</label>
            <div class="col-sm-6">
              <input name="hardware_assets_ec[cpu]" id="cpu" value="<?php echo isset($data)?$data['cpu']:''; ?>" type="text">
            </div>
          </div>
          <div class="form-group">
            <label for="gpu">显卡型号</label>
            <div class="col-sm-6">
              <input name="hardware_assets_ec[gpu]" id="gpu" value="<?php echo isset($data)?$data['gpu']:''; ?>" type="text">
            </div>
          </div>
          <div class="form-group">
            <label for="IP">IP地址</label>
            <div class="col-sm-6">
              <input name="IP" id="IP" value="<?php echo isset($data)?$data['IP']:''; ?>" type="text">
              <span class="text-danger loginemail"></span>
            </div>
          </div>
          <div class="form-group">
            <label for="EIP">带外IP</label>
            <div class="col-sm-6">
              <input name="hardware_assets_ec[EIP]" id="EIP" value="<?php echo isset($data)?$data['EIP']:''; ?>" type="text">
              <span class="text-danger loginemail"></span>
            </div>
          </div>
          <div class="form-group">
            <label for="cabinet_no">机架编号</label>
            <div class="col-sm-6">
              <select id="cabinet_no" name="cabinet_no">
                <option value="H1">H1</option>
                <option value="H2">H2</option>
                <option value="H3">H3</option>
                <option value="H4">H4</option>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label for="department">部门</label>
            <div class="col-sm-6">
              <input name="department" id="department" value="<?php echo isset($data)?$data['department']:''; ?>" type="text">
              <span class="text-danger loginemail"></span>
            </div>
          </div>
          <div class="form-group">
            <label for="buy_date">购置日期</label>
            <div class="col-sm-6">
              <div class="order-number input-append date" id="buy_datetimepicker" data-date-format="yyyy-mm-dd" style="height:28px;margin:0;line-height:28px;">
                <input  type="text" name="time" id="buy_date" value="" readonly style="height:28px;margin:0;line-height:28px;">
                <span class="add-on"><i class="icon-th"></i></span>
              </div>
            </div>
          </div>
          <div class="form-group">
            <label for=""></label>
            <button type="submit" id="submit" class="btn btn-primary" >保存</button>
          </div>
        </div>
      </form>
    </div>    

  </div>

</div>
<?= $this->Html->script('bootstrap-datetimepicker.js'); ?>
<?= $this->Html->script(['validator.bootstrap.js']); ?>
<script>
  $(document).ready(
      function(){

          var myDate = new Date();
          var year = myDate.getFullYear();
          var month =myDate.getMonth()+1;
          var day =  myDate.getDate();
          var time =year+'-'+month+'-'+day;
          $('#buy_datetimepicker').datetimepicker({
                  autoclose:true,
                  minView:2,
                  startDate:'',
                  endDate:time
              }
          );
  });

$('#ecs-add-form').bootstrapValidator({
  submitButtons: 'button[type="submit"]',
  submitHandler: function(validator, form, submitButton){
    $.post(form.attr('action'), form.serialize(), function(data){
      if(data.code== 0){
          layer.alert(data.msg);
          window.location.href ="<?php echo $this->Url->build(array('controller' => 'ecs','action'=>'index')); ?>";
      }else{
          layer.alert(data.msg);
      }
    });
  },
  fields : {
      assets_no: {
      // group: '.col-sm-6',
      validators: {
        notEmpty: {
          message: '资产编号不能为空'
        }
      }
    },
    SN: {
      // group: '.col-sm-6',
      validators: {
        notEmpty: {
          message: 'SN不能为空'
        }
      }
    },
      manufacturer: {
      // group: '.col-sm-6',
      validators: {
        notEmpty: {
          message: '品牌型号不能为空'
        }
      }
    },
  }
});
</script>