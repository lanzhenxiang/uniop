<!-- 主页模板 -->
<?= $this->element('content_header'); ?>
<script src="/js/layer2/layer.js"></script>

<div class="content-body clearfix">
    <div id="maindiv-alert" class="content-list-page"></div>
    <div class="content-operate clearfix">

<div class="pull-left">
             <a type="button" id="search_d_t" href="javascript:;" class="btn btn-addition pull-left" style="margin-right: 10px"><i class="icon-refresh"></i>&nbsp;&nbsp;刷新</a>

             <a type="button" href="<?php echo $this->Url->build(array('controller' => 'Spec','action'=>'add')); ?>" class="btn btn-addition pull-left" style="margin-right: 10px"><i class="icon-plus" ></i>&nbsp;&nbsp;新建</a>

            <a type="button" href="javascript:;" onclick="deleteg()" class="btn btn-addition pull-left" ><i class="icon-remove"></i>&nbsp;&nbsp;删除</a>

        </div>
        <div class="pull-right">
            
           
           <div class="input-group content-search  pull-right" >
            <input type="text" class="form-control" id="searchtext"  placeholder="搜索品牌名称或者规格名..">
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
        }) ;
        if(str!=''){
            $.getJSON("/admin/Spec/getAgent?ids="+str,function(data){
                if(data.code==0){
                    layer.confirm(
                            '您确认要删除？',
                            {
                                btn: ['确认','取消']
                            },
                            function(){
                                deletegg()
                            });
                }else{
                    layer.confirm(
                            data.msg+ '<br>您确认要删除？',
                            {
                                btn: ['确认','取消']
                            },
                            function(){
                                deletegg()
                            });
                }
            });


        }else{
            layer.alert(
                    '请选择要删除的云桌面规格'
            )
        }



    }

    function cloneSpec(id){
    layer.confirm(
    '您确认要复制本规格？',
    {
        btn: ['确认','取消']
    },
    function(){
       window.location.href="/admin/spec/clone-spec?id="+id
    })
    }



    function deletegg(){

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
       $.get("/admin/spec/delete?ids="+str,function(data){
        refresh()
        layer.closeAll()
      })
    }



      function initTable() {  
        //初始化表格,动态从服务器加载数据  
        $("#mainTable").bootstrapTable({  
            method: "get",  //使用get请求到服务器获取数据  
            url: "/admin/spec/lists", //获取数据的Servlet地址  
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
                {field: 'id',title: '',formatter:function(i,row){
                    return '<input name="ids" value="'+row.id+'" type="checkbox">';
                }},
                {field: 'id',title: 'id'},
                {field: 'brand',title: '品牌'},
                {field: 'name',title: '规格'},
                {field: 'set_name',title: '计算能力'},
                {field: 'image_name',title: '镜像'},
                {field: '',title: '按秒计费单价/元',formatter:function(value,row,i){return initPrice(value,row,i,'S')}},
                {field: '',title: '按分钟计费单价/元',formatter:function(value,row,i){return initPrice(value,row,i,'I')}},
                {field: '',title: '按小时计费单价/元',formatter:function(value,row,i){return initPrice(value,row,i,'H')}},
                {field: '',title: '按天计费单价/元',formatter:function(value,row,i){return initPrice(value,row,i,'D')}},
                {field: '',title: '按月计费单价/元',formatter:function(value,row,i){return initPrice(value,row,i,'M')}},
                {field: '',title: '按年计费单价/元',formatter:function(value,row,i){return initPrice(value,row,i,'Y')}},
                {field: '',title: '操作',formatter:function(i,row){
                    str =  '<a href="/admin/spec/add?id='+row.id+'">修改</a> | ';
                    str+=  '<a href="javascript:;" onclick="cloneSpec('+row.id+')">复制</a> ';
                    return str;
                    
                }}

            ],
            queryParams: function queryParams(params) {   //设置查询参数  
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
    function initPrice(value,row,i,field){
        str = '-';
        if( row.price==null || row.price ==""){
            return '-';
        }
        arr=row.price.split(" ");
        for (x in arr)
        {
            if(arr[x].indexOf(field) >0){
                str =  '￥'+Number(arr[x].replace(field,"")).toFixed(4);
            }
        }
        return str
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