<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div class="content-operate clearfix">
        <div class="pull-left">
            <a type="button"  href="/excel/fics.xlsx" class="btn btn-addition pull-left">下载模板</a>

           
        </div>
    </div>
    <form action="<?php echo $this->Url->build(array('controller' => 'Fics','action'=>'upexcel')); ?>?action=1" method="post" enctype="multipart/form-data" id="up-excel">
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">选择上传文件</label>
            <div class="col-sm-6">
                <input type="file" accept=".xls,.xlsx,.xl" name="userfile" data-show-preview="false" class="file">
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10"  style = "margin-top:10px;">
                <input type="submit" value="导入" class="btn btn-primary import">
            </div>
        </div>
    </form>
</div>
