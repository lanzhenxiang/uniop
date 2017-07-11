<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div id="maindiv-alert" class="content-list-page"></div>
    <div class="content-operate clearfix">

        <div class="clearfix" style="margin-top:20px;"></div>


        <form class="form-horizontal bv-form" id="aduser-form" action="" method="post" novalidate="novalidate">


        	<div class="form-group">
            	<label for="inputPassword3" class="col-sm-2 control-label">品牌</label>
            	<div class="col-sm-6">
                	<input type="text" class="form-control" name="brand" id="brand" <?php if($edit=="false"){echo 'disabled="disabled"';}?>  value="<?php if(isset($vinfo)){echo $vinfo->brand;}?>" >
           		</div>
        	</div>
        	<div class="form-group">
            	<label for="inputPassword3" class="col-sm-2 control-label">规格名称</label>
            	<div class="col-sm-6">
                	<input type="text" class="form-control" name="name" id="name" <?php if($edit=="false"){echo 'disabled="disabled"';}?>  value="<?php if(isset($vinfo)){echo $vinfo->name;}?>" >
           		</div>
        	</div>

        	<div class="form-group">
            	<label for="inputPassword3" class="col-sm-2 control-label">计算能力</label>
            	<div class="col-sm-6">
                	<select id="instancetype_code" name="instancetype_code"  class="form-control"  <?php if($edit=="false"){echo 'disabled="disabled"';}?>>
                		<?php foreach($hardwares as $key=>$hardware){?>
                        <option  value="<?=$hardware['set_code']?>"  <?php if( isset($vinfo)&&$hardware['set_code']==$vinfo->instancetype_code){echo 'selected="selected"';}?>><?=$hardware['set_name']?></option> 
                        <?php } ?>                    
                   	</select>
           		</div>
        	</div>
        	<div class="form-group">
            	<label for="inputPassword3" class="col-sm-2 control-label">系统镜像</label>
            	<div class="col-sm-6">
                	<select id="image_code" name="image_code"  class="form-control" <?php if($edit=="false"){echo 'disabled="disabled"';}?>>
                		<?php foreach($images as $key=>$image){?>
                        <option value="<?=$image['image_code']?>" <?php if(isset($vinfo)&&$image['image_code']==$vinfo->image_code){echo 'selected="selected"';}?>><?=$image['image_name']?></option> 
                        <?php } ?>           
                   </select>
           		</div>
        	</div>
<div class="fix-pay padding20 ">
    <div class="form-group">
        <label for="inputPassword3" class="col-sm-2 control-label">按日计费（日/元）</label>
        <div class="col-sm-6">
            <input type="text" class="form-control" name="d" id="d"  value="<?php if(isset($vinfoD) && isset($vinfoD['D'])){echo $vinfoD['D'];}?>" >

        </div>
    </div>
    <div class="form-group">
        <label for="inputPassword3" class="col-sm-2 control-label">按月计费（月/元）</label>
        <div class="col-sm-6">
            <input type="text" class="form-control" name="m" id="m"  value="<?php if(isset($vinfoD) && isset($vinfoD['M']) ){echo $vinfoD['M'];}?>" >

        </div>
    </div>
    <div class="form-group">
        <label for="inputPassword3" class="col-sm-2 control-label">按年计费（年/元）</label>
        <div class="col-sm-6">
            <input type="text" class="form-control" name="y" id="y"  value="<?php if(isset($vinfoD) && isset($vinfoD['Y']) ){echo $vinfoD['Y'];}?>" >

        </div>
    </div>
</div>
            <div class="fix-pay time-pay padding20 ">
                <div class="form-group">
                    <label for="inputPassword3" class="col-sm-2 control-label">计费单价（元）</label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" name="price" id="price"  value="<?php if(isset($vinfoD) && isset($vinfoD['price'])){echo $vinfoD['price'];}?>" >

                    </div>
                </div>
                <div class="form-group">
                    <label for="inputPassword3" class="col-sm-2 control-label">计费周期</label>
                    <div class="col-sm-6">
                        <select id="unit" name="unit"  class="form-control" >
                            <option value="I" <?php if(isset($vinfoD) && isset($vinfoD['unit']) && $vinfoD['unit'] == 'I'){echo "selected='selected'";} ?>>分</option>
                            <option value="S" <?php if(isset($vinfoD) && isset($vinfoD['unit']) && $vinfoD['unit'] == 'S'){echo "selected='selected'";} ?>>秒</option>
                        </select>
                    </div>
                </div>
            </div>


        	<div class="form-group margint20">
            	<label for="inputPassword3" class="col-sm-2 control-label">备注</label>
            	<div class="col-sm-6">
                	<textarea name="description" <?php if($edit=="false"){echo 'disabled="disabled"';}?> class="form-control" data-bv-field="description"><?php if(isset($vinfo)){echo $vinfo->description;}?></textarea>
           		</div>
        	</div>
        	<div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                <input type="hidden" value="<?=$this->request->query('id');?>" name="id" id="id">
                    <button type="submit" id="account_submit" class="btn btn-primary">提交</button>
                    <!--<a type="button" href="/admin/spec" class="btn btn-danger">返回</a>-->
                    <a type="button"onclick="window.history.go(-1)" class="btn btn-danger">返回</a>
                </div>
            </div>
        </form>
    </div>
</div>
<script src="/js/validator.bootstrap.js"></script>
<script>
    $('#aduser-form').bootstrapValidator({
    submitButtons: 'button[type="submit"]',
     fields : {
         name:{
             group: '.col-sm-6',
             validators:{
                 notEmpty: {
                     message: '规格名称不能为空'
                 }
             }
         },
         brand:{
             group: '.col-sm-6',
             validators:{
                 notEmpty: {
                     message: '品牌不能为空'
                 }
             }
         },
            m: {
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '月价格不能为空'
                    },
                    stringLength: {
                        min: 1,
                        max: 12,
                        message: '请保持在1-12位'
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
            d: {
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '天价格不能为空'
                    },
                    stringLength: {
                        min: 1,
                        max: 12,
                        message: '请保持在1-12位'
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
            y: {
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '年价格不能为空'
                    },
                    stringLength: {
                        min: 1,
                        max: 12,
                        message: '请保持在1-12位'
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
         price:{
             group: '.col-sm-6',
             validators: {
                 notEmpty: {
                     message: '计费单价不能为空'
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
            instancetype_code :{
                group: '.col-sm-6',
                validators : {
                    remote: {//ajax验证。server result:{"valid",true or false} 向服务发送当前input name值，获得一个json数据。例表示正确：{"valid",true}  
                         url: '/admin/Spec/isAllowEdit',//验证地址
                         message: '桌面规格已经绑定厂商地区，请先解除绑定再修改类型,',//提示消息
                         delay :  1000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
                         type: 'POST',//请求方式
                         data: function(validator) {
                            return {
                                   id: $('#id').val(),
                                   instancetype_code: $('#instancetype_code').val(),
                                   image_code: $('#image_code').val()
                            };
                        }
                     }
                } 
            },
            image_code :{
                group: '.col-sm-6',
                validators : {
                    remote: {//ajax验证。server result:{"valid",true or false} 向服务发送当前input name值，获得一个json数据。例表示正确：{"valid",true}  
                         url: '/admin/Spec/isAllowEdit',//验证地址
                         message: '桌面规格已经绑定厂商地区，请先解除绑定再修改类型,',//提示消息
                         delay :  1000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
                         type: 'POST',//请求方式
                         data: function(validator) {
                            return {
                                   id: $('#id').val(),
                                   instancetype_code: $('#instancetype_code').val(),
                                   image_code: $('#image_code').val()
                            };
                        }
                     }
                } 
            }
            
        }
});

</script>