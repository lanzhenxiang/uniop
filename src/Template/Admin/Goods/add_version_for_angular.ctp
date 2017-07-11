<!-- 主页模板 -->
<script src="/js/angular1.3.min.js"></script>
<script src="/js/angular-route.js"></script>
<script src="/js/controller/adminVersion.js"></script>
<script>
 var brands =eval('(<?=$brand?>)');

</script>
<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div id="maindiv-alert" class="content-list-page"></div>
    <div class="content-operate clearfix">

        <div class="clearfix" style="margin-top:20px;"></div>


        <form class="form-horizontal bv-form" id="aduser-form" action="" method="post" novalidate="novalidate">


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

        </style>

        <div class="form-group" id="SelectTenantPanle">
                
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


          <div ng-app="adminVersion" ng-controller="version">
                

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

                                            <li ng-repeat="host in hostList" ng-class="{active:$first}" ng-click="changeHost(host)">
                                                {{host.company.name}}
                                            </li>

                                        </ul>
                                    </div>
                                </div>
                                <div class="clearfix location-relative" >
                                    <label for="" class="pull-left" >地域:</label>
                                    <div class="bk-form-row-cell">
                                       <ul class="clearfix city ng-scope" id="agent_b" >
                                          
                                            <li ng-repeat="a in areas.area" ng-class="{active:$first}" ng-click="changeArea(a)">
                                                {{a.name}}
                                            </li>
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



<div class="form-group">
                
                <label for="add" class="col-sm-2 control-label"></label>
                <div class="col-sm-6">
                  <table class="t_panle">
                    <tr>
                      <td class="t_left">实例名称</td>
                      <td class="t_right row-message-right">
                                 <input type="text" class="form-control" name="instanceName" id="instanceName" value="">
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
                      <td class="t_left">非编型号</td>
                      <td class="t_right row-message-right">
                                  
                                  <div class="ng-binding">
                                <div class="clearfix location-relative">
                                    <label for="" class="pull-left" >品牌:</label>
                                    <div class="bk-form-row-cell">

                                        <ul class="clearfix city" id="brand_a">
                                           <li ng-repeat="b in brands" ng-class="{active:$first}" ng-click="changeBrands(b)">
                                                {{b.name}}
                                            </li>
                                          
                                        </ul>
                                    </div>
                                </div>


                                <div class="clearfix location-relative" >
                                    <label for="" class="pull-left" >规格:</label>
                                    <div class="bk-form-row-cell">
                                        
                                            <select id="spec" class="form-control" name="spec" ng-change="selectBrand(specid)" ng-model="specid"  ng-options="b.id as b.name for b in brand" >
                                            <option value=""> --请选择-- </option>
                                            </select>
                                       
                                   </div>
                                </div>

                                <div class="clearfix location-relative" >
                                    <label for="" class="pull-left" >单价:</label>
                                    <div class="bk-form-row-cell"><span id="g_price">{{spec.price}}</span><span id="g_unit"></span></div>
                                </div>

                            </div>


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
                      <td class="t_left">网络</td>
                      <td class="t_right row-message-right">
                                
                                <div class="ng-binding">

                                    <div class="clearfix network-tab">
                                        <label for="" class="pull-left"></label>
                                        <div class="bk-form-row-cell">
                                            <ul class="clearfix city">
                                                <li id="subnet_default" class="active">默认网络 </li>
                                                <li id="subnet_extend_menu" class="hide">扩展网络</li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="network-box">
                                        <!--默认-->
                                        <div class="">
                                            <div class="clearfix">
                                                <label style="width:20%" for="" class="pull-left ">VPC:</label>
                                                <div style="width:80%" class="bk-form-row-cell">
                                                    <select class="select-style" ng-options="vpcvpc.name for vpc in area.vpc" ng-model="vpc"  ng-init="vpc=area.vpc[0]" id="vpc">
                                                  
                                                    </select>
                                                    <span class="text-danger" id="vpc-warning"></span>
                                                       

                                                </div>
                                            </div>
                                            <div class="clearfix">
                                                <label style="width:20%" for="" class="pull-left ">子网:</label>
                                                <div style="width:80%" class="bk-form-row-cell">
                                                    <div >
                                                        <div >
                                                            <div >
                                                               
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--扩展-->
                                        <div class="bk-form-row-cell " >
                                            <p><i class="icon-info-sign"></i>&nbsp;通过勾选设置主机要连接的网络。</p>
                                        </div>
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
                    <a type="button" href="/admin/goods/version" class="btn btn-danger">返回</a>
                </div>
            </div>
       </form>


    </div>
</div>
<script>

$('.bk-form-row-cell').on('click',"li",function(){
  $(this).parent().children().removeClass('active');
  $(this).addClass('active');
});
</script>


<?= $this->end() ?>