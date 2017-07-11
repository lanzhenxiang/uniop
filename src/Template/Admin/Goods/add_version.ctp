<?= $this->element('content_header'); ?>
<script src="/js/layer2/layer.js"></script>
<div class="content-body clearfix">
    <div id="maindiv-alert" class="content-list-page"></div>
    <div class="content-operate clearfix">

        <div class="clearfix" style="margin-top:20px;"></div>


        <form class="form-horizontal bv-form" id="aduser-form" action="" onsubmit="return onformsubmit()" method="post" >

        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">版本名</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="name" id="name"  value="<?php if(isset($vinfo)){echo $vinfo->name;}?>" >
           </div>
        </div>



        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">备注</label>
            <div class="col-sm-6">
                <textarea name="discription" class="form-control" data-bv-field="discription"><?php if(isset($vinfo)){echo $vinfo->description;}?></textarea>
           </div>
        </div>

        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">版本类型</label>
            <div class="col-sm-6">

                  <select <?php if(isset($vinfo)){echo "disabled";}?> id="goodType" name="type"  class="form-control" onchange="showpanel($(this).val())">
                                        <option value="all">请选择</option>
                                        <?php
                                          foreach($resource_type as $key=>$val){
                                        ?>
                                              <option value="<?=$key?>" <?php if(isset($vinfo) && $key==$vinfo->type){ echo 'selected="selected"';}?> ><?=$val?></option>
                                        <?php
                                          }
                                        ?>
                   </select>
                   <?php if(isset($vinfo)){?>

                    <input type="hidden" name="type" value="<?=$vinfo->type?>">
                   <?php } ?> 

           </div>
        </div>

        
        <style>
            .t_panle{width:100%;font-size:12px; }
            .t_panle td.t_right{background:#FAFAFA;}
            .t_panle td{}
            .t_panle td.t_left{
              width:20px;
              padding: 10px 3px;
              color: #bbb;
              background: #f2f2f2;
              text-align: center;
              vertical-align: middle;
             }
             .row-message-right{height: 28px;
  line-height: 28px;
  margin: 0;
  color:#999;}
  .s_panle{display: none;}

        </style>

        <div class="form-group s_panle" id="SelectTenantPanle">
                
                <label for="inputPassword3" class="col-sm-2 control-label"></label>
                <div class="col-sm-6">
                  <table class="t_panle">
                    <tr>
                      <td class="t_left">售卖租户</td>
                      <td class="t_right row-message-right">
                          <?php 
                          if(isset($vinfo)  && isset($vinfoD["tenantid"])){$tid=$vinfoD['tenantid'];}else{$tid='';}
                          echo $this->cell('SelectTenant::display',['tenantid',$tid])
                          ?>
                      </td>
                    </tr>
                  </table>
                </div>

          </div>


        <style>
          #tablebox{display:none}
        </style>
    <div id="select_tenants" class="form-group s_panle">

      <label for="inputPassword3" class="col-sm-2 control-label"></label>
                <div class="col-sm-6">
                  <table class="t_panle">
                    <tr>
                      <td class="t_left">售卖租户</td>
                      <td class="t_right row-message-right">
                          <select name="alltenants" class="form-control" onchange="select_tenants($(this).val())">
                              <option value="all">全部</option>
                              <option value="select" <?php if(isset($vinfo)  && isset($vinfoD["tenantid"]) && $vinfoD["tenantid"] != "all"){echo "selected=selected";}?>>指定</option>
                          </select>
                          <div id="tablebox">
                          <div style="height:20px;"></div>
                          <table id="selectTenansBox-table">
                          
                          </table></div>
                      </td>
                    </tr>
                  </table>
                </div>
    </div>


<script>
<?php
if(isset($vinfo)  && isset($vinfoD["tenantid"]) && $vinfoD["tenantid"] != "all"){
  if($vinfo->type == "bs" || $vinfo->type == "mpaas" || $vinfo->type == "eip" || $vinfo->type == "vfw" || $vinfo->type == "waf"){
      echo "select_tenants('select');";
  }
}
?>
function select_tenants(val){
  if(val=="all"){
    $("#tablebox").hide();
  }else{
    $("#tablebox").show()
  }
}

function initTable2(){
  $("#selectTenansBox-table").bootstrapTable({  
            method: "get",  //使用get请求到服务器获取数据  
            url: "/api/ajax/getTenants", //获取数据的Servlet地址  
            striped: true,  //表格显示条纹  
            pagination: true, //启动分页 
            paginationVAlign:'bottom',
            paginationHAlign:'left',
            pageSize: 100,  //每页显示的记录数  
            pageNumber:1, //当前第几页  
            pageList: [5, 10, 15, 20, 25],  //记录数可选列表  
            search: false,  //是否启用查询  
            showColumns: false,  //显示下拉框勾选要显示的列  
            showRefresh: false,  //显示刷新按钮  
            sidePagination: "server", //表示服务端请求
            queryParamsType : "undefined",
            singleSelect: true,
            columns: [
                {field: 'id',title: '',formatter:function(id){
                    <?php
                      if(isset($vinfo)  && isset($vinfoD["tenantid"])){echo 'tenants = ['.$vinfoD["tenantid"].']';}else{echo 'tenants=[]';}
                    ?>;
                    selected = ""
                    if ($.inArray(parseInt(id), tenants) !=-1){
                      selected = "checked = checked";
                    }
                    return '<input data-index="1" name="selectTenants[]"  '+selected+' class="selectTangentRadio" value="'+id+'" type="checkbox">';
                }},
                {field: 'name',title: '租户',width:150},
                {field: 'dept_code',title: '租户code',width:150},

            ],
            queryParams: function queryParams(params) {   //设置查询参数  
              var param = {    
                  pageNumber: params.pageNumber,    
                  pageSize: params.pageSize,  
                  queryStr : $("#search_d").val()  
              };    
              return param;                   
            },  
            onLoadSuccess: function(){  //加载成功时执行  
              $(".selectTangentRadio").change(function(){
                  if( typeof selectTangentRadio === 'function' ){
                    selectTangentRadio($(this).val())
                  }
              })
              
            },  
            onLoadError: function(){  //加载失败时执行  
             // layer.msg("加载数据失败", {time : 1500, icon : 2});  
            },
            formatLoadingMessage: function () {
                return "";
            },
            formatNoMatches: function () {  //没有匹配的结果
                return '无符合条件的记录';
            }

          });  
}



</script>

        <div id="other" class="s_panle">

       

            <div class="form-group">
                
                <label for="inputPassword3" class="col-sm-2 control-label"></label>
                <div class="col-sm-6">
                  <table class="t_panle">
                    <tr>
                      <td class="t_left">订单流程</td>
                      <td class="t_right row-message-right">
                              <select id="flow" name="processid2" class="form-control">
                            <?php foreach ($flow_data as $_flow_k => $_flow_v) {   ?>
                            <option value="<?php echo $_flow_v['flow_id'];?>" <?php if(isset($vinfoD)  && isset($vinfoD["processid"]) && $vinfoD['processid'] ==$_flow_v['flow_id'] ){echo "selected='selected'";}?>>
                                <span><?php echo $_flow_v['flow_name'];?></span>
                            </option>
                            <?php }   ?>
                        </select>

                      </td>
                    </tr>
                  </table>
                </div>

          </div>

        </div>

        <div id="mpassDiv" style="display: none;">
          <div class="form-group">
                
                <label for="inputPassword3" class="col-sm-2 control-label"></label>
                <div class="col-sm-6">
                  <table class="t_panle">
                    <tr>
                      <td class="t_left">服务厂商</td>
                      <td class="t_right row-message-right">
                        <select id="service_brand" name="service_brand" class="form-control">
                            <?php foreach($service_brand as $key => $value){ ?>
                                <option <?php if(isset($vinfoD)  && isset($vinfoD["service_brand"]) && $vinfoD['service_brand'] ==$key ){echo "selected='selected'";}?>  value="<?php echo $key;?>"><?php echo $value;?></option>
                           <?php }?>
                        </select>

                      </td>
                    </tr>
                  </table>
                </div>

          </div>

          <div class="form-group">
                
                <label for="inputPassword3" class="col-sm-2 control-label"></label>
                <div class="col-sm-6">
                  <table class="t_panle">
                    <tr>
                      <td class="t_left">服务类型</td>
                      <td class="t_right row-message-right">
                        <select id="service_type" name="service_type" class="form-control">
                           <?php foreach($service_type as $key => $value){ ?>
                                <option <?php if(isset($vinfoD)  && isset($vinfoD["service_type"]) && $vinfoD['service_type'] ==$value ){echo "selected='selected'";}?>  value="<?php echo $value;?>"><?php echo $value;?></option>
                           <?php }?>
                        </select>

                      </td>
                    </tr>
                  </table>
                </div>

          </div>
        </div>


        <div id="agentsDiv" style="display: none;">
          
           <div class="form-group">
                
                <label for="add" class="col-sm-2 control-label"></label>
                <div class="col-sm-6">
                  <table class="t_panle">
                    <tr>
                      <td class="t_left">部署区位</td>
                      <td class="t_right row-message-right">
                                  
                                  <div class="ng-binding">
                                <div class="clearfix location-relative">
                                    <label for="" class="pull-left" >厂商:</label>
                                    <div class="bk-form-row-cell">
                                        <input type="hidden" value="" id="region" name="region">
                                        <ul class="clearfix city" id="agent_a">
                                           
                                             
                                          
                                        </ul>
                                    </div>
                                </div>


                                <div class="clearfix location-relative" >
                                    <label for="" class="pull-left" >地域:</label>
                                    <div class="bk-form-row-cell">
                                       <ul class="clearfix city ng-scope" id="agent_b">
                                          
                                        </ul>
                                        <p><i class="icon-info-sign"></i>&nbsp;不同厂商之间的产品内网不互通；订购后不支持更换服务，请谨慎选择</p>
                                   </div>
                                </div>
                            </div>



                      </td>
                    </tr>
                  </table>
                </div>

          </div>
        </div>
        <div id="brandDiv" style="display: none;">
          <div class="form-group">
                
                <label for="add" class="col-sm-2 control-label"></label>
                <div class="col-sm-6">
                  <table class="t_panle">
                    <tr>
                      <td class="t_left">非编型号</td>
                      <td class="t_right row-message-right">
                                  
                                  <div class="ng-binding">
                                <div class="clearfix location-relative">
                                    <label for="" class="pull-left" >品牌:</label>
                                    <div class="bk-form-row-cell">

                                        <ul class="clearfix city" id="brand_a">
                                           
                                          
                                        </ul>
                                    </div>
                                </div>


                                <div class="clearfix location-relative" >
                                    <label for="" class="pull-left" >规格:</label>
                                    <div class="bk-form-row-cell">
                                         <select id="spec" class="form-control" name="spec" onchange="brand_select_b($(this).val())">
                                           
                                         </select>
                                   </div>
                                </div>

                                <div class="clearfix location-relative" >
                                    <label for="" class="pull-left" >单价:</label>
                                    <div class="bk-form-row-cell"><span id="g_price"></span><span id="g_unit"></span></div>
                                </div>

                            </div>



                      </td>
                    </tr>
                  </table>
                </div>
                </div>

        </div>
        <div id="citrix" class="s_panle">
          

          <div class="form-group">
                
                <label for="inputPassword3" class="col-sm-2 control-label"></label>
                <div class="col-sm-6">
                  <table class="t_panle">
                    <tr>
                      <td class="t_left">订单流程</td>
                      <td class="t_right row-message-right">
                              <select id="flow" name="processid" class="form-control">
                            <?php foreach ($flow_data as $_flow_k => $_flow_v) {   ?>
                            <option value="<?php echo $_flow_v['flow_id'];?>"  <?php if(isset($vinfoD)  && isset($vinfoD["processid"]) && $vinfoD['processid'] ==$_flow_v['flow_id'] ){echo "selected='selected'";}?>>
                                <span><?php echo $_flow_v['flow_name'];?></span>
                            </option>
                            <?php }   ?>
                        </select>

                      </td>
                    </tr>
                  </table>
                </div>

          </div>

         

          <div class="form-group">
                
                <label for="inputPassword3" class="col-sm-2 control-label"></label>
                <div class="col-sm-6">
                  <table class="t_panle">
                    <tr>
                      <td class="t_left">实例名称</td>
                      <td class="t_right row-message-right">
                             <input type="text" class="form-control" name="instance_name" id="instance_name"  value="<?php if(isset($vinfoD) && isset($vinfoD['instance_name'])){echo $vinfoD['instance_name'];}?>" >

                      </td>
                    </tr>
                  </table>
                </div>

          </div>











<div class="form-group">
                
                <label for="add" class="col-sm-2 control-label"></label>
                <div class="col-sm-6">
                  <table class="t_panle">
                    <tr>
                      <td class="t_left">网络设置</td>
                      <td class="t_right row-message-right">
                            <div class="ng-binding">
                            <div class="clearfix location-relative">
                                    <label for=" " class="pull-left" >  </label>
                                    <div class="bk-form-row-cell">
                                         <ul class="clearfix city" >
                                           <li id="subnet_default" onclick="showSubnet(0)" class="active">默认网络 </li>
                                              <li id="subnet_extends" onclick="showSubnet(1)" >扩展网络</li>
                                          </ul>
                                    </div>
                             </div>
               

                            <div class="ng-binding" id="subnet_default_cell">
                                <div class="clearfix location-relative">
                                    <label for="" class="pull-left" >vpc:</label>
                                    <div class="bk-form-row-cell">
                                         <select id="vpc" class="form-control" name="vpc" onchange="vpc_select($(this).val())">
                                           
                                         </select>
                                    </div>
                                </div>


                                <div class="clearfix location-relative" >
                                    <label for="" class="pull-left" >子网:</label>
                                    <div class="bk-form-row-cell">
                                       <select id="subnet" class="form-control" name="subnet" onchange="">
                                           
                                         </select>
                                        
                                   </div>
                                </div>
                            </div>
                            <div class="ng-binding" id="subnet_extends_cell" style="display: none;">
                            
                            </div>


                        </div>
                      </td>
                    </tr>
                  </table>
                </div>

          </div>
        </div>

<div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                <input type="hidden" value="" name="" id="">
                    <button type="submit" id="account_submit" class="btn btn-primary">提交</button>
                    <!--<a type="button" href="/admin/goods/version" class="btn btn-danger">返回</a>-->
                    <!--<a type="button"  onclick="self.location=document.referrer;" class="btn btn-danger">返回</a>-->
                    <a type="button"  onclick="window.history.go(-1)" class="btn btn-danger">返回</a>
                </div>
            </div>
       </form>


    </div>
</div>

<script>


function ajaxget(url){
  text=""
  $.ajax({
        url: url,
        type : 'GET',
        async: false,//使用同步的方式,true为异步方式
        data : {},//这里使用json对象
        success : function(data){
            text = data

        }
    });
  return text;
}
function onformsubmit(){
  typeName = $("#goodType").val();

  if(typeName =='citrix'){
      if($("#instance_name").val() ==""){
          layer.msg('实例名称不能为空', {icon: 2, shade:0.3, time:2000});
          return false;
      }
  }
  if(typeName =='mpaas'){
    url = "/admin/goods/verifyService?service_brand="+$("#service_brand").val()+"&service_type="+$("#service_type").val();
    <?php if(isset($_GET['vid'])){ echo 'url += "&vid='.$_GET['vid'].'"';} ?>

    html= ajaxget(url);
    console.log(html)

    if(html =="true"){
      ajaxform()
      return false;
    }else{
      alert('一个厂商的一个服务只能添加一个版本');
      return false;
    }
  }else{
    ajaxform()
    return false;
  }
}


function ajaxform(){
     layer.msg('数据保存中....', {icon: 4, shade:0.3, time:0});

      $.ajax({
                cache: true,
                type: "POST",
                url:window.location.href,
                data:$('#aduser-form').serialize(),// 你的formid
                error: function(request) {
                    layer.closeAll()
                     alert("Connection error");
                },
                success: function(data) {
                     layer.closeAll()
                     layer.alert('操作成功', {icon: 6});
                     setTimeout( function(){
//                         window.location.href="/admin/goods/version"
                         self.location=document.referrer;
                     }, 1 * 1000 );
                }
            });

}
$(document).ready(function () {
        initTable2();
        //初始化表格,动态从服务器加载数据  
        $("#SelectTenant2Table").bootstrapTable({  
            method: "get",  //使用get请求到服务器获取数据  
            url: "/api/ajax/getTenants", //获取数据的Servlet地址  
            striped: true,  //表格显示条纹  
            pagination: true, //启动分页 
            paginationVAlign:'bottom',
            paginationHAlign:'left',
            pageSize: 50,  //每页显示的记录数  
            pageNumber:1, //当前第几页  
            pageList: [5, 10, 15, 20, 25],  //记录数可选列表  
            search: false,  //是否启用查询  
            showColumns: false,  //显示下拉框勾选要显示的列  
            showRefresh: false,  //显示刷新按钮  
            sidePagination: "server", //表示服务端请求
            queryParamsType : "undefined",
            singleSelect: true,
            columns: [
                {field: 'id',title: '',formatter:function(id){
                    return '<input data-index="1" name="selectTangent2Radio" id="selectTangent2Radio_'+id+'" class="selectTangent2Radio" value="'+id+'" type="checkbox">';
                }},
                {field: 'name',title: '租户',width:150},
                {field: 'dept_code',title: '租户code',width:150},

            ],
            queryParams: function queryParams(params) {   //设置查询参数  
              var param = {    
                  pageNumber: params.pageNumber,    
                  pageSize: params.pageSize,  
                  queryStr : $("#search_d").val()  
              };    
              return param;                   
            },  
            onLoadSuccess: function(){  //加载成功时执行  
              $(".selectTangentRadio").change(function(){
                  if( typeof selectTangentRadio === 'function' ){
                    selectTangentRadio($(this).val())
                  }
              })
              
              
            },  
            onLoadError: function(){  //加载失败时执行  
             // layer.msg("加载数据失败", {time : 1500, icon : 2});  
            },
            formatLoadingMessage: function () {
                return "";
            },
            formatNoMatches: function () {  //没有匹配的结果
                return '无符合条件的记录';
            }

          });  

    $("#subnet_extends_cell").bootstrapTable({  
            method: "get",  //使用get请求到服务器获取数据  
            url: "/console/ajax/network/hosts/getPublicSubnetExtend", //获取数据的Servlet地址  
            striped: true,  //表格显示条纹  
            pagination: true, //启动分页 
            paginationVAlign:'bottom',
            paginationHAlign:'left',
            pageSize: 50,  //每页显示的记录数  
            pageNumber:1, //当前第几页  
            pageList: [5, 10, 15, 20, 25],  //记录数可选列表  
            search: false,  //是否启用查询  
            showColumns: false,  //显示下拉框勾选要显示的列  
            showRefresh: false,  //显示刷新按钮  
            sidePagination: "server", //表示服务端请求
            queryParamsType : "undefined",
            singleSelect: true,
            columns: [
                {field: 'id',title: '',formatter:function(id){
                    str =""
                    all =[];
                    <?php if( isset($vinfoD)&&isset($vinfoD['subnet_extends'])){?>
                     all =  eval('(<?php echo json_encode($vinfoD['subnet_extends'])?>)')
                    
                     if($.inArray(id.toString(), all) >=0){
                        str = "checked = 'checked'"
                     }
                    <?php } ?>
                    return '<input name="subnet_extends[]" '+str+' value="'+id+'" type="checkbox">';
                }},
                {field: 'vpc_name',title: 'vpc',width:150},
                {field: 'subnet_name',title: '子网',width:150},

            ],
            onLoadSuccess: function(){  //加载成功时执行  
              
              
            },  
            onLoadError: function(){  //加载失败时执行  
             // layer.msg("加载数据失败", {time : 1500, icon : 2});  
            },
            formatLoadingMessage: function () {
                return "";
            },
            formatNoMatches: function () {  //没有匹配的结果
                return '无符合条件的记录';
            }

          });
})

      

function showSubnet(id){
  if(id==1){
    $("#subnet_default_cell").hide()
    $("#subnet_extends_cell").show()
    $("#subnet_extends").addClass("active")
    $("#subnet_default").removeClass("active")

  }else{
    $("#subnet_default_cell").show()
    $("#subnet_extends_cell").hide()
    $("#subnet_extends").removeClass("active")
    $("#subnet_default").addClass("active")
  }
}

<?php if(isset($vinfo)){ echo 'showpanel("'.$vinfo->type.'");';}?>


function showpanel(typeName){
  $("#SelectTenantPanle").show()
  $("#select_tenants").show();
  if(typeName=="citrix"){
    $("#citrix").show();
    $("#select_tenants").hide();
    $("#agentsDiv").show();
    $("#other").hide();
    $("#mpassDiv").hide();
    $("#brandDiv").show();
  }else{
    $("#agentsDiv").hide();
     $("#brandDiv").hide();
    $("#mpassDiv").hide();
      if(typeName == "ecs" || typeName =="citrix_public"){
        $("#SelectTenantPanle").hide();
        $("#select_tenants").hide();
      }else{
        $("#SelectTenantPanle").hide();
         $("#select_tenants").show()
      }

      if(typeName =='mpaas'){
       
        $("#mpassDiv").show();
      }
      if(typeName =="citrix_public"){
        $("#agentsDiv").show();
        $("#brandDiv").show();
      }
    $("#citrix").hide();
    $("#other").show();
  }
}

function selectTangentRadio(id){
  intNetwork(id)
}


function initPrice(price){
        str = '-';
        if( price==null || price ==""){
            return '-';
        }
        str = price;
        str = str.replace("Y","元/年 ");
        str = str.replace("M","元/月 ");
        str = str.replace("D","元/日 ");
        str = str.replace("H","元/小时 ");
        str = str.replace("I","元/分钟 ");
        str = str.replace("S","元/秒 ");
        return str
    }

agents =eval('(<?=$agent?>)');
brands =eval('(<?=$brand?>)');
console.log(brands);
intAgent()
intBrand()
var vpcData
function intNetwork(did){
  $.getJSON("/admin/goods/getVpc?did="+did, function(data){
     vpcData=data
     $("#vpc").html("");
     intVpc=''
     <?php
      if(isset($vinfo) && isset($vinfoD["vpc"]) ){
        ?>
 $.each(data.vpcs,function(i,vpc){
  if(vpc.code=='<?php if(isset($vinfoD) && isset($vinfoD["vpc"])){echo $vinfoD["vpc"];}?>'){
    intVpc = vpc.id
  }
 })
        <?php
      }
     ?>
     $.each(data.vpcs,function(i,vpc){
        if(intVpc===''){
            intVpc=vpc.id
        }
        $("#vpc").append('<option id="vpc_'+vpc.id+'" value="'+vpc.code+'">'+vpc.name+'</option>');
        vpc_select(intVpc);
     })
  })
}

function vpc_select(vpcId){
    $("#subnet").html("");
    $("#vpc_"+vpcId).attr("selected","selected");
    $.each(vpcData.subnets,function(i,subnet){
      vpcCode =''


      $.each(vpcData.vpcs,function(ii,vpc){
        if(vpc.id == vpcId || vpc.code == vpcId ){
          //console.log(vpc)
          vpcCode = vpc.code
        }
      })
      select='';
           <?php
      if(isset($vinfo)  && isset($vinfoD["subnet"])){
        ?>
         if(subnet.code == '<?=$vinfoD["subnet"]?>'){
            select = 'selected=select';
         }
        <?php
      }
     ?>
      if(vpcCode == subnet.vpc){
        $("#subnet").append('<option '+select+' id="subnet_'+subnet.id+'" value="'+subnet.code+'">'+subnet.name+'</option>');
      }
      
    })
}

function intBrand(){
  intId='';
  <?php
     if(isset($vinfo)  && isset($vinfoD["specid"])){
      ?>
        $.each( brands, function(index, arr){
      list =   brands[index].subs;
       $.each( list, function(i, agent){

        if(agent.id == '<?=$vinfoD["specid"]?>'){
          intId=index;
        }

       })
   })
      <?php
     }
      ?>



  $.each( brands, function(index, agent)
  {   
      if(intId ===''){
        intId = index
      }
      $("#brand_a").append('<li id="brand_d_'+index+'" class="ng-scope ng-binding" onclick="brand_select_a('+index+')">'+agent.name+' </li>')
  });

  brand_select_a(intId);
}

function brand_select_a(id){
  intId =''
  aintId=''
  list =   brands[id].subs;
  <?php
    if(isset($vinfo)  && isset($vinfoD["specid"])){
      echo 'aintId='.$vinfoD['specid'].';';
    }
  ?>

  if(aintId !=''){
      istrue = false
       $.each( list, function(index, agent){
          if(agent.id == aintId){
            intId = aintId
          }
       })
  }


  $("#brand_a li").each(function(index){
    $(this).removeClass("active")
  })

  $("#brand_d_"+id).addClass("active");
  
  $("#spec").html("");
  $.each( list, function(index, agent)
  {   
      if(intId ===''){
        intId = agent.id
      }
      $("#spec").append('<option id="spec_'+agent.id+'" value="'+agent.id+'">'+agent.name+'</option>');
  });
 brand_select_b(intId);
}
function brand_select_b(id){

   $("#brand_b li").each(function(index){
    $(this).removeClass("active")
  })
    $.each( brands, function(index, arr){
      list =   brands[index].subs;
       $.each( list, function(i, agent){

        if(agent.id == id){
          $("#g_price").html(initPrice(agent.price))
        }

       })
   })
  $("#spec_"+id).attr("selected", true) 

}

function intAgent(){
   <?php
     if(isset($vinfo)  && isset($vinfoD["region"])){
      echo 'intId = 0;';
      ?>

  $.each(agents, function(index, arr){
      list =   agents[index].subs;
       $.each( list, function(i, agent){
        if(agent.region_code == '<?=$vinfoD["region"]?>'){
           intId = index;
        }
       })
   })
      <?php
     }else{
      echo 'intId = 0;';
     }
   ?>
   
   $.each( agents, function(index, agent)
  {   
      if(intId ==0){
        intId = index
      }
      $("#agent_a").append('<li id="agent_d_'+index+'" class="ng-scope ng-binding" onclick="agent_select_a('+index+')">'+agent.agent_name+' </li>')
  });
  agent_select_a(intId);
}


function agent_select_a(id){
  intId =0
  <?php
     if(isset($vinfo)  && isset($vinfoD["region"])){
      ?>
      list =   agents[id].subs;
      $.each( list, function(index, agent){
        if(agent.region_code == '<?=$vinfoD["region"]?>'){
          intId = agent.id;
        }
      })
      <?php } ?>
  $("#agent_a li").each(function(index){
    $(this).removeClass("active")
  })

  $("#agent_d_"+id).addClass("active");
  list =   agents[id].subs;
  $("#agent_b").html("");
  $.each( list, function(index, agent)
  {   
      if(intId ==0){
        intId = index
      }
      $("#agent_b").append('<li id="agent_e_'+index+'" class="ng-scope ng-binding" onclick="agent_select_b('+index+')">'+agent.agent_name+' </li>')
  });
  agent_select_b(intId);
}


function agent_select_b(id){
   $("#agent_b li").each(function(index){
    $(this).removeClass("active")
  })
   $.each( agents, function(i, a){
      $.each(agents[i].subs, function(ii, agent){
          if(agent.id == id){
            $("#region").val(agent.region_code)
          }
      })
   })
  
  $("#agent_e_"+id).addClass("active");
}
</script>

<?= $this->end() ?>