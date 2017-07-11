<!-- 主页模板 -->
<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div id="maindiv-alert" class="content-list-page"></div>
    <div class="content-operate clearfix">
<script src="/js/layer2/layer.js"></script>


<?php
  if($vinfo->type=='citrix'){
   ?>

 <form class="form-horizontal bv-form" id="aduser-form" action="" method="post" novalidate="novalidate">

  <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">品牌</label>
            <div class="col-sm-6">
                <input type="text" disabled="disabled" class="form-control" name="name" id="name"  value="<?php if(isset($priceInfo)){echo $priceInfo['brand'];}?>"  >
           </div>
        </div>

 </form>

<form class="form-horizontal bv-form" id="aduser-form" action="" method="post" novalidate="novalidate">

  <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">规格</label>
            <div class="col-sm-6">
                <input type="text" disabled="disabled" class="form-control" name="name" id="name"  value="<?php if(isset($priceInfo)){echo $priceInfo['name'];}?>"  >
           </div>
        </div>

 </form>

 <form class="form-horizontal bv-form" id="aduser-form" action="" method="post" novalidate="novalidate">

  <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">硬件套餐</label>
            <div class="col-sm-6">
                <input type="text" disabled="disabled" class="form-control" name="name" id="name"  value="<?php if(isset($priceInfo)){echo $priceInfo['instancetype_name'];}?>"  >
           </div>
        </div>

 </form>

 <form class="form-horizontal bv-form" id="aduser-form" action="" method="post" novalidate="novalidate">

  <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">镜像</label>
            <div class="col-sm-6">
                <input type="text" disabled="disabled" class="form-control" name="name" id="name"  value="<?php if(isset($priceInfo)){echo $priceInfo['image_name'];}?>"  >
           </div>
        </div>

 </form>

 <form class="form-horizontal bv-form" id="aduser-form" action="" method="post" novalidate="novalidate">

  <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">价格</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="price" id="price"  value="<?php if(isset($priceInfo)){echo $priceInfo['price'];}?>"  >
           </div>
        </div>


 <div class="form-group">

             <label for="inputPassword3" class="col-sm-2 control-label">计价单位</label>
            <div class="col-sm-6">
                <select name="unit" class="form-control">
                    <option value="1" <?php if(isset($priceInfo) && $priceInfo['unit'] == 1){echo 'selected=selected';}?>> 秒</option>
                    <option value="60" <?php if(isset($priceInfo) && $priceInfo['unit'] == 60){echo 'selected=selected';}?>>分</option>
                </select>
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


   <?php
   }else if($vinfo->type=='bs'   || $vinfo->type == "vfw" || $vinfo->type == "waf"){
?>
<script>
  function openAdd(){
    layer.open({
        title:'添加价格',
        area: ['500px'],
        content: '<div id="addDialog">'+$("#add").html()+"</div>",
        btn: ['添加', '取消'],
        yes: function(index, layero){
              unit = $("#addDialog #unit").val();
              if(unit < 0){
                  alert('时长不能小于0')
                  return
              }
              price = $("#addDialog #price").val();
              $.post(window.location.href+"&ac=addPost",{unit:unit,price:price},function(result){
                  layer.closeAll()
                  if(result == "ok"){
                      layer.alert('添加成功', {icon: 6});
                      setTimeout( function(){location.reload() }, 1 * 1000 );
                  }else{
                      layer.alert('添加失败', {icon: 5});
                  }
              });
          },
        btn2: function(index, layero){},
        scrollbar: false
      });
  }

function openDel(){
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
    '您确认要删除这些价格？',
    {
        btn: ['确认','取消']
    },
    function(){
       $.post(window.location.href+"&ac=del",{ids:id},function(result){
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
 function openEdit(id){
  layer.open({
        title:'编辑价格价格',
        area: ['500px'],
        content: '<div id="editDialog">'+$("#add").html()+"</div>",
        btn: ['修改', '取消'],
        yes: function(index, layero){
              unit = $("#editDialog #unit").val();
              price = $("#editDialog #price").val();
              if(unit < 0){
                  alert('时长不能小于0')
                  return
              }
              $.post(window.location.href+"&ac=editPost",{id:id,unit:unit,price:price},function(result){
                  layer.closeAll()
                  if(result == "ok"){
                      layer.alert('编辑成功', {icon: 6});
                      setTimeout( function(){location.reload() }, 1 * 1000 );
                  }else{
                      layer.alert('编辑失败', {icon: 5});
                  }
              });
          },
        btn2: function(index, layero){},
        scrollbar: false
      });
     $("#editDialog #unit").val($("#unit_"+id).html())
     $("#editDialog #price").val($("#price_"+id).html())

 }

</script>
<div id="add" style="display:none">
        <table width="100%">
          <tr>
            <td height="50px">时长</td>
            <td><input type="text" class="form-control" name="unit" id="unit" value=""></td>
          </tr>
          <tr>
            <td>价格</td>
            <td><input type="text" class="form-control" name="price" id="price" value=""></td>
          </tr>
        </table>
</div>

<div class="content-operate clearfix">
  <div class="pull-left">
          

             <a type="button" href="javascript:;" onclick="openAdd()" class="btn btn-addition pull-left" style="margin-right: 10px"><i class="icon-plus" ></i>&nbsp;&nbsp;添加价格</a>

            <a type="button" href="javascript:;" onclick="openDel()" class="btn btn-addition pull-left" ><i class="icon-remove"></i>&nbsp;&nbsp;删除</a>

 </div>

</div>

<table id="mainTable" class="table table-hover table-striped">
  <thead>
    <tr>
        <th ><div class="th-inner ">id</div></th>
        <th ><div class="th-inner ">使用时长 （月）</div></th>
        <th ><div class="th-inner ">价格（元）</div></th>
        <th ><div class="th-inner ">操作</div></th>
        </tr>
    </thead>
  <tbody>
      <?php foreach($price as $key=>$value){ ?>
      
      <tr id="1" data-index="0">
        <td style=""><input name="ids" type="checkbox" value="<?=$value['id']?>" /></td>
        <td id="unit_<?=$value['id']?>" style=""><?=$value['unit']?></td>
        <td id="price_<?=$value['id']?>" style=""><?=$value['price']?></td>
        <td id="" style=""><a href="javascript:;" onclick="openEdit(<?=$value['id']?>)">编辑</a></td>
      </tr>
      <?php } ?>

</tbody></table>




<?php }else if($vinfo->type=="ecs"){ ?>

<h2>云主机定价说明：</h2>

<p>1、管理员统一到计算能力列表或系统镜像列表中定义计费规则后，系统会自动识别;</p>

<p>2、访问入口为：</p>

     <p>云计算规格管理 -> 计算能力列表</p>

     <p>云计算规格管理 -> 系统镜像列表</p>

<?php
  
}else if(in_array($vinfo->type,['eip','vpc','elb','disks'])){
  ?>
  <h2>EIP、VPC、ELB、块存储、定价说明：</h2>

<p>1、管理员统一到其他计费里设置，系统会自动识别;</p>

<p>2、访问入口为：</p>

     <p>云计算规格管理 -> 其他计费</p>

<?php }else{?>
 <form class="form-horizontal bv-form" id="aduser-form" action="" method="post" novalidate="novalidate">
        
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">价格</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="price" id="price"  value="<?php if(isset($priceInfo) && isset($priceInfo->id)){echo $priceInfo->price;}?>"  >
           </div>
        </div>


 <div class="form-group">

             <label for="inputPassword3" class="col-sm-2 control-label">计价单位</label>
            <div class="col-sm-6">
                <select name="unit" class="form-control">
                    <option value="S" <?php if(isset($priceInfo) && isset($priceInfo->id) && $priceInfo->interval == 'S'){echo 'selected=selected';}?>> 秒</option>
                    <option value="I" <?php if(isset($priceInfo) && isset($priceInfo->id) && $priceInfo->interval == 'I'){echo 'selected=selected';}?>> 分</option>
                </select>
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

 </form>

  <?php


} ?>





</div></div>

<script type="text/javascript">
</script> 
<?= $this->end() ?>