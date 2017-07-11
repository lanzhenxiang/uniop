<!-- login content -->
	<div class="login-content">
		<div class="login-logo">
			<?= $this->Html->image('login-logo.png'); ?>
		</div>
		<div class="login-header">
			<h1 class="text-center">欢迎登录</h1>
		</div>
        <form id="form" method="post" action="login">
            <div class="login-body">
                <div class="login-field">
                    <label>帐号</label>
                    <input type="text" name="loginname" data-container="body" data-placement="right" data-toggle="popover" data-content="请输入用户名" data-trigger="focus">
                </div>
                <div class="login-field">
                    <label>密码</label>
                    <input type="password" name="password" data-container="body" data-placement="right" data-toggle="popover" data-content="请输入密码" data-trigger="focus">
                </div>
            </div>
            <span style="text-align: center;color: red"><?php echo  $this->Flash->render(); ?></span>
            <div class="login-footer">
                <button class="btn btn-primary" id="login" type="button">登 录</button>
            </div>
        </form>
	</div>




<?php  $this->start('script_last'); ?>
<script>
    $(function () {
        $('[data-toggle="popover"]').popover();
        document.onkeydown = function (event){
            if (event.keyCode==13) //回车键的键值为13
                $('#form').submit();
        };

    });

    $('#login').on('click',function(){
        $('#form').submit();
    })

</script>
<?php $this->end(); ?>