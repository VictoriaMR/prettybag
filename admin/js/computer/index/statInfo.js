var STATINFO = {
	init: function() {
		this.getInfo();
		var setInterval = window.setInterval(function(){
			STATINFO.getInfo();
		}, 4500);
	},
	getInfo: function() {
		$.post(URI + 'index/statInfo', {opn: 'getSystemInfo'}, function(res){
			if (res.code == 200) {
				STATINFO.initdata(res.data);
			}
		});
	},
	initdata: function(data) {
	}
};