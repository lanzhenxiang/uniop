<!-- 登陆页面布局 -->
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
        <?="启动云桌面"?>_<?= $system_info['name'] ?>
    </title>
    <meta name="viewport" content="width=device-width">
<?php 
    echo $this->Html->script('jQuery-2.1.3.min.js');
    echo $this->Html->script('layer/layer.js');
?>
</head>
<body lang="zh">
<?php 
    if (isset($_msg)){
        echo $this->Html->script('jQuery-2.1.3.min.js');
        echo $this->Html->script('layer/layer.js');
?>
<script type="text/javascript">
	alert("<?= $_msg  ?>");
	layer.msg("<?= $_msg  ?>",{
		time: 10000, //20s后自动关闭
	});
</script>
<?php 
    }
?>
</body>
</html>
