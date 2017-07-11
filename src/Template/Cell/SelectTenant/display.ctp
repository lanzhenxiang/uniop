<style>
.selectTenantBox{width:100%}
#selectTenanBox-table{ margin-top: 10px; }
#selectTenanBox-table thead tr{width: 100%}
.selectTenanBox .pagination{margin: 0px !important}
</style>
<div class='selectTenanBox'>
	
	<div class="input-group content-search pull-left">
            <input class="form-control"  id="search_d" placeholder="搜索名称或code或供应商..." type="text">
             <span class="input-group-btn">
                 <button class="btn btn-primary" id="search_d_t" type="button">搜索</button>
             </span>
    </div>
    <div class='clearfix'></div>

    <div id="selectTenanBox-table">
   
   </div>

</div>
<script type="text/javascript">            
      function initTable() {  
        //初始化表格,动态从服务器加载数据  
        $("#selectTenanBox-table").bootstrapTable({  
            method: "get",  //使用get请求到服务器获取数据  
            url: "/api/ajax/getTenants", //获取数据的Servlet地址  
            striped: true,  //表格显示条纹  
            pagination: true, //启动分页 
            paginationVAlign:'bottom',
            paginationHAlign:'left',
            pageSize: 5,  //每页显示的记录数  
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
                    return '<input data-index="1" name="<?=$formName?>" id="selectTangentRadio_'+id+'" class="selectTangentRadio" value="'+id+'" type="radio">';
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
              if('<?=$did?>'!=''){
                  $("#selectTangentRadio_<?=$did?>").attr("checked", "checked");
                  if( typeof selectTangentRadio === 'function' ){
                    selectTangentRadio('<?=$did?>')
                  }
              }
              
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
                $("#selectTenanBox-table").bootstrapTable('refresh',{query:{queryStr:$("#search_d").val()}})
          });  
      });


</script> 
