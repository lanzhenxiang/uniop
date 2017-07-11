<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div id="maindiv-alert"></div>
    <form class="form-horizontal" id="service-form" action="<?php echo $this->Url->build(array('controller'=>'ChargeExtend','action'=>'addedit'));?>" method="post">
        <input type="hidden" name="id" id="id"  value="<?php if(isset($data['id'])){echo $data['id']; }?>" />
        <div class="form-group">
            <label for="service_code" class="col-sm-2 control-label">计费对象</label>
            <div class="col-sm-6">
            <select id="charge_object" name="charge_object" class="form-control">
                 <?php foreach($charge_objects as $key =>$name):?>
                    <option value="<?=$key?>" 
                    <?php if(isset($data['charge_object']) && $data['charge_object'] == $key):?>
                    selected="selected"
                    <?php endif;?>
                    >
                        <?=$name?>
                    </option>
                 <?php endforeach;?>
            </select>
             
            </div>
        </div>
        <div class="form-group">
            <label for="service_code" class="col-sm-2 control-label">云厂商</label>
            <div class="col-sm-6">
            <select id="agent_id" name="agent_id" class="form-control">
                <?php foreach($agents as $agent):?>
                 <option value="<?=$agent->id?>" <?php if(isset($data['agent_id']) && $data['agent_id'] == $agent->id){echo 'selected'; }  ?> ><?=$agent['agent_name']?></option>
                <?php endforeach;?>
            </select>
             
            </div>
        </div>
        <div class="form-group">
            <label for="service_code" class="col-sm-2 control-label" >按天计费单价（元/天）</label>
            <div class="col-sm-6">
                <input type="text" min="0" class="form-control" name="daily_price"  id="daily_price" placeholder=""  value="<?php if(isset($data['daily_price'])){ echo $data['daily_price'];}  ?>">
            </div>
         </div>

         <div class="form-group">
            <label for="service_code" class="col-sm-2 control-label">按月计费单价（元/月）</label>
            <div class="col-sm-6">
                <input type="text" min="0" class="form-control" name="monthly_price"  id="monthly_price" placeholder=""  value="<?php if(isset($data['monthly_price'])){ echo $data['monthly_price'];}  ?>">
            </div>

         </div>

         <div class="form-group">
            <label for="service_code" class="col-sm-2 control-label">按年计费单价（元/年）</label>
            <div class="col-sm-6">
                <input type="text" min="0" class="form-control" name="yearly_price"  id="yearly_price" placeholder=""  value="<?php if(isset($data['yearly_price'])){ echo $data['yearly_price'];}  ?>">
            </div>
          
         </div>

        <div class="form-group">
            <label for="min_instance" class="col-sm-2 control-label">计费说明</label>
            <div class="col-sm-6">
                <textarea  class="form-control" name="charge_note"  id="charge_note" placeholder="计费说明"  ><?php if(isset($data['charge_note'])){ echo $data['charge_note'];}  ?></textarea> 
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" id="service_submit" class="btn btn-primary">保存</button>
                <!--<a type="button" href="<?php echo $this->Url->build(array('controller' => 'ChargeExtend','action'=>'index')); ?>" class="btn btn-danger">返回</a>-->
                <a type="button" id="back" class="btn btn-danger" onclick="window.history.go(-1)">返回</a>
            </div>
        </div>

    </form>
</div>
<?=$this->Html->script(['adminjs.js','jquery.cookie.js','validator.bootstrap.js','jquery.uploadify.min.js']); ?>
<?= $this->start('script_last'); ?>
<script type="text/javascript">
$('#charge_object').change(function(){
    var selected = $(this).val();
    if(selected == "disks"){
        $('#daily_price').attr('placeholder','单价为1GB每天价格');
        $('#monthly_price').attr('placeholder','单价为1GB每月价格');
        $('#yearly_price').attr('placeholder','单价为1GB每年价格');
    }else{
        $('#daily_price').attr('placeholder','');
        $('#monthly_price').attr('placeholder','');
        $('#yearly_price').attr('placeholder','');
    }
});

$('#service-form').bootstrapValidator({
    submitButtons: 'button[type="submit"]',
    submitHandler: function(validator, form, submitButton){

        $.post(form.attr('action'), form.serialize(), function(data){
            var data = eval('(' + data + ')');
            if (data.code == 0) {
                tentionHide(data.msg,0);
                if(data.source==1){
                    location.href = '<?php echo $this->Url->build(array('controller'=>'ChargeExtend','action'=>'check'));?>/'+data.id;
                }else {
                    location.href = '<?php echo $this->Url->build(array('controller'=>'ChargeExtend','action'=>'index'));?>/index';
                }
            } else {
                tentionHide(data.msg,1);
            }
        });
    },
    fields : {
        charge_object:{
            group: '.col-sm-6',
            validators: {
                remote: {//ajax验证。server result:{"valid",true or false} 向服务发送当前input name值，获得一个json数据。例表示正确：{"valid",true}  
                    url: '<?php echo $this->Url->build(array('controller'=>'ChargeExtend','action'=>'check'));?>',//验证地址
                    message: '计费对象已存在',//提示消息
                    delay :  1000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
                    type: 'POST',//请求方式
                    data: function(validator) {
                        return {
                            charge_object: $('#charge_object').val(),
                            agent_id: $('#agent_id').val(),
                            id : $('#id').val()
                        };
                    }
                         
                }
            }
        },
        agent_id:{
            group: '.col-sm-6',
            validators: {
                remote: {//ajax验证。server result:{"valid",true or false} 向服务发送当前input name值，获得一个json数据。例表示正确：{"valid",true}  
                    url: '<?php echo $this->Url->build(array('controller'=>'ChargeExtend','action'=>'check'));?>',//验证地址
                    message: '计费对象已存在',//提示消息
                    delay :  1000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
                    type: 'POST',//请求方式
                    data: function(validator) {
                        return {
                            charge_object: $('#charge_object').val(),
                            agent_id: $('#agent_id').val(),
                            id : $('#id').val()
                        };
                    }
                         
                }
            }
        },
        daily_price: {
            group: '.col-sm-6',
            validators: {
                notEmpty: {
                    message: '按天计费单价不能为空'
                },
                numeric:{
                    message: '请输入正确的价格'
                },
                greaterThan:{
                    message:'价格必须大于0'
                }   
            }
        },
        monthly_price: {
            group: '.col-sm-6',
            validators: {
                notEmpty: {
                    message: '按月计费单价不能为空'
                },
                numeric:{
                    message: '请输入正确的价格'
                },
                greaterThan:{
                    message:'价格必须大于0'
                } 
            }
        },
        yearly_price: {
            group: '.col-sm-6',
            validators: {
                notEmpty: {
                    message: '按年计费单价不能为空'
                },
                numeric:{
                    message: '请输入正确的价格'
                },
                greaterThan:{
                    message:'价格必须大于0'
                } 
            }
        }
    }

});
</script>
<?= $this->end() ?>