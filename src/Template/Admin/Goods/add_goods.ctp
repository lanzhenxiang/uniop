<!-- 主页模板 --><?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div id="maindiv-alert" class="content-list-page"></div>
    <div class="content-operate clearfix">

        <div class="clearfix" style="margin-top:20px;"></div>


        <form class="form-horizontal bv-form" id="aduser-form" action="" method="post">


        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">商品名称</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="name" id="name"  value="<?php if(isset($vinfo)){echo $vinfo->name;}?>" >
           </div>
        </div>

        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">商品分类</label>
            <div class="col-sm-6">

                  <select id="category_id" name="category_id"  class="form-control">
  
                                        <?php
                                          foreach($category as $key=>$c){
                                        ?>
                                              <option value="<?=$c->id?>" <?php if(isset($vinfo) && $c->id==$vinfo->category_id){ echo 'selected="selected"';}?> ><?=$c->name?></option>
                                        <?php
                                          }
                                        ?>
                   </select>
           </div>
        </div>
            <div class="form-group">
                <label for="inputPassword3" class="col-sm-2 control-label">排序</label>
                <div class="col-sm-6">
                    <input type="number" class="form-control" name="sort" id="sort" value="<?php if(isset($vinfo)){echo $vinfo->sort;}?>">
                </div>
            </div>
            <div class="form-group">
                <label for="inputPassword3" class="col-sm-2 control-label"></label>
                <div class="col-sm-6">
                    <span class="control-label text-danger">按从小到大的顺序排序</span>
                </div>
            </div>

        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">备注</label>
            <div class="col-sm-6">
                <textarea name="remark" class="form-control" data-bv-field="remark"><?php if(isset($vinfo)){echo $vinfo->remark;}?></textarea>
           </div>
        </div>

        

     <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                <input type="hidden" value="" name="" id="">
                    <button type="submit" id="account_submit" class="btn btn-primary">提交</button>
                    <!--<a type="button" href="/admin/goods/index-new" class="btn btn-danger">返回</a>-->
                    <a type="button" onclick="window.history.go(-1)" class="btn btn-danger">返回</a>
                </div>
            </div>
       </form>


    </div>
</div>

<?=$this->Html->script(['adminjs.js','validator.bootstrap.js']); ?>
<?= $this->start('script_last'); ?>
<script type="text/javascript">
    $('#aduser-form').bootstrapValidator({
        fields:{
            sort:{
                group:'col-sm-6',
                validators:{
//                regexp:{
//                    regexp:/^[0-9]+$/ ,
//                    message: '请输入正整数'
//                },
                    greaterThan:{
                        value : 0,
                        inclusive : false,
                        message: '请输入大于等于0的排序'
                    },
                    lessThan:{
                        value : 10000,
                        inclusive : true,
                        message: '请输入小于10000的排序'
                    }
                }
            }
        }
    });



</script>
<?= $this->end() ?>