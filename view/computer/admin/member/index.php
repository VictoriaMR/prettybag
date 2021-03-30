<?php $this->load('common/header');?>
<div class="container-fluid">
	<form action="<?php echo url();?>">
		<div class="row-item">
			<input type="hidden" name="status" value="<?php echo $status;?>">
			<div class="btn-group" role="group">
				<button type="button" data-id="-1" class="btn <?php echo ($status == 1 || $status == 0) ? 'btn-default' : 'btn-primary';?>">全部</button>
				<button type="button" data-id="0" class="btn <?php echo $status == 0 ? 'btn-primary' : 'btn-default';?>">未启用</button>
				<button type="button" data-id="1" class="btn <?php echo $status == 1 ? 'btn-primary' : 'btn-default';?>">已启用</button>
			</div>
		</div>
		<div class="mt20">
			<div class="form-group">
				<input type="text" class="form-control" placeholder="Search">
	        </div>
	        <div class="form-group">
				<input type="text" class="form-control" placeholder="Search">
	        </div>
		</div>
	</form>
	<table class="table table-hover">
		
	</table>
	<?php echo page($size, $total);?>
</div>
<script type="text/javascript">
$(function(){
	MEMBERLIST.init();
});
</script>
<?php $this->load('common/footer');?>