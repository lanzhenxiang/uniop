<style>
    .point-host-startup{
        margin-right: 150px;
    }
</style>
<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div id="maindiv-alert"></div>
    <form class="form-horizontal" id="editsoft-form"
          action="<?php echo $this->Url->build(array('controller' => 'Software','action'=>'postedit')); ?>"
          method="post">
        <div>
            <div class="content-operate clearfix">
                <div class="pull-left">
                    <span style="font-size: 20px;">修改工具分类</span>
                </div>

            </div>
            <hr>
            <!--添加内容-->
            <input type="hidden" name="id" value="<?=$id?>">
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="home">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">分类名:</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="software_name" id="software_name" value="<?=$data['software_name']?>">
                        </div>
                    </div>
                    <div class="form-group" >
                        <label for="type_note" class="col-sm-2 control-label"></label>
                        <div class="col-sm-6">
                            <span>工具中心界面中工具分类的检索条件</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">缩略图:</label>
                        <div class="col-sm-4">
                            <input type="hidden" id="icon_file" name="icon_file">
                            <input type="file" accept="image/*" name="upfile[]" id="userfile" multiple data-show-preview="false" class="file" onchange="change(event);">

                        </div>
                        <span>图片尺寸要求</span>
                    </div>
                    <div class="form-group" >
                        <label for="type_note" class="col-sm-2 control-label"></label>
                        <div class="col-sm-6">
                            <span>该图片用于在工具中心展示工具的缩略图</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">厂商:</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="product_name" id="product_name" value="<?=$data['product_name']?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">分类排序:</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="sort_order" id="sort_order" value="<?=$data['sort_order']?>">
                        </div>
                    </div>
                    <div class="form-group" >
                        <label for="type_note" class="col-sm-2 control-label"></label>
                        <div class="col-sm-6">
                            <span>工具中心界面中工具的分类的排序，排序相同的分类，按名字升序排列</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">备注</label>
                        <div class="col-sm-6">
                            <textarea type="text" class="form-control" rows="6" name="note" id="note"><?=$data['note']?></textarea>
                        </div>
                    </div>
                    <div class="form-group" >
                        <label for="type_note" class="col-sm-2 control-label"></label>
                        <div class="col-sm-6">
                            <span>最多录入200字</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" id="submit_edit" class="btn btn-primary">保存</button>
                            <!--<a type="button" href="<?php echo $this->Url->build(array('controller' => 'Software','action'=>'index')); ?>" class="btn btn-danger">返回</a>-->
                            <a type="button" onclick="window.history.go(-1)" class="btn btn-danger">返回</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<?=$this->Html->script(['adminjs.js','bootstrap-datetimepicker.js','validator.bootstrap.js','jquery.cookie.js']); ?>
<?= $this->start('script_last'); ?>
<script type="text/javascript">


    //上传图片
    $("#userfile").fileinput({
        uploadUrl: "<?php echo $this->Url->build(array('controller'=>'Goods','action'=>'image'));?>", // server upload action
        uploadAsync: false,
        showPreview: true,
        allowedFileExtensions: ['jpg', 'png', 'gif'],
        maxFileCount: 5
    }).on("filebatchselected", function(event, files,e) {
        $(this).fileinput("upload");
    });


    function change(e){
        var src=e.target || window.event.srcElement;
        var filename=src.value;
        $('#icon_file').val(filename.substring( filename.lastIndexOf('\\')+1 ) );
    }

    //提交
    $('#editsoft-form').bootstrapValidator({
        submitButtons: 'button[type="submit"]',
        submitHandler: function(validator, form, submitButton){
            $.post(form.attr('action'), form.serialize(), function(data){
                var data = eval('(' + data + ')');
                if (data.code == 0) {
                    tentionHide(data.msg,0);
                    setTimeout(function () {
//                        location.href = "<?php echo $this->Url->build(array('controller'=>'Software','action'=>'index'));?>";
                        window.location.reload();
                    }, 500);

                } else {
                    tentionHide(data.msg,1);
                }
            });
        },
        fields : {
            software_name: {
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '分类名不能为空'
                    },
                    stringLength: {
                        min: 2,
                        max: 16,
                        message: '请保持在2-16位'
                    },
                    regexp: {
                        regexp: /^\S*$/,
                        message: '分类名不能有空格'
                    }
                }
            },
            product_name: {
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '厂商不能为空'
                    },
                    stringLength: {
                        min: 2,
                        max: 16,
                        message: '请保持在2-16位'
                    }
                }
            },
            sort_order: {
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '排序不能为空'
                    },
                    between: {
                        min: 0,
                        max: 1000,
                        message: '排序只能在0-1000之间'
                    },
                    regexp: {
                        regexp: /^([1-9]\d*|0)$/,
                        message: '请输入整数'
                    }
                }
            }


        }
    });
</script>
<?= $this->end() ?>