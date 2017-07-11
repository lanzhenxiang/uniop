<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div id="maindiv-alert"></div>
    <form class="form-horizontal" id="aduser-form" action="<?php echo $this->Url->build(array('controller' => 'GoodsVpc','action'=>'addecspost')); ?>" method="post">
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">配置项类型</label>
            <div class="col-sm-6">
                <select class="form-control" id="type"  onchange="changetype(this,<?php echo $vpc_id; ?>)" name="type">
                    <?php if($_canCreate=="true"||$_canCreate=="desktop"){ ?>
                        <option value="ecs"  <?php if(isset($data)){ if($data['type']== 'ecs'){ echo 'selected';}} ?> >云主机</option>
                        <option value="desktop"  <?php if(isset($data)){ if($data['type']== 'desktop'){ echo 'selected';}} ?> >云桌面</option>
                        <option value="firewall"  <?php if(isset($data)){ if($data['type']== 'firewall'){ echo 'selected';}} ?> >防火墙</option>
                        <option value="elb"  <?php if(isset($data)){ if($data['type']== 'elb'){ echo 'selected';}} ?> >负载均衡</option>
                    <?php }else if($_canCreate=="issa"){ ?>
                        <option value="ecs"  <?php if(isset($data)){ if($data['type']== 'ecs'){ echo 'selected';}} ?> >云主机</option>
                        <option value="firewall"  <?php if(isset($data)){ if($data['type']== 'firewall'){ echo 'selected';}} ?> >防火墙</option>
                        <option value="elb"  <?php if(isset($data)){ if($data['type']== 'elb'){ echo 'selected';}} ?> >负载均衡</option>
                    <?php } ?>

                </select>
                <input type="hidden" value="<?php echo  $vpc_id; ?>" name="vpc_id" id="vpc_id" >
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
        <div class="form-group firewall-hide elb-hide">
            <label for="inputPassword3" class="col-sm-2 control-label">计算能力</label>
            <div class="col-sm-6">
                <select onchange="changeImage(this)"  class="form-control" id="instance_code"  name="instance_code">
                </select>
            </div>
        </div>
        <div class="form-group firewall-hide elb-hide">
            <label for="inputPassword3" class="col-sm-2 control-label">系统镜像</label>
            <div class="col-sm-6">
                <select class="form-control" id="image_code"  name="image_code">
                </select>
            </div>
        </div>
        <div class="form-group firewall-hide">
            <label for="inputPassword3" class="col-sm-2 control-label">所属子网</label>
            <div class="col-sm-6">
                <select class="form-control" id="subnet_id"  name="subnet_id">
                    <option value="">请选择</option>
                    <?php foreach ($subnet_data as  $value) {   ?>
                        <option  value="<?php echo $value->id; ?>" <?php if(isset($data)){ if($data['subnet_id']== $value->id){ echo 'selected';}} ?>  >
                            <?php echo $value->tagname;?>
                        </option>
                    <?php }   ?>
                </select>
            </div>
        </div>
        <div class="form-group firewall-hide elb-hide">
            <label for="inputPassword3" class="col-sm-2 control-label">扩展子网</label>
            <div class="col-sm-6">
                <select class="form-control" id="subnet2_tags"  name="subnet2_tags">
                    <option value="">请选择</option>
                    <?php foreach ($_public_subnet as  $value) {   ?>
                        <option  value="<?php echo $value["name"]; ?>"  <?php if(isset($data)){ if($data['subnet2_tags']== $value["name"]){ echo 'selected';}} ?> >
                            <?php echo $value["name"]?>
                        </option>
                    <?php }   ?>
                </select>
            </div>
        </div>
        <div class="form-group firewall-hide elb-hide">
            <label for="inputEmail3" class="col-sm-2 control-label">数量</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" id="number" name="number" value="<?php if(isset($data)){  echo $data['number'];}else{echo "1";} ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">排序</label>
            <div class="col-sm-6">
                <input type="text" class="form-control"  value="<?php if(isset($data)){ echo $data['sort_order'];}else{echo "100";} ?>" name="sort_order" id="sort_order" >
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" id="ds" class="btn btn-primary">保存</button>
                <a type="button" href="<?php echo $this->Url->build(array('controller' => 'GoodsVpc','action'=>'configure',$vpc_id)); ?>" class="btn btn-danger">返回</a>
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
                if($('#type').val() != 'elb'){
                    if($('#instance_code').val() == ''){
                    tentionHide('请选择一个硬件套餐', 1);
                    isTrue = 1;
                    }
                    if($('#image_code').val() == ''){
                        tentionHide('请选择一个镜像套餐', 1);
                        isTrue = 1;
                    }
                }
                if($('#subnet_id').val() == ''){
                    tentionHide('请选择一个所属子网', 1);
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
                        location.href = '<?php echo $this->Url->build(array('controller'=>'GoodsVpc','action'=>'configure'));?>/'+data.vpc_id;
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
                        regexp: /^([1-9]\d*|0)$/,
                        message: '请输入正整数'
                    }
                }
            }
        }
    });

    $(function(){
        var type = $('#type').val();
        createHtml(type);
        if(type == 'firewall'){
            $(".firewall-hide").hide();
            $("#instance_code,#image_code,#subnet_id").removeAttr("name");
            $("#number").val(1);
        }else if(type == 'elb'){
            $(".elb-hide").hide();
            $("#instance_code,#image_code").removeAttr("name");
            $("#number").val(1);
        }else{
            $(".firewall-hide").show();
            $("#instance_code").attr("name","instance_code");
            $("#image_code").attr("name","image_code");
            $("#subnet_id").attr("name","subnet_id");
        }

    })

    function changetype(e,vpc_id){
        var type = $(e).val();
        if(type == 'firewall'){
            $.ajax({
                type: "post",
                url: "<?= $this -> Url -> build(['controller' => 'GoodsVpc', 'action' => 'firewall']); ?>",
                data: {vpc_id: vpc_id},
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
        }else if(type == 'desktop'){
            $.ajax({
                type: "post",
                url: "<?= $this -> Url -> build(['controller' => 'GoodsVpc', 'action' => 'firewall']); ?>",
                data: {vpc_id: vpc_id},
                success: function (data) {
                    datas = $.parseJSON(data);
                    if (datas.code == 0) {
                        tentionHide('该VPC配置单下没有防火墙,请先创建', 0);
                    }
                }
            });
        }else if(type == 'elb'){
            $.ajax({
                type: "post",
                url: "<?= $this -> Url -> build(['controller' => 'GoodsVpc', 'action' => 'elb']); ?>",
                data: {vpc_id: vpc_id},
                success: function (data) {
                    datas = $.parseJSON(data);
                    if (datas.code == 0) {
                        $('#ds').removeAttr('disabled');
                        $$(".elb-hide").hide();
                        $("#instance_code,#image_code").removeAttr("name");
                        $("#number").val(1);
                    } else {
                        $('#ds').attr('disabled','');
                        $(".firewall-hide").show();
                        $("#instance_code").attr("name","instance_code");
                        $("#image_code").attr("name","image_code");
                        $("#subnet_id").attr("name","subnet_id");
                        tentionHide('该vpc配置单下已经存在负载均衡', 0);
                    }
                }
            });
        }else{
            $(".firewall-hide").show();
            $("#instance_code").attr("name","instance_code");
            $("#image_code").attr("name","image_code");
            $("#subnet_id").attr("name","subnet_id");
        }
        createHtml(type);
    }

function createHtml(type){
    var set_data = <?= json_encode($set_data); ?>;
    var image_data = <?= json_encode($imagelist_data); ?>;
    var desktop_data = <?= json_encode($desktopset_data); ?>;
    if(type=="ecs"){
        var setHtml = "<option value=\"\">请选择</option>";
        $.each(set_data,function(i,v){
            setHtml += "<option type=\""+type+"\" value=\""+v["set_code"]+"\">"+v["set_name"]+"</option>";
        });
        $("#instance_code").html(setHtml);
        var imageHtml = "<option value=\"\">请选择</option>";
        $.each(image_data,function(i,v){
            imageHtml += "<option  value=\""+v["image_code"]+"\">"+v["image_name"]+"</option>";
        });
        $("#image_code").html(imageHtml);
    }else if(type=="desktop"){
        var setHtml = "<option value=\"\">请选择</option>";
        var imageHtml = "<option value=\"\">请选择</option>";
        $.each(desktop_data,function(i,v){
            setHtml += "<option type=\""+type+"\"  value=\""+v["set_code"]+"\">"+v["set_name"]+"</option>"
        });
        $("#instance_code").html(setHtml);
        $("#image_code").html(imageHtml);
    }else{

    }
    var data  = "<?php if(!empty($data)){echo $data["instance_code"];}else{echo "";} ?>";
    if(data!=""){
        $("#instance_code").val(data);
        changeImage($("#instance_code"));
    }
}
function changeImage (bbb){
    var desktop_data = <?= json_encode($desktopset_data); ?>;
    console.log($(bbb).children('option:selected').attr("type"));
    var select = $(bbb).children('option:selected');
    var type = select.attr("type");
    if(type=="desktop"){
        console.log(select.val());
        var html = "";
        $.each(desktop_data,function(i,v){
            if(v["set_code"]==select.val()){
                // console.log(v["set_code"]);
                html += "<option  value=\""+v["image_code"]+"\">"+v["image_name"]+"</option>";
            }
            // html += "<option  value=\""+v["image_code"]+"\">"+v["image_name"]+"</option>";
        });
        $("#image_code").html(html);
    }
    var data  = "<?php if(!empty($data)){echo $data["image_code"];}else{echo "";} ?>";
    if(data!=""){
        $("#image_code").val(data);
    }
}


</script>
<?= $this->end() ?>

