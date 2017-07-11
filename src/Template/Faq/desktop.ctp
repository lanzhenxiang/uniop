<div class="desktop-main" style="background:#f1f1f1">
	<div class="desktop-panel">
		<div class="desktop-header">
			<h5>帮助中心 - 云桌面</h5>
		</div>
		<div class="desktop-body">
			<ul class="nav nav-tabs" style="margin-top:15px;">
				<li class="active"><a href="#desktop-fail" data-toggle="tab">常见问题</a></li>
				<li><a href="#desktop-download" data-toggle="tab">用户手册</a></li>
				<li><a href="#desktop-info" data-toggle="tab">云桌面插件下载</a></li>
			</ul>

			<div class="desktop-tab-content tab-content">
				<div class="desktop-tab-panel tab-pane active" id="desktop-fail">
					<h3>登录桌面失败</h3>
					<ul>
						<li>
							1. <a href="#anchor1">使用云桌面必须做些什么准备工作？</a>
						</li>
						<li>
							2. <a href="#anchor2">云桌面支持哪些使用终端？</a>
						</li>
						<li>
							3. <a href="#anchor3">用户已经成功安装云桌面插件和安全证书，为什么在连接时仍弹框提示“连接失败，状态为（1110）”？</a>
						</li>
						<li>
							4. <a href="#anchor4">云桌面插件和安全证书都安装正确，仍无法连接云桌面？</a>
						</li>
					</ul>
					<div class="desktop-faq-text">
						<h4 id="anchor1">使用云桌面必须做些什么准备工作？</h4>
						<p>必须先安装云桌面插件和安全证书，具体的安装步骤请到用户手册中，根据你的使用终端下载对应终端的云桌面使用手册。</p>
						<p>1) 通常在没有安装云桌面插件会弹窗提示框如下图：</p>
						<img src="/images/faq1.png" />
						<p>2) 未安装安全证书会弹出提示框如下图：</p>
						<img src="/images/faq2.png" />
						<h4 id="anchor2">云桌面支持哪些使用终端？</h4>
						<p>支持Windows、Mac OSX、iPad三种使用终端。</p>
						<h4 id="anchor3">用户已经成功安装云桌面插件和安全证书，为什么在连接时仍弹框提示“连接失败，状态为（1110）”？</h4>
						<p>这种情况是由于你要使用的云桌面是管理员在新的安全域发布的，需要安装新的安全证书，请点击桌面图标右边 “下载桌面证书”按钮安装，如下图：</p>
						<img src="/images/faq3.png" />
						<h4 id="anchor4">云桌面插件和安全证书都安装正确，仍无法连接云桌面</h4>
						<p>出现这种情况，是因远程终端无法访问Ctrix网关所导致的。请按下图所示方法检测本地是否能访问Ctrix网关，若能打开Ctrix登录页面，则说明是由其它原因导致的，请联系管理员排查问题；若不能打开Ctrix登录页面，则说明是本地的网络环境所致，请联系本地网管解决。<br/>
						<a onclick="check()">检测Citrix网关</a>
						</p>
						<img src="/images/faq4.png" />
						<h6 style="margin-top:10px;">Ctrix网关登录界面如图</h6>
					</div>
				</div>
				<div class="desktop-tab-panel tab-pane" id="desktop-download">
					<h5>下载</h5>
					<ul>
						<li>
							<a href="/docs/云桌面安装使用手册(Windows).pdf">[下载云桌面使用手册(windows)]</a>
						</li>
						<li>
							<a href="/docs/云桌面安装使用手册(Mac).pdf">[下载云桌面使用手册(mac)]</a>
						</li>
						<li>
							<a href="/docs/云桌面安装使用手册(iPad).pdf">[下载云桌面使用手册(ipad)]</a>
						</li>
					</ul>
				</div>
				<div class="desktop-tab-panel tab-pane" id="desktop-info">
					<p>首次使用媒体桌面前,需要下载安装桌面控件</p>
					<h5>下载</h5>
					<ul>
						<li>
							<a version="14.1.200.13" href="/clients/Windows/CitrixReceiver.exe" >[下载用于Windows的安装包]</a>
						</li>
						<li>
							<a minimumSupportedOSVersion="10.6" version="11.8.2.255309" href="/clients/Mac/CitrixReceiver.dmg" >[下载用于Mac OSX的安装包]</a>
						</li>
						<li>
							<a  href="https://itunes.apple.com/us/app/citrix-receiver/id363501921?mt=8#" >[下载用于IPad的安装包]</a>
						</li>
					</ul>
					<p>下载完成后请双击或者右键启动安装.(安装前请关闭杀毒软件,如360防火墙等)</p>
					<p>安装完控件请重启浏览器.</p>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- 修改 -->
<div class="modal fade" id="modal-modify" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h5 class="modal-title">检测Citrix网关</h5>
      </div>
      <form id="modal-modify-form" action="" method="post">
        <div class="modal-body">
          <div class="modal-form-group">
            <div>
              桌面名称:&nbsp;&nbsp;<input id="desktop-name" name="name" type="text" /><span id="desktop-err" class="text-danger"></span>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button id="sumbiter" type="button" class="btn btn-primary">检查</button>
          <button type="button" class="btn btn-danger" data-dismiss="modal">取消</button>
        </div>
      </form>

    </div>
  </div>
</div>

<script type="text/javascript">
	function check(){
		$("#desktop-name").val('');
		$("#desktop-err").html("");
		$("#modal-modify").modal('show');
	}

	$("#sumbiter").on('click',function(){
		$("#desktop-err").html("");
		var name = $("#desktop-name").val();
		if(name == null ||name ==''){
			$("#desktop-err").html("请输入桌面名称");
			return false;
		}
		$.ajax({
              url:'<?= $this->Url->build(['controller'=>'faq','action'=>'check']); ?>/'+name,
              method:'post',
              dataType:'json',
              success:function(data){
              	$("#modal-modify").modal('hide');
              	console.log(data.msg);
            	if (data.code == '0') {
            		url = data.data;
                	window.open(url);
            	} else {
                	alert(data.msg);
            	}
              }
            });
	})
</script>