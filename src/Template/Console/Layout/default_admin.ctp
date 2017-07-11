<!-- 首页页面布局 -->
<?= $this->element('seo',['_request_params',$_request_params]); ?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <?= $this->Html->meta('icon') ?>
    <title>
        <?= $this->fetch('title') ?>_<?= $system_info['name'] ?>
    </title>
    <meta name="viewport" content="width=device-width">


    <!-- Bootstrap CSS -->

    <?= $this->Html->css(['normalize.css','styleBack.css','bootstrap.css','bootstrap-table.css','admin.css','font-awesome.min.css','bootstrap-treeview.css','fileinput.min.css']) ?>
    <?= $this->Html->script(['jQuery-2.1.3.min.js']); ?>

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>
</head>
<body lang="zh">

<!--内容为资源中心内容-->
<?= $this->element('header');?>
<div class="main">
    <?= $this->element('sidebar');?>
    <div class="content">
        <?= $this->fetch('content') ?>
    </div>
</div>


<!-- jQuery first, then Bootstrap JS. -->
<?= $this->Html->script(['jQuery-2.1.3.min.js','bootstrap.min.js','plugins.js','bootstrap-treeview.js','fileinput.min.js','bootstrap-paginator.js','jquery.uploadify.min.js','bootstrap-table.js','adminjs.js']); ?>
<?= $this->fetch('script_last') ?>


</body>
</html>