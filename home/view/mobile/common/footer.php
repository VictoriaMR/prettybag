<div id="pb-footbar">
	<?php $router = \Router::$_route;?>
	<a class="tab" href="<?php echo url();?>">
		<span class="iconfont icon-shouye<?php echo $router['path']=='Index'&&$router['func']=='index'?'fill':'';?>"></span>
		<p class="text">首页</p>
	</a>
	<a class="tab" href="<?php echo url('cart');?>">
		<span class="iconfont icon-gouwuche<?php echo $router['path']=='Cart'&&$router['func']=='index'?'fill':'';?>"></span>
		<p class="text">购物车</p>
	</a>
	<a class="tab" href="<?php echo url('order');?>">
		<span class="iconfont icon-dingdan<?php echo $router['path']=='Order'&&$router['func']=='index'?'fill':'';?>"></span>
		<p class="text">订单</p>
	</a>
	<a class="tab" href="<?php echo url('message');?>">
		<span class="iconfont icon-liuyan<?php echo $router['path']=='Message'&&$router['func']=='index'?'fill':'';?>"></span>
		<p class="text">消息</p>
	</a>
	<a class="tab" href="<?php echo url('userInfo');?>">
		<span class="iconfont icon-wode<?php echo $router['path']=='UserInfo'&&$router['func']=='index'?'fill':'';?>"></span>
		<p class="text">我的</p>
	</a>
</div>
</body>
</html>