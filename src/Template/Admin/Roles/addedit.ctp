<?= $this->element('content_header'); ?>
<style>
    #date-mode-control{
        padding-top:5px;
        padding-bottom:5px;
    }
</style>
<div class="content-body clearfix">
    <div id="maindiv-alert"></div>
    <form class="form-horizontal" id="roles-form" action="<?php echo $this->Url->build(array('controller' => 'Roles','action'=>'addedit')); ?>" method="post">
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">角色名称</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="name"  id="name" placeholder="角色名称" value="<?php if(isset($data['name'])){ echo $data['name'];}  ?>">
                <?php if(isset($data)){ ?>
                <input type="hidden" class="form-control" name="id"  id="id" value="<?php if(isset($data)){ echo $data['id']; } ?>">
                <?php } ?>
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">角色说明</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="note"  id="note" placeholder="角色说明" value="<?php if(isset($data['note'])){ echo $data['note'];}  ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="expire" class="col-sm-2 control-label">可见范围</label>
            <div class="col-sm-6" id="date-mode-control" style="line-height: 24px;">
                <?php
                if ($this->request->session()->read("Auth.User.popedomname")) {
                    $popedomname = $this->request->session()->read("Auth.User.popedomname");
                    if(in_array('cmop_global_sys_admin', $popedomname)){ ?>
                    <input type="radio" name="department" <?php if(isset($data)){if($data['department_id'] === 0){ echo 'checked="true"';}} ?> value="-1" /> 全部可见
                    <input type="radio"   name="department" <?php if(isset($data)){if($data['department_id'] !== 0 && $data['department_id'] !==null){ echo 'checked="true"';}}else{  echo 'checked="true"';}   ?> value="0" /> 局部可见
                    <?php }elseif(in_array('cmop_global_tenant_admin', $popedomname) && !in_array('cmop_global_sys_admin', $popedomname)){ ?>
                    <input type="radio" name="department" value="-1" disabled/> 全部可见
                    <input type="radio" name="department" checked="true" value="0" /> 局部可见
                    <?php }} ?>
                    <?php if(in_array('cmop_global_sys_admin', $popedomname)){ ?>
            		<select name="department_id" id="department" style="margin-left: 5px;">
                    <?php  foreach ($dept_grout as $key => $value) { ?>
                        <option value="<?php echo $value['id']; ?>"   <?php if(isset($data)){ if($value['id']==$data['department_id']){
                        	echo 'selected';
                        } }else{if($value['id']==$this->request->session()->read("Auth.User.department_id")){ echo 'selected';}} ?> >
                            <span><?php echo $value['name'];?></span>
                        </option>
                       <?php }  ?>
               		 </select>
               		 <?php }else{ ?>
               		 <select name="department_id" id="department" style="margin-left: 5px;">
               		 	<option value="<?php echo $this->request->session()->read("Auth.User.department_id");?>" >
	                            <span><?php echo $this->request->session()->read("Auth.User.department_name");?></span>
	                        </option>
	               		 </select>
               		 <?php } ?>
	            </div>
        	</div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" id="ds" class="btn btn-primary">保存</button>
                    <a type="button" href="<?php echo $this->Url->build(array('controller' => 'Roles','action'=>'index')); ?>" class="btn btn-danger">返回</a>
                </div>
            </div>


        </form>
    </div>

    <?=$this->Html->script(['adminjs.js','validator.bootstrap.js']); ?>
    <?= $this->start('script_last'); ?>
    <script type="text/javascript">
	//选择租户
	$(function(){
		$("input[name='department']").on('click',function(){

			 if($("input[name='department']:checked").val()==-1){
				$('#department').css('display','none');
			}else{
				$('#department').css('display','inline-block');
			}
		});


		if($("input[name='department']:checked").val()==-1){
			$('#department').css('display','none');
		}else{
			$('#department').css('display','inline-block');
		}
	})



	$('#roles-form').bootstrapValidator({
	    submitButtons: 'button[type="submit"]',
	    submitHandler: function(validator, form, submitButton){
	        $.post(form.attr('action'), form.serialize(), function(data){
	            var data = eval('(' + data + ')');
	            if(data.code==0){
	                tentionHide(data.msg, 0);
	                location.href=data.url;
	            }else{
	                tentionHide(data.msg, 1);
	            }
	        });
	    },
	    fields : {
	        name: {
	            group: '.col-sm-6',
	            validators: {
	                notEmpty: {
	                    message: '角色名称不能为空'
	                },
	                stringLength: {
	                    min: 2,
	                    max: 16,
	                    message: '请保持在2-16位'
	                },
	                regexp: {
	                    regexp: /^\S*$/,
	                    message: '角色名称不能有空格'
	                }
	            }
	        },
	        note: {
	            group: '.col-sm-6',
	            validators: {
	             notEmpty: {
	                message: '角色说明不能为空'
	            },
	            stringLength: {
	                min: 2,
	                max: 16,
	                message: '请保持在2-16位'
	            },
	            regexp: {
	                regexp: /^\S*$/,
	                message: '角色说明不能有空格'
	            }
	        }
	    },
	}
	});

</script>
<?= $this->end() ?>

