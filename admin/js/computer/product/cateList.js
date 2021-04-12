var CATEGORYLIST = {
	init: function() {
		$('#dealbox').offsetCenter();
		$('#dealbox-language').offsetCenter();
		$('#dealbox .switch_botton').on('click', function(){
	    	var status = $(this).data('status');
	    	status = status == 0 ? 1 : 0;
	    	$(this).switchBtn(status);
	    	$(this).next().val(status);
	    });
	    //新增修改
	    $('.btn.modify').on('click', function(){
	    	var btnobj = $(this);
	    	var id = btnobj.data('id');
	    	btnobj.button('loading');
	    	CATEGORYLIST.loadData(id, function(data){
	    		CATEGORYLIST.initData(data);
	    		$('#dealbox').dealboxShow();
	    		btnobj.button('reset');
	    	});
	    });
	    //多语言配置
	    $('.glyphicon-globe').on('click', function(){
	    	var id = $(this).data('id');
	    	post(URI+'product/cateList', {opn: 'getCateLanguage', cate_id: id}, function(data){
	    		var obj = $('#dealbox-language');
	    		obj.find('input[name="cate_id"]').val('id');
	    		obj.find('table input').val('');
	    		for (var i in data) {
	    			obj.find('table input[name="language['+data[i].lan_id+']"]').val(data[i].name);
	    		}
	    		obj.dealboxShow();
			});
	    });
	},
	loadData: function(id, callback) {
		if (id) {
			post(URI+'product/cateList', {opn: 'getCateInfo', cate_id: id}, function(data){
				callback(data);
			});
		} else {
			callback({});
		}
	},
	initData: function(data) {
		var obj = $('#dealbox');
		if (data) {
			obj.find('input[name="cate_id"]').val(data.cate_id);
			obj.find('input[name="name"]').val(data.name);
			obj.find('input[name="image"]').val(data.avatar);
			obj.find('.form-category-img img').attr('src', data.avatar_format);
		} else {
			obj.find('input[name="cate_id"]').val(0);
			obj.find('input[name="name"]').val('');
			obj.find('input[name="image"]').val('');
		}
		return true;
	},
};