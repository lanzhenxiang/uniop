<?= $this->element('desktop/lists/left',['active_action'=>'group']); ?>
<script src="/js/layer2/layer.js"></script>
<div class="wrap-nav-right">
    <div class="wrap-manage">
        <div class="top">
            <span class="title">桌面分组与弹性管理</span>
            <div id="maindiv-alert"></div>
        </div>

        <div class="center clearfix">
            <div class="pull-left">
                <a href="javascript:;" class="btn btn-default" onclick="refresh()">刷新</a>
                <a class="btn btn-default" href="javascript:" id="add_group">建立分组</a>
                <a href="javascript:;" class="btn btn-default" onclick="del()">删除</a>
            </div>
            <!--筛选-->
            <div class="input-group content-search pull-right col-sm-3">
                <input type="text" class="form-control" id="txtsearch" placeholder="搜索分组名">
                 <span class="input-group-btn">
                     <button class="btn btn-primary" id="search-btn" type="button">搜索</button>
                 </span>
            </div>

            <div class="pull-right">
                <input type="hidden" id="txtdeparmetId" value="<?= $department["id"] ?>" />
                <?php if (in_array('ccf_all_select_department', $this->request->session()->read('Auth.User.popedomname'))) { ?>
              <div class="dropdown">
                租户:
                <a href="#" class="dropdown-toggle btn btn-addition text-right" data-toggle="dropdown" role="button">
                    <span class="pull-left" id="deparmets" val="<?= $department["id"] ?>"><?= $department["name"] ?></span>
                    <span class="caret"></span>
                </a>
                <ul class="dropdown-menu">
                      <?php foreach($departments as $value) { ?>
                         <li><a href="#" onclick="departmentlist(<?php echo $value['id'] ?>,'<?php echo $value['name'] ?>')"><?php echo $value['name'] ?></a></li>
                      <?php }?>
                </ul>
              </div>
              <?php }?>
              
            </div>

            <div style="clear: both;"></div>
        </div>
        <!--表格-->
        <div class="tab-content bot">
            <table id="group" class="bot">
            
             </table>
        </div>

    </div>
</div>

<!--新建分组-->
<div class="modal fade" id="modal-add" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h5 class="modal-title">新建桌面分组</h5>
            </div>

            <div class="modal-body">

                <div class="modal-form-group">
                    <label style="width: 150px;">为该租户新建桌面分组:</label>
                    <div class="col-sm-6">
                        <select name="add_department" id="add_department" class="form-control form-control-blue" onchange="changeGroup()">
                            <?php if(isset($departments)){?>
                            <?php foreach($departments as $key => $value){?>
                            <option value="<?= $value['id'];?>" <?php if(isset($this_depart)&&$value['id']==$this_depart['id']){echo 'selected';}?>><?= $value['name'];?></option>
                            <?php }?>
                            <?php }?>
                        </select>
                    </div>

                </div>
                <div class="modal-form-group">
                    <label for="" style="width: 150px;"></label>
                    <div class="col-sm-6">
                        <span class="text-danger" id="department_cue"></span>
                    </div>
                </div>


            </div>
            <div class="modal-footer">
                <button id="yes_add" type="submit" class="btn btn-primary">确认</button>
                <button id="add_cancel" type="button" class="btn btn-danger"data-dismiss="modal">取消</button>
            </div>

        </div>
    </div>
</div>
<div class="modal fade" id="modal-msg" tabindex="-1" role="dialog"></div>

<?= $this->start('script_last'); ?>
<script type="text/javascript">
function checkAll(){

    if(document.getElementById("checkAll").checked){   
        $("tbody :checkbox[name='ids']").prop("checked", true);  
    }else{   
        $("tbody :checkbox[name='ids']").prop("checked", false);
    }   

}
function departmentlist(id,name){
  $("#deparmets").attr('val',id);
  $("#deparmets").html(name);
    $('#add_department').val(id);
  refresh()
}
function initTable() {  
        //初始化表格,动态从服务器加载数据  
        $("#group").bootstrapTable({  
            method: "get",  //使用get请求到服务器获取数据  
            url: "/console/desktop/group_data", //获取数据的Servlet地址  
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

                {field: 'id',title: '<input  onclick="checkAll()" id="checkAll" value="" type="checkbox">',width:"29",formatter:function(i,row){
                    return '<input name="ids"  value="'+row.id+'" type="checkbox">';
                }},
                {field: 'software_name',title: '分组名'},
                {field: 'software_code',title: '分组code'},
                {field: 'sort_order',title: '分组排序'},
                {field: '',title: '操作',formatter:function(i,row){
                    str = ' <a href="/console/desktop/desktopGroup?id='+row.id+'">管理桌面</a> ';
                    str += ' <a href="/console/desktop/group_policy?id='+row.id+'">管理弹性策略</a> ';
                    str += ' <a href="/console/desktop/group_add?id='+row.id+'">修改分组</a> ';
                    return str
                    
                }},
                {field: 'note',title: '备注'}

            ],
            queryParams: function queryParams(params) {   //设置查询参数  
              var param = {    
                  pageNumber: params.pageNumber,    
                  pageSize: params.pageSize,
                  department_id:$("#deparmets").attr('val') ,
                  name:$("#txtsearch").val()
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
          $("#search-btn").bind("click", function(){
                refresh()
          });

 

          
      });
      function sub(s, n) {
　　　　return s.slice(0, n).replace(/([^x00-xff])/g, "$1a").slice(0, n).replace(/([^x00-xff])a/g, "$1");
　　}
      function refresh(){
            $("#group").bootstrapTable('refresh')
     }

//新建
$('#add_group').on('click', function () {
    <?php if(in_array('ccf_all_select_department', $this->Session->read('Auth.User.popedomname'))) { ?>
       $('#modal-add').modal('show');
       <?php }else{?>
        location.href = "<?=$this->Url->build(['controller'=>'Desktop','action'=>'groupAdd']);?>";
       <?php }?>
    });
$('#yes_add').on('click',function(){
    var department_id=$('#add_department').val();
    if(department_id==0){
        $('#department_cue').html('请选择一个租户');
    }else {
        location.href = "<?=$this->Url->build(['controller'=>'Desktop','action'=>'groupAdd']);?>?department_id="+department_id;
    }
});
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

  layer.confirm(
    '您确认要删除这些分组？',
    {
        btn: ['确认','取消']
    },
    function(){
       $.post("/console/desktop/group_del",{ids:id},function(result){
                  layer.closeAll()
                  if(result == "ok"){
                      layer.alert('删除成功', {icon: 6});
                      refresh()
                  }else{
                      layer.alert('删除失败', {icon: 5});
                  }
              });
    })

}
</script>
<?= $this->end() ?>