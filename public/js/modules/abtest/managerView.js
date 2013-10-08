$(function(){
	var lists = new ManagerCollection();
	var ManagerView = Backbone.View.extend({
		tagName: 'tr',
		template: _.template($('#list-template').html()),
		render: function(){
			this.$el.html(this.template(this.model.toJSON()));
			return this;
		},
		 initialize: function() {
			 alert($('#list-template').html());
			 lists.on('all', this.render, this);
			 lists.fetch({error: function(model, error){alert(error);}});
			 lists.each(this.addOne);
			 // this.render();
		 },
		addOne: function(model) {
			this.$("#listcontent").append(this.$el);
		}
	});
	var manager = new ManagerView();
})
