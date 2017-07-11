<?= $this->element('desktop/lists/left',['active_action'=>'charge_management']); ?>
<div class="wrap-nav-right">
    <div class="wrap-manage">
        <div class="top">
            <span class="title">计费管理</span>
            <div id="maindiv-alert"></div>
        </div>

        <div class="center clearfix">
            <div class="pull-left">
                <button class="btn btn-default" id="edit-charge">
                    修改计费规则
                </button>
            </div>
            <!--筛选-->
            <div class="input-group content-search pull-right col-sm-3">
                <input type="text" class="form-control" id="txtsearch" placeholder="搜索桌面名、CODE">
                 <span class="input-group-btn">
                     <button class="btn btn-primary" id="search-btn" type="button">搜索</button>
                 </span>
            </div>

            <div class="pull-right">
                <input type="hidden" id="txtdeparmetId" value="<?= $department["id"] ?>" />
                <?php if (in_array('ccf_all_select_department', $this->request->session()->read('Auth.User.popedomname'))) { ?>
              <div class="dropdown">
                租户:
                <a href="#" class="dropdown-toggle btn btn-addition text-right" data-toggle="dropdown" role="button">
                    <span class="pull-left" id="deparmets" val="<?= $department["id"] ?>"><?= $department["name"] ?></span>
                    <span class="caret"></span>
                </a>
                <ul class="dropdown-menu">
                      <?php foreach($departments as $value) { ?>
                         <li><a href="#" onclick="departmentlist(<?php echo $value['id'] ?>,'<?php echo $value['name'] ?>')"><?php echo $value['name'] ?></a></li>
                      <?php }?>
                </ul>
              </div>
              <?php }?>
              <div class="dropdown">
                计费方式:
                <a href="#" class="dropdown-toggle btn btn-addition text-right" data-toggle="dropdown" role="button">
                    <span class="pull-left" id="chargeType" val="<?= $chargeType["val"] ?>"><?= $chargeType["name"] ?></span>
                    <span class="caret"></span>
                </a>
                <ul class="dropdown-menu">
                        <li><a href="#" onclick="chargeType('','全部')">全部</a></li>
                      <?php foreach($chargeTypes as $key=>$value) { ?>
                         <?php $value = '按'.$value."计费"; ?>
                         <li><a href="#" onclick="chargeType('<?php echo $key ?>','<?php echo $value ?>')"><?php echo $value ?></a></li>
                      <?php }?>
                </ul>
              </div>
            </div>

            <div style="clear: both;"></div>
        </div>
        <!--表格-->
        <div class="bot">
            <div>
                <input type="hidden" value="not" id="type">
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="not">
                        <table class="table table-striped" id="table" data-toggle="table" data-pagination="true" 
                               data-side-pagination="server"
                               data-page-list="[20,30]" data-page-size="20"
                               data-locale="zh-CN" data-click-to-select="true"
                               data-url="<?= $this->Url->build(['prefix'=>'console','controller'=>'desktop','action'=>'chargeList','?'=>['department_id'=>$department['id']]]); ?>"
                               data-unique-id="id">
                            <thead>
                            <tr>
                                <th data-checkbox="true"></th>
                                <th data-field="Departments.name">所属租户</th>
                                <th data-field="code">桌面code</th>
                                <th data-field="name">桌面名</th>
                                <th data-field="instance_charge.interval" data-formatter="format_mode">所有模式</th>
                                <th data-field="instance_charge.charge_type_txt" >计费方式</th>
                                <th data-field="instance_charge.charge_price_txt">计费单价</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>


            </div>
        </div>
    </div>
</div>
<!-- 修改计费模式 -->
<div class="modal fade" id="charge-modal-edit" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
    <form id="chargeModeForm" method="post" class="form-horizontal">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">修改计费方式</h4>
      </div>
      <div class="modal-body">
        <div class="modal-form-group">
            <!-- <p><i class="icon-question-sign text-primary"></i>&nbsp;&nbsp;<span class="text-primary">修改计费方式</span></p> -->
        </div>
        <div class="modal-form-group">
                <label>所有模式:</label>
                <div class="form-group">
                    <input type="text" id="mode" name="mode" value="临时购买"  disabled="disabled" />
                </div>
        </div>
        <div class="modal-form-group">
                <label>计费类型:</label>
                <div class="form-group">
                  <select name="charge_mode" id="charge_mode">
                      <option value="">选择计费模式</option>
                      <option value="permanent|P">永久免费</option>
                      <option value="cycle|D">按天计费</option>
                      <option value="cycle|M">按月计费</option>
                      <option value="cycle|Y">按年计费</option>
                      <option value="duration|I">按分钟计费</option>
                  </select>
                </div>
        </div>
        <div class="modal-form-group">
                <label>成交单价:</label>
                <div class="form-group">
                  <input type="text" id="price"  name="price" value="" /><span id="UnitText" style="padding: 0 10px"></span>
                </div>
        </div>
        <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="not">
                        <table class="table table-striped" id="charge_table" data-toggle="table" 
                               data-locale="zh-CN"
                               data-unique-id="id">
                            <thead>
                            <tr>
                                <th data-field="code">桌面code</th>
                                <th data-field="name">桌面名</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
        </div>
      </div>
      <div class="modal-footer">
        <input type="hidden" id="basicIds" name="basicIds"  value="" >
        <button type="submit" class="btn btn-primary" id="charge_submit">确 认</button>
        <button type="button" class="btn btn-danger" id="charge_cancel" data-dismiss="modal">取 消</button>
      </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="modal-msg" tabindex="-1" role="dialog"></div>
<?= $this->Html->css(['zTreeStyle.css']) ?>
<?= $this->Html->script(['jquery.ztree.core-3.5.js']); ?>
<?= $this->Html->script(['jquery.ztree.excheck-3.5.js']); ?>
<?=$this->Html->script(['adminjs.js','validator.bootstrap.js','bootstrap-datetimepicker.js','jquery.cookie.js','bootstrap-paginator.js']); ?>
<?= $this->start('script_last'); ?>
<script type="text/javascript">

    function format_mode(value){
        if(value == 'P'){
            return '自有许可';
        }
        return '临时购买';
    }

//搜索
    $('#deptname').on('keyup', function (e) {
        var key = e.which;
        if (key == 13) {
            e.preventDefault();
            searchs();
        }
    });
//分页
    $('#show_not').on('click',function(){
        $('#type').val('not');
        $('#add_group').css('display', 'block');
        $('#out_group').css('display', 'none');
        searchs();
    });
    $('#show_had').on('click',function(){
        $('#type').val('had');
        $('#add_group').css('display', 'none');
        $('#out_group').css('display', 'block');
        searchs();
    });

    //修改计费
    $('#edit-charge').on('click',function(){
        var rows = $('#table').bootstrapTable('getSelections');
        var id='';
        var name='';
        if(rows.length==0){
            made_modal('提示', '请先选择要修改计费的桌面');
        }else{
            console.log(rows);
            $('#charge_table').bootstrapTable('load',rows);
            $.each(rows,function(i,n){
                id+= n.id+',';
                name+= n.name+',';
            });
            // $('#selected_desk').html(name);
            $('#basicIds').val(id);

            $('#charge-modal-edit').modal('show');
        }
    });

    //提示信息
    function tentionHide(content, state) {
        $("#maindiv-alert").empty();
        var html = "";
        if (state == 0) {
            html += '<div class="point-host-startup "><i></i>' + content + '</div>';
            $("#maindiv-alert").append(html);
            $(".point-host-startup ").slideUp(3000);
        } else {
            html += '<div class="point-host-startup point-host-startdown"><i></i>' + content + '</div>';
            $("#maindiv-alert").append(html);
            $(".point-host-startdown").slideUp(3000);
        }
    }
    //弹框
    function made_modal(name, msg) {
        $("#modal-msg").empty();
        var html = '<div class="modal-dialog" role="document"><div class="modal-content"><div class="modal-header">' +
            '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
            '<h5 class="modal-title">' + name + '</h5></div><div class="modal-body">' +
            '<i class="icon-question-sign text-primary"></i>&nbsp;&nbsp;' + msg + '<span class="text-primary" ></span>' +
            '</div><div class="modal-footer"><button type="button" class="btn btn-danger" data-dismiss="modal">确认</button></div> </div> </div>';

            $('#modal-msg').append(html);
            $('#modal-msg').modal('show');
    }
    function departmentlist(id,name){
        $("#txtdeparmetId").val(id);
        $("#deparmets").html(name);
        refreshTable();
    }
    function chargeType(val,name){
        $('#chargeType').val(val);
        $('#chargeType').html(name);
        refreshTable();
    }
    $('#search-btn').on('click', function () {
        refreshTable();
    });
    function refreshTable() {
        var search= $("#txtsearch").val();
        //$('#table').bootstrapTable('showLoading');

        $('#table').bootstrapTable('refresh', {
          url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'desktop','action'=>'chargeList']); ?>?search=" + search +'&department_id='+$("#txtdeparmetId").val()+'&charge_type='+$('#chargeType').val()
        });
    }

    //定义修改计费模式的表单验证
  $("#chargeModeForm").bootstrapValidator({
        submitButtons: '#charge_submit',
        submitHandler: function(validator, form, submitButton) {
            // 实用ajax提交表单
            $('#charge-modal-edit').modal('hide');
            $('#charge-modal-edit').one('hidden.bs.modal',function(){
                $.ajax({
                    url:"<?= $this->Url->build(['prefix'=>'console','controller'=>'desktop','action'=>'editChargeMode']); ?>",
                    data:$('#chargeModeForm').serialize(),
                    method:'post',
                    dataType:'json',
                    success:function(response){
                        alert(response.msg);
                        refreshTable();
                    }
                });
                $("#chargeModeForm").data('bootstrapValidator').resetForm();
            });
            
        },
        fields: {
          //多个重复
          price: {
              //隐藏或显示 该字段的验证
              enabled: true,
              //错误提示信息
              message: 'This value is not valid',
              /**
              * 定义错误提示位置  值为CSS选择器设置方式
              * 例如：'#firstNameMeg' '.lastNameMeg' '[data-stripe="exp-month"]'
              */
              container: null,
              /**
              * 定义验证的节点，CSS选择器设置方式，可不必须是name值。
              * 若是id，class, name属性，<fieldName>为该属性值
              * 若是其他属性值且有中划线链接，<fieldName>转换为驼峰格式  selector: '[data-stripe="exp-month"]' =>  expMonth
              */
              selector: null,
              /**
              * 定义触发验证方式（也可在fields中为每个字段单独定义），默认是live配置的方式，数据改变就改变
              * 也可以指定一个或多个（多个空格隔开） 'focus blur keyup'
              */
              trigger: null,
              // 定义每个验证规则
              validators: {
                    notEmpty: {
                        message: '价格不能为空'
                    },
                    numeric:{
                        message: '请输入正确的价格'
                    },
                    greaterThan:{
                        value : 0,
                        inclusive : true,
                        message: '价格不能小于0'
                    }
              }
          },
          charge_mode: {
              // 定义每个验证规则
              validators: {
                    notEmpty: {
                        message: '请选择计费模式'
                    }
              }
          }

      }
    });
    //取消modal，重置表单验证
    $("#charge_cancel").click(function(){
      $("#chargeModeForm").data('bootstrapValidator').resetForm();
    });

    //永久许可不用设置单价
    $("#charge_mode").change(function(){
      //.select(['option:selected'])
      var priceForm = $(this).parent().parent().next();
      if($(this).val() == "permanent|P"){
        $('#mode').val('自有许可');
        priceForm.hide();
      }else{
        $('#mode').val('临时购买');
        priceForm.show();
      }
      switch($(this).val()){
        
        case "cycle|D":
            $("#UnitText").html("元/天");
            break;
        case "cycle|M":
            $("#UnitText").html("元/月");
            break;
        case "cycle|Y":
            $("#UnitText").html("元/年");
            break;
        case "duration|I":
            $("#UnitText").html("元/分钟");
            break;
        default:
            $("#UnitText").html("");
      }

    });
</script>
<?= $this->end() ?>