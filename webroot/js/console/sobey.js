/**
 * 索贝控制台
 */
//定义索贝全局变量
var sobey = (typeof sobey == "undefined" || !sobey) ? {} : sobey;
//索贝web控制台
sobey.console = {};
//索贝控制台语言
sobey.console.lang ={
	'Connecting...':'连接中...',
	'Power On':'启动',
	'Suspend':'暂停',
	'Power Off':'关闭',
	'Reset':'重启',
	'Send ctrl+alt+del (disabled)':'发送ctrl+alt+del(禁用)',
	'Open in fullscreen (disabled)':'全屏(禁用)',
	'Connected':'已连接',
	'Error connecting':'连接失败' 
}

sobey.console.imagesUrl = 'http://cmop.zrtg.com/images';

sobey.console.init = function(){
	//初始化语言选项
	i18n.addAll(sobey.console.lang);
};
