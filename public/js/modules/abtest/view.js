$(function(){
	var configure = new ConfigureCollection;
	var Item = Backbone.Model.extend({
		
	});
	var ListView = Backbone.View.extend({    
	    el: $('body'),
	    template : _.template($('#item-template').html()),
	    
	    events: {
	    	'click input#project-check': 'changeState',
	    	'click a#tipAdd': 'addVersion',
	    	'click a[action="remove"]': 'delVersion',
	    	'click button#savebtn': 'addData'
	    },
	    
	    initialize: function(){
	    	this.$('#tipAdd').tooltip(); 
	    	this.render();
	    },
	    
	    render: function(){
	    	return this;
	    },
	    
	    changeState: function(){
	    	if(!!$('#project-check').attr("checked")) {
				$('#related-id').removeAttr('disabled');
			} else{
				$('#related-id').attr('disabled', 'disabled');
			}
	    },
	    
	    addVersion: function() {
	    	var i = parseInt($('[data-node-name="abtest_item"]:last').attr('id').substr(4))+1;
	    	var item = new Item();
	    	item.set('id', i);
	    	$('[name="abtest-list-table"]').append(this.template(item.toJSON()));
			$('#defaultVersion').append('<option value="'+i+'">'+i+'</option>');
	    },
	    delVersion: function(e){
	    	var i = $(e.currentTarget).attr('data');
	    	var abtest_item = $('[data-node-name="abtest_item"]');
	    	
			if(abtest_item.length > i) {
				alert("版本号必须连续，需从最后版本删除");
				return;
			}
			$('#item'+i).fadeOut("slow", function(){
				$(this).remove();
			})
			$('#defaultVersion option[value='+i+']"').remove();
	    },
	    addData: function(){
	    	var data = parseData();
	    	if(!data) {
	    		return;
	    	}
		    configure.create(data, {success: function(model, response){
		    	if(response.status) {
		    		DD.Redirect.url(DD.baseUrl()+'/abtest/manager/');
		    	} else {
		    		alert('保存失败');
		    	}
		    }});
	    }
	});
	function parseData(){
		var data = {};
	    data.name = $('input[name="project_name"]').val();
	    data.pdescribe = $('#project-describe').val();
	    data.url = $('input[name="abtest-url"]').val();
	    data.type = $('input[name="abtest[type]"]:checked').val();
	    data.defaultV = $('#defaultVersion').val();
		data.mails = $('#partner-mails').val();
		data.relatedid = $('#related-id').val();
		
	    data.version = [];
	    $('[data-node-name="abtest_item"]').each(function(i) {
	    	if($('[name="version[percent]"]', this).val()) {
	    		  data.version[i] = {versionnum: $('span[name="adtest_version"]', this).text(),
	    				  				percent: $('[name="version[percent]"]', this).val(),
	    				  				describe: $('[name="version[describe]"]', this).val(), 
	    				  				param1: $('[name="version[param1]"]', this).val(), 
	    				  				param2: $('[name="version[param2]"]', this).val(), 
	    				  				param3: $('[name="version[param3]"]', this).val(),
	    				  				param4: $('[name="version[param4]"]', this).val(), 
	    				  				param5: $('[name="version[param5]"]', this).val()};
	    	}
	    });
	    
	    if(!data.name) {
	    	showError($('input[name="project_name"]'));
	    	return  false;
	    }
	    if(!data.url) {
	    	showError($('input[name="abtest-url"]'));
	        return false;
	    }
	    if(!$('[name="version[percent]"]').val()) {
	    	showError($('[name="version[percent]"]'));
	        return false;
	    }
	    if(jQuery.isEmptyObject(data.version)) {
	        return false;
	    }
	    if(0 == data.defaultV) {
	    	showError($('#defaultVersion'));
            return false;
        }
	    return data;
	}
	/**
	 * 表单变红
	 */
	function showError(object) {
		$(object).one('focus', function() {$(object).parents('.control-group').removeClass('error')}).parents('.control-group').addClass('error');
	}
	var listView = new ListView();
})