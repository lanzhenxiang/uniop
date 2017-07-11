
<style>
.tit-right{
    white-space: nowrap;
}
</style>
<!-- 主页模板 -->
<?= $this->element('content_header'); ?>
<script src="/js/layer2/layer.js"></script>
<div class="content-body clearfix">
    <div id="maindiv-alert" class="content-list-page"></div>
    <div class="content-operate clearfix">

<div class="pull-left">
             <a type="button" id="search_d_t" href="javascript:;" class="btn btn-addition pull-left" style="margin-right: 10px"><i class="icon-refresh"></i>&nbsp;&nbsp;刷新</a>

             <a type="button" href="<?php echo $this->Url->build(array('controller' => 'Goods','action'=>'addGoods')); ?>" class="btn btn-addition pull-left" style="margin-right: 10px"><i class="icon-plus" ></i>&nbsp;&nbsp;新建基本信息</a>

            <a type="button" href="javascript:;" onclick="deleteg()" class="btn btn-addition pull-left" ><i class="icon-remove"></i>&nbsp;&nbsp;删除</a>

        </div>
        <div class="pull-right tit-right">
            <div class="pull-left form-group" >
                <label for="name" class=" control-label">商品分类: </label>
                <select id="goodType" name="goodType" onchange="refresh()"  class="form-control">
                    <option value="all">全部 </option>
                <?php foreach($category as $key=>$c){ ?>
                    <option value="<?=$c->id?>"><?=$c->name?></option>
                <?php } ?>
                </select>
            </div>
            <div class="pull-left form-group marginl20">
                                <label for="name" class=" control-label"> 状态: </label>
                                    <select id="goodStatus"  class="form-control" onchange="refresh()">
                                        <option value="all">全部</option>
                                              <option value="0">未发布</option>
                                              <option value="1">已发布</option>
                                    </select>
            </div>
           <div class="input-group content-search  pull-right marginl20" >
            <input type="text" class="form-control" id="searchtext"  placeholder="搜索商品名称...">
            <span class="input-group-btn">
                <button class="btn btn-primary" id="search" type="button">搜索</button>
            </span>
            </div>
        </div>
    </div>







<table id='mainTable'>

</table>
</div></div>

<script type="text/javascript">
function checkAll(){

    if(document.getElementById("checkAll").checked){   
        $("tbody :checkbox[name='ids']").prop("checked", true);  
    }else{   
        $("tbody :checkbox[name='ids']").prop("checked", false);
    }   

}
    function deleteg(){
      var str="";
      $("input[name='ids']").each(function(){ 
         if(this.checked){

            if(str ==""){
              str +=$(this).val()
            }else{
              str +=","+$(this).val()
            }
           
          }
      }) 
       $.get("/admin/goods/delete-new?ids="+str,function(data){
        refresh()
      })
    }      

      function push_goods(id,s){
         $.get("/admin/goods/push_goods/"+id+"/"+s,function(data){
            obj = JSON.parse(data);
            if(obj.code ==0){
              refresh()
            }else{
              layer.msg(obj.msg, {icon: 2, shade:0.3, time:3000});
            }
            
         })
      }  
      function initTable() {  
        //初始化表格,动态从服务器加载数据  
        $("#mainTable").bootstrapTable({  
            method: "get",  //使用get请求到服务器获取数据  
            url: "/admin/goods/getIndexList", //获取数据的Servlet地址  
            striped: true,  //表格显示条纹  http://cmop.com/admin/goods
            pagination: true, //启动分页 
            paginationVAlign:'bottom',
            paginationHAlign:'left',
            pageSize: 10,  //每页显示的记录数  
            pageNumber:1, //当前第几页  
            pageList: [10, 15, 20, 25,50,100],  //记录数可选列表  
            search: false,  //是否启用查询  
            showColumns: false,  //显示下拉框勾选要显示的列  
            showRefresh: false,  //显示刷新按钮  
            sidePagination: "server", //表示服务端请求
            queryParamsType : "undefined",
            clickToSelect: true,
            singleSelect: true,
            columns: [
                {field: 'id',title: '<input onclick="checkAll()" id="checkAll" value="" type="checkbox">',formatter:function(i,row){
                    if(row.goodStatus !=0){
                      
                      return '<input name="idss" disabled="disabled"  value="'+row.id+'" type="checkbox">';
                    }
                    return '<input name="ids"  value="'+row.id+'" type="checkbox">';
                }},
                {field: 'id',title: 'id'},
                {field: 'name',title: '商品名',formatter(val,row){
                    return sub(val,15)
                }},
                {field: '',title: '商品分类',formatter(i,row){
                  if(row.category_id =="" || row.goods_category == undefined || row.goods_category == null){
                    return "-";
                  }
                  return row.goods_category.name
                }},
                {field: 'goodStatus',title: '状态',formatter:function(i,row){
                   if(row.goodStatus == 0){
                      return '未发布'
                   }else{
                      return '已发布'
                   }
                }},
                {
                    field:'sort',title:'排序'
                },
                {field: 'remark',title: '备注',formatter(val,row){
                   c =  sub(val,30);
                   content = '<span data-toggle="tooltip" data-placement="bottom" title='+val+'>'+c+'</span>';
                   return content
                }},
                {field: '',title: '操作',formatter:function(i,row){
                    str =  '<a href="/admin/goods/add-goods?gid='+row.id+'">修改基本信息</a> | ';
                    str+=  '<a href="/admin/goods/edit-datail?gid='+row.id+'">商品详情</a> | ';
                    str+=  '<a href="/admin/goods/select-version?gid='+row.id+'">选择版本</a> | ';
                    if(row.goodStatus ==0){
                      str+=  '<a href="javascript:;" onclick="push_goods('+row.id+',1)">发布</a>  ';
                    }else{
                      str+=  '<a href="javascript:;" onclick="push_goods('+row.id+',0)">取消发布</a>';
                    }
                    return str;
                    
                }}

            ],
            queryParams: function queryParams(params) {   //设置查询参数  
              layer.msg('数据加载中....', {icon: 4, shade:0.3, time:0});
              var param = {    
                  pageNumber: params.pageNumber,    
                  pageSize: params.pageSize,
                  goodType:$("#goodType").val(),
                  goodStatus:$("#goodStatus").val(),
                  name:$("#searchtext").val()
              };    
              return param;                   
            },  
            onLoadSuccess: function(){  //加载成功时执行
                    layer.closeAll()
            },  
            onLoadError: function(){  //加载失败时执行  
                layer.msg("加载数据失败", {time : 1500, icon : 2});
                layer.closeAll()
            },
            formatLoadingMessage: function () {
               
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
function sub(s, n) {
　　　　return s.slice(0, n).replace(/([^x00-xff])/g, "$1a").slice(0, n).replace(/([^x00-xff])a/g, "$1");
　　}
</script> 
<?= $this->end() ?>