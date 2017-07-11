<?= $this->element('content_header'); ?>
  <?= $this->Html->css(['bootstrap-table.css']); ?>
<style>
    table{
        width: 100%;
        margin: 20px 0 30px;
    }
    td,th{
        padding: 3px 0;
        border: 1px solid #d2d2d2;
        text-align: center;
    }
    th{
        background: #373D3D;
        color: #fff;
        border: 1px solid #fff;
    }
.nav > li > a{
        width: auto;
        padding:8px 30px;
    }
</style>
<div class="content-body clearfix">
    <div id="maindiv-alert"></div>
    <form class="form-horizontal" enctype="multipart/form-data" id="goods-form" action="<?php echo $this->Url->build(array('controller'=>'Goods','action'=>'editadd'));?>" method="post">
        <div>
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist" id="myTab">
                <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">基本信息</a></li>
                <!--<li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">商品详细描述</a></li>-->
                <li role="presentation"><a href="#spec_display" aria-controls="messages" role="tab" data-toggle="tab">商品展示规格</a></li>
                <li role="presentation"><a href="#spec_need" aria-controls="messages" role="tab" data-toggle="tab">商品接口规格</a></li>
                <li role="presentation"><a href="#image" aria-controls="image" role="tab" data-toggle="tab">图片上传</a></li>
                <li role="presentation" data="version">
                    <a href="#version" aria-controls="version" role="tab" data-toggle="tab">
                        VPC
                    </a>
                </li>
            </ul>
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="home">
                    <div class="form-group">
                        <label for="name" class="col-sm-2 control-label">商品名称</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="name" id="name" value="<?php echo $acc[0]['name']; ?>" placeholder="商品名称">
                        </div>
                    </div>
                     <input type="hidden" name="goods_txt_buytype" id="goods_txt_buytype">
                    <div class="form-group">
                        <label for="sn" class="col-sm-2 control-label">商品序列号</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" id="sn" name="sn" value="<?php echo $acc[0]['sn']; ?>" placeholder="商品序列号">
                            <?php if(isset($acc)){ ?>
                            <input type="hidden" class="form-control" name="id"  id="id" value="<?php if(isset($acc[0])){ echo $acc[0]['id']; } ?>">
                            <?php } ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="description" class="col-sm-2 control-label">产品描述</label>
                        <div class="col-sm-6">
                            <textarea type="text" name="description" class="form-control" rows="4" id="description" placeholder="产品描述 "><?php echo $acc[0]['description']; ?></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="price" class="col-sm-2 control-label">价格</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="price" value="<?php echo $acc[0]['price']; ?>" id="price" placeholder="价格">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="category" class="col-sm-2 control-label">商品分类</label>
                        <div class="col-sm-6">
                            <select id="category" name="category_id" class="form-control">
                                <?php foreach ($data as $key => $value) {   ?>
                                <option value="<?php echo $key;?>" <?php if(isset($acc[0]['category_id']) && $acc[0]['category_id']==$key){ echo 'selected="select"';} ?> >
                                    <span><?php echo $value;?></span>
                                </option>
                                <?php }   ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="star" class="col-sm-2 control-label">推荐指数</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="star" value="<?php echo $acc[0]['star']; ?>" id="star" placeholder="推荐指数">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputPassword3" class="col-sm-2 control-label">是否热销</label>
                        <div class="col-sm-6">
                            <label class="radio-inline">
                                <input type="radio" name="hot" <?php if($acc[0]['hot']==1){echo 'checked';}  ?> value="1"> 热销
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="hot" <?php if($acc[0]['hot']==0){echo 'checked';}  ?> value="0"> 不热销
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="icon" class="col-sm-2 control-label">首页商品图标</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="icon" id="icon" value="<?php echo $acc[0]['icon']; ?>" placeholder="首页商品图标" ></input>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="mini_icon" class="col-sm-2 control-label">商品订单图标</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="mini_icon" id="mini_icon" value="<?php echo $acc[0]['mini_icon']; ?>" placeholder="商品订单图标"></input>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="picture" class="col-sm-2 control-label">商品详情图片</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="picture" value="<?php echo $acc[0]['picture']; ?>" id="picture" placeholder="商品详情图片"></input>
                        </div>
                    </div>
                    <div class="form-group">
                                <label for="goods_charging_ways" class="col-sm-2 control-label">计费周期</label>
                                <div class="col-sm-6">
                                    <select id="goods_charging_ways" name="goods_charging_ways" class="form-control">
                                        <option value="">
                                            请选择
                                        </option>
                                        <?php foreach ($billcycle as $key => $value) { ?>
                                            <?php if(!empty($value)){ ?>
                                                <option value="<?= $key ?>" <?php if($acc[0]['goods_charging_ways']==$key){echo 'selected';} ?>><?= $value ?></option>
                                            <?php } ?>
                                        <?php } ?>

                                        </select>
                                </div>
                                <input type="button" onclick="showSet()" name="btnSet" value="配置可选计费">
                            </div>
                            <input type="hidden" id="user_chargings" name="user_chargings" value="">
                </div>
                <div role="tabpanel" class="tab-pane" id="profile">
                    <div style="margin-top:15px;">
                        <?php echo $this->Html->script('ueditor.config.js'); ?>
                        <?php echo $this->Html->script('ueditor.all.js'); ?>
                        <script id="detail" name="detail" type="text/plain" style="width:1024px;height:500px;"><?php echo $acc[0]['detail']; ?></script>
                        <div style="height:30px" id="123"></div>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="spec_display">
                    <div class="clearfix">
                        <button class="btn btn-default" type="button" style="vertical-align:top;" data-toggle="modal" onclick="addSpec(1,0)">
                            <span class="glyphicon glyphicon-plus" aria-hidden="true">新增</span>
                        </button>
                        <select style="width:240px;margin-left:10px;" name="group_id" class="form-control" onchange="changeSpecGroup(this.value,1,0)">
                            <?php foreach ($query as $key => $value) {   ?>
                            <option value="<?php echo $value->group_id;?>" <?php if($acc[0]['group_id']==$value->group_id){echo 'selected';} ?>>
                                <span><?php echo $value->group_name;?></span>
                            </option>
                            <?php }   ?>
                        </select>
                    </div>
                    <div>
                        <?php if(isset($acc[0])){ $good_id= $acc[0]['id'];}else{$good_id=0;} ?>
                        <input type="hidden" name="display_spec_name" id="display_spec_name"></input>
                        <input type="hidden" name="display_spec_value" id="display_spec_value"></input>
                        <input type="hidden" name="display_spec_code" id="display_spec_code"></input>
                         <table >
                            <thead>
                                <tr>
                                    <th>名 称</th>
                                    <th>代 码</th>
                                    <th>规格值</th>
                                    <th>操 作</th>
                                </tr>
                            </thead>
                            <tbody id="display_tbody">
                                <?php if(isset($result)){
                                    foreach($result as $_v_spec){
                                        if($_v_spec['is_display']==1){
                                        ?>
                                        <tr>
                                            <td><?php echo $_v_spec['spec_name']; ?></td>
                                            <td><?php echo $_v_spec['spec_code']; ?></td>
                                            <td><?php echo $_v_spec['spec_value']; ?></td>
                                            <td>
                                               <a href="#" onclick="editSpec(1,0,this)">修改</a> |
                                               <a href="#" onclick="delSpec(1,0,this)">删除</a>
                                           </td>
                                       </tr>
                                       <?php }
                                   }
                               }
                               ?>
                           </tbody>
                        </table>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="spec_need">
                    <div class="clearfix">
                        <button class="btn btn-default" type="button" style="vertical-align:top;" data-toggle="modal" onclick="addSpec(0,1)">
                            <span class="glyphicon glyphicon-plus" aria-hidden="true">新增</span>
                        </button>
                        <select style="width:240px;margin-left:10px;" id="group_id" name="group_id" class="form-control"  onchange="changeSpecGroup(this.value,0,1)">
                            <?php foreach ($query as $key => $value) {   ?>
                            <option value="<?php echo $value->group_id;?>" <?php if($acc[0]['group_id']==$value->group_id){echo 'selected';} ?>>
                                <span><?php echo $value->group_name;?></span>
                            </option>
                            <?php }   ?>
                        </select>
                    </div>
                    <div>
                        <?php if(isset($acc[0])){ $good_id= $acc[0]['id'];}else{$good_id=0;} ?>
                        <input type="hidden" name="need_spec_name" id="need_spec_name"></input>
                        <input type="hidden" name="need_spec_value" id="need_spec_value"></input>
                        <input type="hidden" name="need_spec_code" id="need_spec_code"></input>
                         <table >
                            <thead>
                                <tr>
                                    <th>名 称</th>
                                    <th>代 码</th>
                                    <th>规格值</th>
                                    <th>操 作</th>
                                </tr>
                            </thead>
                            <tbody id="need_tbody">
                                <?php if(isset($result)){
                                    foreach($result as $_v_spec){
                                        if($_v_spec['is_need']==1){
                                        ?>
                                        <tr>
                                            <td><?php echo $_v_spec['spec_name']; ?></td>
                                            <td><?php echo $_v_spec['spec_code']; ?></td>
                                            <td><?php echo $_v_spec['spec_value']; ?></td>
                                            <td>
                                               <a href="#" onclick="editSpec(0,1,this)">修改</a> |
                                               <a href="#" onclick="delSpec(0,1,this)">删除</a>
                                           </td>
                                       </tr>
                                       <?php }
                                   }
                               }
                               ?>
                           </tbody>
                        </table>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="image">
                    <div class="form-group">
                        <label for="software_name" class="col-sm-2 control-label">上传图片</label>
                        <div id="up" class="col-sm-6">
                            <!-- <label>上传图片</label> -->
                            <input type="file" accept="image/*" name="upfile[]" multiple id="userfile" class="file">
                        </div>
                    </div>
                </div>
                    <div role="tabpanel" class="tab-pane" id="version">
                    <div class="form-group">
                        <label for="category" class="col-sm-2 control-label">
                            选择VPC
                        </label>
                        <div class="col-sm-6">
                            <select id="goods_buytype" name="goods_buytype" onchange="vpc_version()" class="form-control">
                                <option value="0">
                                    请选择
                                </option>
                                <option value="1">
                                        <span>
                                            VPC配置
                                        </span>
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group" id="div_vpc">
                        <label for="category" class="col-sm-2 control-label">
                            VPC配置单
                        </label>
                        <div class="col-sm-6">
                            <select id="goods_vpc" name="goods_vpc" onchange="goods_vpcs()" class="form-control">
                                <option value="0">
                                    请选择
                                </option>
                                <?php foreach ($goods_vpc_data as $value) { ?>
                                    <option value="<?php echo $value['vpc_id'];?>">
                                        <span>
                                            <?php echo $value[ 'vpc_name'];?>
                                        </span>
                                    </option>
                                    <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group" id="div_vpc2">
                    <label for="flow" class="col-sm-2 control-label">订单流程</label>
                    <div class="col-sm-6">
                        <select id="flow" name="flow_id" class="form-control">
                            <?php foreach ($flow_data as $_flow_k => $_flow_v) {   ?>
                            <option value="<?php echo $_flow_v['flow_id'];?>"  <?php if($_flow_v['flow_id']==$acc[0]['flow_id']){echo 'selected="selected"';}?>>
                                <span><?php echo $_flow_v['flow_name'];?></span>
                            </option>
                            <?php }   ?>
                        </select>
                    </div>
                </div>
                    <div class="form-group" id="div_version">
                        <table class="table table-striped" id="table" data-toggle="table" data-pagination="true"
                        data-side-pagination="server" data-locale="zh-CN" data-click-to-select="true"
                        data-url="<?= $this->Url->build(['controller'=>'Goods','action'=>'attributelist']); ?>"
                        data-unique-id="id">
                            <thead>
                                <tr>
                                    <th data-checkbox="true">
                                    </th>
                                    <th data-field="id" data-sortable="true">
                                        Id
                                    </th>
                                    <th data-field="attribute_name">
                                        名称
                                    </th>
                                    <th data-field="attribute_className">
                                        厂商区域
                                    </th>
                                    <!-- <th data-field="id" data-formatter="toDoAttribute">操作</th> -->
                                    <th data-field="create_time" data-formatter=timestrap2date>
                                        创建时间
                                    </th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" id="account_submit" class="btn btn-primary">提交</button>
                    <!--<a type="button" href="<?php echo $this->Url->build(array('controller' => 'Goods','action'=>'index')); ?>" class="btn btn-danger">返回</a>-->
                    <a type="button" onclick="window.history.go(-1)" class="btn btn-danger">返回</a>
                </div>
            </div>
        </div>
    </div>
    </form>
</div>

<div class="modal fade" id="add-modal" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">规格新增</h4>
      </div>
      <div class="modal-body">
            <form class="form-horizontal">
            <div class="form-group">
                <label class="col-sm-2 control-label">名 称</label>
                <div class="col-sm-8">
                    <input type="text" id="addedit_spec_name" name="spec_name" class="form-control">
                    <input type="hidden" id="addedit_is_display" name="is_display" class="form-control">
                    <input type="hidden" id="addedit_is_need" name="is_need" class="form-control">
                    <input type="hidden" id="addedit_tr_index" name="tr_index" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">代 码</label>
                <div class="col-sm-8">
                  <input type="text" id="addedit_spec_code" name="spec_code" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">规格值</label>
                <div class="col-sm-8">
                  <input type="text" id="addedit_spec_value" name="spec_value" class="form-control">
                </div>
            </div>
            </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="submit-spec" value="">确定</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">取消</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="del-spec-modal" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">删除规格</h4>
      </div>
      <div class="modal-body">
           <i class="icon-question-sign text-primary"></i>&nbsp;&nbsp;确认要删除该规格么？<span class="text-primary" id="sure"></span>？
           <input type="hidden" id="del_is_display" name="is_display" class="form-control">
           <input type="hidden" id="del_is_need" name="is_need" class="form-control">
           <input type="hidden" id="del_tr_index" name="tr_index" class="form-control">
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="submit-del-spec" value="">确定</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">取消</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="char-manage" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">
                        ×
                    </span>
                </button>
                <h5 class="modal-title">
                    配置计费方式
                </h5>
            </div>
            <div class="modal-body">
                <table id="image-table" data-toggle="table" data-locale="zh-CN" data-click-to-select="true"
                data-url="<?php echo $this->Url->build(array('controller'=>'Goods','action'=>'getBillCycle'));?>"
                data-unique-id="id">
                    <thead>
                        <tr>
                            <th data-checkbox="true">
                            </th>
                            <th data-field="id" data-sortable="true">
                                Id
                            </th>
                            <th data-field="name">
                                计费周期
                            </th>
                        </tr>
                    </thead>
                </table>
            </div>
            <div class="modal-footer">
                <button onclick="setValue()" type="button" class="btn btn-primary">
                    确认
                </button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">
                    取消
                </button>
            </div>
        </div>
    </div>
</div>

<?=$this->Html->script(['adminjs.js','validator.bootstrap.js']); ?>
<?= $this->start('script_last'); ?>
<?php echo $this->Html->script(['jquery.uploadify.min.js','bootstrap-table.js']); ?>
<script type="text/javascript">
function formatter_is_true(value){
        if(value=null){
            return '-';
        }else{
            if(value==0){
                return '否';
            }else{
                return '是';
            }
        }
    }
    //时间戳转换日期格式
  function timestrap2date(value){
    var now = new Date(parseInt(value) * 1000);
    return now.toLocaleString().replace(/年|月/g, "-").replace(/日/g, " ");
  }

function setValue(){
    var tot;
    var val="";
    tot = $('#image-table').bootstrapTable('getSelections');
        $.each(tot, function(i, n) {
            // console.log(n.set_id);
            val+= n.id + ",";
        });
     $("#user_chargings").val(val);
    $('#char-manage').modal("hide");
}

    function showSet(){
        $("#image-table").bootstrapTable("checkBy", {field:"id", values:eval("["+$("#user_chargings").val()+"]")});
        $('#char-manage').modal("show");
    }
    $("#userfile").fileinput({
        uploadUrl: "<?php echo $this->Url->build(array('controller'=>'Goods','action'=>'image'));?>",
        uploadAsync: false,
        showPreview: true,
        allowedFileExtensions: ['jpg', 'png', 'gif'],
        maxFileCount: 5,
    });

    var ue = UE.getEditor('detail');

    $(function(){
        $('#myTab a').click(function (e) {
          if(e.target.hash=="#version"){
            $("#table").bootstrapTable("checkBy", {field:"id", values:eval("["+$("#goods_txt_buytype").val()+"]")});
          }
          e.preventDefault();//阻止a链接的跳转行为
          $(this).tab('show');//显示当前选中的链接及关联的content
        });
        var validator = $('#goods-form').bootstrapValidator().data('bootstrapValidator');
        $('a[data-toggle="tab"]').on('hide.bs.tab', function (e) {
            validator.validate();
            if (!validator.isValid()) {
                return false;
            }else{
                $('#account_submit').removeAttr('disabled');
            }
        });

        $('#table').on('check.bs.table', function (row, e) {
            var txt = $("#goods_txt_buytype").val();
            txt+=e.id+",";
            $("#goods_txt_buytype").val(txt);
        });
        $('#table').on('uncheck.bs.table', function (row, e) {
            var txt = $("#goods_txt_buytype").val("");
            var tot = $('#table').bootstrapTable('getSelections');
            var val = "";
            if(tot.length!=0){
                $.each(tot, function(i, n) {
                    val+=n.id+",";
                });
                $("#goods_txt_buytype").val(val);
            }
        });
        $('#image-table').on('check.bs.table', function (row, e) {
            var txt = $("#user_chargings").val();
            txt+=e.id+",";
            $("#user_chargings").val(txt);
        });
        $('#image-table').on('uncheck.bs.table', function (row, e) {
            var txt = $("#user_chargings").val("");
            var tot = $('#image-table').bootstrapTable('getSelections');
            var val = "";
            if(tot.length!=0){
                $.each(tot, function(i, n) {
                    val+=n.id+",";
                });
                $("#user_chargings").val(val);
            }
        });
        // console.log($("#user_chargings").val());
        // $("#image-table").bootstrapTable("checkBy", {field:"id", values:eval("["+$("#user_chargings").val()+"]")});

        $("#user_chargings").val("<?= $acc[0]["user_chargings"] ?>");
        if("<?= $acc[0]["goods_vpc"] ?>"!=null&&"<?= $acc[0]["goods_vpc"] ?>"!=""){
             $("#goods_txt_buytype").val("<?= $acc[0]["goods_vpc"] ?>");
             $("#goods_vpc").val("<?= $acc[0]["goods_vpc"] ?>");
             $("#goods_buytype").val("1");
         }
         if("<?= $acc[0]["attribute_ids"] ?>"!=null&&"<?= $acc[0]["attribute_ids"] ?>"!=""){
            // console.log("<?= $acc[0]["attribute_ids"] ?>");
             $("#goods_txt_buytype").val("<?= $acc[0]["attribute_ids"] ?>");
             $("#goods_buytype").val("2");
         }
         if(parseInt($("#goods_buytype").val())==1){
            $("#div_version").css("display","none");
            $("#div_vpc").css("display","block");
            $("#div_vpc2").css("display","block");
        }else if(parseInt($("#goods_buytype").val())==2){
            $("#div_version").css("display","block");
            $("#div_vpc").css("display","none");
            $("#div_vpc2").css("display","none");
        }
    });
    function goods_vpcs(){
        if(parseInt($("#goods_vpc").val())!=0){
            $("#goods_txt_buytype").val($("#goods_vpc").val());
        }
    }
    function vpc_version(){
        $("#goods_txt_buytype").val("");
        if(parseInt($("#goods_buytype").val())==1){
            $("#div_version").css("display","none");
            $("#div_vpc").css("display","block");
            $("#div_vpc2").css("display","block");
        }else if(parseInt($("#goods_buytype").val())==2){
            $("#div_version").css("display","block");
            $("#div_vpc").css("display","none");
            $("#div_vpc2").css("display","none");
            $("#table").bootstrapTable("checkBy", {field:"id", values:eval("["+$("#goods_txt_buytype").val()+"]")})
        }
    }
    $('#goods-form').bootstrapValidator({
        submitButtons: 'button[type="submit"]',
        submitHandler: function(validator, form, submitButton){
            var displaySpecName='';
            var displaySpecCode='';
            var displaySpecValue='';
            var needSpecName='';
            var needSpecCode='';
            var needSpecValue='';

            var isNullDName='';
            var isNullDCode='';
            var isNullDValue='';
            var isNullNName='';
            var isNullNCode='';
            var isNullNValue='';
            var isTrue = 0;

            $.each($('#display_tbody').children(),function(i,n){
                $.each($(n).children(),function(tdI,tdN){
                    if (tdI==0){
                        if($(tdN).html()){
                            displaySpecName +=$(tdN).html()+',';
                        }else{
                            isNullDName = 1;
                            isTrue = 1;
                        }
                    }else if(tdI == 2){
                        if($(tdN).html()){
                            displaySpecValue +=$(tdN).html()+',';
                        }else{
                            isNullDValue = 1;
                            isTrue = 1;
                        }
                    }else if(tdI == 1){
                        displaySpecCode +=$(tdN).html()+',';
                    }
                })
            })
            $.each($('#need_tbody').children(),function(i,n){
                $.each($(n).children(),function(needTdI,needTdN){
                    switch (needTdI){
                        case 0:
                            if($(needTdN).html()){
                                needSpecName +=$(needTdN).html()+',';
                            }else{
                               isNullNName = 1;
                            isTrue = 1;
                            }
                        break;
                        case 2:
                            if($(needTdN).html()){
                                needSpecValue +=$(needTdN).html()+',';
                            }else{
                                isNullNValue = 1;
                            isTrue = 1;
                            }
                        break;
                        case 1:
                            if($(needTdN).html()){
                                needSpecCode +=$(needTdN).html()+',';
                            }else{
                                isNullNCode = 1;
                            isTrue = 1;
                            }
                        break;
                    }
                })
            })
            if(isNullDName ==1){
                alert('展示规格名不能为空')
            }else if(isNullDValue ==1){
                alert('展示规格值不能为空')
            }

            if(isNullNName ==1){
                alert('接口规格名不能为空')
            }else if(isNullNValue ==1){
                alert('接口规格值不能为空')
            }else if(isNullNCode ==1){
                alert('接口规格代码不能为空')
            }

            if(isTrue == 0){
                $('#display_spec_name').val(displaySpecName);
                $('#display_spec_value').val(displaySpecValue);
                $('#display_spec_code').val(displaySpecCode);
                $('#need_spec_name').val(needSpecName);
                $('#need_spec_value').val(needSpecValue);
                $('#need_spec_code').val(needSpecCode);
                $.post(form.attr('action'), form.serialize(), function(data){
                    var data = eval('(' + data + ')');
                    if(data.code==0){
                        tentionHide(data.msg,0);
                        location.href='<?php echo $this->Url->build(array('controller'=>'Goods','action'=>'index'));?>';
                    }else{
                        tentionHide(data.msg,1);
                    }
                });
            }
        },
        fields : {
            name: {
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '商品名称不能为空'
                    },
                    stringLength: {
                        min: 1,
                        max: 32,
                        message: '请保持在1-32位'
                    }
                }
            },
            sn: {
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '商品序列号不能为空'
                    },
                    stringLength: {
                        min: 1,
                        max: 30,
                        message: '请保持在1-30位'
                    },
                    regexp: {
                        regexp: /^[^\u4e00-\u9fa5\s]{0,}$/,
                        message: '商品序列号中不能有空格和中文'
                    }
                }
            },
            price: {
                group: '.col-sm-6',
                validators: {
                    regexp: {
                        regexp: /^\d+(\.\d{1,2})?$/,
                        message: '请填写正确的商品价格，小数点后只保留两位'
                    }
                }
            },
            hot: {
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '请选择是否热销'
                    }
                }
            },
           /* is_auto: {
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '请选择是否自动创建'
                    }
                }
            },
            is_console: {
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '请选择是否跳转资源中心'
                    }
                }
            },*/
            star: {
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '推荐指数不能为空'
                    },
                    between: {
                        min: 0,
                        max: 5,
                        message: '推荐指数只能在0-5'
                    },
                    regexp: {
                        regexp: /^([1-9]\d*|0)$/,
                        message: '请输入整数'
                    }
                }
            },
            goods_charging_ways: {
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '请选择计费'
                    },
                }
            },
        }
    });

    function addSpec(display,need){
        $('#addedit_spec_code').val('');
        $('#addedit_spec_value').val('');
        $('#addedit_spec_name').val('');
        $('#addedit_tr_index').val('');
        $('#addedit_is_display').val(display);
        $('#addedit_is_need').val(need);

        $('#add-modal').modal('show');
    }

    function editSpec(display,need,event){
        var trIndex =$(event).parent().parent().index();

        // console.log($("#display_tbody tr").eq(trIndex))
        $('#addedit_tr_index').val(trIndex);
        $.each($(event).parent().parent().children(),function(i,n){
            switch (i){
                case 0:
                    $('#addedit_spec_name').val($(n).html());
                break;
                case 2:
                $('#addedit_spec_value').val($(n).html());
                break;
                case 1:
                    $('#addedit_spec_code').val($(n).html());
                break;
            }
        })
        $('#addedit_is_display').val(display);
        $('#addedit_is_need').val(need);

        $('#add-modal').modal('show');
    }

    function delSpec(display,need,event){
        var trIndex =$(event).parent().parent().index();
        $('#del_tr_index').val(trIndex);
        console.log($(event).parent().parent().children())
        $.each($(event).parent().parent().children(),function(i,n){
            switch (i){
                case 0:
                    $('#sure').html($(n).html());
                break;
            }
        })
        $('#del_is_display').val(display);
        $('#del_is_need').val(need);

        $('#del-spec-modal').modal('show');
    }

    $('#submit-spec').on('click',function(){
        var is_display = $('#addedit_is_display').val();
        var is_need = $('#addedit_is_need').val();
        if($('#addedit_tr_index').val() ==""){
            var html = '<tr>';
            html += '<td>'+$('#addedit_spec_name').val()+'</td>';
            html += '<td>'+$('#addedit_spec_code').val()+'</td>';
            html += '<td>'+$('#addedit_spec_value').val()+'</td>';
            html += '<td><a href="#" onclick="editSpec('+is_display+','+is_need+',this)">修改</a>|<a href="#" onclick="delSpec('+is_display+','+is_need+',this)">删除</a></td>';
            html += '</tr>';

            if(is_display == 1){
                $(html).prependTo('#display_tbody');
            }else if(is_need == 1){
                $(html).prependTo('#need_tbody');
            }
        }else{
            var trIndex =$('#addedit_tr_index').val();

            if(is_display == 1){
                var trHtml = $("#display_tbody tr").eq(trIndex);
            }else if(is_need == 1){
                var trHtml = $("#need_tbody tr").eq(trIndex);
            }

            $.each($(trHtml).children(),function(i,n){
            switch (i){
                case 0:
                    $(n).html($('#addedit_spec_name').val());
                break;
                case 2:
                    $(n).html($('#addedit_spec_value').val());
                break;
                case 1:
                     $(n).html($('#addedit_spec_code').val());
                break;
            }
        })
        }
        $('#add-modal').modal('hide');
        $('#account_submit').removeAttr('disabled');
    })

    $('#submit-del-spec').on('click',function(){
        var trIndex =$('#del_tr_index').val();
        var is_display = $('#del_is_display').val();
        var is_need = $('#del_is_need').val();

        if(is_display == 1){
            var trHtml = $("#display_tbody tr").eq(trIndex);
        }else if(is_need == 1){
            var trHtml = $("#need_tbody tr").eq(trIndex);
        }
        $(trHtml).remove();
        $('#del-spec-modal').modal('hide');
        $('#account_submit').removeAttr('disabled');
    })

    function changeSpecGroup(id,display,need){
        if(id!=0){
            $.ajax({
                type: "POST",
                url: '<?php echo $this->Url->build(array('controller'=>'Goods','action'=>'check_spec'));?>',
                data: {'id': id},
                success: function (data) {
                    datas = $.parseJSON(data);
                    if(datas){
                        var html = '';
                        $.each( datas, function(i, n){
                            if(display == 1){
                                if(n.is_display == 1){
                                    if(n.spec_name){
                                        if (n.spec_code==null) {n.spec_code='';}
                                        html += '<tr>';
                                        html += '<td>'+n.spec_name+'</td>';
                                        html += '<td>'+n.spec_code+'</td>';
                                        html += '<td></td>';
                                        html += '<td><a href="#" onclick="editSpec('+display+','+need+',this)">修改</a>|<a href="#" onclick="delSpec('+display+','+need+',this)">删除</a></td>';
                                        html += '</tr>';
                                    }
                                }
                            }else if(need == 1){
                                if(n.is_need == 1){
                                    if(n.spec_name){
                                        if (n.spec_code==null) {n.spec_code='';}
                                        html += '<tr>';
                                        html += '<td>'+n.spec_name+'</td>';
                                        html += '<td>'+n.spec_code+'</td>';
                                        html += '<td></td>';
                                        html += '<td><a href="#" onclick="editSpec('+display+','+need+',this)">修改</a>|<a href="#" onclick="delSpec('+display+','+need+',this)">删除</a></td>';
                                        html += '</tr>';
                                    }
                                }
                            }

                        });
                    }
                    if(display == 1){
                        $('#display_tbody').html(html);
                    }else if(need == 1){
                        $('#need_tbody').html(html);
                    }
                }
            });
        }
    }

</script>
<?= $this->end() ?>