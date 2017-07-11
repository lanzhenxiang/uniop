<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <form class="form-horizontal">
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">参数类型</label>
            <div class="col-sm-6">
                <select class="form-control" name="type_id" id="type_id">
                    <option value="">请选择</option>
                    <?php foreach ($type as $key => $value) {   ?>
                        <option value="<?php echo $value['type_id'];?>" <?php if(isset($data['type_id']) && $data['type_id']==$value['type_id']){ echo 'selected';}  ?>>
                            <span><?php echo $value['type_name'];?></span>
                        </option>
                    <?php }   ?>
                </select>
            </div>
            <label class="control-label text-danger"><i class="icon-asterisk" id="type"></i></label>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">数据代码</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="data_code"  id="data_code" placeholder="数据代码" value="<?php if(isset($data['data_code'])){ echo $data['data_code'];}  ?>">
               <?php if(isset($data)){ ?>
                <input type="hidden" class="form-control" name="id"  id="id" value="<?php if(isset($data)){ echo $data['id']; } ?>">
                <?php } ?>
            </div>
            <label class="control-label text-danger"><i class="icon-asterisk" id="code"></i></label>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">数据值</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="data_value"  id="data_value" placeholder="数据值" value="<?php if(isset($data['data_value'])){ echo $data['data_value'];}  ?>">
            </div>
            <label class="control-label text-danger"><i class="icon-asterisk" id="value"></i></label>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">数据说明</label>
            <div class="col-sm-6">
                <textarea class="form-control" name="data_note" rows="4"><?php if(isset($data['data_note'])){ echo $data['data_note'];}  ?></textarea>
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">排序</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="sort_order"  id="sort_order" value="<?php if(isset($data['sort_order'])){ echo $data['sort_order'];}  ?>">
            </div>
            <label class="control-label text-danger"><i class="icon-asterisk" id="order"></i></label>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <a type="submit" id="ds" class="btn btn-primary">保存</a>
                <a type="submit" href="<?php echo $this->Url->build(array('controller' => 'BasicDataList','action'=>'index')); ?>" class="btn btn-danger">返回</a>
            </div>
        </div>
    </form>
</div>
<?= $this->start('script_last'); ?>
<script type="text/javascript">
$("#ds").click(function() {
        var validate = true;
        var type_id = $('#type_id').val();
        var code = $('#data_code').val();
        var value = $('#data_value').val();
        var sort_order = $('#sort_order').val();
        if(type_id==''){
            $('#type').html('请选择参数类型');
        }else{
            $('#type').html('');
        }
        if (code) {
            if (code.length < 2 || code.length > 16) {
                $('#code').html('数据代码应在2到16位之间');
                validate = false;
            } else {
                $('#code').html('');
            }
        } else {
            $('#code').html('请输入数据代码');
            validate = false;
        }
        if (value) {
            if (value.length < 2 || value.length > 16) {
                $('#value').html('数据值应在2到16位之间');
                validate = false;
            } else {
                $('#value').html('');
            }
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
                url: '<?php echo $this->Url->build(array('controller' => 'BasicDataList','action'=>'addedit')); ?>',
                data: $("form").serialize(),
                success: function (data) {
                    var data = eval('(' + data + ')');
                    if (data.code == 0) {
                        alert(data.msg);
                        location.href = '<?php echo $this->Url->build(array('controller'=>'BasicDataList','action'=>'index'));?>';
                    } else {
                        alert(data.msg);
                    }
                }
            });
        }
    }
);
</script>
<?= $this->end() ?>

