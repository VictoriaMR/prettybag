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
					<div class="nav-content">
						<ul>
							<li data-title="概览" data-to="about-view">
								<span class="glyphicon glyphicon-eye-open"></span>
								<span class="ml6">概览</span>
							</li>
							<li data-title="管理人员" data-to="guanliyuan" data-src="<?php echo url('member/memberList');?>">
								<span class="glyphicon glyphicon-object-align-left"></span>
								<span class="ml6">管理人员</span>
							</li>
							<li data-title="系统设置">
								<span class="glyphicon glyphicon-cog"></span>
								<span class="ml6">系统设置</span>
							</li>
						</ul>
					</div>
				</div>
				<div class="left-two">
					<div class="title">
						<span class="text block c5f e1">页面标题</span>
						<span class="glyphicon glyphicon-backward" title="收起"></span>
					</div>
					<div class="nav-son-content">
						<div class="item" data-for="about-view">
							<ul>
								<li data-src="<?php echo url('index/statInfo');?>" class="selected">
									<span class="glyphicon glyphicon-user"></span>
									<span class="ml6">全部概览</span>
									<a class="glyphicon glyphicon-link right" title="新窗口打开" target="_blank" href="<?php echo url('index/statInfo');?>"></a>
								</li>
							</ul>
						</div>
						<div class="item" data-for="guanliyuan">
							<ul>
								<li data-src="<?php echo url('member/memberList');?>" class="selected">
									<span class="glyphicon glyphicon-user"></span>
									<span class="ml6">人员列表</span>
									<a class="glyphicon glyphicon-link right" title="新窗口打开" target="_blank" href="<?php echo url('member/memberList');?>"></a>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="content-right" style="background: transparent url(<?php echo url('login/signature');?>) repeat;">
			<iframe src="javascript:;" id="href-to-iframe" width="100%" marginwidth="0" height="100%" marginheight="0" align="top" scrolling="Yes" frameborder="0" hspace="0" vspace="0"></iframe>
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