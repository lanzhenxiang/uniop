<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <form class="form-horizontal">
        <div class="form-group">
            <label for="value" class="col-sm-2 control-label">属性详情值</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="value"  id="value" placeholder="属性详情值" value="<?php if(isset($department_data['value'])){ echo $department_data['value'];}  ?>">
                <?php if(isset($department_data)){ ?>
                <input type="hidden" class="form-control" name="id"  id="id" value="<?php if(isset($department_data)){ echo $department_data['id']; } ?>">
                <?php } ?>
            </div>
            <label class="control-label text-danger"><i class="icon-asterisk" id="attvalue"></i></label>
        </div>
        <div class="form-group">
            <label for="label" class="col-sm-2 control-label">标签</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="label"  id="label" placeholder="标签"  value="<?php if(isset($department_data['label'])){ echo $department_data['label'];}  ?>">
            </div>
            <label class="control-label text-danger"><i class="icon-asterisk" id="biaoqian"></i></label>
        </div>
        <div class="form-group">
            <label for="category_id" class="col-sm-2 control-label">所属分类</label>
            <div class="col-sm-6">
                <select class="form-control" id="category_id" name="category_id" onchange="onChangeGoodsType(this.value,0)">
                    <option value=""><span>请选择分类</span></option>
                    <?php foreach ($query as $key => $value) {   ?>
                    <option value="<?php echo $key;?>" <?php if(isset($department_data) && $catgory['goods_category_id']==$key){ echo 'selected';}  ?>>
                        <span><?php echo $value;?></span>
                    </option>
                    <?php }   ?>
                </select>
            </div>
            <label class="control-label text-danger"><i class="icon-asterisk" id="fenlei"></i></label>
        </div>
        <div class="form-group" id="attrGroups" <?php if(!isset($department_data['goods_cat_attrs_cat_id'])){ echo 'style="display: none;"'; } ?>>
            <?php if(isset($department_data['goods_cat_attrs_cat_id'])){   ?>
            <label for="goods_cat_attrs_cat_id" class="col-sm-2 control-label">所属属性</label>
            <div class="col-sm-6">
                <select class="form-control" id="goods_cat_attrs_cat_id" name="goods_cat_attrs_cat_id">
                    <?php foreach ($catattrs as $key => $value) {   ?>
                    <option value="<?php echo $value->id;?>" <?php if(isset($department_data['goods_cat_attrs_cat_id']) && $department_data['goods_cat_attrs_cat_id']==$value->id){ echo 'selected';}  ?>>
                        <span><?php echo $value->name;?></span>
                    </option>
                    <?php }   ?>
                </select> 
            </div>
            <?php }   ?>
        </div>
        <div class="form-group">
            <label for="sort_order" class="col-sm-2 control-label">排序</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="sort_order"  id="sort_order" placeholder="排序"  value="<?php if(isset($department_data['sort_order'])){ echo $department_data['sort_order'];}  ?>">
            </div>
            <label class="control-label text-danger"><i class="icon-asterisk" id="paixu"></i></label>
        </div>
        <div class="form-group">
            <label for="price" class="col-sm-2 control-label">价格</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="price"  id="price" placeholder="价格"  value="<?php if(isset($department_data['price'])){ echo $department_data['price'];}  ?>">
            </div>
            <label class="control-label text-danger"><i class="icon-asterisk" id="jiage"></i></label>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <a type="submit" id="ds" class="btn btn-primary">保存</a>
                 <a type="submit" href="<?php echo $this->Url->build(array('controller' => 'GoodsCatAttrs','action'=>'index')); ?>" class="btn btn-danger">返回</a>
            </div>
        </div>
    </form>
</div>
<?= $this->start('script_last'); ?>
<script type="text/javascript">
    $("#ds").click(function(){

        $('#attvalue').html('');
        $('#biaoqian').html('');
        $('#fenlei').html('');
        $('#jiage').html('');
        $('#paixu').html('');
        var validate=true;
        var value=$("#value").val();
        var label=$("#label").val();
        var icon=$("#icon").val();
        var description=$("#description").val();
        var sort=$("#sort_order").val();
        var price=$('#price').val();
        var category=$('#category_id').val();
        if(value.length > 17){
            $('#attvalue').html('用户名应小于16位');
            var validate =false;
        }
        /*if(label.length > 17){
            $('#biaoqian').html('标签应小于16位');
            var validate =false;
        }*/
        if(sort> 256){
            $('#paixu').html('请输入小于255的数');
            var validate =false;
        }
        if(!(/^[+]?[1-9]+\d|0*$/i.test(sort)))
        {
            $('#paixu').html('请输入正整数');
            var validate =false;
        }
        if(!(/^\d+(\.\d*[1-9]{1})?$/i.test(price)))
        {
            $('#jiage').html('请输入正确的价格');
            var validate =false;
        }
        if(!value){
            $('#attvalue').html('请输入详细值');
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
        if(!category){
            $('#fenlei').html('请选择分类');
            var validate =false;
        }
        if(!price){
            $('#jiage').html('请输入价格');
            var validate =false;
        }
        if(validate){
            $.ajax({
                type: 'post',
                url: '<?php echo $this->Url->build(array('controller' => 'GoodsCatAttrs','action'=>'addedit')); ?>',
                data: $("form").serialize(),
                success: function(data) {
                    var data = eval('(' + data + ')');
                    if(data.code==0){
                        alert(data.msg);
                        location.href='<?php echo $this->Url->build(array('controller'=>'GoodsCatAttrs','action'=>'index'));?>';
                    }else{
                        alert(data.msg);
                    }
                }
            });
        }
    }
    );


    function onChangeGoodsType(id,attr_group){
        if(id!=0){
            $.ajax({
                type: "POST",
                url: '<?php echo $this->Url->build(array('controller'=>'GoodsCatAttrs','action'=>'checkcat'));?>',
                data: {'id': id},
                success: function (data) {
                    datas = $.parseJSON(data);
                    console.log(datas);
                    if (datas==1) {
                        attrs='';
                        $('#attrGroups').html(attrs);
                        $('#attrGroups').val(attrGroups);
                        $('#attrGroups').hide();
                    }
                    if(datas!=1){
                        var attrs = '<label for="goods_cat_attrs_cat_id" class="col-sm-2 control-label">所属属性</label><div class="col-sm-6"><select class="form-control" name="goods_cat_attrs_cat_id">';
                        for(i=0;i<datas.id.length;i++){
                            attrs +="<option value='"+datas.id[i]+"'><span>"+datas.name[i]+"</span></option>";
                        }
                        attrs+= '</select></div>';
                        $('#attrGroups').html(attrs);
                        $('#attrGroups').val(attrGroups);
                        $('#attrGroups').show();
                    }
                }
            });
        }else{
            $('#attrGroups').hide();
        }
    }
</script>
<?= $this->end() ?>