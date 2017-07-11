<!-- 首页页面布局 -->
<?= $this->element('seo',['_request_params',$_request_params]); ?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7" ng-app> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8" ng-app> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9" ng-app> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" ng-app> <!--<![endif]-->
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

    <?= $this->Html->css(['normalize.css','jquery-ui-1.10.0.custom.css','bootstrap.css','swiper.css','style.css','font-awesome.min.css']) ?>
	<?= $this->Html->script(['jQuery-2.1.3.min.js',"angular.min.js"]); ?>
    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>
</head>
<body lang="zh zh_Hans">
    
    <?= $this->fetch('content') ?>
    
    
    <!-- jQuery first, then Bootstrap JS. -->
    <?= $this->Html->script(['bootstrap.min.js','plugins.js']); ?>
    <?= $this->fetch('script_last') ?>
<script type="text/javascript">
$(function(){
	// AJAX Request: My API (http://citrix.cmop.sobey.com/Citrix/StoreWeb/Home/Configuration)

	$.ajax({
		url: "http://citrix.cmop.sobey.com/Citrix/StoreWeb/Home/Configuration",
		type: "POST",
		timeout: 30000,
		dataType:'xml',

		// Success Callback: http://citrix.cmop.sobey.com/Citrix/StoreWeb/Home/Configuration

		success:function(data, textStatus) {
			console.log("Received response HTTP "+textStatus+" (http://citrix.cmop.sobey.com/Citrix/StoreWeb/Home/Configuration)");
			console.log(data);
		},

		// Error Callback: http://citrix.cmop.sobey.com/Citrix/StoreWeb/Home/Configuration

		error:function(jqXHR, textStatus, errorThrown) {
			console.log("Error during request "+textStatus+" (http://citrix.cmop.sobey.com/Citrix/StoreWeb/Home/Configuration)");
			console.log(errorThrown);
		},
	});
	    
});
</script>

</body>
</html>