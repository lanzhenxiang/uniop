<!-- 主页模板 -->
<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div id="maindiv-alert" class="content-list-page"></div>
    <div class="content-operate clearfix">

<div class="pull-left">
             <a type="button" id="search_d_t" href="javascript:;" class="btn btn-addition pull-left" style="margin-right: 10px"><i class="icon-refresh"></i>&nbsp;&nbsp;刷新</a>


        </div>
        <div class="pull-right">
            <div class="pull-left" >
              <div class="form-group">
                                <label for="name" class=" control-label">类型: </label>
                
                                    <select id="goodType"  class="form-control" onchange="refresh()">
                                        <option value="all">全部</option>
                                        <?php
                                          foreach($resource_type as $key=>$val){
                                        ?>
                                              <option value="<?=$key?>"><?=$val?></option>
                                        <?php
                                          }
                                        ?>
                                    </select>
              </div>
            </div>
           <div class="input-group content-search  pull-right" >
            <input type="text" class="form-control" id="searchtext" >
            <span class="input-group-btn">
                <button class="btn btn-primary" id="search" type="button">搜索</button>
            </span>
            </div>
        </div>
    </div>






 <form class="form-horizontal bv-form" id="aduser-form" action="" method="post" novalidate="novalidate">
<table id='mainTable'>

</table>
<div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                <!--<input type="hidden" value="" name="" id="">-->
                    <input type="hidden" value="0" name="check">
                    <button type="submit" id="account_submit" class="btn btn-primary">提交</button>
                    <a type="button" href="/admin/goods/index-new" class="btn btn-danger">返回</a>
                    <!--<a type="button" onclick="window.history.go(-1)" class="btn btn-danger">返回</a>-->
                </div>
            </div>
       </form>
</form>
</div></div>

<script type="text/javascript">
//弹框
    $(function(){
        (function($){
            $.getUrlParam = function(name)
            {
                var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
                var r = window.location.search.substr(1).match(reg);
                if (r!=null) return unescape(r[2]); return null;
            }
        })(jQuery);
        $(function(){
            if($.getUrlParam('unsimilar')==1){
                var gid=$.getUrlParam('gid');
                alert('请选择相同类型版本');
                var stateObject = {};
                var title = "";
                var newUrl = "/admin/goods/select_version?gid="+gid;
                history.pushState(stateObject,title,newUrl);

            }else if($.getUrlParam('published')==1){
                var gid=$.getUrlParam('gid');
                alert('请选择未关联已发布商品的版本');
                var stateObject = {};
                var title = "";
                var newUrl = "/admin/goods/select_version?gid="+gid;
                history.pushState(stateObject,title,newUrl);
            }
        });
    });


    all =eval('(<?=$data?>)'); 
    console.log(all)         
      function initTable() {  
        //初始化表格,动态从服务器加载数据  
        $("#mainTable").bootstrapTable({  
            method: "get",  //使用get请求到服务器获取数据  
            url: "/admin/goods/versionData", //获取数据的Servlet地址  
            striped: true,  //表格显示条纹  http://cmop.com/admin/goods
            pagination: true, //启动分页 
            paginationVAlign:'bottom',
            paginationHAlign:'left',
            pageSize: 100,  //每页显示的记录数  
            pageNumber:1, //当前第几页  
            pageList: [10, 15, 20, 25,50,100],  //记录数可选列表  
            search: false,  //是否启用查询  
            showColumns: false,  //显示下拉框勾选要显示的列  
            showRefresh: false,  //显示刷新按钮  
            sidePagination: "server", //表示服务端请求
            queryParamsType : "undefined",
            columns: [
                {field: 'id',title: '选择',formatter:function(i,row){
                    if($.inArray(row.id, all) >=0){
                      return '<input name="ids[]"  checked="checked"  value="'+row.id+'" type="checkbox">';
                    }else{
                      return '<input name="ids[]" value="'+row.id+'" type="checkbox">';
                    }
                    
                }},
                {field: 'name',title: '版本名称',formatter:function(i,row){
                    if(row.goods_name ==""){
                      return row.name
                    }else{
                      return row.name+" (已绑定："+row.goods_name+") "
                    }
                    
                }},
                {field: 'type',title: '版本类型'},
                {field: '',title: '操作',formatter:function(i,row){
                    str =  '<a href="/admin/goods/add-version?vid='+row.id+'">详情</a> ';
                    return str;
                    
                }},
                {field: 'description',title: '备注'},
                {field: 'time',title: '创建时间',formatter:function(i,row){
                    
                    return new Date(parseInt(row.create) * 1000).toLocaleString().replace(/:\d{1,2}$/,' ');    
                }}

            ],
            queryParams: function queryParams(params) {   //设置查询参数  
              var param = {    
                  pageNumber: params.pageNumber,    
                  pageSize: params.pageSize,
                  goodType:$("#goodType").val(),
                  name:$("#searchtext").val()
              };    
              return param;                   
            },  
            onLoadSuccess: function(){  //加载成功时执行  
              //layer.msg("加载成功");  
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
  
      $(document).ready(function () {          
          //调用函数，初始化表格  
          initTable();  
          //当点击查询按钮的时候执行  
          $("#search_d_t").bind("click", function(){
                refresh()
          });
           $("#search").bind("click", function(){
                refresh()
          });  

          
      });
      function refresh(){
            $("#mainTable").bootstrapTable('refresh')
          }

</script> 
<?= $this->end() ?>