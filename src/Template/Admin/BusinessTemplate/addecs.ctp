<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div id="maindiv-alert"></div>
    <form class="form-horizontal" id="aduser-form" action="<?php echo $this->Url->build(array('controller' => 'BusinessTemplate','action'=>'addecspost')); ?>" method="post">
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">配置项类型</label>
            <div class="col-sm-6">
                <select class="form-control" id="type"  onchange="changetype(this,<?php echo $biz_tid; ?>)" name="type">
                    <option value="">请选择</option>
                    <option value="ecs"  <?php if(isset($data)){ if($data['type']== 'ecs'){ echo 'selected';}} ?> >ECS</option>
<!--                     <option value="desktop"  <?php if(isset($data)){ if($data['type']== 'desktop'){ echo 'selected';}} ?> >云桌面</option> -->
                </select>
                <input type="hidden" value="<?php echo  $biz_tid; ?>" name="biz_tid" id="biz_tid" >
                <?php if(isset($data)){ ?>
                <input type="hidden" value="<?php echo $data['id']; ?>" name="id" id="id" >
                <?php } ?>
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">配置项名称</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" value="<?php if(isset($data)){  echo $data['tagname'];} ?>" name="tagname">
            </div>
        </div>
        <div class="form-group firewall-hide">
            <label for="instance_code" class="col-sm-2 control-label">硬件套餐</label>
            <div class="col-sm-6">
                <select class="form-control" id="instance_code"  name="instance_code">
                    <option value="">请选择</option>
                    <?php foreach ($set_data as $key => $value) {   ?>
                        <option  value="<?php echo $value->set_code; ?>"  <?php if(isset($data)){ if($data['instance_code']== $value->set_code){ echo 'selected';}} ?> >
                            <?php echo $value->set_name;?>
                        </option>
                    <?php }   ?>
                </select>
            </div>
        </div>
        <div class="form-group firewall-hide">
            <label for="image_code" class="col-sm-2 control-label">系统镜像</label>
            <div class="col-sm-6">
                <select class="form-control" id="image_code"  name="image_code">
                    <option value="">请选择</option>
                    <?php foreach ($imagelist_data as  $value) {   ?>
                        <option  value="<?php echo $value->image_code; ?>" <?php if(isset($data)){ if($data['image_code']== $value->image_code){ echo 'selected';}} ?>  >
                            <?php echo $value->image_name;?>
                        </option>
                    <?php }   ?>
                </select>
            </div>
        </div>
        <div class="form-group firewall-hide">
            <label for="number" class="col-sm-2 control-label">数量</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" id="number" name="number" value="<?php if(isset($data)){  echo $data['number'];} ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="sort_order" class="col-sm-2 control-label">排序</label>
            <div class="col-sm-6">
                <input type="text" class="form-control"  value="<?php if(isset($data)){ echo $data['sort_order'];} ?>" name="sort_order" id="sort_order" >
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" id="ds" class="btn btn-primary">保存</button>
                <a type="button" href="<?php echo $this->Url->build(array('controller' => 'BusinessTemplate','action'=>'configure',$biz_tid)); ?>" class="btn btn-danger">返回</a>
            </div>
        </div>
    </form>
</div>

<?=$this->Html->script(['adminjs.js','validator.bootstrap.js']); ?>
<?= $this->start('script_last'); ?>
<script type="text/javascript">

    $('#aduser-form').bootstrapValidator({
        submitButtons: 'button[type="submit"]',
        submitHandler: function(validator, form, submitButton){

            var isTrue = 0;
            if($('#type').val() != 'firewall'){
                //console.log($('#instance_code').val());
                if($('#instance_code').val() == ''){
                    tentionHide('请选择一个硬件套餐', 1);
                    isTrue = 1;
                }
                if($('#image_code').val() == ''){
                    tentionHide('请选择一个系统镜像', 1);
                    isTrue = 1;
                }
            }
            if(isTrue == 1){
                $('#aduser-form').bootstrapValidator('resetForm');
            }
            if(isTrue == 0) {
                $.post(form.attr('action'), form.serialize(), function (data) {
                    var data = eval('(' + data + ')');
                    if (data.code == 0) {
                        tentionHide(data.msg, 0);
                        location.href = '<?php echo $this->Url->build(array('controller'=>'BusinessTemplate','action'=>'configure'));?>/'+data.biz_tid;
                    } else {
                        tentionHide(data.msg, 1);
                    }
                });
            }
        },
        fields : {
            tagname: {
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '配置单名称不能为空'
                    }
                }
            },
            type: {
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '请选择一个配置项类型'
                    }
                }
            },
            number: {
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '请输入数量'
                    },
                    regexp: {
                        regexp: /^([1-9]\d*)$/,
                        message: '请输入正整数'
                    }
                }
            }
        }
    });

    $(function(){
        var type = $('#type').val();
        if(type == 'firewall'){
            $(".firewall-hide").hide();
            $("#instance_code,#image_code,#subnet_id").removeAttr("name");
            $("#number").val(1);
        }else{
            $(".firewall-hide").show();
            $("#instance_code").attr("name","instance_code");
            $("#image_code").attr("name","image_code");
            $("#subnet_id").attr("name","subnet_id");
        }

    })

    function changetype(e,biz_tid){
        var type = $(e).val();
        if(type == 'firewall'){
            $.ajax({
                type: "post",
                url: "<?= $this -> Url -> build(['controller' => 'BusinessTemplate', 'action' => 'firewall']); ?>",
                data: {biz_tid: biz_tid},
                success: function (data) {
                    datas = $.parseJSON(data);
                    if (datas.code == 0) {
                        $('#ds').removeAttr('disabled');
                        $(".firewall-hide").hide();
                        $("#instance_code,#image_code,#subnet_id").removeAttr("name");
                        $("#number").val(1);
                    } else {
                        $('#ds').attr('disabled','');
                        $(".firewall-hide").show();
                        $("#instance_code").attr("name","instance_code");
                        $("#image_code").attr("name","image_code");
                        $("#subnet_id").attr("name","subnet_id");
                        tentionHide('该vpc配置单下已经存在防火墙', 0);
                    }
                }
            });

        }else{
            $(".firewall-hide").show();
            $("#instance_code").attr("name","instance_code");
            $("#image_code").attr("name","image_code");
            $("#subnet_id").attr("name","subnet_id");
        }

    }


</script>
<?= $this->end() ?>

