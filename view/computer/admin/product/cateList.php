<?php $this->load('common/header');?>
<div class="container-fluid">
	<div class="row-item">
		<div class="right">
            <button class="btn btn-info" type="button" style="width: 200px; margin-right: 20px;"><i class="glyphicon glyphicon-asterisk"></i> 显示统计数据</button>
            <button class="btn btn-success" type="button" style="width: 200px;"><i class="glyphicon glyphicon-plus"></i> 添加子类目</button>
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
	            	<div <?php echo $value['level'] ? 'style="padding-left:'.($value['level']*20).'px;"' : '';?>><?php echo $value['name'];?></div>
	            </td>
	            <td class="col-md-2 f16 sort-btn-content">
	            	<span class="glyphicon glyphicon-arrow-up"></span>
	            	<span class="glyphicon glyphicon-chevron-up ml10"></span>
	            	<span class="glyphicon glyphicon-chevron-down ml10"></span>
	            	<span class="glyphicon glyphicon-arrow-down ml10"></span>
	            </td>
	            <td class="col-md-2">
	            	<button class="btn btn-primary btn-xs ml4 modify"><span class="glyphicon glyphicon-edit"></span>&nbsp;修改</button>
	            	<button class="btn btn-success btn-xs ml4 add"><span class="glyphicon glyphicon-plus"></span>&nbsp;增加</button>
	            	<button class="btn btn-danger btn-xs ml4 delete"><span class="glyphicon glyphicon-trash"></span>&nbsp;删除</button>
	            </td>
        	</tr>
        	<?php } ?>
        	<?php }?>
	    </tbody>
	</table>
</div>
<?php $this->load('common/footer');?>