<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <form class="form-horizontal">
        <div class="form-group">
            <label for="name" class="col-sm-2 control-label">属性名称</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="name"  id="name" placeholder="属性名称" value="<?php if(isset($department_data['name'])){ echo $department_data['name'];}  ?>">
                <?php if(isset($department_data)){ ?>
                <input type="hidden" class="form-control" name="id"  id="id" value="<?php if(isset($department_data)){ echo $department_data['id']; } ?>">
                <?php } ?>
            </div>
            <label class="control-label text-danger"><i class="icon-asterisk" id="attname"></i></label>
        </div>
        <div class="form-group">
            <label for="goods_category_id" class="col-sm-2 control-label">所属分类</label>
            <div class="col-sm-6">
                <select class="form-control" name="goods_category_id">
                    <?php foreach ($query as $key => $value) {   ?>
                    <option value="<?php echo $key;?>" <?php if(isset($department_data['goods_category_id']) && $department_data['goods_category_id']==$key){ echo 'selected';}  ?>>
                        <span><?php echo $value;?></span>
                    </option>
                    <?php }   ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="label" class="col-sm-2 control-label">标签</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="label"  id="label" placeholder="标签"  value="<?php if(isset($department_data['label'])){ echo $department_data['label'];}  ?>">
            </div>
            <label class="control-label text-danger"><i class="icon-asterisk" id="biaoqian"></i></label>
        </div>
        <div class="form-group">
            <label for="sort_order" class="col-sm-2 control-label">排序</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="sort_order"  id="sort_order" placeholder="排序"  value="<?php if(isset($department_data['sort_order'])){ echo $department_data['sort_order'];}  ?>">
            </div>
            <label class="control-label text-danger"><i class="icon-asterisk" id="paixu"></i></label>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <a type="submit" id="ds" class="btn btn-primary">保存</a>
                 <a type="submit" href="<?php echo $this->Url->build(array('controller' => 'GoodsCatAttrsCat','action'=>'index')); ?>" class="btn btn-danger">返回</a>
            </div>
        </div>
    </form>
</div>
<?= $this->start('script_last'); ?>
<script type="text/javascript">
    $("#ds").click(function(){
        $('#attname').html('');
        $('#biaoqian').html('');
        $('#paixu').html('');
        var validate=true;
        var name=$("#name").val();
        var label=$("#label").val();
        var sort=$("#sort_order").val();
        if(name.length > 17){
            $('#attname').html('用户名应小于16位');
            var validate =false;
        }
        if(sort> 256){
            $('#paixu').html('请输入小于255的数');
            var validate =false;
        }
        if(!(/^[+]?[1-9]+\d*$/i.test(sort)))
        {
            $('#paixu').html('请输入正整数');
            var validate =false;
        }
        if(!name){
            $('#attname').html('请输入用属性名称');
            var validate =false;
        }
        if(!label){
            $('#biaoqian').html('请输入标签');
            var validate =false;
        }
        if(!sort){
            $('#paixu').html('请输入排序');
            var validate =false;
        }
        if(validate){

            $.ajax({
                type: 'post',
                url: '<?php echo $this->Url->build(array('controller' => 'GoodsCatAttrsCat','action'=>'addedit')); ?>',
                data: $("form").serialize(),
                success: function(data) {
                    var data = eval('(' + data + ')');
                    if(data.code==0){
                        alert(data.msg);
                        location.href='<?php echo $this->Url->build(array('controller'=>'GoodsCatAttrsCat','action'=>'index'));?>';
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

