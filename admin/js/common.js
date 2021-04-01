var VERIFY = {
	phone: function (phone) {
		var reg = /^1[3456789]\d{9}$/;
		return VERIFY.check(phone, reg);
	},
	email: function (email) {
		var reg = /^([\.a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-])+/;
		return VERIFY.check(email, reg);
	},
	password: function (password) {
		var reg = /^[0-9A-Za-z]{6,}/;
		return VERIFY.check(password, reg);
	},
	code: function(code) {
		var reg = /^[a-zA-Z0-9]{4,}/;
		return VERIFY.check(code, reg);
	},
	check: function(input, reg) {
		input = input.trim();
		if (input == '') return false;
		return reg.test(input);
	}
};
function addRightTips(info, type, delay) {
    if(typeof delay == 'undefined') {
        delay = 5000;
    }
    info = info.replace(/\n/g,'<br>');
    if($('#rightTips').length == 0) {
        $('body').append('<div id="rightTips"></div>');
        $('#rightTips').on('click', '.info .glyphicon-remove', function(){
            $(this).parent().remove();
        });
    }
    var timestamp = new Date().getTime();
    var str='<div class="info '+type+'" id="info_'+timestamp+'"><i class="glyphicon glyphicon-remove"></i>'+info+'</div>';
    $('#rightTips').prepend(str);
    $("#info_" + timestamp).delay(delay).fadeOut('slow', function () {
        $("#info_" + timestamp).remove()
    });
}
function successTips(msg) {
	addRightTips(msg, 'success');
}
function errorTips(msg) {
	addRightTips(msg, 'error');
}
function isScroll() {
    return document.body.scrollHeight > (window.innerHeight || document.documentElement.clientHeight);
}
(function($){
	$.fn.offsetCenter = function(width, height) {
	    var obj = $(this).find('.centerShow');
	    if(typeof width != 'undefined' && width>0){
	        var w = width;
	    } else {
	        var w = $(window).innerWidth();
	    }
	    w = (w - obj.innerWidth())/2;
	    if(typeof height != 'undefined' && height>0){
	        var h = height;
	    } else {
	        var h = $(window).innerHeight();
	    }
	    h = (h / 2) - (h - obj.actual('innerHeight'))/2 - 50;
	    obj.css('position','fixed');
	    obj.css('top',h+'px');
	    obj.css('left',w+'px');
	    if (obj.data("resizeSign") !='ok') {
	        obj.data('resizeSign','ok');
	        $(window).resize(function () {
	            obj.offsetCenter(width, height);
	        });
	        obj.find('.close').on('click', function() {
	            obj.parent().dealboxHide();
	        });
	        obj.parent().find('.mask').on('click', function() {
	            obj.parent().dealboxHide();
	        });
	    }
	};
	$.fn.dealboxShow = function(width, height) {
		var obj = $(this);
		$('body').css({'overflow': 'hidden'});
		if (isScroll()) {
			$('body').css({'padding-right': '6.5px'});
		}
		obj.show();
		return obj;
	};
	$.fn.dealboxHide = function(width, height) {
		var obj = $(this);
		$('body').css({'overflow': 'auto'});
		$('body').css({'padding-right': 0});
		obj.hide();
		return obj;
	};
	$.fn.switchBtn = function(status) {
		var obj = $(this);
		console.log(status,'status')
		obj.data('status', status);
		if (status == 1) {
			obj.find('.switch_status').removeClass('off').addClass('on');
		} else {
			obj.find('.switch_status').removeClass('on').addClass('off');
		}
		return obj;
	}
}(jQuery));
$(function(){
	//选择按钮组点击
	$('form .btn-group .btn').on('click', function(){
		var obj = $(this).parents('.row-item').find('input[type="hidden"]');
		if (obj.length > 0) {
			obj.val($(this).data('id'));
			obj.parents('form').eq(0).submit();
		}
	});
	//时间选择插件
    $('.form_datetime').datetimepicker({
        language: 'zh-CN',
        format: 'yyyy-mm-dd',
        weekStart: 1,
        todayBtn:  1,
        autoclose: 1,
        todayHighlight: 1,
        startView: 2,
        forceParse: 0,
        clearBtn: 1,
        minView: 'month'
    });
});