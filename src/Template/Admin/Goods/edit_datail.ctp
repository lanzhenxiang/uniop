<!-- 主页模板 -->
<script src="/uploadify/jquery.uploadify.min.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="/uploadify/uploadify.css">
   <script type="text/javascript" src="/js/ueditor.config.js"></script>
   <script src="/js/layer2/layer.js"></script>
    <!-- 编辑器源码文件 -->
    <script type="text/javascript" src="/js/ueditor.all.min.js"></script>
<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div id="maindiv-alert" class="content-list-page"></div>
    <div class="content-operate clearfix">

        <div class="clearfix" style="margin-top:20px;"></div>

        <!--<form class="form-horizontal bv-form" id="aduser-form" action="" method="post">-->
        <div class="form-horizontal bv-form">

        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">商品名称</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="name" id="name" disabled="disabled" value="<?php if(isset($vinfo)){echo $vinfo->name;}?>" >
           </div>
        </div>

        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">摘要</label>
            <div class="col-sm-6">
                <textarea name="description" class="form-control" data-bv-field="description"><?php if(isset($vinfo)){echo $vinfo->description;}?></textarea>
           </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">首页图片</label>
            <div class="col-sm-6">
                

               
                <table data-sort="sortDisabled">
    <tbody>
        <tr class="firstRow">
            <td  valign="top" width="150"> <input type="file"  value="" id="pic1">
                <input type="hidden" name="icon" value="<?php if(isset($vinfo)){echo $vinfo->icon;}?>" id="pic1input"/></td>
            <td  valign="top">  <input id="pic1button"  <?php if($vinfo->icon ==""){echo "style='display:none'";}?> type="button" class="btn btn-primary btn-sm" onclick="showPic('pic1input')" value="预览图片"></td>
        </tr>
        <tr>
            <td valign="top" rowspan="1" colspan="2">图片尺寸要求： 260 X 320 <span id="img1"><?php if($vinfo->icon ==""){echo "还未上传";}?></span></td>
        </tr>
    </tbody>
</table>

           </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">详情图片</label>
            <div class="col-sm-6">

               
                
                

<table data-sort="sortDisabled">
    <tbody>
        <tr class="firstRow">
            <td  valign="top" width="150"> <input type="file"  value="" id="pic3">
                <input type="hidden" name="picture" value="<?php if(isset($vinfo)){echo $vinfo->picture;}?>" id="pic3input"/></td>
            <td  valign="top">  <input id="pic3button"  <?php if($vinfo->picture ==""){echo "style='display:none'";}?>  type="button" class="btn btn-primary btn-sm" onclick="showPic('pic3input')" value="预览图片"></td>
        </tr>
        <tr>
            <td valign="top" rowspan="1" colspan="2">图片尺寸要求： 260 X 320 <span id="img2"> <?php if($vinfo->picture ==""){echo "还未上传";}?></span></td>
        </tr>
    </tbody>
</table>


           </div>
        </div>






        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">订单图片</label>
            <div class="col-sm-6">

<table data-sort="sortDisabled">
    <tbody>
        <tr class="firstRow">
            <td  valign="top" width="150"> <input type="file"  value="" id="pic2">
                 <input type="hidden" name="mini_icon" value="<?php if(isset($vinfo)){echo $vinfo->mini_icon;}?>" id="pic2input"/></td>
            <td  valign="top">  <input id="pic2button"  <?php if($vinfo->mini_icon ==""){echo "style='display:none'";}?>  type="button" class="btn btn-primary btn-sm" onclick="showPic('pic2input')" value="预览图片"></td>
        </tr>
        <tr>
            <td valign="top" rowspan="1" colspan="2">图片尺寸要求： 60 X 60 <span id="img3"><?php if($vinfo->mini_icon ==""){echo "还未上传";}?></span></td>
        </tr>
    </tbody>
</table>




           </div>
        </div>

      <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">广告信息</label>
            <div class="col-sm-8">
              

<script id="detail" name="detail" type="text/plain">
        <?php if(isset($vinfo)){echo $vinfo->detail;}?>
    </script>

           </div>
        </div>
        

     <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                <input type="hidden" value="<?php if(isset($vinfo)){echo $vinfo->id;}?>" name="id" id="id">
                    <button type="submit" id="account_submit" class="btn btn-primary">提交</button>
                    <!--<a type="button" href="/admin/goods/index-new" class="btn btn-danger">返回</a>-->
                    <a type="button"  onclick="window.history.go(-1)" class="btn btn-danger">返回</a>
                </div>
            </div>
       <!--</form>-->
        </div>


    </div>
</div>

<script>
    var ue;
function showPic(src){
    layer.closeAll()
    layer.open({
  type: 1,
  title:'预览',
  content: '<img src="/images/'+$("#"+src).val()+'" />'
});

}
        $(function() {
            $('#pic1').uploadify({
                'fileObjName' : 'upfile',
                'buttonText' : '选择文件',
                'swf'      : '/uploadify/uploadify.swf',
                'uploader' : '/admin/goods/image2',
                'onUploadSuccess' : function(file, data, response) {

                    var image = $.parseJSON(data);
                     $('#pic1url').attr("src","/images/"+image.name);
                     $('#pic1input').val(image.name);
                     $('#pic1button').show();

                     $("#img1").html("");

                     if(image.width !=260 || image.height != 320 ){
                        $('#img1').html('上传图片大小不为260 X 320，可能造成图片失真变形。');
                        $('#img1').css('color','red');
                     }
                }
            });

            $('#pic3').uploadify({
                'fileObjName' : 'upfile',
                'buttonText' : '选择文件',
                'swf'      : '/uploadify/uploadify.swf',
                'uploader' : '/admin/goods/image2',
                'onUploadSuccess' : function(file, data, response) {
                    var image = $.parseJSON(data);
                     $('#pic3url').attr("src","/images/"+image.name);
                     $('#pic3input').val(image.name);
                     $('#pic3button').show();
                     $("#img2").html("");

                     if(image.width !=260 || image.height != 320 ){
                        $('#img2').html('上传图片大小不为260 X 320，可能造成图片失真变形。');
                        $('#img2').css('color','red');
                     }

                }
            });

            $('#pic2').uploadify({
                'fileObjName' : 'upfile',
                'buttonText' : '选择文件',
                'swf'      : '/uploadify/uploadify.swf',
                'uploader' : '/admin/goods/image2',
                'onUploadSuccess' : function(file, data, response) {
                    var image = $.parseJSON(data);
                     $('#pic2url').attr("src","/images/"+image.name);
                     $('#pic2input').val(image.name);
                     $('#pic2button').show();
                     $("#img3").html("");

                     if(image.width !=60 || image.height != 60 ){
                        $('#img3').html('上传图片大小不为60 X 60，可能造成图片失真变形。');
                        $('#img3').css('color','red');
                     }
                }
            });

             ue = UE.getEditor('detail');
        });


    $('#account_submit').on('click',function(){
        if($("input[name='name']").val()==''){
            alert('请填写商品名称');
        }else{
            if($("textarea[name='description']").val()==''){
                alert('请填写商品摘要');
            }else{
                var description=$("textarea[name='description']").val();
                    if($("input[name='icon']").val()==''){
                        alert('请选择首页图片');
                    }else{
                        var icon=$("input[name='icon']").val();
                        if($("input[name='picture']").val()==''){
                            alert('请选择详情图片');
                        }else{
                            var picture=$("input[name='picture']").val();
                            if($("input[name='mini_icon']").val()==''){
                                alert('请选择订单图片');
                            }else{
                                var mini_icon=$("input[name='mini_icon']").val();
                                if(ue.hasContents()==false){
                                    alert('请输入广告信息');
                                }else{
                                    var detail=ue.getContent();
                                    $.ajax({
                                        async:true,
                                        url:"/admin/goods/editDatail",
                                        data:{description:description,icon:icon,picture:picture,mini_icon:mini_icon,detail:detail,id:$('#id').val()},
                                        type:'post',
                                        success:function(data){
                                            data= $.parseJSON(data);
                                            if(data.code==0){
                                                setTimeout(function () {
                                                    location.href = "<?php echo $this->Url->build(array('controller'=>'Goods','action'=>'indexNew'));?>";
                                                }, 500);
                                            }else{
                                                alert('修改商品详情失败');
                                            }
                                        }
                                    });
                                }
                            }
                        }
                    }
                }
            }
    });

</script>

<?= $this->end() ?>