<!-- 主页模板 -->
<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div id="maindiv-alert" class="content-list-page"></div>
    <div class="content-operate clearfix">
    <script src="/js/layer2/layer.js"></script>

<div class="pull-left">
             <a type="button" id="search_d_t" href="javascript:;" class="btn btn-addition pull-left" style="margin-right: 10px"><i class="icon-refresh"></i>&nbsp;&nbsp;刷新</a>

             <a type="button" href="<?php echo $this->Url->build(array('controller' => 'Goods','action'=>'addVersion')); ?>" class="btn btn-addition pull-left" style="margin-right: 10px"><i class="icon-plus" ></i>&nbsp;&nbsp;新建</a>



            <a type="button" href="javascript:;" onclick="del()" class="btn btn-addition pull-left" ><i class="icon-remove"></i>&nbsp;&nbsp;删除</a>

            

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

function cloneVersion(id){

  layer.confirm(
    '您确认要克隆这个版本？',
    {
        btn: ['确认','取消']
    },
    function(){
       $.post("/admin/goods/clone-version",{id:id},function(result){
                  layer.closeAll()
                  if(result == "ok"){
                      layer.alert('克隆成功', {icon: 6});
                      setTimeout( function(){location.reload() }, 1 * 1000 );
                  }else{
                      layer.alert('克隆失败', {icon: 5});
                  }
              });
    })
}    
function del(){
   var spCodesTemp = "";
      $('input:checkbox[name=ids]:checked').each(function(i){
       if(0==i){
        spCodesTemp = $(this).val();
       }else{
        spCodesTemp += (","+$(this).val());
       }
      });
      id = spCodesTemp;
      if(id ==""){
        alert('请选着需要操作的数据');
        return false;
      }
    //是否绑定商品
    $.post("/admin/goods/bindGoods",{id:id},function(exist){
        if(exist=='yes'){
            layer.alert('该版本已绑定商品,请解绑后删除', {icon: 6});
        }else{
              layer.confirm(
    '您确认要删除这个版本？',
    {
        btn: ['确认','取消']
    },
    function(){
       $.post("/admin/goods/del-version",{id:id},function(result){
                  layer.closeAll()
                  if(result == "ok"){
                      layer.alert('删除成功', {icon: 6});
                      setTimeout( function(){location.reload() }, 1 * 1000 );
                  }else{
                      layer.alert('删除失败', {icon: 5});
                  }
              });
    })
        }
    });


  }      
      function initTable() {  
        //初始化表格,动态从服务器加载数据  
        $("#mainTable").bootstrapTable({  
            method: "get",  //使用get请求到服务器获取数据  
            url: "/admin/goods/versionData", //获取数据的Servlet地址  
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
                    return '<input name="ids"  value="'+row.id+'" type="checkbox">';
                }},
//                {field: 'id',title: 'id'},
                {field: 'name',title: '版本名称'},
                {field: 'type',title: '版本类型'},
                {field: 'goods_name',title: '关联商品',formatter:function(i,row){
                    if(row.goods_name ==""){
                      return "-"
                    }else{
                      return row.goods_name
                    }
                    
                }},
                {field: 'description',title: '备注',formatter(v,row){
                  return sub(v,30)
                }},
                {field: '',title: '操作',formatter:function(i,row){
                    str =  '<a href="/admin/goods/add-version?vid='+row.id+'">修改</a> | ';
                    str +=  '<a href="javascript:;" onclick="cloneVersion('+row.id+')">复制</a> | ';
                    if(row.type == "citrix" || row.type == 'citrix_public'){
                        str+=  '<a href="/admin/spec/add?edit=false&id='+row.details.specid+'">定价</a>  ';
                    }else{
                        str+=  '<a href="/admin/goods/price?vid='+row.id+'">定价</a>  ';
                    }
                    
                    return str;
                    
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
      function sub(s, n) {
　　　　return s.slice(0, n).replace(/([^x00-xff])/g, "$1a").slice(0, n).replace(/([^x00-xff])a/g, "$1");
　　}
      function refresh(){
            $("#mainTable").bootstrapTable('refresh')
          }

</script> 
<?= $this->end() ?>