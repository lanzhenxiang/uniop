<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <form class="form-horizontal">
        <div class="form-group">
            <label for="set_name" class="col-sm-2 control-label">套餐名称</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="set_name"  id="set_name" placeholder="套餐名称" value="<?php if(isset($department_data['set_name'])){ echo $department_data['set_name'];}  ?>">
                <?php if(isset($department_data)){ ?>
                <input type="hidden" class="form-control" name="id"  id="id" value="<?php if(isset($department_data)){ echo $department_data['id']; } ?>">
                <?php } ?>
            </div>
            <label class="control-label text-danger"><i class="icon-asterisk" id="name"></i></label>
        </div>
        <div class="form-group">
            <label for="agent_id" class="col-sm-2 control-label">供应商</label>
            <div class="col-sm-6">
                <select class="form-control" id="agent_id" name="agent_id">
                    <option value=""><span>请选择供应商</span></option>
                    <?php foreach ($query as $key => $value) {   ?>
                    <option value="<?php echo $value->id;?>" <?php if(isset($department_data) && $department_data['agent_id']==$value->id){ echo 'selected';}  ?>>
                        <span><?php echo $value->agent_name;?></span>
                    </option>
                    <?php }   ?>
                </select>
            </div>
            <label class="control-label text-danger"><i class="icon-asterisk" id="gys"></i></label>
        </div>
        <div class="form-group">
            <label for="set_type_code" class="col-sm-2 control-label">套餐代码</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="set_type_code"  id="set_type_code" placeholder="套餐代码"  value="<?php if(isset($department_data['set_type_code'])){ echo $department_data['set_type_code'];}  ?>">
            </div>
            <label class="control-label text-danger"><i class="icon-asterisk" id="daima"></i></label>
        </div>
        
        <div class="form-group">
            <label for="cpu_number" class="col-sm-2 control-label">cpu数量</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="cpu_number"  id="cpu_number" placeholder="cpu数量"  value="<?php if(isset($department_data['cpu_number'])){ echo $department_data['cpu_number'];}  ?>">
            </div>
            <label class="control-label text-danger"><i class="icon-asterisk" id="cpu"></i></label>
        </div>
        <div class="form-group">
            <label for="memory_gb" class="col-sm-2 control-label">内存数量</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="memory_gb"  id="memory_gb" placeholder="内存数量"  value="<?php if(isset($department_data['memory_gb'])){ echo $department_data['memory_gb'];}  ?>">
            </div>
            <label class="control-label text-danger"><i class="icon-asterisk" id="neicun"></i></label>
        </div>
        <div class="form-group">
            <label for="set_type" class="col-sm-2 control-label">机器类型</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="set_type"  id="set_type" placeholder="机器类型"  value="<?php if(isset($department_data['set_type'])){ echo $department_data['set_type'];}  ?>">
            </div>
            <label class="control-label text-danger"><i class="icon-asterisk" id="leixin"></i></label>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <a type="submit" id="ds" class="btn btn-primary">保存</a>
                <a type="submit" class="btn btn-danger">返回</a>
            </div>
        </div>
    </form>
</div>
<?= $this->start('script_last'); ?>
<script type="text/javascript">
    $("#ds").click(function(){
        $('#name').html('');
        $('#gys').html('');
        $('#daima').html('');
        $('#cpu').html('');
        $('#neicun').html('');
        $('#leixin').html('');
        var validate=true;
        var name=$("#set_name").val();
        var agent=$("#agent_id").val();
        var code=$("#set_type_code").val();
        var cpu=$("#cpu_number").val();
        var memory=$("#memory_gb").val();
        var type=$("#set_type").val();
        // if(name.length > 17){
        //     $('#name').html('用户名应小于16位');
        //     var validate =false;
        // }
        if(!(/^[1-9]\d?$/i.test(cpu)))
        {
            $('#cpu').html('请输入正整数');
            var validate =false;
        }
        if(!(/^[1-9]\d?$/i.test(memory)))
        {
            $('#neicun').html('请输入正整数');
            var validate =false;
        }
        if(!name){
            $('#name').html('请输入套餐名称');
            var validate =false;
        }
        if(!code){
            $('#daima').html('请输入套餐代码');
            var validate =false;
        }
        if(!agent){
            $('#gys').html('请选择供应商');
            var validate =false;
        }
        if(!cpu){
            $('#cpu').html('请输入cpu数量');
            var validate =false;
        }
        if(!memory){
            $('#neicun').html('请输入内存大小');
            var validate =false;
        }
        if(!type){
            $('#leixin').html('请输入机器类型');
            var validate =false;
        }
        if(validate){
            $.ajax({
                type: 'post',
                url: '<?php echo $this->Url->build(array('controller' => 'AgentSet','action'=>'addedit')); ?>',
                data: $("form").serialize(),
                success: function(data) {
                    var data = eval('(' + data + ')');
                    if(data.code==0){
                        alert(data.msg);
                        location.href='<?php echo $this->Url->build(array('controller'=>'AgentSet','action'=>'index'));?>';
                    }else{
                        alert(data.msg);
                    }
                }
            });
        }
    }
    );
</script>
<?= $this->end() ?>