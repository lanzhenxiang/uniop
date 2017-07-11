<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <form class="form-horizontal">
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">类型名称</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="type_name"  id="type_name" placeholder="类型名称" value="<?php if(isset($data['type_name'])){ echo $data['type_name'];}  ?>">
               <?php if(isset($data)){ ?>
                <input type="hidden" class="form-control" name="type_id"  id="type_id" value="<?php if(isset($data)){ echo $data['type_id']; } ?>">
                <?php } ?>
            </div>
            <label class="control-label text-danger"><i class="icon-asterisk" id="name"></i></label>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">类型说明</label>
            <div class="col-sm-6">
                <textarea class="form-control" name="type_note" rows="4"><?php if(isset($data['type_note'])){ echo $data['type_note'];}  ?></textarea>
            </div>
            <label class="control-label text-danger"><i class="icon-asterisk" id="note"></i></label>
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
                <a type="submit" class="btn btn-danger">返回</a>
            </div>
        </div>
    </form>
</div>
<?= $this->start('script_last'); ?>
<script type="text/javascript">
$("#ds").click(function(){
        var validate =true;
        var name = $('#type_name').val();
        var type_note =$('#type_note').val();
        var sort_order =$('#sort_order').val();
        if(name){
            if(name.length<2 || name.length>16){
                $('#name').html('类型名称应在2到16位之间');
                validate =false;
            }else{
                $('#name').html('');
            }
        }else{
            $('#name').html('请输入类型名称');
            validate =false;
        }

        if(sort_order && !(/^[0-9]+$/.test(sort_order))){
            $('#order').html('请输入正整数');
            validate =false;
        }else{
            $('#order').html('');
        }
        if(validate){
            $.ajax({
                type: 'post',
                url: '<?php echo $this->Url->build(array('controller' => 'BasicDataType','action'=>'addedit')); ?>',
                data: $("form").serialize(),
                success: function(data) {
                    var data = eval('(' + data + ')');
                    if(data.code==0){
                        alert(data.msg);
                        location.href='<?php echo $this->Url->build(array('BasicDataType'=>'Tenants','action'=>'index'));?>';
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

