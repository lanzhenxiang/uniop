<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div id="maindiv-alert"></div>
    <form class="form-horizontal">
        <div class="form-group">
            <label for="owner_type" class="col-sm-2 control-label">参数类型</label>
            <div class="col-sm-6">
                <select class="form-control" name="type" id="owner_type" onchange="onChangeType(this.value)">
                    <option value="">请选择</option>
                    <option value="1" <?php if($data['owner_type']==1){echo "selected";}?>>用户</option>
                    <option value="2" <?php if($data['owner_type']==2){echo "selected";}?>>租户</option>
                    <option value="3" <?php if($data['owner_type']==3){echo "selected";}?>>机房/区域</option>
                </select>
            </div>
            <label class="control-label text-danger"><i class="icon-asterisk" id="type"></i></label>
        </div>
        <div class="form-group">
            <label for="owner_id" class="col-sm-2 control-label">用户名称</label>
            <?php if(isset($data)){ ?>
            <input type="hidden" class="form-control" name="id"  id="id" value="<?php if(isset($data['id'])){ echo $data['id']; } ?>">
            <?php } ?>
            <div class="col-sm-6">
                <select class="form-control" name="owner_id" id="owner_id">
                    <?php if (!empty($data['owner_type'])) {
                        if($data['owner_type']==1){
                            foreach ($query as $key => $value) {
                                echo '<option value="'.$value["id"].'"';
                                if ($data['owner_id']==$value["id"]) {
                                    echo  "selected";
                                }
                                echo '>'.$value["username"].'</option>';
                            }
                        }else if($data['owner_type']==2){
                            foreach ($query as $key => $value) {
                                echo '<option value="'.$value["id"].'"';
                                if ($data['owner_id']==$value["id"]) {
                                    echo  "selected";
                                }
                                echo '>'.$value["name"].'</option>';
                            }
                        }else if($data['owner_type']==3){
                            foreach ($query as $key => $value) {
                                echo '<option value="'.$value["id"].'"';
                                if ($data['owner_id']==$value["id"]) {
                                    echo  "selected";
                                }
                                echo '>'.$value["agent_name"].'</option>';
                            }
                        }else{
                            echo '<option value="">请选择</option>';
                        }
                    } ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">参数代码</label>
            <div class="col-sm-6"  id="select-para">
                <input type="text" class="form-control" name="para_code"  id="para_code" placeholder="参数代码" value="<?php if(isset($data['para_code'])){ echo $data['para_code'];}  ?>">
            </div>
            <label class="control-label text-danger"><i class="icon-asterisk" id="code"></i></label>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">参数值</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="para_value"  id="para_value" placeholder="参数值" value="<?php if(isset($data['para_value'])){ echo $data['para_value'];}  ?>">
            </div>
            <label class="control-label text-danger"><i class="icon-asterisk" id="value"></i></label>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">参数说明</label>
            <div class="col-sm-6">
                <textarea class="form-control" name="para_note" rows="4"><?php if(isset($data['para_note'])){ echo $data['para_note'];}  ?></textarea>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <a type="submit" id="ds" class="btn btn-primary">保存</a>
                <a type="submit" href="<?php echo $this->Url->build(array('controller' => 'UserSetting','action'=>'index')); ?>" class="btn btn-danger">返回</a>
            </div>
        </div>
    </form>
</div>
<?=$this->Html->script(['adminjs.js']); ?>
<?= $this->start('script_last'); ?>
<script type="text/javascript">

    $(function(){
        if ($("#owner_type").val() == 2) {
            var i = '';
            i += '<select class="form-control" name="para_code" id="para_code"> <option <?php if($data["para_code"]=="cpu_bugedt"){echo "selected";}?> value="cpu_bugedt">cpu_bugedt</option> <option <?php if($data["para_code"]=="memory_buget"){echo "selected";}?> value="memory_buget">memory_buget</option> <option <?php if($data["para_code"]=="gpu_bugedt"){echo "selected";}?> value="gpu_bugedt">gpu_bugedt</option> <option <?php if($data["para_code"]=="subnet_bugedt"){echo "selected";}?> value="subnet_bugedt">subnet_bugedt</option> <option <?php if($data["para_code"]=="router_bugedt"){echo "selected";}?> value="router_bugedt">router_bugedt</option> <option <?php if($data["para_code"]=="disks_bugedt"){echo "selected";}?> value="disks_bugedt">disks_bugedt</option> <option <?php if($data["para_code"]=="host_max_disk"){echo "selected";}?> value="host_max_disk">host_max_disk</option></select>';
            $("#select-para").html(i);
        }
    })

$("#ds").click(function() {
    var validate = true;
    var type_id = $('#para_type').val();
    var code = $('#para_code').val();
    var value = $('#para_value').val();
    var para_note = $('#para_note').val();
    var sort_order = $('#sort_order').val();
    if(type_id==''){
        $('#type').html('请选择参数类型');
    }else{
        $('#type').html('');
    }
    if (code) {
            // if (code.length < 2 || code.length > 16) {
            //     $('#code').html('数据代码应在2到16位之间');
            //     validate = false;
            // } else {
                $('#code').html('');
            // }
        } else {
            $('#code').html('请输入数据代码');
            validate = false;
        }
        if (value) {
            // if (value.length < 2 || value.length > 16) {
            //     $('#value').html('数据值应在2到16位之间');
            //     validate = false;
            // } else {
                $('#value').html('');
            // }
        } else {
            $('#value').html('请输入数据值');
            validate = false;
        }

        if (sort_order && !(/^[0-9]+$/.test(sort_order))) {
            $('#order').html('请输入正整数');
            validate = false;
        } else {
            $('#order').html('');
        }
        if (validate) {
            $.ajax({
                type: 'post',
                url: '<?php echo $this->Url->build(array('controller' => 'UserSetting','action'=>'addedit')); ?>',
                data: $("form").serialize(),
                success: function (data) {
                    var data = eval('(' + data + ')');
                    if (data.code == 0) {
                        tentionHide(data.msg, 0);
                        location.href = '<?php echo $this->Url->build(array('controller'=>'UserSetting','action'=>'index'));?>';
                    } else {
                        tentionHide(data.msg, 1);
                    }
                }
            });
        }
    }
    );

function onChangeType(id){
    $.ajax({
        url: '<?php echo $this->Url->build(array('controller' => 'UserSetting','action'=>'check')); ?>/'+id,
        success: function (data) {
            datas = $.parseJSON(data);
            if(datas){
                var type = '';
                var i = '';
                if(datas.type==1){
                    $.each( datas.row, function(i, n){
                        type +='<option value="'+n.id+'">'+n.username+'</option>';
                    });
                    i += '<input type="text" class="form-control" name="para_code"  id="para_code" placeholder="参数代码" value="">';
                    $("#select-para").html(i);
                }else if(datas.type==2){
                    $.each( datas.row, function(i, n){
                        type +='<option value="'+n.id+'">'+n.name+'</option>';
                    });
                    i += '<select class="form-control" name="para_code" id="para_code"><option value="cpu_bugedt">cpu_bugedt</option><option value="memory_buget">memory_buget</option><option value="gpu_bugedt">gpu_bugedt</option><option value="subnet_bugedt">subnet_bugedt</option><option value="router_bugedt">router_bugedt</option><option value="disks_bugedt">disks_bugedt</option><option value="host_max_disk">host_max_disk</option></select>';
                    $("#select-para").html(i);
                }else if(datas.type==3){
                    $.each( datas.row, function(i, n){
                        type +='<option value="'+n.id+'">'+n.display_name+'</option>';
                    });
                    i += '<input type="text" class="form-control" name="para_code"  id="para_code" placeholder="参数代码" value="">';
                    $("#select-para").html(i);
                }else{
                    type +='<option value="">请选择</option>';
                    i += '<input type="text" class="form-control" name="para_code"  id="para_code" placeholder="参数代码" value="">';
                    $("#select-para").html(i);
                }
                $("#owner_id").html(type);
            }
        }
    });
}
</script>
<?= $this->end() ?>

