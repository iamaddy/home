$(function(){
	var ConfigureModel = Backbone.Model.extend({
		url: 'index.php/abtest/configure/add',
		defaults: {
			name: '',
			pdescribe: '',
			url: '',
			type: '1',
			defaultV: '1',
			mails: '',
			relatedid: '0',
			version: []
		},
		initialize: function(){
			this.bind('error', function(model, error){
				alert(error);
			});
		},
		validate: function(attribute) {
			if(attribute.name == '') {
				return '项目名称不能为空';
			}
			if(attribute.url == '') {
				return 'url不能为空';
			}
			var totalRatio = 0;
			for(var i=0; i < attribute.version.length; i++){
				totalRatio +=  parseFloat(attribute.version[i].percent);
			}
			if(totalRatio != 100) {
				return '版本百分比总和需为100';
			}
		}
	});

	var ConfigureCollection = Backbone.Collection.extend({
		model: ConfigureModel
	})
})
