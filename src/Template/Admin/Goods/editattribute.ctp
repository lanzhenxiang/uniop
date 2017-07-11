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
</style>
<div class="content-body clearfix">
   <div id="maindiv-alert"></div>
   <form class="form-horizontal" enctype="multipart/form-data" id="goods-form" action="<?php echo $this->Url->build(array('controller'=>'Goods','action'=>'editattribute',$table->id));?>" method="post">
    <div>
        <input type="hidden"  name="attribute_id"  id="attribute_id" value="<?= $table->id ?>">
        <input type="hidden"  name="attributedetail_id"  id="attributedetail_id" value="<?= $table->goods_attribute_detail['id'] ?>">
        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active" ><a href="#spec_need " aria-controls="messages" role="tab" data-toggle="tab">工作流配置</a></li>
            <li role="presentation" ><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">广告详细</a></li>

        </ul>
        <!-- Tab panes -->
        <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="spec_need">
        <div class="form-group">
                    <label for="name" class="col-sm-2 control-label">版本名称</label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" name="attribute_name" id="attribute_name" value="<?= $table->attribute_name ?>" placeholder="版本名称">
                    </div>
                </div>
                <div class="form-group">
                    <label for="name" class="col-sm-2 control-label">地区</label>
                    <div class="col-sm-6">
                        <select id="attribute_region" name="attribute_region" onchange="getVpcByRegion();" class="form-control">
                            <option value="">请选择</option>
                            <?php foreach ($agent as $value) {   ?>
                                <?php if($value["parentid"]!=0){ ?>
                                    <option value="<?php echo $value['region_code'];?>" class_code="<?php echo $value['class_code'];?>">
                                    <span><?php echo $value['display_name'];?></span>
                                </option>
                                <?php } ?>
                            <?php }   ?>
                        </select>
                    </div>
                </div>

            <div class="form-group">
                    <label for="name" class="col-sm-2 control-label">资源Tag</label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" name="name" id="name" value="<?= $table->goods_attribute_detail['instance_name'] ?>" placeholder="设备创建名称">
                    </div>
                </div>
                     <div class="form-group">
                    <label for="departments" class="col-sm-2 control-label">售卖租户</label>
                    <div class="col-sm-6">
                        <select id="departments" name="departments" class="form-control" onchange="getVpcByDepartment();">
                            <option value="">请选择</option>
                            <option value="0">所有租户</option>
                            <?php foreach ($departments as $value) {   ?>
                                <option value="<?php echo $value['id'];?>">
                                    <span><?php echo $value['name'];?></span>
                                </option>
                            <?php }   ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="product_type" class="col-sm-2 control-label">产品类型</label>
                    <div class="col-sm-6">
                        <select id="product_type" name="product_type" onchange="getSetByType();" class="form-control">
                            <option value="0">云主机</option>
                            <option value="1">云桌面</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="instance_vpc" class="col-sm-2 control-label">所在VPC</label>
                    <div class="col-sm-6">
                        <select id="instance_vpc" onchange="getSubnetByVpc();" name="instance_vpc" class="form-control">
                            <option value="">请选择</option>
                            <?php foreach ($instance_vpc as $value) {   ?>
                                <option value="<?php echo $value['code'];?>">
                                    <span><?php echo $value['name'];?></span>
                                </option>
                            <?php }   ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="instance_subnet" class="col-sm-2 control-label">所在子网</label>
                    <div class="col-sm-6">
                        <select id="instance_subnet" name="instance_subnet" class="form-control">
                            <option value="">请选择</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="instance_set" id="lbl_set" class="col-sm-2 control-label">硬件套餐</label>
                    <input type="hidden" name="set_ids" id="set_ids" value="<?= $table->goods_attribute_detail["set_ids"]; ?>"/>
                    <div class="col-sm-6">
                        <select id="instance_set" name="instance_set" class="form-control">
                            <option value="">
                                请选择
                            </option><?php foreach ($instance_set as $value) {   ?>
                            <option value="<?php echo $value['set_code'];?>">
                                <?php echo $value['set_name'];?>
                            </option><?php }   ?>
                        </select>
                    </div><input type="button" onclick="showSet()" name="btnSet" value="配置可选套餐">
                </div>
                <div class="form-group" id="div_image">
                    <label for="instance_image" class="col-sm-2 control-label">系统镜像</label>
                    <input type="hidden" name="image_ids" id="image_ids" value="<?= $table->goods_attribute_detail["image_ids"]; ?>"/>
                    <div class="col-sm-6">
                        <select id="instance_image" name="instance_image" class="form-control">
                            <option value="">
                                请选择
                            </option><?php foreach ($instance_image as $value) {   ?>
                            <option value="<?php echo $value['image_code'];?>">
                                <?php echo $value['image_name'];?>
                            </option><?php }   ?>
                        </select>
                    </div><input type="button" onclick="showImage()" name="btnImage" value="配置可选镜像">
                </div>
                <div class="form-group">
                    <label for="instance_work" class="col-sm-2 control-label">带宽</label>
                    <div class="col-sm-6">
                        <input name="haveEip" id="haveEip" type="checkbox">是否售卖 <input style="width:50px;" type="text" value="5" name="txtbandwidth" id="txtbandwidth" placeholder="5"> Mbps<input name="updateEip" id="updateEip" type="checkbox">用户可调 带宽上限<input style="width:50px;" type="text" value="200" name="txtMaxbandwidth" id="txtMaxbandwidth" placeholder="200"> Mbps
                    </div>
                </div>
                <div class="form-group">
                    <label for="instance_work" class="col-sm-2 control-label">块存储</label>
                    <div class="col-sm-6">
                     <input name="haveMemory" id="haveMemory" type="checkbox">是否售卖 <input style="width:50px;" type="text" value="10" name="txtSize" id="txtSize" placeholder="10"> GB
                    </div>
                </div>
                <div class="form-group">
                    <label for="instance_work" class="col-sm-2 control-label">订单流程</label>
                    <div class="col-sm-6">
                        <select id="instance_work" name="instance_work" class="form-control">
                            <option value="">请选择</option>
                            <?php foreach ($instance_work as $value) {   ?>
                                <option value="<?php echo $value['flow_id'];?>">
                                    <span><?php echo $value['flow_name'];?></span>
                                </option>
                            <?php }   ?>
                        </select>
                    </div>
                </div>
                </div>
            <div role="tabpanel" class="tab-pane" id="profile">
                <div style="margin-top:15px;">
                    <?php echo $this->Html->script('ueditor.config.js'); ?>
                    <?php echo $this->Html->script('ueditor.all.js'); ?>
                    <script id="detail" name="detail" type="text/plain" style="width:1024px;height:500px;"><?= $table->goods_attribute_detail["attribute_detail"]; ?></script>
                    <div style="height:30px"></div>
                </div>
            </div>

        </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" id="account_submit" class="btn btn-primary">提交</button>
                    <a type="button" href="<?php echo $this->Url->build(array('controller' => 'Goods','action'=>'attribute')); ?>" class="btn btn-danger">返回</a>
                </div>
            </div>
        </div>
    </div>
</form>
</div>
<div class="modal fade" id="set-manage" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                        <h5 class="modal-title">
                            配置可选套餐
                        </h5>
                    </div>
                    <div class="modal-body">
                        <table style="display:none;" id="set-table-0" data-toggle="table" data-locale="zh-CN" data-click-to-select="true" data-url="<?php echo $this->Url->build(array('controller'=>'Goods','action'=>'getSetByType','0'));?>" data-unique-id="set_id">
                            <thead>
                                <tr>
                                    <th data-checkbox="true"></th>
                                    <th data-field="set_id" data-sortable="true">
                                        Id
                                    </th>
                                    <th data-field="set_name">
                                        套餐名称
                                    </th>
                                    <th data-field="set_code">
                                        套餐Code
                                    </th>
                                    <th data-field="cpu_number">
                                        CPU核数(GB)
                                    </th>
                                    <th data-field="memory_gb">
                                        内存大小(GB)
                                    </th>
                                    <th data-field="gpu_gb">
                                        GPU大小(GB)
                                    </th>
                                </tr>
                            </thead>
                        </table>
                        <table style="display:none;" id="set-table-1" data-toggle="table" data-locale="zh-CN" data-click-to-select="true" data-url="<?php echo $this->Url->build(array('controller'=>'Goods','action'=>'getSetByType','1'));?>" data-unique-id="set_id">
                            <thead>
                                <tr>
                                    <th data-checkbox="true"></th>
                                    <th data-field="set_id" data-sortable="true">
                                        Id
                                    </th>
                                    <th data-field="set_name">
                                        套餐名称
                                    </th>
                                    <th data-field="hardware_set">
                                        套餐Code
                                    </th>
                                    <th data-field="image_code">
                                        镜像Code
                                    </th>
                                    <th data-field="provider">
                                        品牌
                                    </th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button onclick="setValue(0)" type="button" class="btn btn-primary">确认</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">取消</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="image-manage" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                        <h5 class="modal-title">
                            配置可选镜像
                        </h5>
                    </div>
                    <div class="modal-body">
                        <table id="image-table" data-toggle="table" data-locale="zh-CN" data-click-to-select="true" data-url="<?php echo $this->Url->build(array('controller'=>'Goods','action'=>'getImage'));?>" data-unique-id="set_id">
                            <thead>
                                <tr>
                                    <th data-checkbox="true"></th>
                                    <th data-field="id" data-sortable="true">
                                        Id
                                    </th>
                                    <th data-field="image_name">
                                        镜像名称
                                    </th>
                                    <th data-field="image_code">
                                        镜像Code
                                    </th>
                                    <th data-field="plat_form">
                                        操作系统
                                    </th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button onclick="setValue(1)" type="button" class="btn btn-primary">确认</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">取消</button>
                    </div>
                </div>
            </div>
        </div>

<?=$this->Html->script(['adminjs.js','validator.bootstrap.js']); ?>
<?= $this->start('script_last'); ?>
<?php echo $this->Html->script('jquery.uploadify.min.js'); ?>
<script type="text/javascript">
 $(function() {
    var ue = UE.getEditor('detail');
    //初始化页面
    // var table =
     $("#attribute_region").val("<?= $table->attribute_regionCode ?>");
     $("#departments").val("<?= $table->goods_attribute_detail["sell_tenant"] ?>");
     if("<?= $table->goods_attribute_detail["method"] ?>"=="desktop_add"){
        $("#product_type").val("1");
     }else{
        $("#product_type").val("0");
     }
     getVpcByRegion();
//     getVpcByDepartment();
     $("#instance_vpc").val("<?= $table->goods_attribute_detail["vpcCode"] ?>");

     getVpcByDepartment();
     $("#instance_vpc").val("<?= $table->goods_attribute_detail["vpcCode"] ?>");

     getSubnetByVpc();
     $("#instance_subnet").val("<?= $table->goods_attribute_detail["subnetCode"] ?>");
     getSetByType();
     $("#instance_set").val("<?= $table->goods_attribute_detail["instanceTypeCode"] ?>");
     $("#instance_image").val("<?= $table->goods_attribute_detail["imageCode"] ?>");
     $("#instance_work").val("<?= $table->goods_attribute_detail["flow_id"] ?>");
     //eip ebs
     if("<?= $table->goods_attribute_detail["haveEip"] ?>"=="1"){
        $("#haveEip").attr("checked","checked");
        $("#txtbandwidth").val("<?= $table->goods_attribute_detail["bandWidth"] ?>")

     }
      if("<?= $table->goods_attribute_detail["haveMemory"] ?>"=="1"){
        $("#haveMemory").attr("checked","checked");
        $("#txtSize").val("<?= $table->goods_attribute_detail["size"] ?>")
     }
     if("<?= $table->goods_attribute_detail["updateEip"] ?>"=="1"){
        $("#updateEip").attr("checked","checked");
        $("#txtMaxbandwidth").val("<?= $table->goods_attribute_detail["bandMaxWidth"] ?>")
     }
     isShow();

 });
 function setValue(type){
    var tot;
    var val="";
    if (type==0) {
        if(parseInt($("#product_type").val())==0){
           tot = $('#set-table-0').bootstrapTable('getSelections');
        }else{
            tot = $('#set-table-1').bootstrapTable('getSelections');
        }
        $.each(tot, function(i, n) {
            console.log(n.set_id);
            val+= n.set_id + ",";
        });
    }else{
        tot = $('#image-table').bootstrapTable('getSelections');
        $.each(tot, function(i, n) {
            console.log(n.set_id);
            val+= n.id + ",";
        });
    }
    //0 套餐,//1镜像
    if(type==0){
        $("#set_ids").val(val);
    }else{
        $("#image_ids").val(val);
    }
    $('#set-manage').modal("hide");
    $('#image-manage').modal("hide");
}

function showSet(){
    isShow();
    if(parseInt($("#product_type").val())==0){
        $("#set-table-0").bootstrapTable("checkBy", {field:"set_id", values:eval("["+$("#set_ids").val()+"]")})
    }else{
        $("#set-table-1").bootstrapTable("checkBy", {field:"set_id", values:eval("["+$("#set_ids").val()+"]")})
    }
    $('#set-manage').modal("show");
}
function isShow(){
        //隐藏显示
        if(parseInt($("#product_type").val())==0){
            $("#set-table-1").css('display','none');
            $("#set-table-0").css('display','block');

        }else{
            $("#set-table-0").css('display','none');
            $("#set-table-1").css('display','block');
        }
    }
function showImage(){
    $("#image-table").bootstrapTable("checkBy", {field:"id", values:eval("["+$("#image_ids").val()+"]")})
    $('#image-manage').modal("show");
}


 function getVpcByDepartment(){
     var html="<option value=\"\">请选择</option>";
     $.ajax({
         type: "post",
         url: "<?php echo $this->Url->build(array('controller'=>'Goods','action'=>'getVpcByDepartment'));?>",
         async: false,
         data: {
             department_id:$("#departments").val(),
             location_code:$("#attribute_region").find("option:selected").attr("class_code")
         },
         success: function(data) {
             var data = eval('(' + data + ')');
             $.each(data,function(i,n){
                 console.log(n);
                 html+="<option value=\""+n.code+"\"><span>"+n.name+"</span></option>";
             });
         }
     });
     console.log(html);
     $("#instance_vpc").html(html);

 }


function getVpcByRegion(){
    var html="<option value=\"\">请选择</option>";
        $.ajax({
                    type: "post",
                    url: "<?php echo $this->Url->build(array('controller'=>'Goods','action'=>'getVpcByRegion'));?>",
                    async: false,
                    data: {
                        location_code:$("#attribute_region").find("option:selected").attr("class_code")
                    },
                    success: function(data) {
                        var data = eval('(' + data + ')');
                        $.each(data,function(i,n){
                            console.log(n);
                             html+="<option value=\""+n.code+"\"><span>"+n.name+"</span></option>";
                        });
                    }
            });
        console.log(html);
        $("#instance_vpc").html(html);
}

function getSetByType(){
        if($("#product_type").val()=="1"){
        // $("#set-table").html('');
            $("#lbl_set").html("非编套餐");
            $("#div_image").css('display','none');
        }else{
            $("#lbl_set").html("硬件套餐");
            $("#div_image").css('display','block');
        }
    var html="<option value=\"\">请选择</option>";
        $.ajax({
                    type: "post",
                    url: "<?php echo $this->Url->build(array('controller'=>'Goods','action'=>'getSetByType'));?>",
                    async: false,
                    data: {
                        type:$("#product_type").val()
                    },
                    success: function(data) {
                        var data = eval('(' + data + ')');
                        $.each(data,function(i,n){
                            console.log(n);
                            if(parseInt($("#product_type").val())=="0"){
                                html+="<option value=\""+n.set_code+"\"><span>"+n.set_name+"</span></option>";
                            }else{
                                html+="<option value=\""+n.hardware_set+"\"><span>"+n.set_name+"</span></option>";
                            }

                        });
                    }
            });
        console.log(html);
        $("#instance_set").html(html);
}
function getSubnetByVpc(){
        var html="<option value=\"\">请选择</option>";
        $.ajax({
                    type: "post",
                    url: "<?php echo $this->Url->build(array('controller'=>'Goods','action'=>'getSubnetByVpc'));?>",
                    async: false,
                    data: {
                        vpc:$("#instance_vpc").val()
                    },
                    success: function(data) {
                        var data = eval('(' + data + ')');
                        $.each(data,function(i,n){
                            html+="<option value=\""+n.code+"\"><span>"+n.name+"</span></option>";
                        });
                    }
            });
        $("#instance_subnet").html(html);
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
            }
            //data就是服务器输出的内容。
        });
    })




    $(function(){
        var validator = $('#goods-form').bootstrapValidator().data('bootstrapValidator');
        $('a[data-toggle="tab"]').on('hide.bs.tab', function (e) {
            validator.validate();
            if (!validator.isValid()) {
                return false;
            }else{
                $('#account_submit').removeAttr('disabled');
            }
        })
    });

    $('#goods-form').bootstrapValidator({
        submitButtons: 'button[type="submit"]',
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
            departments: {
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '请选择租户'
                    },
                }
            },
            product_type: {
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '请选择类型'
                    },
                }
            },
            instance_vpc: {
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '请选择VPC'
                    },
                }
            },
            instance_subnet: {
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '请选择子网'
                    },
                }
            },
            instance_set: {
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '请选择套餐'
                    },
                }
            },
            instance_image: {
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '请选择镜像'
                    },
                }
            },
            instance_work: {
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '请选择订单流程'
                    },
                }
            },
            attribute_name: {
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '版本名称不能为空'
                    },
                }
            },
            attribute_region: {
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '请选择地区'
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