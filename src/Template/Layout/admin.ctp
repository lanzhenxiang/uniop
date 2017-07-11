<!-- 管理页面布局 -->
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

    <?= $this->Html->css(['normalize.css','bootstrap.min.css']) ?>
    
    

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>
</head>
<body lang="zh">
    <?= $this->fetch('content') ?>
    <!-- jQuery first, then Bootstrap JS. -->
    <?= $this->Html->script(['jQuery-2.1.3.min.js','bootstrap.min.js','plugins.js']); ?>
    <?= $this->fetch('script_last') ?>
</body>
</html>