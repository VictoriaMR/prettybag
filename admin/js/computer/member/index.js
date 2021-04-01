var MEMBERLIST = {
	init: function() {
		$('#dealbox').offsetCenter();
	    $('#add-data-btn').on('click', function(){
	    	var obj = $(this);
	    	obj.button('loading');
	    	MEMBERLIST.initDealbox(0, function(){
	    		obj.button('reset');
	    	});
	    });
	    //保存按钮
	    $('#dealbox .btn.save').on('click', function(){
	    	
	    });
	    //改变状态按钮
	    $('#data-list .switch_botton').on('click', function(){
	    	var obj = $(this);
	    	var status = obj.data('status') == 0 ? 1 : 0;
	    	$.post(URI+'member', {opn:'modify', mem_id: $(this).parents('tr').data('id'), status: status}, function(res) {
	    		if (res.code == 200) {
	    			obj.switchBtn(status);
					successTips(res.message);
				} else {
					errorTips(res.message);
				}
	    	});
	    });
	},
	initDealbox: function(mem_id, callback) {
		if (mem_id) {
			$.post(URI+'member', {opn:'getInfo', mem_id: mem_id}, function(res) {
				if (res.code == 200) {
					MEMBERLIST.dealboxData(res.data, callback);
				} else {
					errorTips(res.message);
				}
			});
		} else {
			MEMBERLIST.dealboxData({}, callback);
		}
	},
	dealboxData: function(data, callback) {
		var obj = $('#dealbox');
		obj.find('input').val('');
		if (data) {
			obj.find('.dealbox-title').text('编辑管理员');
			for (var i in data) {
				obj.find('[name="'+i+'"]').show().val(data[i]);
			}
		} else {
			obj.find('.dealbox-title').text('新增管理员');
			obj.find('input[name="salt"]').hide();
		}
		obj.dealboxShow();
		if (callback) {
			callback();
		}
	}
};