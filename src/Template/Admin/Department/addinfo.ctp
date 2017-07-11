<style>
    .point-host-startup{
        margin-right: 150px;
    }
</style>
<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div id="maindiv-alert"></div>
    <form class="form-horizontal" id="department-form"
          action="<?php echo $this->Url->build(array('controller' => 'Department','action'=>'postadd')); ?>"
          method="post">
        <div>
            <div class="content-operate clearfix">
                <div class="pull-left">
                    <span style="font-size: 20px;">新建租户基本信息</span>
                </div>

            </div>
            <!--添加内容-->
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="home">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">租户名称</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="name" id="name" placeholder="租户名称" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="type" class="col-sm-2 control-label">租户类型</label>
                        <div class="col-sm-6">
                            <select class="form-control" id="type" name="type" onchange="changeType()">
                                <option value="" data-name="请选择"><sapn>请选择</sapn></option>
                                <option value="normal_inner" data-name="内部租户"><span>内部租户</span></option>
                                <option value="normal_outer" data-name="外部租户"><span>外部租户</span></option>
                                <option value="platform" data-name="平台租户"><span>平台租户</span></option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="type" class="col-sm-2 control-label">阿里云账号</label>
                        <div class="col-sm-6">
                            <select class="form-control" id="aliyun_account" name="aliyun_account">
                                <option value="" data-name="默认账号"><sapn>默认账号</sapn></option>
                                <?php foreach ($other_accounts as $a):?>
                                    <option value="<?= $a['para_value'] ?>" data-name="<?= $a['para_note'] ?>">
                                        <span><?= $a['para_note'] ?></span>
                                    </option>
                                <?php endforeach;?>
                            </select>
                        </div>
                    </div>
                    <!--类型选择提示-->
                    <div class="form-group">
                        <label for="type_note" class="col-sm-2 control-label"></label>
                        <div class="col-sm-6">
                            <label class="control-label text-danger"><i class="icon-exclamation-sign"  id="type_note">保存信息前，请选择租户类型</i></label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">备注</label>
                        <div class="col-sm-6">
                            <textarea type="text" class="form-control" rows="6" name="note" id="note"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" id="submit_add" class="btn btn-primary">保存</button>
                            <!--<a type="button" href="<?php echo $this->Url->build(array('controller' => 'Department','action'=>'index')); ?>" class="btn btn-danger">返回</a>-->
                            <a type="button" id="back" class="btn btn-danger" onclick="window.history.go(-1)">返回</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<?=$this->Html->script(['adminjs.js','validator.bootstrap.js']); ?>
<?= $this->start('script_last'); ?>
<script type="text/javascript">
//    提交表单
    $('#department-form').bootstrapValidator({
        submitButtons: 'button[type="submit"]',
        submitHandler: function (validator, form, submitButton) {
            $.post(form.attr('action'), form.serialize(), function (data) {
                var data = eval('(' + data + ')');
                if (data.code == 0) {
                    tentionHide(data.msg, 0);
                    location.href = "<?php echo $this->Url->build(array('controller'=>'Department','action'=>'index'));?>";
                } else {
                    tentionHide(data.msg, 1);
                }
            });
        },
        fields: {
            name: {
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '租户名不能为空'
                    },
                    stringLength: {
                        min: 2,
                        max: 16,
                        message: '请保持在2-16位'
                    },
                    regexp: {
                        regexp: /^\S*$/,
                        message: '租户名中不能有空格'
                    }
                }
            },
            type: {
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '租户类型不能为空'
                    }
                }
            }
        }
    });
// 选择类型
  function changeType(){
      var type=$('#type').val();
      if(type==''){
          $('#type_note').html('保存信息前，请选择租户类型');
      }else if(type=='normal_inner'){
          $('#type_note').html('内部租户名下的资源为私有性质的，比如租户自建的VPC、子网、主机等');
      }else if(type=='normal_outer'){
          $('#type_note').html('外部租户只能从商城门户上购买资源');
      }else if(type=='platform'){
          $('#type_note').html('平台租户名下的资源或服务，为公共性质的，普通租户可以购买使用，比如共享存储、转码、合成等');
      }
  }

</script>
<?= $this->end() ?>