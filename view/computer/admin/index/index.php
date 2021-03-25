<?php $this->load('common/header');?>
<div id="index-page">
	<div class="header"></div>
	<div class="body">
		<div class="nav-left">
			<div class="person">
				<div class="avatar">
					<img src="<?php echo $info['avatar'];?>">
				</div>
				<div class="info">
					<p class="e1 cf"><?php echo $info['name'];?>&nbsp;&nbsp;&nbsp;<?php echo $info['mem_id'];?></p>
					<p class="e1 cr"><?php echo $info['mobile'];?></p>
					<a href="<?php echo url('login/logout');?>" class="glyphicon glyphicon-off cr" title="退出登录"></a>
				</div>
			</div>
			<div class="left-content">
				<div class="left-one">
					<div class="toggle open" data-title="菜单切换开关">
						<span class="glyphicon glyphicon-align-justify"></span>
					</div>
				</div>
				<div class="left-two">
					<div class="title">
						<span class="text block c5f e1">页面标题</span>
						<span class="glyphicon glyphicon-backward"></span>
					</div>
				</div>
			</div>
		</div>
		<div class="content-right" style="background: transparent url(<?php echo url('login/signature');?>) repeat;">
			<iframe src="<?php echo url('index/statInfo');?>" width="100%" marginwidth="0" height="100%" marginheight="0" align="top" scrolling="Yes" frameborder="0" hspace="0" vspace="0"></iframe>
		</div>
		<div class="claer"></div>
	</div>
</div>
<script type="text/javascript">
$(function(){
	INDEX.init();
});
</script>
<?php $this->load('common/footer');?>