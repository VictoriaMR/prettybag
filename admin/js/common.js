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
$(function(){
	//选择按钮组点击
	$('form .btn-group .btn').on('click', function(){
		var obj = $(this).parents('.row-item').find('input[type="hidden"]');
		if (obj.length > 0) {
			obj.val($(this).data('id'));
			obj.parents('form').eq(0).submit();
		}
	});
})