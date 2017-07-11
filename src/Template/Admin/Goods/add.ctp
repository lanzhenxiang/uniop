<?= $this->element('content_header'); ?>
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
    <div id="maindiv-alert">
    </div>
    <form class="form-horizontal" enctype="multipart/form-data" id="goods-form"
    action="<?php echo $this->Url->build(array('controller'=>'Goods','action'=>'editadd'));?>"
    method="post">
        <div>
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active">
                    <a href="#home" aria-controls="home" role="tab" data-toggle="tab">
                        基本信息
                    </a>
                </li>

                <!--<li role="presentation">-->
                    <!--<a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">-->
                        <!--商品详细描述-->
                    <!--</a>-->
                <!--</li>-->

                <li role="presentation">
                    <a href="#spec_display" aria-controls="messages" role="tab" data-toggle="tab">
                        商品展示规格
                    </a>
                </li>
                <li role="presentation">
                    <a href="#spec_need" aria-controls="messages" role="tab" data-toggle="tab">
                        商品接口规格
                    </a>
                </li>
                <li role="presentation">
                    <a href="#images" aria-controls="images" role="tab" data-toggle="tab">
                        图片上传
                    </a>
                </li>
                <li role="presentation">
                    <a href="#version" aria-controls="version" role="tab" data-toggle="tab">
                        VPC
                    </a>
                </li>
            </ul>
            <!-- Tab panes -->
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="home">
                    <div class="form-group">
                        <label for="name" class="col-sm-2 control-label">
                            商品名称
                        </label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="name" id="name" placeholder="商品名称">
                        </div>
                    </div>
                    <input type="hidden" name="goods_txt_buytype" id="goods_txt_buytype">
                    <div class="form-group">
                        <label for="sn" class="col-sm-2 control-label">
                            商品序列号
                        </label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="sn" id="sn" placeholder="商品序列号">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="description" class="col-sm-2 control-label">
                            产品描述
                        </label>
                        <div class="col-sm-6">
                            <textarea type="text" class="form-control" rows="4" name="description"
                            id="description" placeholder="产品描述 ">
                            </textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="price" class="col-sm-2 control-label">
                            价格
                        </label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="price" id="price" placeholder="价格">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="category" class="col-sm-2 control-label">
                            商品分类
                        </label>
                        <div class="col-sm-6">
                            <select id="category" name="category_id" class="form-control">
                                <?php foreach ($data as $key=>
                                    $value) { ?>
                                    <option value="<?php echo $key;?>">
                                        <span>
                                            <?php echo $value;?>
                                        </span>
                                    </option>
                                    <?php } ?>
                            </select>
                        </div>
                    </div>
                    <!-- <div class="form-group">
                    <label for="private_attrs" class="col-sm-2 control-label">商品私有属性</label>
                    <div class="col-sm-6">
                    <input type="text" class="form-control" name="private_attrs" id="private_attrs" placeholder="商品私有属性">
                    </div>
                    <label class="control-label text-danger"><i class="icon-asterisk" id="siyou"></i></label>
                    </div> -->
                    <div class="form-group">
                        <label for="star" class="col-sm-2 control-label">
                            推荐指数
                        </label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="star" id="star" placeholder="推荐指数">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputPassword3" class="col-sm-2 control-label">
                            是否热销
                        </label>
                        <div class="col-sm-6">
                            <label class="radio-inline">
                                <input type="radio" name="hot" value="1">
                                热销
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="hot" value="0">
                                不热销
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="icon" class="col-sm-2 control-label">
                            首页商品图标
                        </label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="icon" id="icon" placeholder="首页商品图标">
                            </input>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="mini_icon" class="col-sm-2 control-label">
                            商品订单图标
                        </label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="mini_icon" id="mini_icon"
                            placeholder="商品订单图标">
                            </input>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="picture" class="col-sm-2 control-label">
                            商品详情图片
                        </label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="picture" id="picture" placeholder="商品详情图片">
                            </input>
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
                                                <option value="<?= $key ?>"><?= $value ?></option>
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
                        <?php echo $this->
                            Html->script('ueditor.config.js'); ?>
                            <?php echo $this->
                                Html->script('ueditor.all.js'); ?>
                                <script id="detail" name="detail" type="text/plain" style="width:1024px;height:500px;">
                                </script>
                                <div style="height:30px">
                                </div>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="spec_display">
                    <div class="clearfix">
                        <button class="btn btn-default" type="button" style="vertical-align:top;"
                        data-toggle="modal" onclick="addSpec(1,0)">
                            <span class="glyphicon glyphicon-plus" aria-hidden="true">
                                新增
                            </span>
                        </button>
                        <select style="width:240px;margin-left:10px;" name="group_id" class="form-control"
                        onchange="changeSpecGroup(this.value,1,0)">
                            <?php foreach ($query as $key=>
                                $value) { ?>
                                <option value="<?php echo $value->group_id;?>">
                                    <span>
                                        <?php echo $value->
                                            group_name;?>
                                    </span>
                                </option>
                                <?php } ?>
                        </select>
                    </div>
                    <div>
                        <input type="hidden" name="display_spec_name" id="display_spec_name">
                        </input>
                        <input type="hidden" name="display_spec_value" id="display_spec_value">
                        </input>
                        <input type="hidden" name="display_spec_code" id="display_spec_code">
                        </input>
                        <table>
                            <thead>
                                <tr>
                                    <th>
                                        名 称
                                    </th>
                                    <th>
                                        代 码
                                    </th>
                                    <th>
                                        规格值
                                    </th>
                                    <th>
                                        操 作
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="display_tbody">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="spec_need">
                    <div class="clearfix">
                        <button class="btn btn-default" type="button" style="vertical-align:top;"
                        data-toggle="modal" onclick="addSpec(0,1)">
                            <span class="glyphicon glyphicon-plus" aria-hidden="true">
                                新增
                            </span>
                        </button>
                        <select style="width:240px;margin-left:10px;" id="group_id" name="group_id"
                        class="form-control" onchange="changeSpecGroup(this.value,0,1)">
                            <?php foreach ($query as $key=>
                                $value) { ?>
                                <option value="<?php echo $value->group_id;?>">
                                    <span>
                                        <?php echo $value->
                                            group_name;?>
                                    </span>
                                </option>
                                <?php } ?>
                        </select>
                    </div>
                    <div>
                        <input type="hidden" name="need_spec_name" id="need_spec_name">
                        </input>
                        <input type="hidden" name="need_spec_value" id="need_spec_value">
                        </input>
                        <input type="hidden" name="need_spec_code" id="need_spec_code">
                        </input>
                        <table>
                            <thead>
                                <tr>
                                    <th>
                                        名 称
                                    </th>
                                    <th>
                                        代 码
                                    </th>
                                    <th>
                                        规格值
                                    </th>
                                    <th>
                                        操 作
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="need_tbody">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="images">
                    <div class="form-group">
                        <label for="software_name" class="col-sm-2 control-label">
                            上传图片
                        </label>
                        <div id="up" class="col-sm-6">
                            <!-- <label>上传图片</label> -->
                            <input type="file" accept="image/*" name="upfile[]" multiple id="userfile"
                            class="file">
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
                            <option value="<?php echo $_flow_v['flow_id'];?>">
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
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" id="account_submit" class="btn btn-primary">
                        提交
                    </button>
                    <a type="button" href="<?php echo $this->Url->build(array('controller' => 'Goods','action'=>'index')); ?>"
                    class="btn btn-danger">
                        返回
                    </a>
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
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">
                        &times;
                    </span>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    规格新增
                </h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">
                            名 称
                        </label>
                        <div class="col-sm-8">
                            <input type="text" id="addedit_spec_name" name="spec_name" class="form-control">
                            <input type="hidden" id="addedit_is_display" name="is_display" class="form-control">
                            <input type="hidden" id="addedit_is_need" name="is_need" class="form-control">
                            <input type="hidden" id="addedit_tr_index" name="tr_index" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">
                            代 码
                        </label>
                        <div class="col-sm-8">
                            <input type="text" id="addedit_spec_code" name="spec_code" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">
                            规格值
                        </label>
                        <div class="col-sm-8">
                            <input type="text" id="addedit_spec_value" name="spec_value" class="form-control">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="submit-spec" value="">
                    确定
                </button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">
                    取消
                </button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="del-spec-modal" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">
                        &times;
                    </span>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    删除规格
                </h4>
            </div>
            <div class="modal-body">
                <i class="icon-question-sign text-primary">
                </i>
                &nbsp;&nbsp;确认要删除该规格么？
                <span class="text-primary" id="sure">
                </span>
                ？
                <input type="hidden" id="del_is_display" name="is_display" class="form-control">
                <input type="hidden" id="del_is_need" name="is_need" class="form-control">
                <input type="hidden" id="del_tr_index" name="tr_index" class="form-control">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="submit-del-spec" value="">
                    确定
                </button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">
                    取消
                </button>
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
<?php echo $this->Html->script('jquery.uploadify.min.js'); ?>
<script type="text/javascript">

    $(function(){
        vpc_version();
        var validator = $('#goods-form').bootstrapValidator().data('bootstrapValidator');

        $('a[data-toggle="tab"]').on('hide.bs.tab', function (e) {
            validator.validate();
            if (!validator.isValid()) {
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

    });
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
        $('#char-manage').modal("show");
    }
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
        }else{
            $("#div_version").css("display","none");
            $("#div_vpc").css("display","block");
            $("#div_vpc2").css("display","block");
        }
    }
//时间戳转换日期格式
  function timestrap2date(value){
    var now = new Date(parseInt(value) * 1000);
    return now.toLocaleString().replace(/年|月/g, "-").replace(/日/g, " ");
  }
    $("#userfile").fileinput({
        uploadUrl: "<?php echo $this->Url->build(array('controller'=>'Goods','action'=>'image'));?>",
        uploadAsync: false,
        showPreview: true,
        allowedFileExtensions: ['jpg', 'png', 'gif'],
        maxFileCount: 5,
    });
    //加载图片上传样式
    $("<link>")
    .attr({ rel: "stylesheet",
        type: "text/css",
        href: "/css/uploadify.css"
    })
    .appendTo("head");


    //图片相册上传功能
    $(function() {
        $('#upfile').uploadify({
            'buttonText'   : '选择图片上传',
            'multi'    : true,
            'fileObjName':'upfile',
            'debug': false,
            'auto'     : true,
            'method': 'post',
            'uploader' :"<?php echo $this->Url->build(array('controller'=>'Goods','action'=>'image'));?>",
            'fileTypeDesc': 'XML',
            /*  'simUploadLimit'    : 1,*/
            'fileTypeExts': '*.gif; *.jpg; *.png',
            'width': 100,
            'height': 20,
            'swf'      : '/js/uploadify.swf',
            'onUploadError' : function(file, errorCode, errorMsg, errorString) {
                alert('The file ' + file.name + ' could not be uploaded: ' + errorString);
            },

            'onUploadSuccess': function (file, data, response) {
                // if (data != '') {
                   // console.log(data);
                    // var data = eval('(' + data + ')');
                    // console.log(data);
                    // var ace = '<div style="position:relative;width:200px;height:200px;display:inline-block;margin-left: 75px;"><img style="width:200px;height:200px;" src="/images/'+ data.url+'" /></div>';

                    // $("#up").append(ace);
                    // $(".pic-close").on("click",function(){
                    //     $(this).parent().remove();
                    // });
                // }
            }
            //data就是服务器输出的内容。
        });
    })

    var ue = UE.getEditor('detail');
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
            html += '<td>'+$('#addedit_spec_value').val()+'</td>';
            html += '<td>'+$('#addedit_spec_code').val()+'</td>';
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