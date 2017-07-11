<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div id="maindiv-alert"></div>
    <form class="form-horizontal" id="imagelist-form" action="<?php echo $this->Url->build(array('controller' => 'Imagelist','action'=>'addedit')); ?>" method="post">
        <div class="form-group">
            <label for="image_name" class="col-sm-2 control-label">镜像名称</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="image_name"  id="image_name" placeholder="镜像名称" value="<?php if(isset($department_data['image_name'])){ echo $department_data['image_name'];}  ?>">
                <?php if(isset($department_data)){ ?>
                <input type="hidden" class="form-control" name="id"  id="id" value="<?php if(isset($department_data)){ echo $department_data['id']; } ?>">
                <?php } ?>
            </div>

        </div>
        <div class="form-group">
            <label for="image_code" class="col-sm-2 control-label">镜像CODE</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="image_code"  id="image_code" placeholder="镜像CODE"  value="<?php if(isset($department_data['image_code'])){ echo $department_data['image_code'];}  ?>">
                <p><i class="icon-info-sign"></i>&nbsp;请按CMDB规划填写</p>
            </div>

        </div>
        <div class="form-group">
            <label for="os_family" class="col-sm-2 control-label">操作系统</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="os_family"  id="os_family" placeholder="操作系统"  value="<?php if(isset($department_data['os_family'])){ echo $department_data['os_family'];}  ?>">
            </div>

        </div>
        <div class="form-group">
            <label for="image_type" class="col-sm-2 control-label">镜像适用范围</label>
            <div class="col-sm-6">
                <select class="form-control" id="image_type" name="image_type">
                    <option value="1"<?php if(isset($department_data) && $department_data['image_type']==1){ echo 'selected';}  ?>><span>云主机</span></option>
                    <option value="2"<?php if(isset($department_data) && $department_data['image_type']==2){ echo 'selected';}  ?>><span>云桌面</span></option>
                    <option value="4"<?php if(isset($department_data) && $department_data['image_type']==4){ echo 'selected';}  ?>><span>云主机与云桌面</span></option>
                </select>
            </div>
        </div>
        <!-- <div class="form-group">
            <label for="ostype" class="col-sm-2 control-label">操作系统类型</label>
            <div class="col-sm-6">
                <select class="form-control" id="ostype" name="ostype">
                    <option value=""><span>请选择系统类型</span></option>
                    <option value="1"<?php if(isset($department_data) && $department_data['ostype']==1){ echo 'selected';}  ?>><span>Linux</span></option>
                    <option value="2"<?php if(isset($department_data) && $department_data['ostype']==2){ echo 'selected';}  ?>><span>Windows Server</span></option>
                    <option value="3"<?php if(isset($department_data) && $department_data['ostype']==3){ echo 'selected';}  ?>><span>Windows Desktop</span></option>
                    
                </select>
            </div>
            <label class="control-label text-danger"><i class="icon-asterisk" id="czxt"></i></label>
        </div> -->
        <div class="form-group ecs-form">
            <label for="plat_form" class="col-sm-2 control-label">业务分类</label>
            <div class="col-sm-6">
                <!--<input type="text" class="form-control" name="plat_form"  id="plat_form" placeholder="业务分类"  value="<?php if(isset($department_data['plat_form'])){ echo $department_data['plat_form'];}  ?>">-->
                <select class="form-control" id="plat_form" name="plat_form">
                    <option value="Windows"<?php if(isset($department_data) && $department_data['plat_form']=='Windows'){ echo 'selected';}  ?>><span>Windows</span></option>
                    <option value="Linux"<?php if(isset($department_data) && $department_data['plat_form']=='Linux'){ echo 'selected';}  ?>><span>Linux</span></option>
                </select>
            </div>

        </div>
        <div class="form-group ecs-form">
            <label for="sort_order" class="col-sm-2 control-label">排序</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="sort_order"  id="sort_order" placeholder="排序"  value="<?php if(isset($department_data['sort_order'])){ echo $department_data['sort_order'];}  ?>">
            </div>

        </div>
        <div class="form-group ecs-form">
            <label for="price_day" class="col-sm-2 control-label">按日计费单价（元/日）</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="price_day"  id="price_day" placeholder="按日计费单价（元/日）"  value="<?php if(isset($department_data['price_day'])){ echo $department_data['price_day'];}  ?>">
            </div>
        </div>
        <div class="form-group ecs-form">
            <label for="price_month" class="col-sm-2 control-label">按月计费单价（元/月）</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="price_month"  id="price_month" placeholder="按月计费单价（元/月）"  value="<?php if(isset($department_data['price_month'])){ echo $department_data['price_month'];}  ?>">
            </div>
        </div>
        <div class="form-group ecs-form">
            <label for="price_month" class="col-sm-2 control-label">按年计费单价（元/年）</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="price_year"  id="price_year" placeholder="按年计费单价（元/年）"  value="<?php if(isset($department_data['price_year'])){ echo $department_data['price_year'];}  ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="image_note" class="col-sm-2 control-label">备注</label>
            <div class="col-sm-6">
                <textarea type="text" class="form-control" rows="4" name="image_note" id="image_note" placeholder="备注 "><?php if(isset($department_data)){ echo $department_data['image_note'];}?></textarea>
            </div>
            <!-- <label id="miaoshu" style="color:#ac2925;margin-top: 5px;">* 必填项</label> -->
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" id="ds" class="btn btn-primary">保存</button>
                <!--<a type="button" href="<?php echo $this->Url->build(array('controller' => 'Imagelist','action'=>'index')); ?>" class="btn btn-danger">返回</a>-->
                <a type="button" onclick="window.history.go(-1)" class="btn btn-danger">返回</a>
            </div>
        </div>
    </form>
</div>


<?=$this->Html->script(['adminjs.js','validator.bootstrap.js']); ?>
<?= $this->start('script_last'); ?>
<script type="text/javascript">

    function showPrice(){
        var image_type = $('#image_type').val();
        if(image_type == "1"||image_type=='4'){
            $('.ecs-form').show();
        }else{
            $('.ecs-form').hide();
            $('#price_day').val('');
            $('#price_month').val('');
            $('#price_year').val('');
        }
    }
    showPrice();
    $('#image_type').change(showPrice);

$('#imagelist-form').bootstrapValidator({
    submitButtons: 'button[type="submit"]',
    submitHandler: function(validator, form, submitButton){
        $.post(form.attr('action'), form.serialize(), function(data){
            var data = eval('(' + data + ')');
            if(data.code==0){
                tentionHide(data.msg,0);
                location.href='<?php echo $this->Url->build(array('controller'=>'Imagelist','action'=>'index'));?>';
            }else{
                tentionHide(data.msg,1);
            }
        });
    },
    fields : {
        image_name: {
            group: '.col-sm-6',
            validators: {
                notEmpty: {
                    message: '镜像名不能为空'
                },
                stringLength: {
                    min: 2,
                    max: 32,
                    message: '请保持在2-32位'
                },
            }
        },
        image_code: {
            group: '.col-sm-6',
            validators: {
                notEmpty: {
                    message: '镜像Code不能为空'
                },
                regexp: {
                    regexp: /^[^\u4e00-\u9fa5\s]{0,}$/,
                    message: '镜像Code不能有空格和中文'
                }
            }
        },
        sort_order: {
            group: '.col-sm-6',
            validators: {
                notEmpty: {
                    message: '排序不能为空'
                },
                between: {
                    min: 0,
                    max: 1000,
                    message: '排序只能在0-1000之间'
                },
                regexp: {
                    regexp: /^([1-9]\d*|0)$/,
                    message: '请输入整数'
                }
            }
        },
        plat_form: {
            group: '.col-sm-6',
            validators: {
                notEmpty: {
                    message: '镜像分类不能为空'
                }
//                stringLength: {
//                    min: 2,
//                    max: 16,
//                    message: '请保持在2-16位'
//                }
            }
        },
        os_family: {
            group: '.col-sm-6',
            validators: {
                notEmpty: {
                    message: '操作系统不能为空'
                },
                stringLength: {
                    min: 2,
                    max: 64,
                    message: '请保持在2-64位'
                }
            }
        },
        price_day : {
                group: '.col-sm-6',
                validators : {
                    notEmpty: {
                        message: '价格不能为空'
                    },
                    numeric: {
                        message: '请填写正确的价格'
                    },
                    greaterThan :{
                        value :0,
                        inclusive : true,
                        message: '价格必须大于0'
                    }
                } 
            },
            price_month : {
                group: '.col-sm-6',
                validators : {
                    notEmpty: {
                        message: '价格不能为空'
                    },
                    numeric: {
                        message: '请填写正确的价格'
                    },
                    greaterThan :{
                        value :0,
                        inclusive : true,
                        message: '价格必须大于0'
                    }
                } 
            },
            price_year : {
                group: '.col-sm-6',
                validators : {
                    notEmpty: {
                        message: '价格不能为空'
                    },
                    numeric: {
                        message: '请填写正确的价格'
                    },
                    greaterThan :{
                        value :0,
                        inclusive : true,
                        message: '价格必须大于0'
                    }
                } 
            },
            image_type :{
                group: '.col-sm-6',
                validators : {
                    remote: {//ajax验证。server result:{"valid",true or false} 向服务发送当前input name值，获得一个json数据。例表示正确：{"valid",true}  
                         url: '/admin/Imagelist/isAllowEdit',//验证地址
                         message: '系统镜像已经绑定厂商地区或者云桌面规格，请先解除绑定再修改类型,',//提示消息
                         delay :  1000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
                         type: 'POST',//请求方式
                         data: function(validator) {
                            return {
                                   id: $('#id').val(),
                                   image_type: $('#image_type').val(),
                                   image_code: $('#image_code').val()
                            };
                        }
                     }
                } 
            }
    }
});  
</script>
<?= $this->end() ?>