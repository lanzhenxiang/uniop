
<?php if (in_array('ccf_host_new', $this->Session->read('Auth.User.popedomname'))) { ?>
                <?php if (in_array('ccf_all_select_department', $this->Session->read('Auth.User.popedomname'))) { ?>
                <a class="btn btn-addition" onclick="switchDepartment()" href="javascript:void(0);">
                <i class="icon-plus"></i>&nbsp;&nbsp;新建</a>
                <?php }else{?>
            <a class="btn btn-addition" href="<?=$callback_url?>">
            <i class="icon-plus"></i>&nbsp;&nbsp;新建</a>
                <?php } ?>
<?php } ?>

<!-- 跨租户创建 -->
<?php if (in_array('ccf_all_select_department', $this->Session->read('Auth.User.popedomname'))) { ?>
      <div class="modal fade" id="modal-admin-add" tabindex="-1" role="dialog">
       <div class="modal-dialog" role="document">
        <div class="modal-content">
         <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"
          aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h5 class="modal-title">跨租户新建--<?=$typeName?></h5>
      </div>
      <form id="dept-modal-modify-form" action="<?= $this -> Url -> build(['controller' => 'console', 'action' => 'switchDepartment']) ?>" method="post">
        <div class="modal-body" >
            <!-- <div class="modal-form-group">
            <label>当前租户:</label>
            <div class="form-group">
                <input type="text" id="" name="create_department_name" value="<?=$this->request->session()->read('Auth.User.department_name') ?>" disabled="disabled" />
            </div>
          </div> -->
          <div class="modal-form-group" style="padding: 10px 20px 0;">
            <label>选择租户:</label>
            <div class="form-group">
                <select id="create_department_id" name="create_department_id">
                    <?php foreach($_deparments as $value) { ?>
                         <option value="<?=$value['id']?>" <?php if($this->request->session()->read("Auth.User.create_department_id") == $value['id']):?> selected="selected" <?php endif;?> ><?=$value['name']?></option>
                    <?php }?>
                </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
         <input type="hidden" name="callback_url" value="<?=$callback_url?>" />
         <button id="dept_sumbiter" type="submit" class="btn btn-primary">确认</button>
         <button id="dept_reseter" type="button" class="btn btn-danger"
         data-dismiss="modal">取消</button>
       </div>
     </form>
    </div>
    </div>
</div>
<?php }?>
<script type="text/javascript">
   function departmentlist(id, name, type='default'){
        $("#txtdeparmetId").val(id);
      $("#deparmets").html(name);
      <?php if (in_array('ccf_all_select_department', $this->Session->read('Auth.User.popedomname'))) { ?>
        $.ajax({

           type: "POST",
           url: "/console/console/switchDepartment",
           data: {
                create_department_id: id
            },
           success: function(rep){
                response = $.parseJSON(rep);
                if(response.code == '0'){
                    $("#create_department_id").children('option').removeAttr('selected');
                    $("#create_department_id").children('option[value="'+id+'"]').attr('selected','selected');
                }else{
                    alert( "切换租户失败！" );
                }
           }
        });

		switch (type) {
		case 'hosts':
			$.ajax({
		           type: "GET",
		           url: "/console/network/getDepartVpcByDepartID/"+id,
		           success: function(rep){
		                response = $.parseJSON(rep);
		                var data = '<li><a href="javascript:;" onclick="selectVpc(\'0\', \'\', \'全部\')">全部</a></li>';
		                $.each(response, function(i, n) {
		                	data += '<li><a href="#" onclick="selectVpc(\'' + n.id + '\',\'' + n.code + '\',\'' + n.name + '\')">' + n.name + '</a></li>';

		                });
		                $('#vpcInfo').html(data);
		           }
		        });
			$('#vpcCode').html('全部');
            $('#vpcCode').attr('val', '');
			break;
		}

      <?php }?>
      refreshTable()
    }
    <?php if (in_array('ccf_all_select_department', $this->Session->read('Auth.User.popedomname'))) { ?>
    function switchDepartment(){
        $.ajax({
           type: "POST",
           url: "/console/console/isCurrentDepartment",
           data: {},
           success: function(rep){
                response = $.parseJSON(rep);
                if(response.code == '0'){
                   window.location.href="<?=$callback_url?>";
                }else{
                    $('#modal-admin-add').modal('show');
                }
           }
        });
    }
    <?php }?>
</script>