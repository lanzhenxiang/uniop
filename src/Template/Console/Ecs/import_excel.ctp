
<div class="wrap-nav-right wrap-index-page">
  <div class="index-total section">
    <div class="section-header clearfix relative">
      <h5 class="pull-left">
        导入服务器信息
      </h5>
      <div id="maindiv-alert"></div>
    </div>
    <div class="section-body change-pw clearfix">
      <form id="import-form" enctype="multipart/form-data" action="<?php echo $this->Url->build(array('controller' => 'ecs','action'=>'importExcel')); ?>" method="post">
        <div class="pull-left col-sm-6">
          <div class="form-group">
            <label for="file">导入Excel文件</label>
            <div class="col-sm-6">
              <input name="file" id="file" type="file">
            </div>
          </div>
        </div>
        <div class="pull-right col-sm-6">
          <div class="form-group">
            <label for=""></label>
            <button type="submit" id="submit" class="btn btn-primary" >导入</button>
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

//$('#import-form').bootstrapValidator({
//  fields : {
//      import: {
//      // group: '.col-sm-6',
//      validators: {
//        notEmpty: {
//          message: '请选择上传的文件'
//        }
//      }
//    }
//  }
//});
</script>