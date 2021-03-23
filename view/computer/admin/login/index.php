<?php $this->load('common/header');?>
<div id="login-bg">
	<img src="<?php echo siteUrl('image/computer/login_bg.jpg');?>">
</div>
<div class="login-box">
	<div class="poptip-content hidden">扫码登录更安全</div>
	<div class="title">
		<a class="f16 f800 select" href="javascript:void(0);">密码登录</a>
		<a class="f16 f800 hidden" href="javascript:void(0);">短信登录</a>
	</div>
	<div class="clear"></div>
	<form class="relative" style="padding-top: 50px;">
		<div id="login-error" class="hidden">
			<i class="iconfont icon-bang left orange"></i>
			<div id="login-error-msg" class="left ml4">请输入帐户名</div>
		</div>
		<div>
			<input type="text" class="input" name="phone" placeholder="请输入手机号码" autocomplete="off">
		</div>
		<div class="mt20">
			<input type="password" class="input" name="password" placeholder="请输入密码" autocomplete="off">
		</div>
		<div class="mt20">
			<input type="text" class="input w50 left" name="code" placeholder="验证码" autocomplete="off">
			<img id="refresh" class="left pointer ml10" height="38" width="80" src="<?php echo url('login/loginCode');?>" onclick="document.getElementById('refresh').src='<?php echo url('login/loginCode');?>'" title="看不清？换一张">
			<div class="clear"></div>
		</div>
		<button id="login-btn" type="button" class="btn btn-primary btn-lg btn-block mt20" data-loading-text="Loading...">登录</button>
	</form>
</div>
<script type="text/javascript">
$(function(){
	LOGIN.init();
});
</script>
<?php $this->load('common/footer');?>