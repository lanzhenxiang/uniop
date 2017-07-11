<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <form class="form-horizontal" id="goods-spec-form" action="<?php echo $this->Url->build(array('controller' => 'GoodsSpec','action'=>'addedit')); ?>" method="post">
        <div class="form-group">
            <label for="spec_name" class="col-sm-2 control-label">规格名称</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="spec_name"  id="spec_name" placeholder="规格名称" value="<?php if(isset($department_data['spec_name'])){ echo $department_data['spec_name'];}  ?>">
                <?php if(isset($department_data)){ ?>
                <input type="hidden" class="form-control" name="id"  id="id" value="<?php if(isset($department_data)){ echo $department_data['id']; } ?>">
                <?php } ?>
            </div>
            <label id="mingcheng" style="color:#ac2925;margin-top: 5px;">* 必填项</label>
        </div>
        <div class="form-group">
            <label for="goods_id" class="col-sm-2 control-label">所属商品</label>
            <div class="col-sm-6">
                <select class="form-control" id="goods_id" name="goods_id">
                    <option value=""><span>请选择商品</span></option>
                    <?php foreach ($query as $key => $value) {   ?>
                    <option value="<?php echo $value->id;?>" <?php if(isset($department_data) && $department_data['goods_id']==$value->id){ echo 'selected';}  ?>>
                        <span><?php echo $value->name;?></span>
                    </option>
                    <?php }   ?>
                </select>
            </div>
            <label id="shangping" style="color:#ac2925;margin-top: 5px;">* 必填项</label>
        </div>
        <div class="form-group">
            <label for="spec_code" class="col-sm-2 control-label">规格代码</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="spec_code"  id="spec_code" placeholder="规格代码"  value="<?php if(isset($department_data['spec_code'])){ echo $department_data['spec_code'];}  ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="spec_value" class="col-sm-2 control-label">规格描述</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="spec_value"  id="spec_value" placeholder="规格描述"  value="<?php if(isset($department_data['spec_value'])){ echo $department_data['spec_value'];}  ?>">
            </div>
            <label id="miaoshu" style="color:#ac2925;margin-top: 5px;">* 必填项</label>
        </div>
        <div class="form-group">
            <label for="sort_order" class="col-sm-2 control-label">排序</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="sort_order"  id="sort_order" placeholder="排序"  value="<?php if(isset($department_data['sort_order'])){ echo $department_data['sort_order'];}  ?>">
            </div>
            <label id="paixu" style="color:#ac2925;margin-top: 5px;">* 必填项</label>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">展示</label>
            <div class="col-sm-6">
                <label class="radio-inline">
                    <input type="radio" name="is_display" id="is_display"  value="1" <?php if(isset($department_data) && $department_data['is_display']==1){echo 'checked';}  ?>> 展示
                </label>
                <label class="radio-inline">
                    <input type="radio" name="is_display" id="is_display" value="0" <?php if(isset($department_data) && $department_data['is_display']==0){echo 'checked';}  ?>> 不展示
                </label>
            </div>
            <label id="zhanshi" style="color:#ac2925;margin-top: 5px;">* 必填项</label>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">创建时是否必须</label>
            <div class="col-sm-6">
                <label class="radio-inline">
                    <input type="radio" name="is_need" id="is_need" value="1" <?php if(isset($department_data) && $department_data['is_need']==1){echo 'checked';}  ?>> 是
                </label>
                <label class="radio-inline">
                    <input type="radio" name="is_need" id="is_need" value="0" <?php if(isset($department_data) && $department_data['is_need']==0){echo 'checked';}  ?>> 否
                </label>
            </div>
            <label id="chuangjian" style="color:#ac2925;margin-top: 5px;">* 必填项</label>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" id="ds" class="btn btn-primary">保存</button>
                <a type="button" href="<?php echo $this->Url->build(array('controller' => 'GoodsSpec','action'=>'index'));if(isset($department_data)){ echo '/index/'.$department_data['goods_id'];}if(isset($id)){ echo '/index/'.$id;}?>" class="btn btn-danger">返回</a>
            </div>
        </div>
    </form>
</div>
<?=$this->Html->script(['adminjs.js','validator.bootstrap.js']); ?>
<script type="text/javascript">
    $("#ds").click(function(){

        if(!(/^[1-9]\d?$/i.test(sort)))
        {
            $('#paixu').html('请输入正整数');
            var validate =false;
        }
        if(sort> 256){
            $('#paixu').html('请输入小于255的数');
            var validate =false;
        }
        if(!name){
            $('#mingcheng').html('请输入名称');
            var validate =false;
        }
        if(!goodsId){
            $('#shangping').html('请选择商品');
            var validate =false;
        }
        if(!sort){
            $('#paixu').html('请输入排序');
            var validate =false;
        }
        // if(!code){
        //     $('#daima').html('请输入规格代码');
        //     var validate =false;
        // }
        if(!value){
            $('#miaoshu').html('请输入规格的值');
            var validate =false;
        }
        if(!display){
            $('#zhanshi').html('请选择是否展示');
            var validate =false;
        }
        if(!need){
            $('#chuangjian').html('请选择创建时是否必须');
            var validate =false;
        }
        if(validate){
            $.ajax({
                type: 'post',
                url: '<?php echo $this->Url->build(array('controller' => 'GoodsSpec','action'=>'addedit')); ?>',
                data: $("form").serialize(),
                success: function(data) {
                    var data = eval('(' + data + ')');
                    if(data.code==0){
                        alert(data.msg);
                        location.href='<?php echo $this->Url->build(array('controller'=>'GoodsSpec','action'=>'index'));?>';
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