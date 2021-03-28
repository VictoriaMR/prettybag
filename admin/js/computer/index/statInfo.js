var STATINFO = {
	init: function() {
		this.getInfo();
		var setInterval = window.setInterval(function(){
			STATINFO.getInfo();
		}, 5000);
	},
	getInfo: function() {
		$.post(URI + 'index/statInfo', {opn: 'getSystemInfo'}, function(res){
			if (res.code == 200) {
				STATINFO.initdata(res.data);
			}
		});
	},
	initdata: function(data) {
		if (data) {
			for (var i in data) {
				$('#' + i).text(data[i]);
			}
		}
	}
};