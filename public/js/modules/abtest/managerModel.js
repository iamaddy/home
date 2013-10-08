var ManagerModel = Backbone.Model.extend({
	defaults: {
		project_name: '',
		url: '',
		default_version: '',
		create_time: '',
		status: '',
		username: '',
		id: ''
	}
});
var ManagerCollection = Backbone.Collection.extend({
	url: 'index.php/abtest/manager/getdata',
	model: ManagerModel
})