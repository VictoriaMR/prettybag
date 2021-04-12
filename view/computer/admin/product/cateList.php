<?php $this->load('common/header');?>
<div class="container-fluid">
	<div class="row-item">
		<div class="right">
            <button class="btn btn-info" type="button" style="width: 200px; margin-right: 20px;"><i class="glyphicon glyphicon-asterisk"></i> 显示统计数据</button>
            <button class="btn btn-success modify" data-id="0" type="button" style="width: 200px;"><i class="glyphicon glyphicon-plus"></i> 添加子类目</button>
        </div>
        <div class="clear"></div>
	</div>
	<table class="table table-hover mt20" id="data-list">
		<tbody>
	        <tr>
	            <th class="col-md-1">ID</th>
	            <th class="col-md-3">名称</th>
	            <th class="col-md-2">排序</th>
	            <th class="col-md-2">操作</th>
	        </tr>
	        <?php if (empty($list)){ ?>
        	<tr>
        		<td colspan="10">
        			<div class="tc orange">暂无数据</div>
        		</td>
        	</tr>
        	<?php } else {?>
        	<?php foreach ($list as $key => $value) { ?>
        	<tr>
        		<td class="col-md-1"><?php echo $value['cate_id'];?></td>
	            <td class="col-md-3">
	            	<div <?php echo $value['level'] ? 'style="padding-left:'.($value['level']*20).'px;"' : '';?>><span class="glyphicon glyphicon-globe" data-id="<?php echo $value['cate_id'];?>"></span>&nbsp;<?php echo $value['name'];?></div>
	            </td>
	            <td class="col-md-2 f16 sort-btn-content">
	            	<span class="glyphicon glyphicon-arrow-up"></span>
	            	<span class="glyphicon glyphicon-chevron-up ml10"></span>
	            	<span class="glyphicon glyphicon-chevron-down ml10"></span>
	            	<span class="glyphicon glyphicon-arrow-down ml10"></span>
	            </td>
	            <td class="col-md-2">
	            	<button class="btn btn-primary btn-xs ml4 modify" data-id="<?php echo $value['cate_id'];?>"><span class="glyphicon glyphicon-edit"></span>&nbsp;修改</button>
	            	<button class="btn btn-success btn-xs ml4 add" data-id="<?php echo $value['parent_id'];?>"><span class="glyphicon glyphicon-plus"></span>&nbsp;增加</button>
	            	<button class="btn btn-danger btn-xs ml4 delete" data-id="<?php echo $value['cate_id'];?>"><span class="glyphicon glyphicon-trash"></span>&nbsp;删除</button>
	            </td>
        	</tr>
        	<?php } ?>
        	<?php }?>
	    </tbody>
	</table>
</div>
<!-- 管理弹窗 -->
<div id="dealbox" class="hidden">
	<div class="mask"></div>
	<div class="centerShow">
	    <form class="form-horizontal">
	        <button type="button" class="close" aria-hidden="true">&times;</button>
	        <div class="f24 dealbox-title">品类管理</div>
	        <input type="hidden" name="cate_id" value="0">
	        <div class="input-group">
	            <div class="input-group-addon"><span>名称：</span></div>
	            <input type="text" class="form-control" name="name">
	        </div>
	        <div class="input-group">
	            <div class="input-group-addon"><span>图片：</span></div>
	            <div class="form-category-img" title="点击修改" onclick="document.getElementById('uploadImg').click();">
	                <img src="<?php echo staticUrl('image/common/noimg.png');?>" alt="" id="preview_pic">
	            </div>
	            <input type="file" name="image" style="display: none" id="uploadImg">
	        </div>
	        <button type="submit" class="btn btn-primary btn-lg btn-block">确认</button>
	    </form>
	</div>
</div>
<!-- 多语言弹窗 -->
<div id="dealbox-language" class="hidden">
	<div class="mask"></div>
	<div class="centerShow">
	    <form class="form-horizontal">
	        <button type="button" class="close" aria-hidden="true">&times;</button>
	        <div class="f24 dealbox-title">多语言配置</div>
	        <input type="hidden" name="cate_id" value="0">
	        <table class="table table-bordered table-hover">
	        	<tbody>
	        		<tr>
	        			<th style="width:88px">语言名称</th>
	        			<th>文本</th>
	        		</tr>
	        		<?php if (empty($language)){?>
	        		<tr><td colspan="2"><div class="tc co">没有获取到语言配置</div></td></tr>
        			<?php } else { ?>
        			<?php foreach ($language as $key => $value) {?>
	        		<tr>
	        			<th>
	        				<span><?php echo $value['name'];?></span>
	        			</th>
	        			<td class="p0">
	        				<input type="text" name="language[<?php echo $value['lan_id'];?>]" class="input">
	        			</td>
	        		</tr>
        			<?php } ?>
        			<?php } ?>
	        	</tbody>
	        </table>
	        <button type="submit" class="btn btn-primary btn-lg btn-block mt20">确认</button>
	    </form>
	</div>
</div>
<script type="text/javascript">
$(function(){
	CATEGORYLIST.init();
});
</script>
<?php $this->load('common/footer');?>