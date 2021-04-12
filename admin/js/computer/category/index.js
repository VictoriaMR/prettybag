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
	    	post(URI+'category', {opn: 'getCateLanguage', cate_id: id}, function(data){
	    		var obj = $('#dealbox-language');
	    		obj.find('input[name="cate_id"]').val(id);
	    		obj.find('table input').val('');
	    		for (var i in data) {
	    			obj.find('table input[name="language['+data[i].lan_id+']"]').val(data[i].name);
	    		}
	    		obj.dealboxShow();
			});
	    });
	    //保存数据
	    $('#dealbox .save-btn').on('click', function(){
	    	var name = $('#dealbox form input[name="name"]').val();
	    	if (name == '') {
	    		errorTips('名称不能为空');
	    		return false;
	    	}
	    	var obj = $(this);
	    	obj.button('loading');
	    	post(URI+'category', $('#dealbox form').serializeArray(), function(){
	    		window.location.reload();
	    	});
	    	obj.button('reset');
	    });
	    //保存语言
	    $('#dealbox-language .save-btn').on('click', function(){
	    	var obj = $(this);
	    	obj.button('loading');
	    	post(URI+'category', $('#dealbox-language form').serializeArray(), function(){
	    		obj.button('reset');
	    		$('#dealbox-language').dealboxHide();
	    	});
	    	return false;
	    });
	},
	loadData: function(id, callback) {
		if (id) {
			post(URI+'category', {opn: 'getCateInfo', cate_id: id}, function(data){
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