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
function confirm(msg, callbck) {
	if ($('#confirm-pop').length == 0) {
		var html = '<div id="confirm-pop">\
						<div class="mask"></div>\
						<div class="content">\
							<div class="message f18 tc"></div>\
							<div class="mt20">\
								<button class="btn cancel left w30">取消</button>\
								<button class="btn btn-primary right confirm w30">确定</button>\
								<div class="clear"></div>\
							</div>\
						</div>\
					</div>';
		$('body').append(html);
	}
	$('#confirm-pop').find('.message').text(msg);
	$('#confirm-pop').show();
	$('#confirm-pop').on('click', '.btn.cancel, .mask', function(){
		$('#confirm-pop').hide();
	});
	$('#confirm-pop').on('click', '.btn.confirm', function(){
		callbck($(this));
	});
}
function post(uri, param, success, error) {
	$.post(uri, param, function(res) {
		if (res.code == 200) {
			if (res.message) {
				successTips(res.message)
			}
			if (success) {
				success(res.data);
			}
		} else {
			errorTips(res.message);
			if (error) {
				error();
			}
		}
	});
}
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
function progressing(val) {
    if (document.readyState == 'complete') {
        val = val + 50;
        val = val > 100 ? 100 : val;
    } else {
        if (val < 80) {
            val = val + 20;
        }
    }
    $('#progressing').stop(true,true).animate({'width':val+'%'}, 100, function(){
        if (val >= 100) {
            $('#progressing').fadeOut(150);
        } else {
            progressing(val);
        }
    });
    return true;
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
	    h = (h / 2) - (obj.actual('innerHeight') / 2);
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
	$.fn.dealboxShow = function(title, width, height) {
		var obj = $(this);
		obj.offsetCenter();
		$('body').css({'overflow': 'hidden'});
		if (isScroll()) {
			$('body').css({'padding-right': '6.5px'});
		}
		if (title) {
			obj.find('.dealbox-title').text(title);
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
		obj.data('status', status);
		if (status == 1) {
			obj.find('.switch_status').removeClass('off').addClass('on');
		} else {
			obj.find('.switch_status').removeClass('on').addClass('off');
		}
		return obj;
	};
	$.fn.formFilter = function() {
		var obj = $(this);
		var status = true;
		obj.find('[required="required"]').each(function(){
			var val = $(this).val();
			if (val == '') {
				errorTips($(this).prev().text()+'不能为空');
				status = false;
				return false;
			}
		});
		return status;
	};
}(jQuery));
$(function(){
	$('form .btn-group .btn').on('click', function(){
		var obj = $(this).parents('.row-item').find('input[type="hidden"]');
		if (obj.length > 0) {
			obj.val($(this).data('id'));
			obj.parents('form').eq(0).submit();
		}
	});
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
    $('#progressing').show();
    progressing(20);
    var progressingTimeHandle = null;
});