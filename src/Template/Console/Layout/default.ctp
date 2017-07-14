<!-- 首页页面布局 -->
<?= $this->element('seo',['_request_params',$_request_params]); ?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" > <!--<![endif]-->
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

  <?= $this->Html->css(['normalize.css','bootstrap.css','bootstrap-table.css','jquery-ui-1.10.0.custom.css','font-awesome.min.css','style.css','styleBack.css','pnotify.core.min.css','swiper.css','sb.css']); ?>
  <?= $this->Html->script('jQuery-2.1.3.min.js'); ?>


  <?= $this->fetch('meta') ?>
  <?= $this->fetch('css') ?>
  <?= $this->fetch('script') ?>
</head>
<body lang="zh">
  <?= $this->element('header'); ?>
  <div class="clearfix wrap-nav button">
    <?= $this->element('left'); ?>
    <div class="wrap-nav-right wrap-index-page">
    <?=$this->Flash->render();?>
    </div>
    <?= $this->fetch('content') ?>
  </div>

  <!-- jQuery first, then Bootstrap JS. -->
  <?= $this->Html->script(['jQuery-2.1.3.min.js','bootstrap.min.js','bootstrap-table.js','jquery-ui-1.10.0.custom.min.js','plugins.js','jquery.mouse-content.js','jquery.cookie.js','Chart.js','all-page.js','socket/swfobject.js','socket/web_socket.js','pnotify.core.min.js','jquery.cropit.js','swiper.jquery.min.js','validator.bootstrap.js','jQuery.fn.extend.js','common/bootstrap-lists.formatter.js','layer/layer.js']); ?>
  <?= $this->fetch('script_last') ?>
</body>

</html>