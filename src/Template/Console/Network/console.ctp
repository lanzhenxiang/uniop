<!-- Web控制台 布局 -->
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
    <?= $this->Html->css(['console/jquery-ui/jquery-ui-1.8.13.custom.css','console/console.css','console/jquery-custom.css']); ?>
    <?= $this->Html->script(['jQuery-2.1.3.min.js','console/constants.js','console/normalize-constants.js','console/core.js','console/sobey.js','console/event-manager.js','console/button-manager.js','console/button.js','console/console.js','console/debug.js','console/vcd-console.js']); ?>
</head>
<body lang="zh">
    <div id="toolbar"></div>
    <div id="mainContent"></div>
    <div id="footer"></div>
</body>
<script type="text/javascript">

$(function(){
	//初始化语言
	sobey.console.init();
	vmware.log.setPrinter(false);
	
	$('#footer').text("Connecting...".localize());
	webConsole = vmware.vcdConsole("mainContent", "toolbar", "footer");
	webConsole.init({
		//mode,messageMode,advancedConfig
		ticketPieces:{
			//host, thumb, allowSSLErrors,ticket, user, pass, vmid, datacenter, vmPath
			host:'<?= $ticketPieces['hostName'] ?>',
			thumb:'<?= $ticketPieces['hostSSLThumbprint'] ?>',
			allowSSLErrors:<?= $ticketPieces['allowSSLValidationErrors'] ?>,
			user:'<?= $ticketPieces['userName'] ?>',
			pass:'<?= $ticketPieces['password'] ?>',
			vmid:'<?= $ticketPieces['vmId'] ?>'
		}
	});
});
</script>
</html>